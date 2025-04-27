<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Question;
use App\Models\Answer;
use App\Repositories\EntryRepository;
use App\Repositories\QuestionRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


class EntryController extends Controller
{
    protected $entryRepo;
    protected $questionRepo;


    /**
     * @param EntryRepository $entryRepo
     * @param QuestionRepository $questionRepo
     */
    public function __construct(EntryRepository $entryRepo, QuestionRepository $questionRepo)
    {
        $this->entryRepo = $entryRepo;
        $this->questionRepo = $questionRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $filter = request()->filter_date ?? null;
        $entries = $this->entryRepo->getFilteredEntries($filter);

        return view('entries.index', [
            'entries' => $entries
        ]);
    }


    /**
     * Finds entries for the home view in blog entry format
     *
     * @return Application|Factory|View
     */
    public function blog()
    {
        $entries = $this->entryRepo->getBlogEntries();

        return view('home', [
            'entries' => $entries
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('entries.create', [
            'questions' => $this->questionRepo->getEnabledQuestions()
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function store()
    {
        // validation
        $validationRules = [];
        foreach($this->questionRepo->getRequiredQuestions() as $requiredQuestion)
        {
            $validationRules['answer_' . $requiredQuestion->id] = 'required';
        }
        request()->validate($validationRules);

        if (!$this->entryRepo->saveAnswers(request()))
        {
            abort('500');
        }

        // redirect to previous URL
        $previousUrl = parse_url(request('previous_url'), PHP_URL_PATH);

        return redirect($previousUrl ?? route('entry.index'))->with('message','Save successful');
    }


    /**
     * Display the specified resource.
     *
     * @param Entry $entry
     * @return Application|ResponseFactory|Response
     */
    public function show(Entry $entry)
    {
        return response($entry);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  Entry  $entry
     * @return Application|Factory|View
     */
    public function edit(Entry $entry)
    {
        // retrieve the list of answers for this journal entry
        return view('entries.edit', [
            'entry' => $entry,
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  Entry  $entry
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Entry $entry)
    {
        $questionModel = new Question();

        // validation
        $entry->performRequestValidation(request(), $this->questionRepo->getRequiredQuestions());

        // get an array of answers from $request
        $answer = new Answer();
        $answers = $answer->getAnswersArrayFromRequest(request());

        // update entry with answers
        if (!$entry->updateAnswers($answer, $answers))
        {
            abort('500');
        }

        return redirect(route('entry.index'))->with('message','Update successful');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Entry  $entry
     * @return Application|RedirectResponse|Redirector|void
     */
    public function destroy(Entry $entry)
    {
        try {
            $entry->delete();

            $this->index();

            return redirect(route('entry.index'))->with('message','Delete successful');
        }
        catch (Exception $exception)
        {
            abort(404);
        }
    }


    /**
     * Display the reports page.
     *
     * @return Application|Factory|View
     */
    public function report()
    {
        // get the number of journal entries per month from the past year
        $monthlyCount = DB::table('ac_journal_entries')
            ->select(DB::raw('DATE_FORMAT(created_at, "%b %Y") AS `created_month`, DATE_FORMAT(created_at, "%Y%m") AS `sort_month`, COUNT(*) `entry_count`'))
            ->where('created_at', '>=', Carbon::now()->subDays(366) )
            ->groupBy('created_month', 'sort_month')
            ->orderBy('sort_month')
            ->get();

        $monthlyEntries = [];

        foreach($monthlyCount as $month)
        {
            $monthlyEntries[$month->created_month] = $month->entry_count;
        }


        // get the journal entry answers from the past year
        $entriesWords = [];
        $averageWords = [];

        $entries = Entry::where('created_at', '>=', Carbon::now()->subDays(366) )
            ->orderBy('created_at', 'asc')
            ->get();

        // for each entry count the number of words and update the average_words result array
        foreach($entries as $entry)
        {
            $wordCount = 0;
            foreach($entry->answers as $answer)
            {
                $wordCount += str_word_count($answer->answer_text);
            }

            $monthLabel = $entry->created_at->format('M Y');

            if (!isset($entriesWords[$monthLabel]))
            {
                $entriesWords[$monthLabel] = [
                    'word_count' => 0,
                    'entry_count' => 0,
                ];
            }

            // add word count total
            $entriesWords[$monthLabel]['word_count'] += $wordCount;
            $entriesWords[$monthLabel]['entry_count'] += 1;

            // update the average
            $averageWords[$monthLabel] = round($entriesWords[$monthLabel]['word_count'] / $entriesWords[$monthLabel]['entry_count'], 1);
        }


        // return report data to view
        return view('entries.report', [
            'monthly_entries' => $monthlyEntries,
            'average_words' => $averageWords,
        ]);
    }
}
