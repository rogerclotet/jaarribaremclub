<?php

namespace App\Service;

use App\Entity\File;
use Aws\S3\S3Client;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class S3FileHandler implements FileHandler
{
    /**
     * @var S3Client
     */
    private $s3;

    /**
     * @var string
     */
    private $bucketName;

    public function __construct(S3Client $s3, string $bucketName)
    {
        $this->s3         = $s3;
        $this->bucketName = $bucketName;
    }

    public function list(): array
    {
        $result = $this->s3->listObjects([
            'Bucket' => $this->bucketName,
        ]);

        $files = [];
        foreach ($result['Contents'] as $object) {
            $files[] = $object['Key'];
        }

        return $files;
    }

    public function upload(UploadedFile $uploadedFile): File
    {
        $fileName = $uploadedFile->getClientOriginalName();
        $folder   = uniqid();
        $filePath = $folder . '/' . $fileName;
        $size     = $uploadedFile->getSize();

        $this->s3->upload($this->bucketName, $filePath, $uploadedFile->openFile(), 'public-read');

        $file = new File();
        $file->setName($fileName);
        $file->setPath($filePath);
        $file->setSize($size);

        return $file;
    }

    public function delete($path): void
    {
        $this->s3->deleteObject([
            'Bucket' => $this->bucketName,
            'Key'    => $path,
        ]);
    }
}
