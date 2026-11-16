<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ORM\Table(name: 'address')]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 80)]
    private ?string $label = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    private ?string $recipientName = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Assert\Length(max: 150)]
    private ?string $company = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    private ?string $addressLine1 = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Length(max: 180)]
    private ?string $addressLine2 = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $city = null;

    #[ORM\Column(length: 2)]
    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/^[A-Z]{2}$/', message: 'Use a two-letter ISO country code.')]
    private string $countryCode = 'FR';

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Length(max: 30)]
    private ?string $phone = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Assert\Regex(pattern: '/^\+[1-9]\d{0,3}$/', message: 'Use a valid international calling code, such as +33.')]
    private ?string $phoneCountryCode = null;

    #[ORM\Column]
    private bool $isDefault = false;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getRecipientName(): ?string
    {
        return $this->recipientName;
    }

    public function setRecipientName(string $recipientName): static
    {
        $this->recipientName = $recipientName;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function setAddressLine1(string $addressLine1): static
    {
        $this->addressLine1 = $addressLine1;

        return $this;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function setAddressLine2(?string $addressLine2): static
    {
        $this->addressLine2 = $addressLine2;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): static
    {
        $this->countryCode = strtoupper($countryCode);

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPhoneCountryCode(): ?string
    {
        return $this->phoneCountryCode ?? '+33';
    }

    public function setPhoneCountryCode(?string $phoneCountryCode): static
    {
        $this->phoneCountryCode = $phoneCountryCode;

        return $this;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    public function setDefault(bool $isDefault): static
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function setIsDefault(bool $isDefault): static
    {
        return $this->setDefault($isDefault);
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
