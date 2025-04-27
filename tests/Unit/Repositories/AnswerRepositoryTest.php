<?php

namespace Tests\Unit\Repositories;

use App\Models\Answer;
use App\Repositories\AnswerRepository;
use Carbon\Carbon;
use Mockery;


class AnswerRepositoryTest extends RepositoryTestCase
{
    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testFindEntryWithoutData()
    {
        $mockQuery = Mockery::mock();
        $mockQuery->allows('get')
            ->andReturns([]);

        /** @var Answer $mockAnswer */
        $mockAnswer = Mockery::mock(Answer::class);
        $mockAnswer->allows('where')
            ->with('entry_id', 1)
            ->once()
            ->andReturnSelf();
        $mockAnswer->allows('where')
            ->with('question_id', 2)
            ->once()
            ->andReturns($mockQuery);

        $repository = new AnswerRepository($mockAnswer);
        $result = $repository->findByEntryAndQuestion(1, 2);

        $this->assertNull($result);
    }

    public function testFindEntryWithData()
    {
        $carbon = Carbon::now();

        /** @var Answer $testAnswer */
        $testAnswer = factory(Answer::class)
            ->make(['entry_id' => 11, 'question_id' => 22, 'created_at' => $carbon->unix()]);

        $mockQuery = Mockery::mock();
        $mockQuery->allows('get')
            ->andReturns([$testAnswer]);

        /** @var Answer $mockAnswer */
        $mockAnswer = Mockery::mock(Answer::class);
        $mockAnswer->allows('where')
            ->with('entry_id', 1)
            ->once()
            ->andReturnSelf();
        $mockAnswer->allows('where')
            ->with('question_id', 2)
            ->once()
            ->andReturns($mockQuery);

        $repository = new AnswerRepository($mockAnswer);
        $result = $repository->findByEntryAndQuestion(1, 2);

        $this->assertEquals($testAnswer, $result);
    }

    public function testFindEntryTakesFirstRecord()
    {
        $carbon = Carbon::now();

        /** @var Answer $testAnswer1 */
        $testAnswer1 = factory(Answer::class)
            ->make(['entry_id' => 11, 'question_id' => 22, 'created_at' => $carbon->unix()]);
        /** @var Answer $testAnswer2 */
        $testAnswer2 = factory(Answer::class)
            ->make(['entry_id' => 12, 'question_id' => 23, 'created_at' => $carbon->unix()]);

        $mockQuery = Mockery::mock();
        $mockQuery->allows('get')
            ->andReturns([$testAnswer1, $testAnswer2]);

        /** @var Answer $mockAnswer */
        $mockAnswer = Mockery::mock(Answer::class);
        $mockAnswer->allows('where')
            ->with('entry_id', 1)
            ->once()
            ->andReturnSelf();
        $mockAnswer->allows('where')
            ->with('question_id', 2)
            ->once()
            ->andReturns($mockQuery);

        $repository = new AnswerRepository($mockAnswer);
        $result = $repository->findByEntryAndQuestion(1, 2);

        $this->assertEquals($testAnswer1, $result);
    }
}
