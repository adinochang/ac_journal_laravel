<?php

namespace Tests\Unit\Models;

use App\Models\Answer;
use App\Models\Entry;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mockery;
use Mockery\MockInterface;


class EntryTest extends AbstractModelTest
{
    private const TEST_REQUIRED_LENGTH = 5;
    private const TEST_ANSWER = 'Lorem ipsum';


    private function setupMockModel(Answer $mockAnswer): MockInterface
    {

        $hasManyMock = Mockery::mock(HasMany::class);

        $hasManyMock->allows('first')->andReturn($mockAnswer);

        return $this->createPartialMockModel(Entry::class, [
            'answers' => $hasManyMock
        ]);
    }

    public function testAnswerExcerptReturnsEmptyAnswer()
    {
        /** @var Entry $model */
        $model = $this->setupMockModel(new Answer(['answer_text' => '']));

        $result = $model->answer_excerpt(self::TEST_REQUIRED_LENGTH);

        $this->assertEquals('', $result);
    }

    public function testAnswerExcerptReturnsLongAnswer()
    {
        /** @var Entry $model */
        $model = $this->setupMockModel(new Answer(['answer_text' => self::TEST_ANSWER]));

        $result = $model->answer_excerpt(self::TEST_REQUIRED_LENGTH);

        $this->assertEquals(
            substr(self::TEST_ANSWER, 0, self::TEST_REQUIRED_LENGTH) . '...', $result
        );
    }

    public function testAnswerExcerptReturnsShortAnswer()
    {
        /** @var Entry $model */
        $model = $this->setupMockModel(new Answer([
            'answer_text' => substr(self::TEST_ANSWER, 0, self::TEST_REQUIRED_LENGTH - 1)
        ]));

        $result = $model->answer_excerpt(self::TEST_REQUIRED_LENGTH);

        $this->assertEquals(
            substr(self::TEST_ANSWER, 0, self::TEST_REQUIRED_LENGTH - 1), $result
        );
    }

    public function testSaveAnswersWithoutAnswers()
    {
        /** @var Entry $model */
        $model = $this->createPartialMockModel(Entry::class, []);

        $result = $model->save_answers([]);

        $this->assertFalse($result);
    }

//    Cannot create unit tests at the moment, until the code is refactored
//    public function testSaveAnswersWithAnswers()
//    {
//
//    }

    public function testUpdateAnswersWithoutAnswers()
    {
        /** @var Entry $model */
        $model = $this->createPartialMockModel(Entry::class, []);

        $result = $model->update_answers([]);

        $this->assertFalse($result);
    }

//    Cannot create unit tests at the moment, until the code is refactored
//    public function testUpdateAnswersWithAnswers()
//    {
//
//    }
}
