<?php

namespace App\Infrastructure\FileReader;

use App\Domain\Interfaces\FileReaderInterface;

class FileReaderLocal implements FileReaderInterface
{
    public function getContent(string $file): string
    {
        return file_get_contents($file);
    }
}
