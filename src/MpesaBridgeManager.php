<?php

namespace Imarishwa\MpesaBridge;

use Illuminate\Support\Manager;
use Imarishwa\MpesaBridge\Contracts\Factory;
use Imarishwa\MpesaBridge\Drivers\BaseDriver;
use InvalidArgumentException;

/**
 * Class MpesaBridgeManager.
 */
class MpesaBridgeManager extends Manager implements Factory
{
    /**
     * Create an instance of the specified driver.
     *
     * @throws \Exception
     *
     * @return \Imarishwa\MpesaBridge\Drivers\BaseDriver
     */
    protected function createSTKPushDriver()
    {
        $config = $this->app['config']['mpesa'];

        return $this->buildProvider('Imarishwa\MpesaBridge\Drivers\C2B\STKPush', $config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @throws \Exception
     *
     * @return \Imarishwa\MpesaBridge\Drivers\BaseDriver
     */
    protected function createUrlRegistrarDriver()
    {
        $config = $this->app['config']['mpesa'];

        return $this->buildProvider('Imarishwa\MpesaBridge\Drivers\C2B\UrlRegistrar', $config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @throws \Exception
     *
     * @return \Imarishwa\MpesaBridge\Drivers\BaseDriver
     */
    protected function createSimulatePaymentDriver()
    {
        $config = $this->app['config']['mpesa'];

        return $this->buildProvider('Imarishwa\MpesaBridge\Drivers\C2B\SimulatePayment', $config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @throws \Exception
     *
     * @return \Imarishwa\MpesaBridge\Drivers\BaseDriver
     */
    protected function createReversalDriver()
    {
        $config = $this->app['config']['mpesa'];

        return $this->buildProvider('Imarishwa\MpesaBridge\Drivers\Reversal', $config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @throws \Exception
     *
     * @return \Imarishwa\MpesaBridge\Drivers\BaseDriver
     */
    protected function createTransactionStatusDriver()
    {
        $config = $this->app['config']['mpesa'];

        return $this->buildProvider('Imarishwa\MpesaBridge\Drivers\TransactionStatus', $config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @throws \Exception
     *
     * @return \Imarishwa\MpesaBridge\Drivers\BaseDriver
     */
    protected function createSTKPushQueryDriver()
    {
        $config = $this->app['config']['mpesa'];

        return $this->buildProvider('Imarishwa\MpesaBridge\Drivers\C2B\STKPushQuery', $config);
    }

    /**
     * Build the driver instance.
     *
     * @param  $provider
     * @param  $config
     *
     * @throws \Exception
     *
     * @return \Imarishwa\MpesaBridge\Drivers\BaseDriver $provider
     */
    protected function buildProvider($provider, $config)
    {
        if (is_subclass_of($provider, BaseDriver::class, true)) {
            return new $provider($config);
        }

        throw new \Exception('No valid driver found');
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
