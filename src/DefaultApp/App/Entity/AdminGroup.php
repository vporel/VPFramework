<?php

namespace VPFramework\DefaultApp\App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vpframework_admins_groups")
 */
class AdminGroup
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="VPFramework\DefaultApp\App\Entity\AdminGroupPermission", mappedBy="admingroup", cascade={"all"}, orphanRemoval=true)
     */
    private $permissions;

    /**
     * @ORM\OneToMany(targetEntity="AVPFramework\DefaultApp\App\Entity\Admin", mappedBy="admingroup", cascade={"all"}, orphanRemoval=true)
     */
    private $admins;
    
    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->admins = new ArrayCollection();

        return $this;
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

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return AdminGroup
     */ 
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function __toString()
    {
        return $this->name;
    }
}
