<?php

namespace Attempt;

use Closure;
use Exception;

class Attempt
{
    /**
     * @var Closure $triable
     */
    private $triable;

    /**
     * @var array $catchables
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
        $this->catchables = $exception;

        return $this;
    }

    public function done(Closure $using = null)
    {
        $catchableClasses = arrayed($this->catchables)->map(function ($catchable) {
            return is_string($catchable) ? $catchable : get_class($catchable);
        })->result();

        try {
            return $this->getTriable()();
        } catch (Exception $exception) {
            return conditional(in_array(get_class($exception), $catchableClasses, true))
                ->then(function () use ($using, $exception) {
                    return $using ? $using($exception) : $this->default;
                })
                ->else($exception);
        }
    }

    private function getTriable()
    {
        return $this->triable;
    }
}