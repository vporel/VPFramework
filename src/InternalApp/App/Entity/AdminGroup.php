<?php

namespace VPFramework\InternalApp\App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use VPFramework\Model\Entity\EntityWithId;
use VPFramework\Utils\FlexibleClassTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="vpframework_admins_groups")
 */
class AdminGroup extends EntityWithId
{
    use FlexibleClassTrait;
    /**
     * @ORM\Column(type="string", nullable = false)
     */
    private $name = "";

    /**
     * @ORM\OneToMany(targetEntity="VPFramework\InternalApp\App\Entity\AdminGroupPermission", mappedBy="group", cascade={"all"}, orphanRemoval=true)
     */
    private $permissions;

    /**
     * @ORM\OneToMany(targetEntity="VPFramework\InternalApp\App\Entity\Admin", mappedBy="group", cascade={"all"}, orphanRemoval=true)
     */
    private $admins;
    
    public function __construct()
    {
        $this->permissions = new ArrayCollection();
        $this->admins = new ArrayCollection();

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

    /**
     * Retourne la permission du groupe concernant l'enttité passée en paramètre
     * Si la valeur retournée est NULL, alors, le groupe n'a mème pas le droit de lire cette entité
     * @return AdminGroupPermission|null 
     */
    public function getPermission(string $entityClass): ?AdminGroupPermission
    {
        $permission = null;
        foreach($this->permissions->getIterator() as $i => $p){
            if($p->getEntityClass() == $entityClass){
                $permission = $p;
            }
        }

        return $permission;
    }

    /**
     * @return bool true si le groupe a la permission de lire l'entité et éventuellement de modifier ses instances
     */
    public function canRead(string $entityClass): bool
    {
        return $this->getPermission($entityClass) != null;
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
