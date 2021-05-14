<?php

namespace Keenops\LaravelSms;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Keenops\LaravelSms\Skeleton\SkeletonClass
 */
class LaravelSmsFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-sms';
    }
}
