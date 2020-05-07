<?php

use Attempt\Attempt;

if (!function_exists('attempt')) {

    /**
     * @param Closure $action
     * @param mixed $default
     * @return Attempt
     */
    function attempt(Closure $action, $default = null)
    {
        if (! $default) {
            return (new Attempt())->try($action);
        }

        return (new Attempt())->try($action)
            ->with(...$default);
    }
}