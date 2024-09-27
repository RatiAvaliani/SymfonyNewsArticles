<?php
namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploaderService
{
    private string $TargetDirectory;

    public function __construct()
    {
        $this->TargetDirectory = 'public/uploads/';
    }

    public function Upload(UploadedFile $File): string
    {
        $NewFileName = hash('sha256', (string)rand(100, 999)) . time() . '.' . $File->guessExtension();

        $File->move($this->TargetDirectory, $NewFileName);
        return $NewFileName;
    }
}