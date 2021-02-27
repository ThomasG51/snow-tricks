<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="* Un compte existe déjà avec cette email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="* L'adresse email ne peut pas être vide.")
     * @Assert\Email(message="* L'adresse email n'est pas valide")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(
     *     min=8,
     *     max=12,
     *     minMessage="* Le mot de passe doit contenir au moins {{ limit }} caractères",
     *     maxMessage="* Le mot de passe doit contenir moins de {{ limit }} caractères"
     * )
     * @Assert\Regex("/[a-z]+/", message="* Le mot de passe doit contenir au moins une minuscule")
     * @Assert\Regex("/[A-Z]+/", message="* Le mot de passe doit contenir au moins une majuscule")
     * @Assert\Regex("/[0-9]+/", message="* Le mot de passe doit contenir au moins un chiffre")
     */
    private $password;

    /**
     * @var string The hashed password
     * @Assert\EqualTo(propertyPath="password", message="* Les deux mots de passe ne correspondent pas")
     */
    private $confirm_password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     max=20,
     *     minMessage="* Le prénom doit contenir au moins {{ limit }} caractères",
     *     maxMessage="* Le prénom doit contenir moins de {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="* Le prénom ne peut pas être vide.")
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min=2,
     *     max=20,
     *     minMessage="* Le nom doit contenir au moins {{ limit }} caractères",
     *     maxMessage="* Le nom doit contenir moins de {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="* Le nom ne peut pas être vide.")
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image(mimeTypesMessage="* Le fichier doit être une image")
     */
    private $avatar;

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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmPassword(): string
    {
        return $this->confirm_password;
    }

    /**
     * @param string $confirm_password
     */
    public function setConfirmPassword(string $confirm_password): void
    {
        $this->confirm_password = $confirm_password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}
