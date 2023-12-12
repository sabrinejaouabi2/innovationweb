<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Favouris
 *
 * @ORM\Table(name="favouris", indexes={@ORM\Index(name="fkevent", columns={"EventId"}), @ORM\Index(name="fkusr", columns={"id"})})
 * @ORM\Entity
 */
class Favouris
{
    /**
     * @var int
     *
     * @ORM\Column(name="idfav", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idfav;

    /**
     * @var \Evenement
     *
     * @ORM\ManyToOne(targetEntity="Evenement")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="EventId", referencedColumnName="EventId")
     * })
     */
    private $eventid;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private $id;

    public function getIdfav(): ?int
    {
        return $this->idfav;
    }

    public function getEventid(): ?Evenement
    {
        return $this->eventid;
    }

    public function setEventid(?Evenement $eventid): self
    {
        $this->eventid = $eventid;

        return $this;
    }

    public function getId(): ?User
    {
        return $this->id;
    }

    public function setId(?User $id): self
    {
        $this->id = $id;

        return $this;
    }


}
