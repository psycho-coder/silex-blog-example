<?php

namespace Blog\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
    /**
     * User's identifier
     * @var int
     */
    protected $id;

    /**
     * Username
     * @var string
     */
    protected $username;

    /**
     * Encrypted password
     * @var string
     */
    protected $password;

    /**
     * Salt for password encryption
     * @var string
     */
    protected $salt;

    /**
     * User's roles
     * @var array
     */
    protected $roles;


    /**
     * Gets the User identifier.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Sets the user's identifier.
     *
     * @param int $id Identifier
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * Sets the username.
     *
     * @param string $username Username
     *
     * @return self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Gets the encrypted password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }
    
    /**
     * Sets the encrypted password.
     *
     * @param string $password Hash that presents encrypted password
     * @return self
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Gets the salt for password encryption
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }
    
    /**
     * Sets the salt for password encryption.
     *
     * @param string $salt the salt
     * @return self
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Gets the User's roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * Sets the user's roles.
     *
     * @param array $roles Array of roles
     * @return self
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // we have nothing to worry about
        return $this;
    }    
}