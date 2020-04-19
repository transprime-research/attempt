<?php

namespace Attempt\Tests;

use Attempt\Attempt;
use PHPUnit\Framework\Exception;
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

        $attempter = function ($data) {
            if (!isset($data[1])) {
                throw new AttemptTestException();
            }
            return $data[1];
        };

        $result = $attempt->try($attempter)
            ->using([])
            ->catch(AttemptTestException::class)
            ->then();

        $this->assertEquals(null, $result);
    }

    public function testAttemptSkipsAGivenException()
    {
        $attempt = new Attempt();

        $attempter = function ($data) {
            if (!isset($data[1])) {
                throw new AttemptTestException();
            }
            return $data[1];
        };

        $this->expectException(AttemptTestException::class);

        $attempt->try($attempter)
            ->using([])
            ->catch(AttemptTest2Exception::class)
            ->then();
    }
}

class AttemptTestException extends Exception
{

}

class AttemptTest2Exception extends Exception
{

}