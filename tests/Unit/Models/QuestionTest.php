<?php

namespace Tests\Unit\Models;

use App\Question;
use Mockery\MockInterface;


class QuestionTest extends AbstractModelTest
{
    private function setupMockBuilder($withRequired = false, array $returnData = []): MockInterface
    {
        $mockBuilder = $this->createMockBuilderWithReturnData($returnData);

        $queryConditions[] = [
            'expects' => 'where',
            'arguments' => ['enabled', 1],
        ];

        if ($withRequired) {
            $queryConditions[] = [
                'expects' => 'where',
                'arguments' => ['required', 1],
            ];
        }

        $queryConditions[] = [
            'expects' => 'orderBy',
            'arguments' => ['id'],
        ];

        $this->applyMockQueryConditions($mockBuilder, $queryConditions);


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
        $mockBuilder = $this->setupMockBuilder(false, self::TEST_DATA);

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->enabled_questions();

        $this->assertEquals($this->createTestDataCollection(), $result);
    }

    public function testRequiredQuestionsReturnsEmptyCollection()
    {
        $mockBuilder = $this->setupMockBuilder(true);

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->required_questions();

        $this->assertEmpty($result);
    }

    public function testRequiredQuestionsReturnsExpectedData()
    {
        $mockBuilder = $this->setupMockBuilder(true, self::TEST_DATA);

        /** @var Question $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->required_questions();

        $this->assertEquals($this->createTestDataCollection(), $result);
    }
}
