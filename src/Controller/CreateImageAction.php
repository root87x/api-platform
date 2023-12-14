<?php
namespace App\Controller;
use App\Entity\Image;
use App\Service\ImageStorageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
final class CreateImageAction extends AbstractController
{
    public function __construct(private readonly ImageStorageService $imageStorageService){}

    public function __invoke(Request $request): Image
    {
        /** @var File $file */
        $file = $request->files->get('file', null);
        if (null === $file) {
            throw new BadRequestHttpException('"file" is required');
        }

        return $this->imageStorageService->create($file);
    }
}