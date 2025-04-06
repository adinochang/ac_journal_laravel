<?php

namespace Tests\Unit\Models;

use App\Models\Question;
use Mockery\MockInterface;


class QuestionTest extends ModelTestCase
{
    private function setupMockBuilder(array $returnData = []): MockInterface
    {
        $mockBuilder = $this->createMockBuilderReturnsData($returnData);

        $this->ignoreQueryConditions($mockBuilder);

        return $mockBuilder;
    }

    private function setupMockModel(MockInterface $mockBuilder): MockInterface
    {
        return $this->createPartialMockModel(Question::class, [
            'newQuery' => $mockBuilder
        ]);
    }

    public function testEnabledQuestionsWithoutData()
    {
        $mockBuilder = $this->setupMockBuilder();

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->enabledQuestions();

        $this->assertEmpty($result);
    }

    public function testEnabledQuestionsWithData()
    {
        $mockBuilder = $this->setupMockBuilder(self::TEST_DATA);

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->enabledQuestions();

        $this->assertEquals($this->createTestDataCollection(), $result);
    }

    public function testRequiredQuestionsWithoutData()
    {
        $mockBuilder = $this->setupMockBuilder();

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->requiredQuestions();

        $this->assertEmpty($result);
    }

    public function testRequiredQuestionsWithData()
    {
        $mockBuilder = $this->setupMockBuilder(self::TEST_DATA);

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->requiredQuestions();

        $this->assertEquals($this->createTestDataCollection(), $result);
    }
}
