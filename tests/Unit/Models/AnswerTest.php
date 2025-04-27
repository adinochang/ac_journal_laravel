<?php

namespace Tests\Unit\Models;

use App\Models\Answer;
use Mockery\MockInterface;
use Illuminate\Http\Request;


class AnswerTest extends ModelTestCase
{
    private function setupMockRequest(array $testResults = []): MockInterface
    {
        return $this->createPartialMockModel(Request::class, [
            'all' => $testResults,
        ]);
    }

    public function testGetAnswersFromEmptyRequest()
    {
        /** @var Request $mockRequest */
        $mockRequest = $this->setupMockRequest();

        /** @var Answer $model */
        $model = $this->createPartialMockModel(Answer::class);

        $result = $model->getAnswersArrayFromRequest($mockRequest);

        $this->assertEquals([], $result);
    }

    public function testGetAnswersFromInvalidRequest()
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

    public function testGetAnswersFromValidRequest()
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
