<?php

namespace App\Domain\Interfaces;

use App\Domain\Entity\MappedXml;

interface XmlExporterInterface
{
    /**
     * Returns an id. Depending on the implementation it could be:
     * - A Google Spreadsheet id (if exported to Google)
     * - An id from a table (if exported to a database)
     * - A name of some CSV created with the XML data
     * - ...
     *
     * @param MappedXml $xmlMapped
     *
     * @return string
     */
    public function export(MappedXml $xmlMapped): string;
}
