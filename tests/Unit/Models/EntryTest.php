<?php

namespace Tests\Unit\Models;

use App\Models\Answer;
use App\Models\Entry;
use App\Models\Question;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Mockery;
use Mockery\MockInterface;


class EntryTest extends ModelTestCase
{
    private const TEST_REQUIRED_LENGTH = 5;
    private const TEST_ANSWER = 'Lorem ipsum';


    private function setupMockModel(Answer $mockAnswer): MockInterface
    {

        $hasManyMock = Mockery::mock(HasMany::class);

        $hasManyMock->allows('first')->andReturn($mockAnswer);

        return $this->createPartialMockModel(Entry::class, [
            'answers' => $hasManyMock
        ]);
    }

    private function setupMockRequest(array $testResults = []): MockInterface
    {
        return $this->createPartialMockModel(Request::class, [
            'all' => $testResults,
        ]);
    }

    public function testAnswerExcerptWithEmptyAnswer()
    {
        /** @var Entry $model */
        $model = $this->setupMockModel(new Answer(['answer_text' => '']));

        $result = $model->answer_excerpt(self::TEST_REQUIRED_LENGTH);

        $this->assertEquals('', $result);
    }

    public function testAnswerExcerptWithLongAnswer()
    {
        /** @var Entry $model */
        $model = $this->setupMockModel(new Answer(['answer_text' => self::TEST_ANSWER]));

        $result = $model->answer_excerpt(self::TEST_REQUIRED_LENGTH);

        $this->assertEquals(
            substr(self::TEST_ANSWER, 0, self::TEST_REQUIRED_LENGTH) . '...', $result
        );
    }

    public function testAnswerExcerptWithShortAnswer()
    {
        /** @var Entry $model */
        $model = $this->setupMockModel(new Answer([
            'answer_text' => substr(self::TEST_ANSWER, 0, self::TEST_REQUIRED_LENGTH - 1)
        ]));

        $result = $model->answer_excerpt(self::TEST_REQUIRED_LENGTH);

        $this->assertEquals(
            substr(self::TEST_ANSWER, 0, self::TEST_REQUIRED_LENGTH - 1), $result
        );
    }

    public function testPerformRequestValidationPass()
    {
        $testAnswers = [
            'answer_1' => 'aaa',
            'answer_2' => 'bbb',
            'answer_5' => 'eee',
        ];

        /** @var Request $mockRequest */
        $mockRequest = $this->setupMockRequest($testAnswers);

        $requiredQuestions = new Collection([
            (new Question())->forceFill(['id' => '1']),
            (new Question())->forceFill(['id' => '2']),
        ]);

        /** @var Entry $model */
        $model = $this->createPartialMockModel(Entry::class, []);

        $model->perform_request_validation($mockRequest, $requiredQuestions);

        $this->assertTrue(true);
    }

    public function testPerformRequestValidationFail()
    {
        $this->expectException(ValidationException::class);

        $testAnswers = [
            'answer_1' => 'aaa',
            'answer_5' => 'eee',
        ];

        /** @var Request $mockRequest */
        $mockRequest = $this->setupMockRequest($testAnswers);

        $requiredQuestions = new Collection([
            (new Question())->forceFill(['id' => 1]),
            (new Question())->forceFill(['id' => 2]),
        ]);

        /** @var Entry $model */
        $model = $this->createPartialMockModel(Entry::class, []);

        $model->perform_request_validation($mockRequest, $requiredQuestions);
    }

    public function testSaveAnswersWithoutAnswers()
    {
        /** @var Entry $model */
        $model = $this->createPartialMockModel(Entry::class, []);

        $result = $model->save_answers(new Answer(), []);

        $this->assertFalse($result);
    }

    public function testSaveAnswersWithAnswers()
    {
        $testAnswers = [
            'answer_1' => 'aaa',
            'answer_2' => 'bbb',
            'answer_5' => 'eee',
        ];

        /** @var Entry $entryModel */
        $entryModel = $this->createPartialMockModel(Entry::class, [
            'save' => true,
        ]);

        /** @var Answer|MockInterface $answerModel */
        $answerModel = $this->createPartialMockModel(Answer::class, []);
        $answerModel->allows('create')->times(sizeof($testAnswers))->andReturn(true);

        $result = $entryModel->save_answers($answerModel, $testAnswers);

        $this->assertTrue($result);
    }

    public function testSaveAnswersWithABlankAnswer()
    {
        $testAnswers = [
            'answer_1' => 'aaa',
            'answer_2' => 'bbb',
            'answer_5' => '',
        ];

        /** @var Entry $entryModel */
        $entryModel = $this->createPartialMockModel(Entry::class, [
            'save' => true,
        ]);

        /** @var Answer|MockInterface  $answerModel */
        $answerModel = $this->createPartialMockModel(Answer::class, []);
        $answerModel->allows('create')->times(sizeof($testAnswers) - 1)->andReturn(true);

        $result = $entryModel->save_answers($answerModel, $testAnswers);

        $this->assertTrue($result);
    }

    public function testUpdateAnswersWithoutAnswers()
    {
        /** @var Entry $model */
        $model = $this->createPartialMockModel(Entry::class, []);

        $result = $model->update_answers(new Answer(), []);

        $this->assertFalse($result);
    }

    public function testUpdateAnswersWithAnswers()
    {
        $testAnswers = [
            1 => 'aaa',
            2 => 'bbb',
            5 => 'eee',
        ];

        /** @var Entry $entryModel */
        $entryModel = $this->createPartialMockModel(Entry::class, [
            'setUpdatedAt' => true,
            'save' => true,
        ]);
        $entryModel->forceFill(['id' => 1]);

        /** @var Answer|MockInterface $mockAnswer */
        $mockAnswer = $this->createPartialMockModel(Answer::class, []);
        $mockAnswer->allows('save')->times(sizeof($testAnswers))->andReturn(true);

        /** @var Answer|MockInterface $answerModel */
        $answerModel = $this->createPartialMockModel(Answer::class, []);
        $answerModel->allows('find_by_entry_and_question')->times(sizeof($testAnswers))->andReturn($mockAnswer);

        $result = $entryModel->update_answers($answerModel, $testAnswers);

        $this->assertTrue($result);
    }

    // TODO: test delete
}
