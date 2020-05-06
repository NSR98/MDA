<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use App\Validator\Constraints\IsValidForm as AcmeAssert;
use Symfony\Component\Validator\Constraints as Assert;


class User implements UserInterface
{
    private $id;
    /**
     * @Assert\Length(min="1", max="30")
     */
    private $username;
    /**
     * @Assert\Length(min="1", max="30")
     */
    private $password;
    /**
     * @Assert\Length(min="1", max="30")
     */
    private $surname;
    /**
     * @Assert\Length(min="1", max="30")
     */
    private $name;

    /**
     * @Assert\Regex("/^(\d{8})([A-Z])$/",
     *      message="Formato incorrecto. Ejemplo: 12345678X")
     */
    private $dni;
    /**
     * @Assert\Email
     */
    private $email;
    /**
     * @Assert\PositiveOrZero()
     */
    private $role;

    /**
     * User constructor.
     * @param array|null $user
     */
    public function __construct(array $user = null)
    {
        $this->id = $user["id"] ?? null;
        $this->username = $user["user"] ?? null;
        $this->password = $user["password"] ?? null;
        $this->surname = $user["surname"] ?? null;
        $this->name = $user["name"] ?? null;
        $this->dni = $user["dni"] ?? null;
        $this->email = $user["email"] ?? null;
        $this->role = $user["rol"] ?? null;
    }
    

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }


    /**
     * @return mixed
     */
    public function getDni()
    {
        return $this->dni;
    }

    /**
     * @param mixed $dni
     */
    public function setDni($dni): void
    {
        $this->dni = $dni;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role): void
    {
        $this->role = $role;
    }

    public function getRoles()
    {
    }

    public function getSalt()
    {
    }

    public function eraseCredentials()
    {
    }
}