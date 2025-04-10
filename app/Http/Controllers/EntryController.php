<?php

namespace App\Http\Controllers;

use App\Models\Entry;
use App\Models\Question;
use App\Models\Answer;
use Carbon\Carbon;



class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if(isset(request()->filter_date))
        {
            $entries = Entry::whereBetween('updated_at', [request()->filter_date,  request()->filter_date . ' 23:59:59'])
                ->orderByDesc('id')->paginate(5);
        }
        else
        {
            $entries = Entry::orderByDesc('id')->paginate(5);
        }




        return view('entries.index', [
            'entries' => $entries
        ]);
    }



    /**
     * Finds entries for the home view in blog entry format
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function blog()
    {
        $entries = Entry::orderByDesc('id')->paginate(2);

        return view('home', [
            'entries' => $entries
        ]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        // retrieve the list of questions
        $question_model = new Question();



        return view('entries.create', [
            'questions' => $question_model->enabledQuestions()
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        // validation
        // TODO: Change to use DI
        $questionModel = new Question();

        $entry_model = new Entry();
        $entry_model->performRequestValidation(request(), $questionModel->requiredQuestions());

        // save new entry with answers
        $answer_model = new Answer();
        $answers_array = $answer_model->getAnswersArrayFromRequest(request());

        if (!$entry_model->saveAnswers($answer_model, $answers_array))
        {
            abort('500');
        }

        // redirect to previous URL
        $previous_url = request('previous_url');

        return redirect(isset($previous_url) ? $previous_url : route('entry.index'))->with('message','Save successful');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function show(Entry $entry)
    {
        return response($entry);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  Entry  $entry
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Entry $entry)
    {
        $questionModel = new Question();

        // validation
        $entry->performRequestValidation(request(), $questionModel->requiredQuestions());

        // get an array of answers from $request
        $answer_model = new Answer();
        $answers_array = $answer_model->getAnswersArrayFromRequest(request());

        // update entry with answers
        if (!$entry->updateAnswers($answer_model, $answers_array))
        {
            abort('500');
        }

        return redirect(route('entry.index'))->with('message','Update successful');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function destroy(Entry $entry)
    {
        try {
            $entry->delete();

            $this->index();

            return redirect(route('entry.index'))->with('message','Delete successful');
        }
        catch (\Exception $exception)
        {
            dd($exception);
            abort(404);
        }
    }



    /**
     * Display the reports page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function report()
    {
        // get the number of journal entries per month from the past year
        $monthly_count = \DB::table('ac_journal_entries')
            ->select(\DB::raw('DATE_FORMAT(created_at, "%b %Y") AS `created_month`, DATE_FORMAT(created_at, "%Y%m") AS `sort_month`, COUNT(*) `entry_count`'))
            ->where('created_at', '>=', Carbon::now()->subDays(366) )
            ->groupBy('created_month', 'sort_month')
            ->orderBy('sort_month')
            ->get();

        $monthly_entries = [];

        foreach($monthly_count as $month)
        {
            $monthly_entries[$month->created_month] = $month->entry_count;
        }



        // get the journal entry answers from the past year
        $entries_words = [];
        $average_words = [];

        $entries = Entry::where('created_at', '>=', Carbon::now()->subDays(366) )
            ->orderBy('created_at', 'asc')
            ->get();

        // for each entry count the number of words and update the average_words result array
        foreach($entries as $entry)
        {
            $word_count = 0;
            foreach($entry->answers as $answer)
            {
                $word_count += str_word_count($answer->answer_text, 0);
            }

            $month_label = $entry->created_at->format('M Y');

            if (!isset($entries_words[$month_label]))
            {
                $entries_words[$month_label] = [
                    'word_count' => 0,
                    'entry_count' => 0,
                ];
            }

            // add word count total
            $entries_words[$month_label]['word_count'] += $word_count;
            $entries_words[$month_label]['entry_count'] += 1;

            // update the average
            $average_words[$month_label] = round($entries_words[$month_label]['word_count'] / $entries_words[$month_label]['entry_count'], 1);
        }



        // return report data to view
        return view('entries.report', [
            'monthly_entries' => $monthly_entries,
            'average_words' => $average_words,
        ]);
    }
}
