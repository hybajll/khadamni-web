<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé par un autre utilisateur.', entityClass: User::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    'etudiant' => Etudiant::class,
    'diplome' => Diplome::class,
    'admin' => Admin::class,
    'ETUDIANT' => Etudiant::class,
    'DIPLOME' => Diplome::class,
    'ADMIN' => Admin::class,
])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const TYPE_ADMIN = 'admin';
    public const TYPE_ETUDIANT = 'etudiant';
    public const TYPE_DIPLOME = 'diplome';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'-]+$/u",
        message: 'Le nom ne doit contenir que des lettres.'
    )]
    protected ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Regex(
        pattern: "/^[\p{L}\s'-]+$/u",
        message: 'Le prenom ne doit contenir que des lettres.'
    )]
    protected ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "L'adresse email n'est pas valide.")]
    protected ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le mot de passe est obligatoire.', groups: ['user_password'])]
    #[Assert\Length(
        min: 4,
        minMessage: 'Le mot de passe doit faire au moins {{ limit }} caracteres.'
        ,
        groups: ['user_password']
    )]
    protected ?string $password = null;

    #[ORM\Column(name: 'actif', type: 'boolean')]
    protected bool $isActive = true;

    #[ORM\Column(name: 'LocalDateTime', type: 'datetime_immutable', nullable: true)]
    protected ?\DateTimeInterface $localDateTime = null;

    // Business role for admins only: super_admin | gestionnaire | moderateur
    #[ORM\Column(name: 'role', length: 255, nullable: true)]
    protected ?string $adminRole = null;

    // Keep DB column name in camelCase to match existing database column created on XAMPP.
    #[ORM\Column(name: 'avatarPath', length: 255, nullable: true)]
    protected ?string $avatarPath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
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

    // --- Legacy Compatibility Methods ---

    public function isActif(): bool
    {
        return $this->isActive();
    }

    public function setActif(bool $actif): self
    {
        return $this->setIsActive($actif);
    }

    public function getLocalDateTime(): ?\DateTimeInterface
    {
        return $this->localDateTime;
    }

    public function setLocalDateTime(?\DateTimeInterface $localDateTime): self
    {
        $this->localDateTime = $localDateTime;
        return $this;
    }

    public function getAdminRole(): ?string
    {
        return $this->adminRole;
    }

    public function setAdminRole(?string $adminRole): self
    {
        $this->adminRole = $adminRole;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->getAdminRole();
    }

    public function setRole(?string $role): self
    {
        return $this->setAdminRole($role);
    }

    public function getAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    public function setAvatarPath(?string $avatarPath): self
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    public function getType(): string
    {
        if ($this instanceof Admin) {
            return self::TYPE_ADMIN;
        }
        if ($this instanceof Diplome) {
            return self::TYPE_DIPLOME;
        }
        return self::TYPE_ETUDIANT;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this instanceof Admin) {
            $roles[] = 'ROLE_ADMIN';
        }

        $adminRole = $this->getAdminRole();
        if ($adminRole) {
            $roles[] = match ($adminRole) {
                'super_admin' => 'ROLE_SUPER_ADMIN',
                'gestionnaire' => 'ROLE_GESTIONNAIRE',
                'moderateur' => 'ROLE_MODERATEUR',
                default => $adminRole,
            };
        }
        return array_unique($roles);
    }

    /**
     * @return Collection<int, Reclamation>
     */
    public function getReclamations(): Collection
    {
        return $this->reclamations;
    }

    public function addReclamation(Reclamation $reclamation): self
    {
        if (!$this->reclamations->contains($reclamation)) {
            $this->reclamations->add($reclamation);
            $reclamation->setUser($this);
        }
        return $this;
    }

    public function removeReclamation(Reclamation $reclamation): self
    {
        if ($this->reclamations->removeElement($reclamation)) {
            if ($reclamation->getUser() === $this) {
                $reclamation->setUser(null);
            }
        }
        return $this;
    }

    public function eraseCredentials(): void
    {
        // Nettoyage des données sensibles temporaires si nécessaire
    }
}