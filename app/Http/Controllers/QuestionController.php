<?php

namespace App\Http\Controllers;

use App\Models\Question;



class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if(isset(request()->filter_label))
        {
            $questions = Question::where('label', 'like' , '%' . request()->filter_label . '%')->paginate(5);
        }
        else
        {
            $questions = Question::paginate(5);
        }



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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        // validation
        $validated_input = request()->validate([
            'label' => ['required','max:200'],
            'required' => 'required',
            'enabled' => 'required',
        ]);

        Question::create($validated_input);

        return redirect(route('question.index'))->with('message','Save successful');
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
     * @param  Question  $question
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Question $question)
    {
        // validation
        $validated_input = request()->validate([
            'label' => ['required','max:200'],
            'required' => 'required',
            'enabled' => 'required',
        ]);

        $question->update($validated_input);

        return redirect(route('question.index'))->with('message','Update successful');
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

            return redirect(route('question.index'))->with('message','Delete successful');
        }
        catch (\Exception $exception)
        {
            abort(404);
        }
    }
}
