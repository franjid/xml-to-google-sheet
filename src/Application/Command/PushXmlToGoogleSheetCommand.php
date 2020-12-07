<?php

namespace App\Application\Command;

use App\Application\Command\Exception\LocalFileNotFoundException;
use App\Domain\Interfaces\XmlExporterInterface;
use App\Domain\Service\XmlDataMapper;
use App\Infrastructure\FileReader\FileReaderFactory;
use Exception;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PushXmlToGoogleSheetCommand extends Command
{
    private const OPTION_SOURCE = 'source';
    private const OPTION_SOURCE_LOCAL = 'local';
    private const OPTION_SOURCE_REMOTE = 'remote';
    private const ARGUMENT_FILE = 'file';

    private XmlDataMapper $xmlDataMapper;
    private XmlExporterInterface $xmlExporter;
    private FileReaderFactory $fileReaderFactory;

    public function __construct(
        XmlDataMapper $xmlDataMapper,
        XmlExporterInterface $xmlExporter,
        FileReaderFactory $fileReaderFactory
    )
    {
        $this->xmlDataMapper = $xmlDataMapper;
        $this->xmlExporter = $xmlExporter;
        $this->fileReaderFactory = $fileReaderFactory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('push:xml:google-sheet')
            ->setDescription('Push XML data to a Google Spreadsheet')
            ->addOption(
                self::OPTION_SOURCE,
                's',
                InputOption::VALUE_REQUIRED,
                'Valid options: local, remote',
                self::OPTION_SOURCE_LOCAL
            )
            ->addArgument(self::ARGUMENT_FILE, InputArgument::REQUIRED, 'Xml local file path or URL');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('# XML to Google API exporter');

        $output->writeln('Getting XML content...');
        $fileContent = $this->getFileContent($input);

        try {
            libxml_use_internal_errors(false);
            $xmlObject = simplexml_load_string($fileContent, null, LIBXML_NOCDATA);
        } catch (Exception $e) {
            die(libxml_get_last_error()->message);
        }

        $mappedXml = $this->xmlDataMapper->map($xmlObject);
        $output->writeln('Pushing content to Google Sheet...');
        $sheetId = $this->xmlExporter->export($mappedXml);

        $output->writeln('https://docs.google.com/spreadsheets/d/' . $sheetId);

        return 0;
    }

    private function getFileContent(InputInterface $input): string
    {
        $file = $input->getArgument(self::ARGUMENT_FILE);

        switch ($input->getOption(self::OPTION_SOURCE)) {
            case self::OPTION_SOURCE_LOCAL:
                if (!file_exists($file)) {
                    throw new LocalFileNotFoundException('Local file not found');
                }

                $fileReader = $this->fileReaderFactory->getReader(FileReaderFactory::LOCAL);
                break;
            case self::OPTION_SOURCE_REMOTE:
                $fileReader = $this->fileReaderFactory->getReader(FileReaderFactory::FTP);
                break;
            default:
                throw new InvalidArgumentException('Source must be local or remote');
        }

        return $fileReader->getContent($file);
    }
}
