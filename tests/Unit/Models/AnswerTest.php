<?php

namespace Tests\Unit\Models;

use App\Models\Answer;
use Mockery\MockInterface;
use Illuminate\Http\Request;


class AnswerTest extends ModelTestCase
{
    private const TEST_ENTRY_ID = 1;
    private const TEST_QUESTION_ID = 11;

    private function setupMockBuilder(array $returnData = []): MockInterface
    {
        $mockBuilder = $this->createMockBuilderReturnsData($returnData);

        $this->ignoreQueryConditions($mockBuilder);

        return $mockBuilder;
    }

    private function setupMockModel(MockInterface $mockBuilder): MockInterface
    {
        return $this->createPartialMockModel(Answer::class, [
            'newQuery' => $mockBuilder
        ]);
    }

    private function setupMockRequest(array $testResults = []): MockInterface
    {
        return $this->createPartialMockModel(Request::class, [
            'all' => $testResults,
        ]);
    }

    public function testGetAnswersArrayFromEmptyRequest()
    {
        /** @var Request $mockRequest */
        $mockRequest = $this->setupMockRequest();

        /** @var Answer $model */
        $model = $this->createPartialMockModel(Answer::class);

        $result = $model->getAnswersArrayFromRequest($mockRequest);

        $this->assertEquals([], $result);
    }

    public function testGetAnswersArrayFromInvalidRequest()
    {
        $testAnswers = [
            'x_1' => 'aaa',
            'x_2' => 'bbb',
        ];

        /** @var Request $mockRequest */
        $mockRequest = $this->setupMockRequest($testAnswers);

        /** @var Answer $model */
        $model = $this->createPartialMockModel(Answer::class);

        $result = $model->getAnswersArrayFromRequest($mockRequest);

        $this->assertEquals([], $result);
    }

    public function testGetAnswersArrayFromValidRequest()
    {
        $testAnswers = [
            'answer_1' => 'aaa',
            'answer_2' => 'bbb',
            'answer_5' => 'eee',
        ];

        $expectedResult = [
            '1' => $testAnswers['answer_1'],
            '2' => $testAnswers['answer_2'],
            '5' => $testAnswers['answer_5'],
        ];

        /** @var Request $mockRequest */
        $mockRequest = $this->setupMockRequest($testAnswers);

        /** @var Answer $model */
        $model = $this->createPartialMockModel(Answer::class);

        $result = $model->getAnswersArrayFromRequest($mockRequest);

        $this->assertEquals($expectedResult, $result);
    }
}
