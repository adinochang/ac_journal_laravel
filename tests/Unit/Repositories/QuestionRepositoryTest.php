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
