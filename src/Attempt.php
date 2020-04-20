<?php

namespace Attempt;

class Attempt
{
    private \Closure $triable;

    private string $catchable;

    private ?string $catchUsing;

    private array $tryUsing;

    public function try(\Closure $action)
    {
        $this->triable = $action;

        return $this;
    }

    public function using()
    {
        $this->tryUsing = func_get_args();

        return $this;
    }

    public function catch(string $exception, \Closure $using = null)
    {
        $this->catchable = $exception;
        $this->catchUsing = $using;

        return $this;
    }

    public function then()
    {
        $triable = $this->triable;
        $catchUsing = $this->catchUsing;

        try {

            return $triable(...$this->tryUsing);

        } catch (\Exception $exception) {
            conditional(get_class($exception) === $this->catchable)
                ->then(fn() => !$catchUsing ?: $catchUsing($exception))
                ->else($exception);
        }
    }
}