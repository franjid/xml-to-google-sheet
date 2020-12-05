<?php

namespace App\Tests\Unit\Domain\Service;

use App\Domain\Service\XmlDataMapper;
use App\Tests\Helper\HelperMappedXml;
use PHPUnit\Framework\TestCase;

class XmlDataMapperTest extends TestCase
{
    public function testMap(): void
    {
        $xmlContent = file_get_contents('tests/data/coffee_feed_test.xml');
        $xml = simplexml_load_string($xmlContent, null, LIBXML_NOCDATA);

        $xmlDataMapper = new XmlDataMapper();
        $mappedXml = $xmlDataMapper->map($xml);

        $testDataMappedXml = (new HelperMappedXml())->getTestDataMappedXml();
        self::assertEquals($testDataMappedXml, $mappedXml);
    }
}
