<?php

namespace App\Domain\Service;

use App\Domain\Entity\MappedXml;
use SimpleXMLElement;

class XmlDataMapper
{
    public function map(SimpleXMLElement $xml): MappedXml
    {
        $xmlArray = json_decode(json_encode((array) $xml, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);

        $headers = [];
        $data = [];

        foreach ($xmlArray['item'] as $item) {
            // Set headers only in the first iteration
            if (!$headers) {
                $headers = array_keys($item);
            }

            $itemDataArray = [];
            foreach ($item as $itemData) {
                $itemDataArray[] = is_array($itemData) ? '' : trim($itemData);
            }

            $data[] = $itemDataArray;
        }

        return new MappedXml($headers, $data);
    }
}
