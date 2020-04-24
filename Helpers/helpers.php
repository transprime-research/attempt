<?php

use Attempt\Attempt;

if (!function_exists('attempt')) {

    function attempt(Closure $action, ...$using)
    {
        if (! $using) {
            return (new Attempt())->try($action);
        }

        return (new Attempt())->try($action)
            ->with(...$using);
    }
}