<?php

namespace Attempt;

use Closure;
use Transprime\Arrayed\Arrayed;

class Attempt
{
    /**
     * @var Closure $triable
     */
    private $triable;

    /**
     * @var Arrayed $catchables
     */
    private $catchables;

    private $default = null;

    public function __invoke(Closure $using = null)
    {
        return $this->done($using);
    }

    /**
     * Creat new instance of Attempt statically and call try()
     *
     * @param Closure $action
     * @return Attempt
     */
    public static function on(Closure $action)
    {
        return (new static())->try($action);
    }

    public function try(Closure $action)
    {
        $this->catchables = arrayed();

        $this->triable = $action;

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
                $default = new $default instanceof \Throwable ? null : $this->default;
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
            return $this->getTriable()();
        } catch (\Throwable $exception) {
            $handler = function ($default) use ($using, $exception) {
                return $using ? $using($exception) : $default;
            };

            $exceptionClass = get_class($exception);

            if ($this->catchables->offsetExists($exceptionClass)) {
                return $handler($this->catchables->offsetGet($exceptionClass) ?? $this->default);
            }

            throw $exception;
        }
    }

    /**
     * @return Closure | callable
     */
    private function getTriable()
    {
        return $this->triable;
    }
}