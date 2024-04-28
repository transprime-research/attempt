<?php

declare(strict_types=1);

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
            throw new AttemptTestException();
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
            if(!isset($data[1])) {
                throw new AttemptTestException('1 failed');
            }
            return $data[1];
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
                throw new AttemptTestException();
            })->catch(AttemptTestException::class)()
        );
    }

    public function testManyExceptions()
    {
        $this->assertEquals(
            'abc',
            Attempt::on(function () {
                throw new AttemptTestException('Attempt fails');
            })->catch(\LengthException::class, AttemptTestException::class)
                ->with('abc')
            ->done()
        );
    }

    public function testMultipleCatchMethods()
    {
        $this->assertEquals(
            'abc',
            Attempt::on(function () {
                throw new AttemptTestException('Attempt fails');
            })
                ->catch(AttemptTestException::class)
                ->catch(\LengthException::class)
                ->with('abc')
                ->done()
        );
    }

    public function testMultipleCatchMethodsWithDefaultValues()
    {
        //get default value exception matters
        $this->assertEquals(
            'ccc',
            Attempt::on(function () {
                throw new AttemptTestException('Attempt fails');
            })
                ->catch(AttemptTest2Exception::class, 'efg')
                ->catch(AttemptTestException::class, \LengthException::class, 'ccc')
                ->done()
        );

        //get default value of the first exception
        $this->assertEquals(
            'efg',
            Attempt::on(function () {
                throw new AttemptTestException('Attempt fails');
            })
                ->catch(AttemptTest2Exception::class, AttemptTestException::class, 'efg')
                ->catch(AttemptTestException::class, \LengthException::class, 'ccc')
                ->done()
        );

        //get default value from a closure of the exception
        $this->assertEquals(
            'efg',
            Attempt::on(function () {
                throw new AttemptTestException('Attempt fails');
            })
                ->catch(\LengthException::class, 'ccc')
                ->catch(AttemptTestException::class, function () {
                    return 'efg';
                })
                ->done()
        );

        //get default value from a closure of the exception, handle all in done()
        $this->assertEquals(
            'EFG',
            Attempt::on(function () {
                throw new AttemptTestException('Attempt fails');
            })
                ->catch(\LengthException::class, 'ccc')
                ->catch(AttemptTestException::class, function () {
                    return 'efg';
                })
                ->done(function ($exception, $caughtValue) {
                    return strtoupper($caughtValue);
                })
        );
    }

    public function testOtherCallablesAreAllowedOnAttempt(): void
    {
        $this->assertEquals(
            'abc',
            Attempt::on(new AttempterStub())
                ->catch(AttemptTestException::class, 'abc')
                ->done(),
        );
    }
}

class AttemptTestException extends Exception
{

}

class AttemptTest2Exception extends Exception
{

}

class AttempterStub
{
    public function __invoke()
    {
        throw new AttemptTestException('Attempt fails');
    }
}