<?php

namespace App\Services;

use Fiscalapi\FiscalApiClient;
use Fiscalapi\FiscalApiSettings;

class FiscalApiService
{
    protected $client;

    public function __construct()
    {
        $settings = new FiscalApiSettings(
            env('FISCALAPI_URL'),
            env('FISCALAPI_KEY'),
            env('FISCALAPI_TENANT'),
            [
                'debug' => env('FISCALAPI_DEBUG', false),
                'verifySsl' => env('FISCALAPI_VERIFY_SSL', true),
                'apiVersion' => env('FISCALAPI_API_VERSION', 'v4'),
                'timeZone' => env('FISCALAPI_TIMEZONE', 'America/Mexico_City')
            ]
        );

        $this->client = new FiscalApiClient($settings);
    }

    public function invoices()
    {
        return $this->client->getInvoicesService();
    }
    
    public function catalogs()
    {
        return $this->client->getCatalogsService();
    }
}