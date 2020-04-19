<?php

namespace Attempt\Tests;

use Attempt\Attempt;
use PHPUnit\Framework\TestCase;

class AttemptTest extends TestCase
{
    public function testAttemptIsCreated()
    {
        $this->assertIsObject(new Attempt());
    }

    public function testTryAndCatch()
    {
        $attempt = new Attempt();

        $attempt->try()
            ->catch();
    }
}