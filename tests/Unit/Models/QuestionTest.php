<?php

namespace Tests\Unit\Models;

use App\Models\Question;
use Mockery\MockInterface;


class QuestionTest extends AbstractModelTest
{
    private function setupMockBuilder(array $returnData = []): MockInterface
    {
        $mockBuilder = $this->createMockBuilderWithReturnData($returnData);

        $this->ignoreQueryConditions($mockBuilder);

        return $mockBuilder;
    }

    private function setupMockModel(MockInterface $mockBuilder): MockInterface
    {
        return $this->createPartialMockModel(Question::class, [
            'newQuery' => $mockBuilder
        ]);
    }

    public function testEnabledQuestionsReturnsEmptyCollection()
    {
        $mockBuilder = $this->setupMockBuilder();

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->enabled_questions();

        $this->assertEmpty($result);
    }

    public function testEnabledQuestionsReturnsExpectedData()
    {
        $mockBuilder = $this->setupMockBuilder(self::TEST_DATA);

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->enabled_questions();

        $this->assertEquals($this->createTestDataCollection(), $result);
    }

    public function testRequiredQuestionsReturnsEmptyCollection()
    {
        $mockBuilder = $this->setupMockBuilder();

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->required_questions();

        $this->assertEmpty($result);
    }

    public function testRequiredQuestionsReturnsExpectedData()
    {
        $mockBuilder = $this->setupMockBuilder(self::TEST_DATA);

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->required_questions();

        $this->assertEquals($this->createTestDataCollection(), $result);
    }
}
