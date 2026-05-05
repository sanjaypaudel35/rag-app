<?php

namespace Sanjay\Ragbot\Tests;

use Illuminate\Support\Facades\DB;
use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (DB::getDriverName() !== 'pgsql') {
            config(['ragbot.vector_store.default' => 'mysql']);
        }
    }
}
