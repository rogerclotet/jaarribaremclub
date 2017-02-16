<?php

namespace App\Service;

use App\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class NoopFileHandler implements FileHandler
{
    public function list(): array
    {
        return [];
    }

    public function upload(UploadedFile $uploadedFile): File
    {
        return new File();
    }

    public function delete($path): void
    {
        // NOOP
    }
}
