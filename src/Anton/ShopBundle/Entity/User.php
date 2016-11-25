<?php

namespace Anton\ShopBundle\Entity;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User implements AdvancedUserInterface, EquatableInterface, \Serializable
{
    /**
     * @ORM\Column(name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id_user;

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     * @Assert\Length(max=15)
     * @Assert\Regex("/^[a-zA-Z0-9_]+$/")
     */
    private $username;

    /**
     * @ORM\Column(name="apikey")
     */
    private $apiKey;

    /**
     * @ORM\Column(name="last_login", type="datetime")
     */
    private $lastLoginTime;

    /**
     * @ORM\Column()
     */
    private $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     * @Assert\Length(max=15)
     * @Assert\Regex("/^[a-zA-Z0-9_.,]+$/")     *
     */
    private $plainPassword;

    /**
     * @ORM\Column()
     * @Assert\NotBlank()
     * @Assert\Length(max=30)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid.",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(name="is_active")
     */
    private $isActive;

    /**
     * @ORM\Column()
     */
    private $role;

    public function __construct()
    {
        $this->isActive = true;
    }

    public function getId()
    {
        return $this->id_user;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($password)
    {
        $this->plainPassword = $password;
    }

    public function getLastLoginTime()
    {
        return $this->lastLoginTime;
    }

    public function setLastLoginTime(\DateTime $lastLoginTime)
    {
        $this->lastLoginTime = $lastLoginTime;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role = null)
    {
        $this->role = $role;
    }

    public function getRoles()
    {
        return [$this->getRole()];
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id_user,
            $this->username,
            $this->password,
            $this->isActive,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id_user,
            $this->username,
            $this->password,
            $this->isActive,
            ) = unserialize($serialized);
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }
}
