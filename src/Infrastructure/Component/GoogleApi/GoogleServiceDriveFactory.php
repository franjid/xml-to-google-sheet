<?php

namespace App\Infrastructure\Component\GoogleApi;

use Google_Client;
use Google_Service_Drive;

class GoogleServiceDriveFactory
{
    public function createService(Google_Client $googleClient): Google_Service_Drive
    {
        return new Google_Service_Drive($googleClient);
    }
}
