<?php

declare(strict_types=1);

namespace Dacastro4\LaravelGmail\Facade;

use Illuminate\Support\Facades\Facade;

final class LaravelGmail extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravelgmail';
    }
}
