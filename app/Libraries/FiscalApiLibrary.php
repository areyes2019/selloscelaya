<?php

namespace App\Libraries;

use Fiscalapi\Http\FiscalApiSettings;
use Fiscalapi\Services\FiscalApiClient;

class FiscalApiLibrary
{
    protected $client;

    public function __construct()
    {
        $settings = new FiscalApiSettings(
            getenv('FISCALAPI_URL'),
            getenv('FISCALAPI_KEY'),
            getenv('FISCALAPI_TENANT'),
            getenv('FISCALAPI_DEBUG') === 'true',
            getenv('FISCALAPI_VERIFY_SSL') === 'true',
            getenv('FISCALAPI_API_VERSION'),
            getenv('FISCALAPI_TIMEZONE')
        );

        $this->client = new FiscalApiClient($settings);
    }

    public function getClient()
    {
        return $this->client;
    }
}
