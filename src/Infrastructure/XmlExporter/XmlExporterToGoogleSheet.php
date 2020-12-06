<?php

namespace App\Infrastructure\XmlExporter;

use App\Domain\Entity\MappedXml;
use App\Domain\Interfaces\XmlExporterInterface;
use App\Infrastructure\XmlExporter\Exception\PermissionsErrorException;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Sheets;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;

class XmlExporterToGoogleSheet implements XmlExporterInterface
{
    private Google_Service_Sheets $googleServiceSheets;
    private Google_Service_Drive $googleServiceDrive;

    public function __construct(
        Google_Client $googleClient,
        Google_Service_Sheets $googleServiceSheets,
        Google_Service_Drive $googleServiceDrive
    )
    {
        $googleClient->setScopes([
            Google_Service_Sheets::SPREADSHEETS,
            Google_Service_Drive::DRIVE,
        ]);

        $this->googleServiceSheets = $googleServiceSheets;
        $this->googleServiceDrive = $googleServiceDrive;
    }

    public function export(MappedXml $mappedXml): string
    {
        $sheetId = $this->createSheet();
        $this->setSheetPermissions($sheetId);

        $this->exportHeaders($mappedXml, $sheetId);
        $this->exportData($mappedXml, $sheetId);

        return $sheetId;
    }

    private function createSheet(): string
    {
        $spreadsheet = $this->googleServiceSheets->spreadsheets->create(new Google_Service_Sheets_Spreadsheet([
            'properties' => [
                'title' => 'XML Data Export',
            ],
        ]));

        return $spreadsheet->spreadsheetId;
    }

    private function setSheetPermissions(string $sheetId): void
    {
        $permission = new Google_Service_Drive_Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');

        try {
            $this->googleServiceDrive->permissions->create($sheetId, $permission);
        } catch (PermissionsErrorException $e) {
            print "An error occurred: " . $e->getMessage();
        }
    }

    private function exportHeaders(MappedXml $mappedXml, string $sheetId): void
    {
        $range = 'A1';
        $body = new Google_Service_Sheets_ValueRange(['values' => [$mappedXml->getHeaders()]]);
        $params = ['valueInputOption' => 'USER_ENTERED'];

        $this->googleServiceSheets->spreadsheets_values->append($sheetId, $range, $body, $params);
    }

    private function exportData(MappedXml $mappedXml, string $sheetId): void
    {
        $range = 'A2';
        $body = new Google_Service_Sheets_ValueRange(['values' => $mappedXml->getData()]);
        $params = ['valueInputOption' => 'USER_ENTERED'];

        $this->googleServiceSheets->spreadsheets_values->append($sheetId, $range, $body, $params);
    }
}
