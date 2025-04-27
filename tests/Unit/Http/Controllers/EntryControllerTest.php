<?php

namespace Tests\Unit\Http\Controllers;

use App\Http\Controllers\QuestionController;
use App\Models\Answer;
use App\Models\Entry;
use App\Models\Question;
use App\Repositories\EntryRepository;
use App\Repositories\QuestionRepository;
use App\Http\Controllers\EntryController;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;


class EntryControllerTest extends ControllerTestCase
{
    /**
     * @param int $id
     * @param string $excerpt
     * @return Entry
     */
    public function createMockEntry(int $id, string $excerpt): Entry
    {
        /** @var Entry $mockEntry */
        $mockEntry = Mockery::mock(Entry::class)->makePartial();

        $mockEntry->id = $id;
        $mockEntry->answers = [];

        $mockEntry->allows('answerExcerpt')->andReturn($excerpt);

        return $mockEntry;
    }

    /**
     * @param int $id
     * @return Question
     */
    public function createMockQuestion(int $id): Question
    {
        /** @var Question $mockQuestion */
        $mockQuestion = Mockery::mock(Question::class)->makePartial();

        $mockQuestion->id = $id;

        return $mockQuestion;
    }

    public function testIndexUnfiltered()
    {
        $mockedEntries = collect([
            $this->createMockEntry(1, 'Test excerpt 1'),
            $this->createMockEntry(2, 'Test excerpt 2'),
        ]);

        $paginatedEntries = new LengthAwarePaginator(
            $mockedEntries,
            $mockedEntries->count(),
            15,
            1
        );

        /** @var EntryRepository $mockRepo */
        $mockRepo = Mockery::mock(EntryRepository::class)->makePartial();
        $mockRepo->allows('getFilteredEntries')
            ->with(null) // No filter passed
            ->once()
            ->andReturn($paginatedEntries);

        $this->app->instance(EntryRepository::class, $mockRepo);

        $response = $this->withoutMiddleware()->get(route('entry.index'));

        $response->assertViewIs('entries.index');
    }

    public function testIndexWithFilter()
    {
        $filterValue = '2024-04-27';

        $mockedEntries = collect([
            $this->createMockEntry(3, 'Test excerpt 3'),
            $this->createMockEntry(4, 'Test excerpt 4'),
        ]);

        $paginatedEntries = new LengthAwarePaginator(
            $mockedEntries,
            $mockedEntries->count(),
            15,
            1
        );

        /** @var EntryRepository $mockRepo */
        $mockRepo = Mockery::mock(EntryRepository::class)->makePartial();
        $mockRepo->allows('getFilteredEntries')
            ->with($filterValue) // Assert that the filter value is passed
            ->once()
            ->andReturn($paginatedEntries);

        $this->app->instance(EntryRepository::class, $mockRepo);

        $response = $this->get(route('entry.index', ['filter_date' => $filterValue]));

        $response->assertViewIs('entries.index');
        $response->assertViewHas('entries', $paginatedEntries);
    }

    public function testBlog()
    {
        $mockedEntries = collect([
            $this->createMockEntry(5, 'Test excerpt 5'),
            $this->createMockEntry(6, 'Test excerpt 6'),
        ]);

        $paginatedEntries = new LengthAwarePaginator(
            $mockedEntries,
            $mockedEntries->count(),
            2,
            1
        );

        /** @var EntryRepository $mockRepo */
        $mockRepo = Mockery::mock(EntryRepository::class)->makePartial();
        $mockRepo->allows('getBlogEntries')
            ->once()
            ->andReturn($paginatedEntries);

        $this->app->instance(EntryRepository::class, $mockRepo);

        $response = $this->withoutMiddleware()->get(route('home', ['entries' => $paginatedEntries]));

        $response->assertViewIs('home');
    }

    public function testCreate()
    {
        $mockedQuestions = collect([
            $this->createMockQuestion(1),
            $this->createMockQuestion(2),
        ]);

        /** @var QuestionRepository $mockRepo */
        $mockRepo = Mockery::mock(QuestionRepository::class)->makePartial();
        $mockRepo->allows('getEnabledQuestions')
            ->once()
            ->andReturn($mockedQuestions);

        $this->app->instance(QuestionRepository::class, $mockRepo);

        View::share('errors', new ViewErrorBag());

        $response = $this->withoutMiddleware()->get(route('entry.create'));

        $response->assertViewIs('entries.create');
        $response->assertViewHas('questions', $mockedQuestions);
    }

    public function testStoreFailValidation()
    {
        $mockedQuestions = collect([
            $this->createMockQuestion(1),
            $this->createMockQuestion(2),
        ]);

        /** @var QuestionRepository $questionRepo */
        $questionRepo = Mockery::mock(QuestionRepository::class)->makePartial();
        $questionRepo->allows('getRequiredQuestions')
            ->once()
            ->andReturn($mockedQuestions);

        /** @var Entry $mockEntry */
        $mockEntry = Mockery::mock(Entry::class)->makePartial();
        $mockEntry->allows('save')->never();

        /** @var Answer $mockAnswer */
        $mockAnswer = Mockery::mock(Answer::class)->makePartial();
        $mockAnswer->allows('create')->never();


        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();

        $exception = ValidationException::withMessages([
            'label' => ['The label field is required.'],
        ]);

        $mockRequest->allows('validate')
            ->once()
            ->andThrow($exception);

        app()->instance('request', $mockRequest);

        $entryRepo = new EntryRepository($mockEntry, $mockAnswer);

        $controller = new EntryController($entryRepo, $questionRepo);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The given data was invalid.');

        $controller->store();
    }

    public function testStore()
    {
        $mockedQuestions = collect([
            $this->createMockQuestion(1),
            $this->createMockQuestion(2),
        ]);

        $testAnswers = [
            'answer_1' => 'aaa',
            'answer_2' => 'bbb',
        ];


        /** @var QuestionRepository $questionRepo */
        $questionRepo = Mockery::mock(QuestionRepository::class)->makePartial();
        $questionRepo->allows('getRequiredQuestions')
            ->once()
            ->andReturn($mockedQuestions);

        /** @var Entry $mockEntry */
        $mockEntry = Mockery::mock(Entry::class)->makePartial();
        $mockEntry->allows('save')->once();

        /** @var Answer $mockAnswer */
        $mockAnswer = Mockery::mock(Answer::class)->makePartial();
        $mockAnswer->allows('create')
            ->times(sizeof($testAnswers));


        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $mockRequest->allows('validate')
            ->once()
            ->andReturn(true);
        $mockRequest->allows('all')
            ->twice()
            ->andReturn($testAnswers);

        app()->instance('request', $mockRequest);

        $this->mockRedirect('Save successful');


        $entryRepo = new EntryRepository($mockEntry, $mockAnswer);

        $controller = new EntryController($entryRepo, $questionRepo);
        $response = $controller->store();

        $this->assertEquals('redirected', $response);
    }

    public function testShow()
    {
        $testEntry = factory(Entry::class)->make(['created_at' => time()]);

        $entryRepo = new EntryRepository($testEntry);
        $questionRepo = new QuestionRepository(factory(Question::class)->make());

        $controller = new EntryController($entryRepo, $questionRepo);
        $response = $controller->show($testEntry);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($testEntry->toJson(), $response->getContent());
    }

    public function testEditRendersView()
    {
        $testEntry = factory(Entry::class)->make(['created_at' => time()]);

        $entryRepo = new EntryRepository($testEntry);
        $questionRepo = new QuestionRepository(factory(Question::class)->make());

        try {
            View::shouldReceive('make')
                ->once()
                ->with('entries.edit', ['entry' => $testEntry], [])
                ->andReturn(self::VIEW_RENDERED_MESSAGE);

            $controller = new EntryController($entryRepo, $questionRepo);
            $response = $controller->edit($testEntry);
        } catch (Exception $exception) {
            $response = $exception->getMessage();
        }

        $this->assertEquals(self::VIEW_RENDERED_MESSAGE, $response);
    }

    // TODO: test update() when code is refactored

    public function testDestroyWithDeleteException()
    {
        /** @var Entry $mockEntry */
        $mockEntry = Mockery::mock(Entry::class)->makePartial();
        $mockEntry->allows('delete')->once()->andThrow(new Exception('Something went wrong!'));

        /** @var EntryController $controller */
        $controller = Mockery::mock(EntryController::class)->makePartial();
        $controller->allows('index')->never();

        $this->expectException(HttpException::class);

        $controller->destroy($mockEntry);
    }

    public function testDestroy()
    {
        /** @var Entry $mockEntry */
        $mockEntry = Mockery::mock(Entry::class)->makePartial();
        $mockEntry->allows('delete')->once()->andReturns(true);

        $mockRedirect = Mockery::mock();
        $mockRedirect->allows('with')
            ->once()
            ->with('message', 'Delete successful')
            ->andReturn('redirected');

        app()->bind('redirect', function () use ($mockRedirect) {
            return new class($mockRedirect) {
                private $mock;
                public function __construct($mock) { $this->mock = $mock; }
                public function to($url) { return $this->mock; }
            };
        });


        /** @var EntryController $controller */
        $controller = Mockery::mock(EntryController::class)->makePartial();
        $controller->allows('index')->once();

        $response = $controller->destroy($mockEntry);

        $this->assertEquals('redirected', $response);
    }
}
