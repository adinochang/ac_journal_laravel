<?php

namespace App\Http\Controllers;

use App\Question;
use Illuminate\Http\Request;



class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $questions = Question::all();

        return view('questions.index', [
            'questions' => $questions
        ]);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('questions.create');
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
        $request->validate([
            'label' => ['required','max:200'],
            'required' => 'required',
            'enabled' => 'required',
        ]);



        // save new question
        $new_question = new Question();

        $new_question->label = request('label');
        $new_question->required = request('required');
        $new_question->enabled = request('enabled');

        $new_question->save();

        return redirect('/question');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Question $question)
    {
        return response($question);
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Question $question)
    {
        return view('questions.edit', [
            'question' => $question
        ]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Question  $question
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Question $question)
    {
        // validation
        $request->validate([
            'label' => ['required','max:200'],
            'required' => 'required',
            'enabled' => 'required',
        ]);



        // update question
        $question->label = request('label');
        $question->required = request('required');
        $question->enabled = request('enabled');

        $question->save();

        return redirect('/question');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Question  $question
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Question $question)
    {
        try {
            $question->delete();

            $this->index();

            return redirect('/question');
        }
        catch (\Exception $exception)
        {
            abort(404);
        }
    }
}
