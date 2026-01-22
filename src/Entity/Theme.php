<?php

namespace App\Entity;

use App\Repository\ThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité Theme - représente un thème de discussion sur le forum
 * Chaque thème contient plusieurs discussions
 */
#[ORM\Entity(repositoryClass: ThemeRepository::class)]
class Theme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    // Titre du thème
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le titre ne peut pas être vide')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Le titre doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le titre ne peut pas dépasser {{ limit }} caractères'
    )]
    private ?string $titre = null;

    // Description du thème (optionnelle)
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // Date de création du thème
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    // Relation avec les discussions du thème
    #[ORM\OneToMany(mappedBy: 'theme', targetEntity: Discussion::class, orphanRemoval: true)]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    private Collection $discussions;

    public function __construct()
    {
        $this->discussions = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Collection<int, Discussion>
     */
    public function getDiscussions(): Collection
    {
        return $this->discussions;
    }

    public function addDiscussion(Discussion $discussion): static
    {
        if (!$this->discussions->contains($discussion)) {
            $this->discussions->add($discussion);
            $discussion->setTheme($this);
        }
        return $this;
    }

    public function removeDiscussion(Discussion $discussion): static
    {
        if ($this->discussions->removeElement($discussion)) {
            if ($discussion->getTheme() === $this) {
                $discussion->setTheme(null);
            }
        }
        return $this;
    }

    /**
     * Retourne le nombre de discussions dans ce thème
     */
    public function getNombreDiscussions(): int
    {
        return $this->discussions->count();
    }

    /**
     * Retourne la date de la dernière discussion
     * Utilisé pour afficher sur la page d'accueil
     */
    public function getLastDiscussionDate(): ?\DateTimeImmutable
    {
        if ($this->discussions->isEmpty()) {
            return null;
        }

        // On récupère la discussion la plus récente
        $lastDiscussion = $this->discussions->first();
        return $lastDiscussion->getCreatedAt();
    }
}
