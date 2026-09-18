<?php

namespace App\Services\SMS;

use Illuminate\Support\Manager;

class SmsManager extends Manager
{
    /**
     * Get the default SMS driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->config->get('services.sms.default', 'dummy');
    }

    /**
     * Create an instance of the dummy driver.
     *
     * @return SmsProviderInterface
     */
    public function createDummyDriver()
    {
        return new DummySmsProvider;
    }
}
