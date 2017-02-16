<?php

namespace App\Command;

use App\Entity\File;
use App\Service\FileHandler;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteUnusedFilesCommand extends Command
{
    private $doctrine;
    private $fileHandler;

    public function __construct(RegistryInterface $doctrine, FileHandler $fileHandler)
    {
        parent::__construct();

        $this->doctrine    = $doctrine;
        $this->fileHandler = $fileHandler;
    }

    protected function configure()
    {
        $this
            ->setName('jac:files:delete_unused')
            ->setDescription('Deletes unused files');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $files     = $this->doctrine->getRepository(File::class)->findAll();
        $filePaths = array_map(function (File $file) {
            return $file->getPath();
        }, $files);

        $deletedFiles = 0;
        foreach ($this->fileHandler->list() as $file) {
            if (!isset($filePaths[$file])) {
                $this->fileHandler->delete($file);
                ++$deletedFiles;
                $output->write('.');
            }
        }

        if ($deletedFiles > 0) {
            $output->writeln('<comment>' . $deletedFiles . ' unused files deleted</comment>');
        } else {
            $output->writeln('<info>No unused files found</info>');
        }
    }
}
