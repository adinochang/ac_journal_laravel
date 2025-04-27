<?php

namespace Tests\Unit\Repositories;

use App\Models\Question;
use App\Repositories\QuestionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;


class QuestionRepositoryTest extends RepositoryTestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testUnfilteredPagination()
    {
        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class);

        $testQuestion->allows('paginate')
            ->with(5)
            ->once()
            ->andReturn(new LengthAwarePaginator([], 0, 5));

        $repository = new QuestionRepository($testQuestion);
        $result = $repository->getFilteredQuestions();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function testFilteredPagination()
    {
        $mockQuery = Mockery::mock();
        $mockQuery->allows('paginate')
            ->with(5)
            ->once()
            ->andReturn(new LengthAwarePaginator([], 0, 5));

        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class);
        $testQuestion->allows('where')
            ->with('label', 'like', '%test%')
            ->once()
            ->andReturn($mockQuery);

        $repository = new QuestionRepository($testQuestion);
        $result = $repository->getFilteredQuestions('test');

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function testEnabledQuestionsWithoutData()
    {
        $mockQuery = Mockery::mock();
        $mockQuery->allows('OrderBy')
            ->andReturnSelf();
        $mockQuery->allows('get')
            ->andReturns([]);

        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class);
        $testQuestion->allows('where')
            ->with('enabled', 1)
            ->once()
            ->andReturn($mockQuery);

        $repository = new QuestionRepository($testQuestion);
        $result = $repository->getEnabledQuestions();

        $this->assertEquals([], $result);
    }

    public function testEnabledQuestionsWithData()
    {
        $mockQuery = Mockery::mock();
        $mockQuery->allows('OrderBy')
            ->andReturnSelf();
        $mockQuery->allows('get')
            ->andReturns(self::TEST_DATA);

        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class);
        $testQuestion->allows('where')
            ->with('enabled', 1)
            ->once()
            ->andReturn($mockQuery);

        $repository = new QuestionRepository($testQuestion);
        $result = $repository->getEnabledQuestions();

        $this->assertEquals(self::TEST_DATA, $result);
    }

    public function testRequiredQuestionsWithoutData()
    {
        $mockQuery = Mockery::mock();
        $mockQuery->allows('OrderBy')
            ->andReturnSelf();
        $mockQuery->allows('get')
            ->andReturns([]);

        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class);

        $testQuestion->allows('where')
            ->with('enabled', 1)
            ->once()
            ->andReturnSelf();

        $testQuestion->allows('where')
            ->with('required', 1)
            ->once()
            ->andReturn($mockQuery);

        $repository = new QuestionRepository($testQuestion);
        $result = $repository->getRequiredQuestions();

        $this->assertEquals([], $result);
    }

    public function testRequiredQuestionsWithData()
    {
        $mockQuery = Mockery::mock();
        $mockQuery->allows('OrderBy')
            ->andReturnSelf();
        $mockQuery->allows('get')
            ->andReturns(self::TEST_DATA);

        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class);

        $testQuestion->allows('where')
            ->with('enabled', 1)
            ->once()
            ->andReturnSelf();

        $testQuestion->allows('where')
            ->with('required', 1)
            ->once()
            ->andReturn($mockQuery);

        $repository = new QuestionRepository($testQuestion);
        $result = $repository->getRequiredQuestions();

        $this->assertEquals(self::TEST_DATA, $result);
    }

    public function testCreateQuestion()
    {
        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class);

        $testQuestion->allows('create')->once();

        $repository = new QuestionRepository($testQuestion);
        $result = $repository->createQuestion([]);

        $this->assertNull($result);
    }
}
