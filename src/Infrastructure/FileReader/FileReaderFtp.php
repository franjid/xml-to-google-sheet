<?php

namespace App\Infrastructure\FileReader;

use App\Domain\Interfaces\FileReaderInterface;
use App\Infrastructure\FileReader\Exception\FtpFileNotFoundException;
use App\Infrastructure\FileReader\Exception\FtpInvalidConnectionException;
use App\Infrastructure\FileReader\Exception\FtpInvalidUserPassException;

class FileReaderFtp implements FileReaderInterface
{
    private string $host;
    private string $user;
    private string $password;

    public function __construct(
        string $host,
        string $user,
        string $password
    )
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    public function getContent(string $file): string
    {
        $connection = ftp_connect($this->host);
        if (!$connection) {
            throw new FtpInvalidConnectionException('Invalid FTP connection. Check FTP host');
        }

        try {
            ftp_login($connection, $this->user, $this->password);
        } catch (\Exception $e) {
            throw new FtpInvalidUserPassException('Invalid user/password');
        }

        $tmpFile = tempnam('/tmp', 'FTP_FILE');

        try {
            ftp_pasv($connection, TRUE);
            ftp_get($connection, $tmpFile, $file, FTP_BINARY);
        } catch (\Exception $e) {
            throw new FtpFileNotFoundException('Error getting file: ' . $file . '(' . $e->getMessage() . ')');
        }

        $fileContents = file_get_contents($tmpFile);
        unlink($tmpFile);

        return $fileContents;
    }
}
