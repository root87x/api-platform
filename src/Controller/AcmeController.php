<?php

namespace App\Controller;

use App\Entity\Storage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

class AcmeController extends AbstractController
{
    #[Route('/api/download/{path}', name: 'download_file', methods: ['GET'])]
    public function downloadImageAction(Storage $storage, DownloadHandler $downloadHandler): Response
    {
        return $downloadHandler->downloadObject($storage, 'file');
    }
}