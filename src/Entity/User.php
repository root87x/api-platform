<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\UserRepository;
use App\State\UserPasswordHasherProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ApiResource(
    cacheHeaders: [
        'max_age' => 240,
        'shared_max_age' => 480,
        'vary' => ['Authorization', 'Accept-Language']
    ],
    operations: [
        new GetCollection(
            openapiContext: [
                'summary' => 'Возвращает пользователей.',
            ],
            normalizationContext: ['groups' => ['user.items.read']],
        ),
        new Post(
            openapiContext: [
                'summary' => 'Создает пользователя.',
            ],
            normalizationContext: ['groups' => ['user.item.read']],
            denormalizationContext: ['groups' => ['user.items.write']],
            processor: UserPasswordHasherProcessor::class,
        ),
        new Put(
            openapiContext: [
                'summary' => 'Обновляет пользователя.',
            ],
            normalizationContext: ['groups' => ['user.item.read']],
            denormalizationContext: ['groups' => ['user.item.write']],
            security: "user === object or is_granted('ROLE_ROOT') or is_granted('ROLE_ADMIN')",
            securityMessage: "В операции отказано, нет доступа."
        ),
        new Get(
            openapiContext: [
                'summary' => 'Возвращает пользователя.',
            ],
            normalizationContext: ['groups' => ['user.item.read']],
        ),
        new Delete(
            openapiContext: [
                'summary' => 'Удаляет пользователя.',
            ],
            security: "user === object or is_granted('ROLE_ROOT') or is_granted('ROLE_ADMIN')",
            securityMessage: "В операции отказано, нет доступа."
        ),
    ]
)]
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', message: "")]
#[UniqueEntity('login', message: "")]
class User implements
    UserInterface,
    PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([
        'user.item.read',
        'user.items.read'
    ])]
    private ?Uuid $id = null;

    #[ORM\Column(length: 50, unique: true, nullable: true)]
    #[Assert\NotBlank(message: "Логин не может быть пустым.")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Логин должен состоять минимум из {{ limit }} символов.",
        maxMessage: "Логин должен состоять максимум из {{ limit }} символов.",
    )]
    #[Groups([
        'user.item.read',
        'user.items.read',
        'user.item.write',
        'user.items.write',
    ])]
    private ?string $login = null;

    #[ORM\Column(length: 255, unique: true, nullable: false)]
    #[Assert\Email(message: "Не почта.")]
    #[Groups([
        'user.item.read',
        'user.items.read',
        'user.item.write',
        'user.items.write',
    ])]
    private ?string $email = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Имя должно состоять минимум из {{ limit }} символов.",
        maxMessage: "Имя должно состоять максимум из {{ limit }} символов.",
    )]
    #[Groups([
        'user.item.read',
        'user.items.read',
        'user.item.write',
        'user.items.write',
    ])]
    private ?string $firstName = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Фамилия должна состоять минимум из {{ limit }} символов.",
        maxMessage: "Фамилия должна состоять максимум из {{ limit }} символов.",
    )]
    #[Groups([
        'user.item.read',
        'user.items.read',
        'user.item.write',
        'user.items.write',
    ])]
    private ?string $lastName = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        min: 4,
        max: 100,
        minMessage: "Отчество должно состоять минимум из {{ limit }} символов.",
        maxMessage: "Отчество должно состоять максимум из {{ limit }} символов.",
    )]
    #[Groups([
        'user.item.read',
        'user.items.read',
        'user.item.write',
        'user.items.write',
    ])]
    private ?string $middleName = null;

    #[ORM\Column(length: 100, nullable: false)]
    #[Assert\NotBlank(message: "Пароль не может быть пустым.")]
    #[Assert\Length(
        min: 6,
        max: 100,
        minMessage: "Пароль должен состоять минимум из {{ limit }} символов.",
        maxMessage: "Пароль должен состоять максимум из {{ limit }} символов.",
    )]
    #[Groups([
        'user.items.write'
    ])]
    private ?string $password = null;

    #[ORM\Column(type: 'json')]
    #[Groups([
        'user.item.read',
        'user.items.read',
    ])]
    private array $roles = [];

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
