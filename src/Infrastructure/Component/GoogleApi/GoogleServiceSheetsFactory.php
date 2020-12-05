<?php

namespace App\Infrastructure\Component\GoogleApi;

use Google_Client;
use Google_Service_Sheets;

class GoogleServiceSheetsFactory
{
    public function createService(Google_Client $googleClient): Google_Service_Sheets
    {
        return new Google_Service_Sheets($googleClient);
    }
}
