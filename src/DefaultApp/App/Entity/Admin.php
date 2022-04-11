<?php

namespace VPFramework\DefaultApp\App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use VPFramework\Service\Security\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="vpframework_admins")
 */
class Admin implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable = false)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", nullable = false)
     */
    private $passwork;
    
    /**
     * @ORM\Column(type="boolean", nullable = false)
     */
    private $isSuperAdmin;

    public function __construct(bool $isSuperAdmin)
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setUserName(string $userName)
    {
        $this->userName = $userName;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    public function isSuperAdmin(){
        return $this->isSuperAdmin;
    }

    public function getRole(){
        return ($this->isSuperAdmin) ? "superAdmin" : "";
    }

    public function getKeyField(){
        return "userName";
    }
}
