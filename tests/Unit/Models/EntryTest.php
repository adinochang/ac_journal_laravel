<?php

namespace Tests\Unit\Models;

use App\Models\Answer;
use App\Models\Entry;
use Mockery\MockInterface;


class EntryTest extends AbstractModelTest
{
    private const TEST_REQUIRED_LENGTH = 5;
    private const TEST_ANSWER = 'Lorem ipsum';

    private function setupMockBuilder(Answer $answer): MockInterface
    {
        $mockBuilder = $this->createMockBuilderWithReturnObject($answer, 'first');

        $this->ignoreQueryConditions($mockBuilder);

        return $mockBuilder;
    }

    private function setupMockModel(MockInterface $mockBuilder): MockInterface
    {
        return $this->createPartialMockModel(Entry::class, [
            'answers' => $mockBuilder
        ]);
    }

    public function testAnswerExcerptReturnsEmptyAnswer()
    {
        $mockBuilder = $this->setupMockBuilder(new Answer(['answer_text' => '']));

        /** @var Entry $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->answer_excerpt(self::TEST_REQUIRED_LENGTH);

        $this->assertEquals('', $result);
    }

    public function testAnswerExcerptReturnsLongAnswer()
    {
        $mockBuilder = $this->setupMockBuilder(new Answer(['answer_text' => self::TEST_ANSWER]));

        /** @var Entry $model */
        $model = $this->setupMockModel($mockBuilder);

        $result = $model->answer_excerpt(self::TEST_REQUIRED_LENGTH);

        $this->assertEquals(
            substr(self::TEST_ANSWER, 0, self::TEST_REQUIRED_LENGTH) . '...', $result
        );
    }

    public function testAnswerExcerptReturnsShortAnswer()
    {
        $mockBuilder = $this->setupMockBuilder(new Answer([
            'answer_text' => substr(self::TEST_ANSWER, 0, self::TEST_REQUIRED_LENGTH - 1)
        ]));

        /** @var Entry $model */
        $model = $this->setupMockModel($mockBuilder);

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
