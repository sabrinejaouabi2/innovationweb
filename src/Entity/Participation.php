<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Participation
 *
 * @ORM\Table(name="participation", indexes={@ORM\Index(name="fk_evnet", columns={"EventId"}), @ORM\Index(name="fk_user", columns={"id"})})
 * @ORM\Entity
 */
class Participation
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_part", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_part", type="date", nullable=false)
     */
    private $datePart;

    /**
     * @var int
     *
     * @ORM\Column(name="Nbplaces", type="integer",options={"default": 5}, nullable=false)
     */
    private $nbplaces=5;

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

    public function getIdPart(): ?int
    {
        return $this->idPart;
    }

    public function getDatePart(): ?\DateTimeInterface
    {
        return $this->datePart;
    }

    public function setDatePart(\DateTimeInterface $datePart): self
    {
        $this->datePart = $datePart;

        return $this;
    }

    public function getNbplaces(): ?int
    {
        return $this->nbplaces;
    }

    public function setNbplaces(int $nbplaces): self
    {
        $this->nbplaces = $nbplaces;

        return $this;
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
