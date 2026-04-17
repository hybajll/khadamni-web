<?php

namespace App\Entity;

use App\Repository\CvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CvRepository::class)]
#[ORM\Table(name: 'cv')]
class Cv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre du CV est obligatoire.')]
    #[Assert\Length(
        max: 255,
        maxMessage: 'Le titre ne doit pas depasser {{ limit }} caracteres.'
    )]
    private ?string $titre = null;

    #[ORM\Column(name: 'contenuOriginal', type: Types::TEXT)]
    private ?string $contenuOriginal = null;

    #[ORM\Column(name: 'contenuAmeliore', type: Types::TEXT, nullable: true)]
    private ?string $contenuAmeliore = null;

    #[ORM\Column(name: 'conseilsAi', type: Types::TEXT, nullable: true)]
    private ?string $conseilsAi = null;

    #[ORM\Column(name: 'dateUpload', type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull(message: 'La date d upload est obligatoire.')]
    private ?\DateTimeInterface $dateUpload = null;

    #[ORM\Column(name: 'nombreAmeliorations')]
    #[Assert\NotNull(message: 'Le nombre d ameliorations est obligatoire.')]
    #[Assert\PositiveOrZero(message: 'Le nombre d ameliorations doit etre positif ou nul.')]
    private ?int $nombreAmeliorations = 0;

    #[ORM\Column(name: 'estPublic')]
    private ?bool $estPublic = false;

    #[ORM\Column(name: 'pdfPath', length: 255, nullable: true)]
    private ?string $pdfPath = null;

    /**
     * Relation avec l'utilisateur. 
     * La colonne SQL s'appelle 'idUser' selon ta base.
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'idUser', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'Veuillez selectionner un utilisateur.')]
    private ?User $user = null;

    // --- GETTERS ET SETTERS ---

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): self { $this->titre = $titre; return $this; }

    public function getContenuOriginal(): ?string { return $this->contenuOriginal; }
    public function setContenuOriginal(?string $contenuOriginal): self { $this->contenuOriginal = $contenuOriginal; return $this; }

    public function getContenuAmeliore(): ?string { return $this->contenuAmeliore; }
    public function setContenuAmeliore(?string $contenuAmeliore): self { $this->contenuAmeliore = $contenuAmeliore; return $this; }

    public function getConseilsAi(): ?string { return $this->conseilsAi; }
    public function setConseilsAi(?string $conseilsAi): self { $this->conseilsAi = $conseilsAi; return $this; }

    public function getDateUpload(): ?\DateTimeInterface { return $this->dateUpload; }
    public function setDateUpload(\DateTimeInterface $dateUpload): self { $this->dateUpload = $dateUpload; return $this; }

    public function getNombreAmeliorations(): ?int { return $this->nombreAmeliorations; }
    public function setNombreAmeliorations(int $nombreAmeliorations): self { $this->nombreAmeliorations = $nombreAmeliorations; return $this; }

    public function isEstPublic(): ?bool { return $this->estPublic; }
    public function setEstPublic(bool $estPublic): self { $this->estPublic = $estPublic; return $this; }

    public function getPdfPath(): ?string { return $this->pdfPath; }
    public function setPdfPath(?string $pdfPath): self { $this->pdfPath = $pdfPath; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
}
