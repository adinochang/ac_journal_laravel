<?php

namespace App\Http\Controllers;

use App\Entry;
use App\Question;
use App\Answer;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $entries = Entry::paginate(5);

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
            'questions' => $question_model->enabled_questions()
        ]);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        // validation
        $entry_model = new Entry();
        $entry_model->perform_request_validation($request);


        // get an array of answers from $request
        $answer_model = new Answer();
        $answers_array = $answer_model->get_answers_array_from_request($request);


        // save new entry with answers
        if (!$entry_model->save_answers($request, $answers_array))
        {
            abort('500');
        }


        // redirect to previous URL
        $previous_url = request('previous_url');
        return redirect(isset($previous_url) ? $previous_url : '/entry');
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
     * @param  \Illuminate\Http\Request  $request
     * @param  Entry  $entry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Entry $entry)
    {
        // validation
        $entry->perform_request_validation($request);


        // get an array of answers from $request
        $answer_model = new Answer();
        $answers_array = $answer_model->get_answers_array_from_request($request);


        // update entry with answers
        if (!$entry->update_answers($request, $answers_array))
        {
            abort('500');
        }


        return redirect('/entry');
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

            return redirect('/entry');
        }
        catch (\Exception $exception)
        {
            dd($exception);
            abort(404);
        }
    }
}
