<?php

namespace App\Service;

use App\Entity\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FileHandler
{
    public function list(): array;

    public function upload(UploadedFile $uploadedFile): File;

    public function delete($path): void;
}
