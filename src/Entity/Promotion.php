<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Promotion
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity
 */
class Promotion
{
    /**
     * @var int
     *
     * @ORM\Column(name="codepromo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $codepromo;

    /**
     * @var string
     *
     * @ORM\Column(name="nomPromo", type="string", length=30, nullable=false)
     * @Assert\NotBlank(message="Le nom est obligatoire.")
     * @Assert\Length(max=10, maxMessage="Le nom ne doit pas dépasser {{ limit }} caractères.")
     */
    private $nompromo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebutPromo", type="date", nullable=false)
     * @Assert\NotBlank(message="La date de début est obligatoire.")
     * @Assert\LessThan(propertyPath="datefinpromo", message="La date de début doit être avant la date de fin")
     */
    private $datedebutpromo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFinPromo", type="date", nullable=false)
     * @Assert\NotBlank(message="La date de fin est obligatoire.")
     * @Assert\GreaterThan(propertyPath="datedebutpromo", message="La date de fin doit être après la date de début")
     */
    private $datefinpromo;

    public function getCodepromo(): ?int
    {
        return $this->codepromo;
    }

    public function getNompromo(): ?string
    {
        return $this->nompromo;
    }

    public function setNompromo(string $nompromo): self
    {
        $this->nompromo = $nompromo;

        return $this;
    }

    public function getDatedebutpromo(): ?\DateTimeInterface
    {
        return $this->datedebutpromo;
    }

    public function setDatedebutpromo(\DateTimeInterface $datedebutpromo): self
    {
        $this->datedebutpromo = $datedebutpromo;

        return $this;
    }

    public function getDatefinpromo(): ?\DateTimeInterface
    {
        return $this->datefinpromo;
    }

    public function setDatefinpromo(\DateTimeInterface $datefinpromo): self
    {
        $this->datefinpromo = $datefinpromo;

        return $this;
    }


}
