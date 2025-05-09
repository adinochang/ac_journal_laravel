<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Repositories\QuestionRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;


class QuestionController extends Controller
{
    protected $questionRepo;


    /**
     * @param QuestionRepository $questionRepo
     */
    public function __construct(QuestionRepository $questionRepo)
    {
        $this->questionRepo = $questionRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $filter = request()->filter_label ?? null;
        $questions = $this->questionRepo->getFilteredQuestions($filter);

        return view('questions.index', [
            'questions' => $questions
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('questions.create');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function store()
    {
        // validation
        $validatedInput = request()->validate([
            'label' => ['required','max:200'],
            'required' => 'required',
            'enabled' => 'required',
        ]);

        $this->questionRepo->createQuestion($validatedInput);

        return redirect(route('question.index'))->with('message','Save successful');
    }


    /**
     * Display the specified resource.
     *
     * @param Question $question
     * @return Application|ResponseFactory|Response
     */
    public function show(Question $question)
    {
        return response($question);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Question $question
     * @return Application|Factory|View
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
    * /**
     * @param Question $question
     * @return Application|RedirectResponse|Redirector
     */
    public function update(Question $question)
    {
        // validation
        $validatedInput = request()->validate([
            'label' => ['required','max:200'],
            'required' => 'required',
            'enabled' => 'required',
        ]);

        $question->update($validatedInput);

        return redirect(route('question.index'))->with('message','Update successful');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Question $question
     * @return Application|RedirectResponse|Redirector|void
     */
    public function destroy(Question $question)
    {
        try {
            $question->delete();

            $this->index();

            return redirect(route('question.index'))->with('message','Delete successful');
        }
        catch (Exception $exception)
        {
            abort(404);
        }
    }
}
