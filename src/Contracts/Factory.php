<?php

namespace Imarishwa\MpesaBridge\Contracts;

interface Factory
{
    /**
     * Get a driver implementation.
     *
     * @param string $driver
     *
     * @return mixed
     */
    public function driver($driver = null);
}
