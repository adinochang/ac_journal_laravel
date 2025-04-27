<?php

namespace Tests\Unit\Repositories;

use App\Models\Answer;
use App\Models\Entry;
use App\Repositories\EntryRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;


class EntryRepositoryTest extends RepositoryTestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testUnfilteredPagination()
    {
        $mockBuilder = Mockery::mock(Builder::class);
        $mockBuilder
            ->allows('paginate')
            ->with(5)
            ->once()
            ->andReturn(new LengthAwarePaginator([], 0, 5));

        /** @var Entry $testEntry */
        $testEntry = Mockery::mock(Entry::class);
        $testEntry
            ->allows('orderByDesc')
            ->with('id')
            ->once()
            ->andReturn($mockBuilder);

        $repository = new EntryRepository($testEntry);
        $result = $repository->getFilteredEntries();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function testFilteredPagination()
    {
        $mockBuilder = Mockery::mock(Builder::class);
        $mockBuilder
            ->allows('paginate')
            ->with(5)
            ->once()
            ->andReturn(new LengthAwarePaginator([], 0, 5));

        $mockQuery = Mockery::mock();
        $mockQuery->allows('orderByDesc')
            ->with('id')
            ->once()
            ->andReturn($mockBuilder);

        /** @var Entry $testEntry */
        $testEntry = Mockery::mock(Entry::class);
        $testEntry->allows('whereBetween')
            ->with('updated_at', '2025-04-25', '2025-04-25 23:59:59')
            ->once()
            ->andReturn($mockQuery);

        $repository = new EntryRepository($testEntry);
        $result = $repository->getFilteredEntries('2025-04-25');

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function testGetBlogEntries()
    {
        $mockBuilder = Mockery::mock(Builder::class);
        $mockBuilder
            ->allows('paginate')
            ->with(2)
            ->once()
            ->andReturn(new LengthAwarePaginator([], 0, 2));

        /** @var Entry $testEntry */
        $testEntry = Mockery::mock(Entry::class);
        $testEntry
            ->allows('orderByDesc')
            ->with('id')
            ->once()
            ->andReturn($mockBuilder);

        $repository = new EntryRepository($testEntry);
        $result = $repository->getBlogEntries();

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
    }

    public function testSaveAnswersWithoutAnswers()
    {
        /** @var Request $mockRequest */
        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $mockRequest->allows('all')
            ->once()
            ->andReturn([]);

        $repository = new EntryRepository();

        $result = $repository->saveAnswers($mockRequest);

        $this->assertFalse($result);
    }

    public function testSaveAnswersWithAnswers()
    {
        $testAnswers = [
            'answer_1' => 'aaa',
            'answer_2' => 'bbb',
            'answer_5' => 'eee',
        ];

        /** @var Request $mockRequest */
        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $mockRequest->allows('all')
            ->once()
            ->andReturn($testAnswers);

        /** @var Entry $mockEntry */
        $mockEntry = Mockery::mock(Entry::class)->makePartial();
        $mockEntry
            ->allows('save')
            ->once();

        /** @var Answer $mockAnswer */
        $mockAnswer = Mockery::mock(Answer::class)->makePartial();
        $mockAnswer
            ->allows('create')
            ->times(sizeof($testAnswers))
            ->andReturn(true);

        $repository = new EntryRepository($mockEntry, $mockAnswer);

        $result = $repository->saveAnswers($mockRequest);

        $this->assertTrue($result);
    }

    public function testSaveAnswersWithABlankAnswer()
    {
        $testAnswers = [
            'answer_1' => 'aaa',
            'answer_2' => 'bbb',
            'answer_5' => '',
        ];

        /** @var Request $mockRequest */
        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $mockRequest->allows('all')
            ->once()
            ->andReturn($testAnswers);

        /** @var Entry $mockEntry */
        $mockEntry = Mockery::mock(Entry::class)->makePartial();
        $mockEntry
            ->allows('save')
            ->once();

        /** @var Answer $mockAnswer */
        $mockAnswer = Mockery::mock(Answer::class)->makePartial();
        $mockAnswer
            ->allows('create')
            ->times(sizeof($testAnswers) - 1)
            ->andReturn(true);

        $repository = new EntryRepository($mockEntry, $mockAnswer);

        $result = $repository->saveAnswers($mockRequest);

        $this->assertTrue($result);
    }

}
