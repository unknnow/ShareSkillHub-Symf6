<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\UuidV7 as Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'ownerUser', targetEntity: Session::class)]
    private Collection $createdSessions;

    #[ORM\ManyToMany(targetEntity: Session::class, mappedBy: 'participantUser')]
    private Collection $joinedSessions;

    #[ORM\OneToMany(mappedBy: 'ownerUser', targetEntity: Notation::class)]
    private Collection $postedNotations;

    #[ORM\OneToMany(mappedBy: 'senderUser', targetEntity: Message::class)]
    private Collection $sendedMessages;

    #[ORM\OneToMany(mappedBy: 'receiverUser', targetEntity: Message::class)]
    private Collection $receivedMessages;

    #[ORM\OneToMany(mappedBy: 'senderUser', targetEntity: Recommandation::class)]
    private Collection $postedRecommandations;

    #[ORM\OneToMany(mappedBy: 'receiverUser', targetEntity: Recommandation::class)]
    private Collection $recommandations;

    #[ORM\ManyToMany(targetEntity: Skill::class, inversedBy: 'users')]
    private Collection $skills;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatar = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $job = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $about = null;

    public function __construct()
    {
        $this->createdSessions = new ArrayCollection();
        $this->joinedSessions = new ArrayCollection();
        $this->postedNotations = new ArrayCollection();
        $this->sendedMessages = new ArrayCollection();
        $this->receivedMessages = new ArrayCollection();
        $this->postedRecommandations = new ArrayCollection();
        $this->recommandations = new ArrayCollection();
        $this->skills = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

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
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getCreatedSessions(): Collection
    {
        return $this->createdSessions;
    }

    public function addCreatedSession(Session $createdSession): static
    {
        if (!$this->createdSessions->contains($createdSession)) {
            $this->createdSessions->add($createdSession);
            $createdSession->setOwnerUser($this);
        }

        return $this;
    }

    public function removeCreatedSession(Session $createdSession): static
    {
        if ($this->createdSessions->removeElement($createdSession)) {
            // set the owning side to null (unless already changed)
            if ($createdSession->getOwnerUser() === $this) {
                $createdSession->setOwnerUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getJoinedSessions(): Collection
    {
        return $this->joinedSessions;
    }

    public function addJoinedSession(Session $joinedSession): static
    {
        if (!$this->joinedSessions->contains($joinedSession)) {
            $this->joinedSessions->add($joinedSession);
            $joinedSession->addParticipantUser($this);
        }

        return $this;
    }

    public function removeJoinedSession(Session $joinedSession): static
    {
        if ($this->joinedSessions->removeElement($joinedSession)) {
            $joinedSession->removeParticipantUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Notation>
     */
    public function getPostedNotations(): Collection
    {
        return $this->postedNotations;
    }

    public function addPostedNotation(Notation $postedNotation): static
    {
        if (!$this->postedNotations->contains($postedNotation)) {
            $this->postedNotations->add($postedNotation);
            $postedNotation->setOwnerUser($this);
        }

        return $this;
    }

    public function removePostedNotation(Notation $postedNotation): static
    {
        if ($this->postedNotations->removeElement($postedNotation)) {
            // set the owning side to null (unless already changed)
            if ($postedNotation->getOwnerUser() === $this) {
                $postedNotation->setOwnerUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getSendedMessages(): Collection
    {
        return $this->sendedMessages;
    }

    public function addSendedMessage(Message $sendedMessage): static
    {
        if (!$this->sendedMessages->contains($sendedMessage)) {
            $this->sendedMessages->add($sendedMessage);
            $sendedMessage->setSenderUser($this);
        }

        return $this;
    }

    public function removeSendedMessage(Message $sendedMessage): static
    {
        if ($this->sendedMessages->removeElement($sendedMessage)) {
            // set the owning side to null (unless already changed)
            if ($sendedMessage->getSenderUser() === $this) {
                $sendedMessage->setSenderUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getReceivedMessages(): Collection
    {
        return $this->receivedMessages;
    }

    public function addReceivedMessage(Message $receivedMessage): static
    {
        if (!$this->receivedMessages->contains($receivedMessage)) {
            $this->receivedMessages->add($receivedMessage);
            $receivedMessage->setReceiverUser($this);
        }

        return $this;
    }

    public function removeReceivedMessage(Message $receivedMessage): static
    {
        if ($this->receivedMessages->removeElement($receivedMessage)) {
            // set the owning side to null (unless already changed)
            if ($receivedMessage->getReceiverUser() === $this) {
                $receivedMessage->setReceiverUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Recommandation>
     */
    public function getPostedRecommandations(): Collection
    {
        return $this->postedRecommandations;
    }

    public function addPostedRecommandation(Recommandation $postedRecommandation): static
    {
        if (!$this->postedRecommandations->contains($postedRecommandation)) {
            $this->postedRecommandations->add($postedRecommandation);
            $postedRecommandation->setSenderUser($this);
        }

        return $this;
    }

    public function removePostedRecommandation(Recommandation $postedRecommandation): static
    {
        if ($this->postedRecommandations->removeElement($postedRecommandation)) {
            // set the owning side to null (unless already changed)
            if ($postedRecommandation->getSenderUser() === $this) {
                $postedRecommandation->setSenderUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Recommandation>
     */
    public function getRecommandations(): Collection
    {
        return $this->recommandations;
    }

    public function addRecommandation(Recommandation $recommandation): static
    {
        if (!$this->recommandations->contains($recommandation)) {
            $this->recommandations->add($recommandation);
            $recommandation->setReceiverUser($this);
        }

        return $this;
    }

    public function removeRecommandation(Recommandation $recommandation): static
    {
        if ($this->recommandations->removeElement($recommandation)) {
            // set the owning side to null (unless already changed)
            if ($recommandation->getReceiverUser() === $this) {
                $recommandation->setReceiverUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Skill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(Skill $skill): static
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
        }

        return $this;
    }

    public function removeSkill(Skill $skill): static
    {
        $this->skills->removeElement($skill);

        return $this;
    }

    public function __toString(): string
    {
        return $this->email;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): static
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->getLastName() . ' ' . $this->getFirstName();
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(?string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(?string $about): static
    {
        $this->about = $about;

        return $this;
    }
}
