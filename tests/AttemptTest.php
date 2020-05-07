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
        $this->assertEquals(
            attempt(function () {
                return 1;
            }),
            (new Attempt())->try(function () {
                return 1;
            })
        );
    }

    public function testTryAndCatch()
    {
        $attempt = new Attempt();

        $attempter = function () {
            return conditional(!isset($data), new AttemptTestException(), 1);
        };

        $result = $attempt->try($attempter)
            ->catch(AttemptTestException::class)
            ->done();

        $this->assertEquals(null, $result);
    }

    public function testAttemptSkipsAGivenException()
    {
        $data = [];

        $attempter = function () use ($data) {
            return conditional(!isset($data[1]), new AttemptTestException('1 failed'))
                ->else($data[1]);
        };

        $this->expectException(AttemptTestException::class);

        attempt($attempter)->catch(AttemptTest2Exception::class)();
    }

    public function testAttemptUsingOnHelper()
    {
        $message = 'message';

        $attempter = function () use ($message) {
            throw new AttemptTestException('1 does not exist in the data ' . $message);
        };

        $this->assertEquals(
            'Works not',
            attempt($attempter, 'Works not')
                ->catch(AttemptTestException::class)
                ->done()
        );
    }

    public function testOnMethod()
    {
        $this->assertEquals(
            null,
            Attempt::on(function () {
                conditional(true, new AttemptTestException);
            })->catch(AttemptTestException::class)()
        );
    }
}

class AttemptTestException extends Exception
{

}

class AttemptTest2Exception extends Exception
{

}