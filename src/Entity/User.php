<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=180, unique=true)
     */
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Groups("users")]
    private ?string $email = null;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json")
     */
    #[Groups("users")]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    #[Groups("users")]
    private ?string $password = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prenom_user", type="string", length=255)
     */
    #[Groups("users")]
    private ?string $prenom_user = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom_user", type="string", length=255)
     */
    #[Groups("users")]
    private ?string $nom_user = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="adresse_user", type="string", length=255)
     */
    #[Groups("users")]
    private ?string $adresse_user = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reset_token", type="string", length=180, nullable=true)
     */
    #[Groups("users")]

    private $reset_token;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="isBlocked", type="boolean")
     * @Groups("users")
     */
    #[Groups("users")]
    private $isBlocked = false;

    /**
     * @var string|null
     *
     * @ORM\Column(name="etat", type="string", length=10, nullable=true, options={"default"="Actif"})
     * @Groups("users")
     */
    #[Groups("users")]
    private ?string $etat = "Actif";

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
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

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenomUser(): ?string
    {
        return $this->prenom_user;
    }

    public function setPrenomUser(string $prenom_user): self
    {
        $this->prenom_user = $prenom_user;

        return $this;
    }

    public function getNomUser(): ?string
    {
        return $this->nom_user;
    }

    public function setNomUser(string $nom_user): self
    {
        $this->nom_user = $nom_user;

        return $this;
    }

    public function getAdresseUser(): ?string
    {
        return $this->adresse_user;
    }

    public function setAdresseUser(string $adresse_user): self
    {
        $this->adresse_user = $adresse_user;

        return $this;
    }

    public function getResetToken()
    {
        return $this->reset_token;
    }

    public function setResetToken($reset_token): void
    {
        $this->reset_token = $reset_token;
    }
    public function isIsBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setIsBlocked(?bool $isBlocked): self
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }
    public function getEtat(): ?string
    {
        return $this->etat;
    }


    public function setEtat(?string $etat): void
    {
        $this->etat = $etat;
    }
public function __toString(){

        return $this->getNomUser();
}
    public function NomComplet(){
        return $this->getNomUser()." ".$this->getPrenomUser();
    }

}