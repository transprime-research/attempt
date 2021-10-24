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
     * @param $exception
     * @return $this
     */
    public function catch(...$exception): self
    {
        $this->catchables = arrayed(...$this->catchables, ...$exception)
            ->unique() //proxied arrayed call for array_unique
            ->values();

        return $this;
    }

    public function done(Closure $using = null)
    {
        $catchableClasses = $this->catchables->map(function ($catchable) {
            return is_string($catchable) ? $catchable : get_class($catchable);
        });

        try {
            return $this->getTriable()();
        } catch (\Throwable $exception) {
            $handler = function () use ($using, $exception) {
                return $using ? $using($exception) : $this->default;
            };

            if ($catchableClasses->contains(get_class($exception))) {
                return $handler();
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