<?php

namespace Tests\Unit\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\QuestionController;
use App\Models\Question;
use Illuminate\Validation\ValidationException;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;


class QuestionControllerTest extends ControllerTestCase
{
    // TODO: test index() when code is refactored

    public function testCreateRendersView()
    {
        try {
            View::shouldReceive('make')
                ->once()
                ->with('questions.create', [], [])
                ->andReturn(self::VIEW_RENDERED_MESSAGE);

            $controller = new QuestionController();
            $response = $controller->create();
        } catch (Exception $exception) {
            $response = $exception->getMessage();
        }

        $this->assertEquals(self::VIEW_RENDERED_MESSAGE, $response);
    }

    // TODO: test store() when code is refactored

    public function testShow()
    {
        $testQuestion = factory(Question::class)->make();

        $controller = new QuestionController();
        $response = $controller->show($testQuestion);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($testQuestion->toJson(), $response->getContent());
    }

    public function testEditRendersView()
    {
        $testQuestion = factory(Question::class)->make();

        try {
            View::shouldReceive('make')
                ->once()
                ->with('questions.edit', ['question' => $testQuestion], [])
                ->andReturn(self::VIEW_RENDERED_MESSAGE);

            $controller = new QuestionController();
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

        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();

        $exception = ValidationException::withMessages([
            'label' => ['The label field is required.'],
        ]);

        $mockRequest->allows('validate')
            ->once()
            ->andThrow($exception);

        app()->instance('request', $mockRequest);

        $testQuestion->allows('update')->never();

        $controller = new QuestionController();

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


        $mockRequest = Mockery::mock(Request::class)->shouldIgnoreMissing();
        $mockRequest->allows('validate')
            ->once()
            ->andReturn($validInput);
        app()->instance('request', $mockRequest);

        $mockRedirect = Mockery::mock();
        $mockRedirect->allows('with')
            ->once()
            ->with('message', 'Update successful')
            ->andReturn('redirected');

        app()->bind('redirect', function () use ($mockRedirect) {
            return new class($mockRedirect) {
                private $mock;
                public function __construct($mock) { $this->mock = $mock; }
                public function to($url) { return $this->mock; }
            };
        });


        $controller = new QuestionController();
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

        /** @var QuestionController $controller */
        $controller = Mockery::mock(QuestionController::class)->makePartial();
        $controller->allows('index')->once();

        $response = $controller->destroy($testQuestion);

        $this->assertEquals('redirected', $response);
    }
}
