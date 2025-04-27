<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected const TEST_DATA = [
        ['id' => 1, 'name' => 'AAA'],
        ['id' => 2, 'name' => 'BBB'],
    ];
}
