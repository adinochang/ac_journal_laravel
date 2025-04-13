<?php

namespace Tests\Unit\Http\Controllers;

use App\Models\Entry;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\EntryController;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;


class EntryControllerTest extends ControllerTestCase
{
    // TODO: test index() when code is refactored

    // TODO: test blog() when code is refactored

    // TODO: test create() when code is refactored

    // TODO: test store() when code is refactored

    public function testShow()
    {
        $testEntry = factory(Entry::class)->make(['created_at' => time()]);

        $controller = new EntryController();
        $response = $controller->show($testEntry);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals($testEntry->toJson(), $response->getContent());
    }

    public function testEditRendersView()
    {
        $testEntry = factory(Entry::class)->make(['created_at' => time()]);

        try {
            View::shouldReceive('make')
                ->once()
                ->with('entries.edit', ['entry' => $testEntry], [])
                ->andReturn(self::VIEW_RENDERED_MESSAGE);

            $controller = new EntryController();
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
