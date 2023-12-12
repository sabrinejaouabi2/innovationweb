<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class UserInteraction
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $interactedUser;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull()
     */
    private $liked;

    /**
     * Constructor to initialize default values.
     */
    public function __construct()
    {
        $this->liked = false;
    }

    /**
     * Get whether the interaction is a like.
     *
     * @return bool
     */
    public function getLiked(): ?bool
    {
        return $this->liked;
    }

    /**
     * Set the interaction as a like or dislike.
     *
     * @param bool $liked
     * @return $this
     */
    public function setLiked(bool $liked): self
    {
        $this->liked = $liked;

        return $this;
    }

    /**
     * Get whether the interaction is a dislike.
     *
     * @return bool
     */
    public function getDisliked(): ?bool
    {
        return !$this->liked;
    }

    /**
     * Set the interaction as a dislike.
     *
     * @param bool $disliked
     * @return $this
     */
    public function setDisliked(bool $disliked): self
    {
        $this->liked = !$disliked;

        return $this;
    }

    /**
     * Set the user initiating the interaction.
     *
     * @param User $user
     * @return $this
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Set the user being interacted with.
     *
     * @param User $interactedUser
     * @return $this
     */
    public function setInteractedUser(User $interactedUser): self
    {
        $this->interactedUser = $interactedUser;

        return $this;
    }
    public function getInteractedUser(): ?User
    {
        return $this->interactedUser;
    }
}
