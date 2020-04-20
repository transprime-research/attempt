<?php

use Attempt\Attempt;

if (!function_exists('attempt')) {

    function attempt(Closure $action)
    {
        return (new Attempt())->try($action);
    }
}