<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Partenaire
 *
 * @ORM\Table(name="partenaire", indexes={@ORM\Index(name="fk_part", columns={"codePromo"})})
 * @ORM\Entity
 */
class Partenaire
{
    /**
     * @var int
     *
     * @ORM\Column(name="idPart", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idpart;

    /**
     * @var string
     *
     * @ORM\Column(name="nomPart", type="string", length=30, nullable=false)
      * @Assert\NotBlank(message="Le nom du partenaire est obligatoire.")
      * @Assert\Length(max=10, maxMessage="Le nom ne doit pas dépasser {{ limit }} caractères.")

     */
    private $nompart;

    /**
     * @var string
     * @ORM\Column(name="logoPart", type="string", length=30, nullable=false)
     */
    private $logopart;

    /**
     * @var \Promotion
     * @ORM\ManyToOne(targetEntity="Promotion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="codePromo", referencedColumnName="codepromo")
     * })
     */
    private $codepromo;
    /**
     * @var int
     * @ORM\Column(name="nbvue", type="integer", nullable=true)
     */
    private $nbvue;


    public function getIdpart(): ?int
    {
        return $this->idpart;
    }

    public function getNompart(): ?string
    {
        return $this->nompart;
    }

    public function setNompart(string $nompart): self
    {
        $this->nompart = $nompart;

        return $this;
    }

    public function getLogopart(): ?string
    {
        return $this->logopart;
    }

    public function setLogopart(string $logopart): self
    {
        $this->logopart = $logopart;

        return $this;
    }

    public function getCodepromo(): ?Promotion
    {
        return $this->codepromo;
    }

    public function setCodepromo(?Promotion $codepromo): self
    {
        $this->codepromo = $codepromo;

        return $this;
    }
    public function getNbvue(): ?int
    {
        return $this->nbvue;
    }

    public function setNbvue(?int $nbvue): self
    {
        $this->nbvue = $nbvue;

        return $this;
    }
    public function incrementNbvue(): self{
        $this-> nbvue++;
        return  $this;
    }


}
