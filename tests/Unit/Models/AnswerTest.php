<?php

namespace Tests\Unit\Models;


use App\Answer;
use Mockery\MockInterface;

class AnswerTest extends AbstractModelTest
{
    private const TEST_ENTRY_ID = 1;
    private const TEST_QUESTION_ID = 11;

    private function setupMockBuilder(array $returnData = []): MockInterface
    {
        $mockBuilder = $this->createMockBuilderWithReturnData($returnData);

        $this->ignoreQueryConditions($mockBuilder);

        return $mockBuilder;
    }

    private function setupMockModel(MockInterface $mockBuilder): MockInterface
    {
        return $this->createPartialMockModel(Answer::class, [
            'newQuery' => $mockBuilder
        ]);
    }

    public function testFindByEntryAndQuestionsReturnsEmptyCollection()
    {
        $mockBuilder = $this->setupMockBuilder();

        /** @var Answer $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->find_by_entry_and_question(self::TEST_ENTRY_ID, self::TEST_QUESTION_ID);

        $this->assertEmpty($result);
    }

    public function testFindByEntryAndQuestionsReturnsExpectedData()
    {
        $mockBuilder = $this->setupMockBuilder([self::TEST_DATA[0]]);

        /** @var Answer $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->find_by_entry_and_question(self::TEST_ENTRY_ID, self::TEST_QUESTION_ID);

        $this->assertEquals(self::TEST_DATA[0], $result);
    }
}
