<?php

namespace App\Infrastructure\FileReader;

use App\Domain\Interfaces\FileReaderInterface;
use LogicException;

class FileReaderFactory
{
    public const LOCAL = 'local';
    public const FTP = 'ftp';

    private string $ftpHost;
    private string $ftpUser;
    private string $ftpPassword;

    public function __construct(
        string $ftpHost,
        string $ftpUser,
        string $ftpPassword
    )
    {
        $this->ftpHost = $ftpHost;
        $this->ftpUser = $ftpUser;
        $this->ftpPassword = $ftpPassword;
    }

    public function getReader(string $type): FileReaderInterface
    {
        switch ($type) {
            case self::LOCAL:
                return new FileReaderLocal();
            case self::FTP:
                return new FileReaderFtp(
                    $this->ftpHost,
                    $this->ftpUser,
                    $this->ftpPassword
                );
            default:
                // @todo: Create more specific exception
                throw new LogicException('FileReader type not valid');
        }
    }
}
