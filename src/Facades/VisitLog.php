<?php
namespace SrDev93\VisitLog\Facades;

use Illuminate\Support\Facades\Facade;

class VisitLog extends Facade
{
    /**
     * Get the binding in the IoC container
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'VisitLog';
    }

}
