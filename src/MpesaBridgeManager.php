<?php

namespace Imarishwa\MpesaBridge;

use Illuminate\Support\Manager;
use Imarishwa\MpesaBridge\Contracts\Factory;
use InvalidArgumentException;

class MpesaBridgeManager extends Manager implements Factory
{
    /**
     * Create an instance of the specified driver.
     *
     * @return \Imarishwa\MpesaBridge\Drivers\AbstractDriver
     */
    protected function createSTKPushDriver()
    {
        $config = $this->app['config']['mpesa'];

        return $this->buildProvider('Imarishwa\MpesaBridge\Drivers\STKPush', $config);
    }

    /**
     * Build the driver instance.
     *
     * @return \Imarishwa\MpesaBridge\Drivers\AbstractDriver
     */
    protected function buildProvider($provider, $config)
    {
        return new $provider($config);
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        throw new InvalidArgumentException('No driver was specified.');
    }
}
