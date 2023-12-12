<?php


namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


/**
 * Reclamation
 *
 * @ORM\Table(name="reclamation")
 * @ORM\Entity
 */


class Reclamation
{
    /**
     * @var int
     *
     * @ORM\Column(name="RecId", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    #[Groups("reclamation")]

    private $recid;


    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = "Le nom ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    #[Groups("reclamation")]

    private $nom;


    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="Le prénom est obligatoire")
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = "Le prénom ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    #[Groups("reclamation")]

    private $prenom;


    /**
     * @var int
     *
     * @ORM\Column(name="tel", type="integer", nullable=false)
     * @Assert\NotBlank(message="Le numéro de téléphone est obligatoire")
     * @Assert\Positive(message="Le numéro de téléphone doit être un nombre positif")
     */

    #[Groups("reclamation")]
    private $tel;


    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="L'adresse email est obligatoire")
     * @Assert\Email(message="L'adresse email n'est pas valide")
     */
    #[Groups("reclamation")]

    private $mail;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ReclDate", type="date", nullable=false)
     * @Assert\NotBlank(message="La date de réclamation est obligatoire")
     * @Assert\LessThanOrEqual("today", message="La date de réclamation ne peut pas être dans le futur")
     */

    #[Groups("reclamation")]
    private $recldate;


    /**
     * @var string
     *
     * @ORM\Column(name="ReclObject", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="L'objet de la réclamation est obligatoire")
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = "L'objet de la réclamation ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    #[Groups("reclamation")]

    private $reclobject;


    /**
     * @var string
     *
     * @ORM\Column(name="ReclDescription", type="string", length=500, nullable=false)
     * @Assert\NotBlank(message="La description de la réclamation est obligatoire")
     * @Assert\Length(
     *      max = 500,
     *      maxMessage = "La description de la réclamation ne peut pas dépasser {{ limit }} caractères"
     * )
     */
    #[Groups("reclamation")]

    private $recldescription;


    /**
     * @var string
     * @Assert\NotBlank(message="La choice de la réclamation est obligatoire")
     * @Assert\Choice(choices={"client", "freelance"})
     * @ORM\Column(name="type", type="string", length=500, nullable=false)
     */

    #[Groups("reclamation")]
    private $type;


    public function getRecid(): ?int
    {
        return $this->recid;
    }


    public function getNom(): ?string
    {
        return $this->nom;
    }


    public function setNom(string $nom): self
    {
        $this->nom = $nom;


        return $this;
    }


    public function getPrenom(): ?string
    {
        return $this->prenom;
    }


    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;


        return $this;
    }


    public function getTel(): ?int
    {
        return $this->tel;
    }


    public function setTel(int $tel): self
    {
        $this->tel = $tel;


        return $this;
    }


    public function getMail(): ?string
    {
        return $this->mail;
    }


    public function setMail(string $mail): self
    {
        $this->mail = $mail;


        return $this;
    }


    public function getRecldate(): ?\DateTimeInterface
    {
        return $this->recldate;
    }


    public function setRecldate(\DateTimeInterface $recldate): self
    {
        $this->recldate = $recldate;


        return $this;
    }


    public function getReclobject(): ?string
    {
        return $this->reclobject;
    }


    public function setReclobject(string $reclobject): self
    {
        $this->reclobject = $reclobject;


        return $this;
    }


    public function getRecldescription(): ?string
    {
        return $this->recldescription;
    }


    public function setRecldescription(string $recldescription): self
    {
        $this->recldescription = $recldescription;


        return $this;
    }


    public function getType(): ?string
    {
        return $this->type;
    }


    public function setType(string $type): self
    {
        $this->type = $type;


        return $this;
    }




}

