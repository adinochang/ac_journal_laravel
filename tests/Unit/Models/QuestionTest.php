<?php

namespace Tests\Unit\Models;

use App\Question;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;


class QuestionTest extends TestCase
{
    private const TEST_DATA = [
        [1, 'A'],
        [2, 'B'],
    ];

    private function setupMockBuilder($withRequired = false, array $returnData = []): MockInterface
    {
        $mockBuilder = Mockery::mock(Builder::class);

        $mockBuilder->expects('where')
            ->with('enabled', 1)
            ->andReturnSelf();

        if ($withRequired) {
            $mockBuilder->expects('where')
                ->with('required', 1)
                ->andReturnSelf();
        }

        $mockBuilder->expects('orderBy')
            ->with('id')
            ->andReturnSelf();

        $mockBuilder->expects('get')
            ->andReturns(new Collection($returnData));

        return $mockBuilder;
    }

    private function setupMockModel(MockInterface $mockBuilder): MockInterface
    {
        $mockModel = Mockery::mock(Question::class)->makePartial();

        $mockModel->allows('newQuery')->andReturns($mockBuilder);

        return $mockModel;
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

        $this->assertEquals(new Collection(self::TEST_DATA), $result);
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

        $this->assertEquals(new Collection(self::TEST_DATA), $result);
    }
}
