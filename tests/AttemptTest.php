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

    public function testAttemptHelperCreatesAttemptObject()
    {
        $this->assertEquals(attempt(fn() => 1), (new Attempt())->try(fn() => 1));
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
            return conditional(!isset($data[1]))
                ->then(new AttemptTestException('1 does not exist in the data'))
                ->else($data[1])
                ->value();
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