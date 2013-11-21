<?php namespace Ryun\Filters;

use Illuminate\Support\Facades\LaravelFacade;

class Facade extends LaravelFacade
{
    /**
     * Get the registered name of the component
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'ryun.filter'; }
}
