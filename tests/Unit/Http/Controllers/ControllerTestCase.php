<?php

namespace Tests\Unit\Http\Controllers;

use Mockery;
use Tests\TestCase;
use Tests\CreatesApplication;


class ControllerTestCase extends TestCase
{
    use CreatesApplication;

    const VIEW_RENDERED_MESSAGE = 'viewRendered';
    const REDIRECTED_MESSAGE = 'redirected';


    /**
     * @param string $successMessage
     * @return void
     */
    public function mockRedirect(string $successMessage): void
    {
        $mockRedirect = Mockery::mock();
        $mockRedirect->allows('with')
            ->once()
            ->with('message', $successMessage)
            ->andReturn(self::REDIRECTED_MESSAGE);

        app()->bind('redirect', function () use ($mockRedirect) {
            return new class($mockRedirect) {
                private $mock;

                public function __construct($mock)
                {
                    $this->mock = $mock;
                }

                public function to($url)
                {
                    return $this->mock;
                }
            };
        });
    }
}
