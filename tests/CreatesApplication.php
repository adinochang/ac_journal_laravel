<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application instance.
     *
     * @return Application
     */
    public function createApplication(): Application
    {
        $application = require __DIR__.'/../bootstrap/app.php';
        $application->make(Kernel::class)->bootstrap();

        return $application;
    }
}
