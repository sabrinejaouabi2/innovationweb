<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Evenement
 *
 * @ORM\Table(name="evenement")
 * @ORM\Entity
 */
class Evenement
{
    /**
     * @var int
     *
     * @ORM\Column(name="EventId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    #[Groups("evenement")]

    private $eventid;

    /**
     * @var string
     *
     * @ORM\Column(name="EventNom", type="string", length=500, nullable=false)
     */
    #[Groups("evenement")]

    private $eventnom;

    /**
     * @var string
     *
     * @ORM\Column(name="EventTheme", type="string", length=500, nullable=false)
     */
    #[Groups("evenement")]

    private $eventtheme;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateDebutEvent", type="date", nullable=false)
     */
    #[Groups("evenement")]

    private $datedebutevent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateFinEvent", type="date", nullable=false)
     */
    #[Groups("evenement")]

    private $datefinevent;

    /**
     * @var string
     *
     * @ORM\Column(name="EventAdresse", type="string", length=500, nullable=false)
     */
    #[Groups("evenement")]

    private $eventadresse;

    /**
     * @var string
     *
     * @ORM\Column(name="EventDescription", type="string", length=500, nullable=false)
     */
    #[Groups("evenement")]

    private $eventdescription;

    /**
     * @var string
     *
     * @ORM\Column(name="Eventimage", type="string", length=500, nullable=false)
     */
    #[Groups("evenement")]

    private $eventimage;

    public function getEventid(): ?int
    {
        return $this->eventid;
    }

    public function getEventnom(): ?string
    {
        return $this->eventnom;
    }

    public function setEventnom(string $eventnom): self
    {
        $this->eventnom = $eventnom;

        return $this;
    }

    public function getEventtheme(): ?string
    {
        return $this->eventtheme;
    }

    public function setEventtheme(string $eventtheme): self
    {
        $this->eventtheme = $eventtheme;

        return $this;
    }

    public function getDatedebutevent(): ?\DateTimeInterface
    {
        return $this->datedebutevent;
    }

    public function setDatedebutevent(\DateTimeInterface $datedebutevent): self
    {
        $this->datedebutevent = $datedebutevent;

        return $this;
    }

    public function getDatefinevent(): ?\DateTimeInterface
    {
        return $this->datefinevent;
    }

    public function setDatefinevent(\DateTimeInterface $datefinevent): self
    {
        $this->datefinevent = $datefinevent;

        return $this;
    }

    public function getEventadresse(): ?string
    {
        return $this->eventadresse;
    }

    public function setEventadresse(string $eventadresse): self
    {
        $this->eventadresse = $eventadresse;

        return $this;
    }

    public function getEventdescription(): ?string
    {
        return $this->eventdescription;
    }

    public function setEventdescription(string $eventdescription): self
    {
        $this->eventdescription = $eventdescription;

        return $this;
    }

    public function getEventimage(): ?string
    {
        return $this->eventimage;
    }

    public function setEventimage(string $eventimage): self
    {
        $this->eventimage = $eventimage;

        return $this;
    }


}