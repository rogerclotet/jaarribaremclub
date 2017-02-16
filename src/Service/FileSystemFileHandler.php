<?php

namespace App\Service;

use App\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileSystemFileHandler implements FileHandler
{
    /**
     * @var string
     */
    private $uploadsDir;

    public function __construct(string $uploadsDir)
    {
        $this->uploadsDir = $uploadsDir;
    }

    public function list(): array
    {
        $files = [];
        foreach (array_diff(scandir($this->uploadsDir), ['.', '..']) as $directory) {
            foreach (array_diff(scandir($this->uploadsDir . DIRECTORY_SEPARATOR . $directory), ['.', '..']) as $file) {
                $files[] = $directory . DIRECTORY_SEPARATOR . $file;
            }
        }

        return $files;
    }

    public function upload(UploadedFile $uploadedFile): File
    {
        $fileName = $uploadedFile->getClientOriginalName();
        $folder   = uniqid();
        $filePath = $folder . '/' . $fileName;
        $size     = $uploadedFile->getSize();

        $uploadedFile->move(
            sprintf(
                '%s/%s',
                $this->uploadsDir,
                $folder
            ),
            $fileName
        );

        $file = new File();
        $file->setName($fileName);
        $file->setPath($filePath);
        $file->setSize($size);

        return $file;
    }

    public function delete($path): void
    {
        unlink($this->uploadsDir . DIRECTORY_SEPARATOR . $path);

        rmdir(pathinfo($this->uploadsDir . DIRECTORY_SEPARATOR . $path, PATHINFO_DIRNAME));
    }
}
