<?php
namespace App\Repositories;

use App\Models\Question;


class QuestionRepository
{
    protected $questionModel;


    /**
     * @param Question|null $questionModel
     */
    public function __construct(Question $questionModel = null)
    {
        $this->questionModel = $questionModel !== null ? $questionModel : new Question();
    }

    /**
     * @param $filter
     * @return mixed
     */
    public function getFilteredQuestions($filter = null)
    {
        if ($filter) {
            return $this->questionModel->where('label', 'like', '%' . $filter . '%')->paginate(5);
        }

        return $this->questionModel->paginate(5);
    }

    /**
     * @return mixed
     */
    public function getEnabledQuestions()
    {
        return $this->questionModel->where('enabled', 1)->OrderBy('id')->get();
    }

    /**
     * @return mixed
     */
    public function getRequiredQuestions()
    {
        return $this->questionModel->where('enabled', 1)->where('required', 1)->OrderBy('id')->get();
    }

    /**
     * @param array $validatedInput
     * @return mixed
     */
    public function createQuestion(array $validatedInput)
    {
        return $this->questionModel->create($validatedInput);
    }
}
