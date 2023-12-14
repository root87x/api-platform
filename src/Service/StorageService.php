<?php

namespace App\Service;

use App\Entity\Storage;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StorageService
{
    public function create(File $file)
    {
        $storage = new Storage();
        $storage->setFile($file);
        $storage->setExtension($file->getExtension());

        return $storage;
    }
}