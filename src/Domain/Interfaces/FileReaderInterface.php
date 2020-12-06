<?php

namespace App\Domain\Interfaces;

interface FileReaderInterface
{
    public function getContent(string $file): string;
}
