<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Table(name="account")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AccountRepository")
 */
class Account
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=500, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=500, nullable=true)
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="level", type="integer", options={"default" : 1})
     */
    private $level;
    

    
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Account
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Account
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return Account
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     *
     * @return Account
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
    
    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * Is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        if ($this->level >= 3)
        {
            return True;
        }
        else
        {
            return False;
        }
    }
    
    /**
     * Is guest
     *
     * @return bool
     */
    public function isGuest()
    {
        if ($this->level >= 2)
        {
            return True;
        }
        else
        {
            return False;
        }
    }
    
    /**
     * Is user
     *
     * @return bool
     */
    public function isUser()
    {
        if ($this->level >= 1)
        {
            return True;
        }
        else
        {
            return False;
        }
    }
    
    /**
     * Set admin
     *
     * @return Account
     */
    public function setAdmin()
    {
        $this->level = 3;
        
        return $this;
    }
    
    /**
     * Set guest
     *
     * @return Account
     */
    public function setGuest()
    {
        $this->level = 2;
        
        return $this;
    }
    
    /**
     * Set user
     *
     * @return Account
     */
    public function setUser()
    {
        $this->level = 1;
        
        return $this;
    }
    
   
}

