<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use App\Attribute\DefaultCurrentUser;
use App\Entity\Interfaces\DefaultCurrentUserInterface;
use App\Repository\StorageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
#[ORM\Index(name: 'container_idx', columns: ['container'])]
#[ORM\Index(name: 'created_at_idx', columns: ['created_at'])]
#[ApiResource(
    operations: [
        new GetCollection(
            openapiContext: [
                'summary' => 'Возвращает список файлов.'
            ],
            normalizationContext: ['groups' => ['storage.items.read']],
        ),
        new Get(
            openapiContext: [
                'summary' => 'Возвращает файл.'
            ],
            normalizationContext: ['groups' => ['storage.item.read']],
        ),
    ]
)]
#[Vich\Uploadable]
#[ORM\HasLifecycleCallbacks]
class Storage implements DefaultCurrentUserInterface
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
    
    #[Vich\UploadableField(
        mapping: 'media_object',
        fileNameProperty: 'path',
        size: 'size',
        originalName: 'name',
        mimeType: 'mimeType'
    )]
    #[Assert\NotNull(
        message: 'Файл обязателен к загрузке.',
        groups: ['storage_object_create'],
    )]
    private ?File $file = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'storage.item.read',
        'storage.items.read',
    ])]
    private ?string $path = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups([
        'storage.item.read',
        'storage.items.read',
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 40)]
    #[Groups([
        'storage.item.read',
        'storage.items.read',
    ])]
    private ?string $extension = null;

    #[ORM\Column(length: 120)]
    #[Groups([
        'storage.item.read',
        'storage.items.read',
    ])]
    private ?string $mimeType = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    #[Groups([
        'storage.item.read',
        'storage.items.read',
    ])]
    private ?string $size = null;

    #[ORM\Column(length: 60)]
    private ?string $container = 'local';

    #[Groups([
        'storage.item.read',
        'storage.items.read',
    ])]
    private ?string $uri = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(?string $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getContainer(): ?string
    {
        return $this->container;
    }

    public function setContainer(string $container): self
    {
        $this->container = $container;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setUri(?string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setValueCreatedAt(): void
    {
        $this->setCreatedAt(new \DateTimeImmutable());
    }
}
