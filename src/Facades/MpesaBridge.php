<?php

namespace Imarishwa\MpesaBridge\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Imarishwa\MpesaBridge\MpesaBridgeManager
 */
class MpesaBridge extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Imarishwa\MpesaBridge\Contracts\Factory';
    }
}
