<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Model\Uuid;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Email;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(
 *     name="users",
 *     indexes={
 *          @ORM\Index(name="IDX_CREATED_AT", columns={"created_at"}),
 *          @ORM\Index(name="IDX_UPDATED_AT", columns={"updated_at"})
 * })
 * @UniqueEntity(fields={"email"}, message="constraints.email.exists")
 */
#[ApiResource(
    collectionOperations: [
        "post" => [
            "denormalization_context" => ["groups" => ["user:create"]],
            "validation_groups" => ["Default", "create"],
        ],
        "me" => [
            "method" => "GET",
            "path" => "/users/me",
            "pagination_enabled" => false
        ],
    ],
    itemOperations: [
        "get" => [
            "security" => "object == user",
        ],
        "put" => [
            "security" => "object == user",
        ],
    ],
    denormalizationContext: ["groups" => ["user:write"]],
    normalizationContext: ["groups" => ["user:read"]]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const MIN_PASSWORD_LENGTH = 6;
    public const MAX_PASSWORD_LENGTH = 4096;


    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="guid", unique=true)
     */
    private string $uuid;

    /**
     * @Assert\Email(
     *     message="constraints.email.incorrect",
     *     mode=Email::VALIDATION_MODE_STRICT
     * )
     * @Assert\NotBlank(message="constraints.email.blank")
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read","user:create"})
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password = '';

    /**
     * @Groups("user:create")
     * @SerializedName("password")
     * @Assert\NotBlank(message="constraints.password.empty", groups={"create"})
     * @Assert\Length(
     *     groups={"create"},
     *     min=User::MIN_PASSWORD_LENGTH,
     *     minMessage="constraints.password.min",
     *     max=User::MAX_PASSWORD_LENGTH,
     *     maxMessage="constraints.password.max"
     * )
     */
    private ?string $plainPassword = null;

    /**
     * @Gedmo\Mapping\Annotation\Timestampable(on="create")
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"user:read"})
     */
    protected DateTime $createdAt;

    /**
     * @Gedmo\Mapping\Annotation\Timestampable(on="update")
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @Groups({"user:read"})
     */
    protected DateTime $updatedAt;

    /**
     * @ORM\Column(type="string")
     * @Groups({"user:read", "user:write","user:create"})
     * @Assert\NotBlank(message="constraints.firstName.empty")
     */
    private ?string $firstName = null;

    /**
     * @ORM\Column(type="string")
     * @Groups({"user:read", "user:write","user:create"})
     * @Assert\NotBlank(message="constraints.lastName.empty")
     */
    private ?string $lastName = null;

    /**
     * @Assert\NotBlank(message="constraints.phone.empty")
     * @ORM\Column(type="string")
     * @Groups({"user:read", "user:write","user:create"})
     */
    private ?string $phone = null;

    public function __construct()
    {
        $this->uuid = Uuid::create();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
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
     * @see UserInterface
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
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     * @return $this
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     * @return User
     */
    public function setCreatedAt(DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     * @return $this
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return $this
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return $this
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

}
