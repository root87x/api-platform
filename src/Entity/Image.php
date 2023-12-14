<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Controller\CreateImageAction;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ORM\Index(name: 'width_idx', columns: ['width'])]
#[ORM\Index(name: 'height_idx', columns: ['height'])]
#[ORM\Index(name: 'width_height_idx', columns: ['width', 'height'])]
#[ORM\HasLifecycleCallbacks]
#[ApiFilter(OrderFilter::class, properties: ['file.createdAt'])]
#[ApiResource(
    operations: [
        new GetCollection(
            openapiContext: [
                'summary' => 'Возвращает изображения.'
            ],
            normalizationContext: ['groups' => ['image.items.read', 'storage.items.read']],
        ),
        new Get(
            openapiContext: [
                'summary' => 'Возвращает изображение.'
            ],
            normalizationContext: ['groups' => ['image.item.read', 'storage.item.read']],
        ),
        new Post(
            types: ['https://schema.org/ImageObject'],
            deserialize: false,
            controller: CreateImageAction::class,
            validationContext: ['groups' => ['Default', 'storage_object_create']],
            openapiContext: [
                'summary' => 'Загружает изображение.',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            normalizationContext: ['groups' => ['image.items.read', 'storage.items.read']],
            denormalizationContext: ['groups' => ['storage.items.write']],
        ),
        new Delete(
            openapiContext: [
                'summary' => 'Удаляет картинку'
            ],
            security: "user === object.getFile().getUser() or is_granted('ROLE_ROOT')",
            securityMessage: "У вас нет доступа для осуществления ӕтой операции.",
        )
    ]

)]
class Image
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([
        'storage.item.read',
        'storage.items.read',
    ])]
    private ?Uuid $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'image.item.read',
        'image.items.read',
    ])]
    private ?int $width = null;

    #[ORM\Column(nullable: true)]
    #[Groups([
        'image.item.read',
        'image.items.read',
    ])]
    private ?int $height = null;

    #[ORM\ManyToOne(targetEntity: Storage::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\Valid]
    #[Groups([
        'image.item.read',
        'image.items.read',
    ])]
    private ?Storage $file = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getFile(): ?Storage
    {
        return $this->file;
    }

    public function setFile(?Storage $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(?int $width): self
    {
        $this->width = $width;

        return $this;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(?int $height): self
    {
        $this->height = $height;

        return $this;
    }

    #[ORM\PrePersist]
    public function setValueWidthHeight(): void
    {
        [$width, $height] = getimagesize($this->file->getFile()->getPathname());

        $this->setWidth($width);
        $this->setHeight($height);
    }
}
