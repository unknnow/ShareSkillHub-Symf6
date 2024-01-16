<?php

namespace App\Entity;

use App\Repository\SessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\UuidV7 as Uuid;

#[ORM\Entity(repositoryClass: SessionRepository::class)]
class Session
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'createdSessions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ownerUser = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'joinedSessions')]
    private Collection $participantUser;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\OneToMany(mappedBy: 'session', targetEntity: Notation::class)]
    private Collection $notations;

    #[ORM\Column(length: 255)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    public function __construct()
    {
        $this->participantUser = new ArrayCollection();
        $this->notations = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getOwnerUser(): ?User
    {
        return $this->ownerUser;
    }

    public function setOwnerUser(?User $ownerUser): static
    {
        $this->ownerUser = $ownerUser;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipantUser(): Collection
    {
        return $this->participantUser;
    }

    public function addParticipantUser(User $participantUser): static
    {
        if (!$this->participantUser->contains($participantUser)) {
            $this->participantUser->add($participantUser);
        }

        return $this;
    }

    public function removeParticipantUser(User $participantUser): static
    {
        $this->participantUser->removeElement($participantUser);

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return Collection<int, Notation>
     */
    public function getNotations(): Collection
    {
        return $this->notations;
    }

    public function addNotation(Notation $notation): static
    {
        if (!$this->notations->contains($notation)) {
            $this->notations->add($notation);
            $notation->setSession($this);
        }

        return $this;
    }

    public function removeNotation(Notation $notation): static
    {
        if ($this->notations->removeElement($notation)) {
            // set the owning side to null (unless already changed)
            if ($notation->getSession() === $this) {
                $notation->setSession(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;

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

    public function getDisplayType(): string
    {
        return $this->type == 'online' ? 'En ligne' : 'Sur site';
    }
}
