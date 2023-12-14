<?php

namespace App\Service;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\File;

class ImageStorageService
{
    public function __construct(private readonly StorageService $storageService) {}

    public function create(File $file)
    {
        $image = new Image();
        $image->setFile($this->storageService->create($file));

        return $image;
    }
}