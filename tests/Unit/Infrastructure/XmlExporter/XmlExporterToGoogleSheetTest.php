<?php

namespace App\Tests\Unit\Infrastructure\XmlExporter;

use App\Infrastructure\XmlExporter\XmlExporterToGoogleSheet;
use App\Tests\Helper\HelperMappedXml;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_Permission;
use Google_Service_Drive_Resource_Permissions;
use Google_Service_Sheets;
use Google_Service_Sheets_Resource_Spreadsheets;
use Google_Service_Sheets_Resource_SpreadsheetsValues;
use Google_Service_Sheets_Spreadsheet;
use Google_Service_Sheets_ValueRange;
use PHPUnit\Framework\TestCase;

class XmlExporterToGoogleSheetTest extends TestCase
{

    private Google_Service_Sheets_Resource_Spreadsheets $spreadsheetsResourceMock;
    private Google_Service_Sheets_Resource_SpreadsheetsValues $spreadsheetsValuesResourceMock;
    private Google_Service_Drive_Resource_Permissions $drivePermissionResourceMock;

    private XmlExporterToGoogleSheet $xmlExporterToGoogleSheet;

    protected function setUp(): void
    {
        parent::setUp();

        $googleClientMock = $this->createMock(Google_Client::class);

        $googleServiceSheetsMock = $this->createMock(Google_Service_Sheets::class);
        $this->spreadsheetsResourceMock = $this->createMock(Google_Service_Sheets_Resource_Spreadsheets::class);
        $this->spreadsheetsValuesResourceMock = $this->createMock(Google_Service_Sheets_Resource_SpreadsheetsValues::class);
        $googleServiceSheetsMock->spreadsheets = $this->spreadsheetsResourceMock;
        $googleServiceSheetsMock->spreadsheets_values = $this->spreadsheetsValuesResourceMock;

        $googleServiceDriveMock = $this->createMock(Google_Service_Drive::class);
        $this->drivePermissionResourceMock = $this->createMock(Google_Service_Drive_Resource_Permissions::class);
        $googleServiceDriveMock->permissions = $this->drivePermissionResourceMock;

        $this->xmlExporterToGoogleSheet = new XmlExporterToGoogleSheet(
            $googleClientMock,
            $googleServiceSheetsMock,
            $googleServiceDriveMock
        );
    }

    public function testExport(): void
    {
        $testDataMappedXml = (new HelperMappedXml())->getTestDataMappedXml();

        $testSheetId = uniqid('', true);
        $createdSheet = new Google_Service_Sheets_Spreadsheet();
        $createdSheet->spreadsheetId = $testSheetId;

        /**
         * Sheet creation
         */
        $this->spreadsheetsResourceMock->expects(static::once())
            ->method('create')
            ->with(new Google_Service_Sheets_Spreadsheet([
                'properties' => [
                    'title' => 'XML Data Export',
                ],
            ]))
            ->willReturn($createdSheet);

        /**
         * Permissions
         */
        $permission = new Google_Service_Drive_Permission();
        $permission->setType('anyone');
        $permission->setRole('reader');

        $this->drivePermissionResourceMock->expects(static::once())
            ->method('create')
            ->with($createdSheet->spreadsheetId, $permission);

        /**
         * Headers and data export
         */
        $headersBody = new Google_Service_Sheets_ValueRange(['values' => [$testDataMappedXml->getHeaders()]]);
        $dataBody = new Google_Service_Sheets_ValueRange(['values' => $testDataMappedXml->getData()]);
        $params = ['valueInputOption' => 'USER_ENTERED'];

        $this->spreadsheetsValuesResourceMock->expects(static::exactly(2))
            ->method('append')
            ->withConsecutive(
                [$createdSheet->spreadsheetId, 'A1', $headersBody, $params],
                [$createdSheet->spreadsheetId, 'A2', $dataBody, $params]
            );

        $sheetId = $this->xmlExporterToGoogleSheet->export($testDataMappedXml);

        self::assertEquals($testSheetId, $sheetId);
    }
}
