<?php
namespace App\Repositories;

use App\Models\Entry;
use App\Models\Answer;
use Illuminate\Http\Request;


class EntryRepository
{
    protected $entryModel;
    protected $answerModel;


    /**
     * @param Entry|null $entryModel
     * @param Answer|null $answerModel
     */
    public function __construct(Entry $entryModel = null, Answer $answerModel = null)
    {
        $this->entryModel = $entryModel !== null ? $entryModel : new Entry();
        $this->answerModel = $answerModel !== null ? $answerModel : new Answer();
    }

    /**
     * @param $filterDate
     * @return mixed
     */
    public function getFilteredEntries($filterDate = null)
    {
        if ($filterDate) {
            return $this->entryModel
                ->whereBetween('updated_at', $filterDate, $filterDate . ' 23:59:59')
                ->orderByDesc('id')->paginate(5);
        }

        return $this->entryModel->orderByDesc('id')->paginate(5);
    }

    /**
     * @return mixed
     */
    public function getBlogEntries()
    {
        return $this->entryModel->orderByDesc('id')->paginate(2);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function saveAnswers(Request $request): bool
    {
        $answersArray = $this->getAnswersArrayFromRequest($request);

        if (!isset($answersArray) || sizeof($answersArray) == 0)
        {
            return false;
        }

        // create an entry record
        $this->entryModel->save();

        // save answers for the entry
        foreach($answersArray as $questionId => $answerText)
        {
            if (strlen($answerText) > 0)
            {
                $this->answerModel->create([
                    'entry_id' => $this->entryModel->id,
                    'question_id' => $questionId,
                    'answer_text' => $answerText,
                ]);
            }
        }

        return true;
    }

    /**
     * Find all the answers from $request and format them into an array with the format of:
     * [ <question_id> => <answer_text> ]
     *
     * This array can then be fed into the save function in the Entry model
     *
     * @param Request $request
     * @return array
     */
    public function getAnswersArrayFromRequest(Request $request): array
    {
        $answers = [];

        foreach ($request->all() as $fieldName => $value)
        {
            if (strpos($fieldName, 'answer_') !== false)
            {
                // format the answers in the format of [ question_id => answer text ]
                $answers[str_replace('answer_', '', $fieldName)] = $value;
            }
        }

        return $answers;
    }
}
