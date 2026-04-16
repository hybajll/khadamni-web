<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ORM\Table(name: 'offers')]
class Offer
{
    public const CONTRACT_INTERNSHIP = 'Internship';
    public const CONTRACT_FREELANCE = 'Freelance';
    public const CONTRACT_PART_TIME = 'Part-time';
    public const CONTRACT_FULL_TIME = 'Full-time';

    public const LEVEL_JUNIOR = 'Junior';
    public const LEVEL_MID = 'Mid';
    public const LEVEL_SENIOR = 'Senior';
    public const LEVEL_MANAGER = 'Manager';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Society $society = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank(message: 'Le titre de l\'offre est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 200,
        minMessage: 'Le titre doit faire au least {{ limit }} caracteres.',
        maxMessage: 'Le titre ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(
        min: 10,
        minMessage: 'La description doit faire au least {{ limit }} caracteres.'
    )]
    private ?string $description = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $domain = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $salary = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Le type de contrat est obligatoire.')]
    #[Assert\Choice(
        choices: [
            self::CONTRACT_INTERNSHIP,
            self::CONTRACT_FREELANCE,
            self::CONTRACT_PART_TIME,
            self::CONTRACT_FULL_TIME,
        ],
        message: 'Le type de contrat selectionne n\'est pas valide.'
    )]
    private ?string $contractType = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Choice(
        choices: [
            self::LEVEL_JUNIOR,
            self::LEVEL_MID,
            self::LEVEL_SENIOR,
            self::LEVEL_MANAGER,
        ],
        message: 'Le niveau d\'experience selectionne n\'est pas valide.'
    )]
    private ?string $experienceLevel = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $expirationDate = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSociety(): ?Society
    {
        return $this->society;
    }

    public function setSociety(?Society $society): self
    {
        $this->society = $society;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(?string $salary): self
    {
        $this->salary = $salary;
        return $this;
    }

    public function getContractType(): ?string
    {
        return $this->contractType;
    }

    public function setContractType(string $contractType): self
    {
        $this->contractType = $contractType;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getExperienceLevel(): ?string
    {
        return $this->experienceLevel;
    }

    public function setExperienceLevel(?string $experienceLevel): self
    {
        $this->experienceLevel = $experienceLevel;
        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }
}
