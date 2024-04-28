<?php

declare(strict_types=1);

namespace Attempt;

use Closure;
use Transprime\Arrayed\Arrayed;

class Attempt
{
    /**
     * @var Closure|callable $triable
     */
    private $triable;

    private Arrayed $catchables;

    /**
     * @var null|mixed $default
     */
    private $default = null;
    private ?array $handler = null;

    public function __invoke(Closure $using = null)
    {
        return $this->done($using);
    }

    /**
     * Create new instance of Attempt statically and call try()
     *
     * @param Closure|callable $action
     * @return Attempt
     */
    public static function on($action, ?array $handler = null): self
    {
        return (new static())->try($action, $handler);
    }


    /**
     * @param Closure|callable $action
     * @return Attempt
     */
    public function try($action, ?array $handler = null): self
    {
        $this->catchables = arrayed();

        $this->triable = $action;

        $this->handler = $handler;

        return $this;
    }

    /**
     * Sets the default value to be returned if the exception is caught
     *
     * @param $default
     * @return $this
     */
    public function with($default): self
    {
        $this->default = $default;

        return $this;
    }

    /**
     * The exception to catch
     *
     * @param string|callable|mixed $exception
     * @return $this
     */
    public function catch(...$exception): self
    {
        $reversed = \arrayed(...$exception);

        $default = $reversed->reverse()->offsetGet(0);

        if (is_string($default)) {
            try {
                $default = new $default instanceof \Throwable ? null : $default;
            } catch (\Throwable $exception) {
                $reversed->offsetUnset(0);
            }
        }

        $exceptionList = $reversed
            ->values()
            ->map(function ($catchable) {
                return is_string($catchable) ? $catchable : get_class($catchable);
            })
            ->flip()
            ->map(function () use ($default) {
                return $default;
            });

        if ($this->catchables->empty()) {
            $this->catchables = $exceptionList;
        } else {
            $this->catchables = \arrayed($exceptionList->result())->merge($this->catchables->result());
        }

        return $this;
    }

    public function done(Closure $using = null)
    {
        try {
            return $this->handleTriable();
        } catch (\Throwable $exception) {
            $handler = function ($default) use ($using, $exception) {
                $defaultCalled = $default;

                if ($this->isClosure($default)) {
                    $defaultCalled = $default();
                }

                if ($this->isClosure($using)) {
                    return $using($exception, $defaultCalled);
                }

                return $defaultCalled;
            };

            $exceptionClass = get_class($exception);

            if ($this->catchables->offsetExists($exceptionClass)) {
                return $handler(
                    $this->catchables->offsetGet($exceptionClass) ?? $this->default
                );
            }

            unset($this->catchables);
            unset($this->triable);
            unset($this->default);

            throw $exception;
        }
    }

    /**
     * @return mixed
     * @throws AttemptInvalidCallableException
     */
    private function handleTriable()
    {
        if (!empty($this->handler)) {
            $methodCalls = [...$this->handler];
            // Add arrayed()->pop();
            // Add arrayed()->head();
            // Add arrayed()->tail();
            $method = \array_pop($methodCalls) ?? null;

            if (empty($method)) {
                throw new AttemptInvalidCallableException('The provided handler is invalid.');
            }

            return $this->getTriable()->{$method}(...$methodCalls);
        }

        return $this->getTriable()();
    }

    /**
     * @return Closure | callable
     */
    private function getTriable()
    {
        return $this->triable;
    }

    private function isClosure($value): bool
    {
        return $value instanceof Closure;
    }

    private function validateCallable($value): void
    {
        if (\arrayed($this->isClosure($value), is_callable($value)) == [false, false]) {
            throw new AttemptInvalidCallableException('The provided value is not a callable.');
        }
    }
}