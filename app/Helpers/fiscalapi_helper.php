<?php

use App\Services\FiscalApiService;

if (! function_exists('fiscalapi')) {
    function fiscalapi()
    {
        return (new FiscalApiService())->getClient();
    }
}