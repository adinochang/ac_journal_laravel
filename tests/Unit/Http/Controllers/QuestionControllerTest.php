<?php

namespace Tests\Unit\Http\Controllers;

use App\Repositories\QuestionRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\QuestionController;
use App\Models\Question;
use Illuminate\Validation\ValidationException;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;


class QuestionControllerTest extends ControllerTestCase
{
    public function testIndexUnfiltered()
    {
        $mockedQuestions = collect([
            factory(Question::class)->make(['id' => 1]),
            factory(Question::class)->make(['id' => 2]),
        ]);

        $paginatedQuestions = new LengthAwarePaginator(
            $mockedQuestions,
            $mockedQuestions->count(),
            15,
            1
        );

        /** @var QuestionRepository $mockRepo */
        $mockRepo = Mockery::mock(QuestionRepository::class)->makePartial();
        $mockRepo->allows('getFilteredQuestions')
            ->with(null) // No filter passed
            ->once()
            ->andReturn($paginatedQuestions);

        $this->app->instance(QuestionRepository::class, $mockRepo);

        $response = $this->withoutMiddleware()->get(route('question.index'));

        $response->assertViewIs('questions.index');
    }

    public function testIndexWithFilter()
    {
        $filterValue = 'test';

        $mockedQuestions = collect([
            factory(Question::class)->make(['id' => 3, 'label' => $filterValue]),
            factory(Question::class)->make(['id' => 4, 'label' => $filterValue]),
        ]);

        $paginatedQuestions = new LengthAwarePaginator(
            $mockedQuestions,
            $mockedQuestions->count(),
            15,
            1
        );

        /** @var QuestionRepository $mockRepo */
        $mockRepo = Mockery::mock(QuestionRepository::class)->makePartial();
        $mockRepo->allows('getFilteredQuestions')
            ->with($filterValue) // Assert that the filter value is passed
            ->once()
            ->andReturn($paginatedQuestions);

        $this->app->instance(QuestionRepository::class, $mockRepo);

        $response = $this->get(route('question.index', ['filter_label' => $filterValue]));

        $response->assertViewIs('questions.index');
        $response->assertViewHas('questions', $paginatedQuestions);
    }

    public function testCreateRendersView()
    {
        try {
            View::shouldReceive('make')
                ->once()
                ->with('questions.create', [], [])
                ->andReturn(self::VIEW_RENDERED_MESSAGE);

            $testQuestion = factory(Question::class)->make();
            $repository = new QuestionRepository($testQuestion);

            $controller = new QuestionController($repository);
            $response = $controller->create();
        } catch (Exception $exception) {
            $response = $exception->getMessage();
        }

        $this->assertEquals(self::VIEW_RENDERED_MESSAGE, $response);
    }

    public function testStoreFailValidation()
    {
        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class)->makePartial();
        $testQuestion->allows('create')->never();

        $repository = new QuestionRepository($testQuestion);

        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();

        $exception = ValidationException::withMessages([
            'label' => ['The label field is required.'],
        ]);

        $mockRequest->allows('validate')
            ->once()
            ->andThrow($exception);

        app()->instance('request', $mockRequest);


        $controller = new QuestionController($repository);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The given data was invalid.');

        $controller->store();
    }

    public function testStore()
    {
        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class)->makePartial();

        $validInput = [
            'label' => 'Lorem ipsum',
            'required' => false,
            'enabled' => true,
        ];

        $testQuestion->allows('create')->once()->with($validInput);

        $repository = new QuestionRepository($testQuestion);


        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $mockRequest->allows('validate')
            ->once()
            ->andReturn($validInput);
        app()->instance('request', $mockRequest);

        $this->mockRedirect('Save successful');

        $controller = new QuestionController($repository);
        $response = $controller->store();

        $this->assertEquals('redirected', $response);
    }

    public function testShow()
    {
        $testQuestion = factory(Question::class)->make();
        $repository = new QuestionRepository($testQuestion);

        $controller = new QuestionController($repository);
        $response = $controller->show($testQuestion);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($testQuestion->toJson(), $response->getContent());
    }

    public function testEditRendersView()
    {
        $testQuestion = factory(Question::class)->make();
        $repository = new QuestionRepository($testQuestion);

        try {
            View::shouldReceive('make')
                ->once()
                ->with('questions.edit', ['question' => $testQuestion], [])
                ->andReturn(self::VIEW_RENDERED_MESSAGE);

            $controller = new QuestionController($repository);
            $response = $controller->edit($testQuestion);
        } catch (Exception $exception) {
            $response = $exception->getMessage();
        }

        $this->assertEquals(self::VIEW_RENDERED_MESSAGE, $response);
    }

    public function testUpdateFailValidation()
    {
        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class)->makePartial();

        $repository = new QuestionRepository($testQuestion);

        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();

        $exception = ValidationException::withMessages([
            'label' => ['The label field is required.'],
        ]);

        $mockRequest->allows('validate')
            ->once()
            ->andThrow($exception);

        app()->instance('request', $mockRequest);

        $testQuestion->allows('update')->never();

        $controller = new QuestionController($repository);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The given data was invalid.');

        $controller->update($testQuestion);
    }

    public function testUpdate()
    {
        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class)->makePartial();

        $validInput = [
            'label' => 'Lorem ipsum',
            'required' => false,
            'enabled' => true,
        ];

        $testQuestion->allows('update')->once()->with($validInput);

        $repository = new QuestionRepository($testQuestion);


        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $mockRequest->allows('validate')
            ->once()
            ->andReturn($validInput);
        app()->instance('request', $mockRequest);

        $this->mockRedirect('Update successful');

        $controller = new QuestionController($repository);
        $response = $controller->update($testQuestion);

        $this->assertEquals('redirected', $response);
    }

    public function testDestroyWithDeleteException()
    {
        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class)->makePartial();
        $testQuestion->allows('delete')->once()->andThrow(new Exception('Something went wrong!'));

        /** @var QuestionController $controller */
        $controller = Mockery::mock(QuestionController::class)->makePartial();
        $controller->allows('index')->never();

        $this->expectException(HttpException::class);

        $controller->destroy($testQuestion);
    }

    public function testDestroy()
    {
        /** @var Question $testQuestion */
        $testQuestion = Mockery::mock(Question::class)->makePartial();
        $testQuestion->allows('delete')->once()->andReturns(true);

        $this->mockRedirect('Delete successful');

        /** @var QuestionController $controller */
        $controller = Mockery::mock(QuestionController::class)->makePartial();
        $controller->allows('index')->once();

        $response = $controller->destroy($testQuestion);

        $this->assertEquals('redirected', $response);
    }
}
