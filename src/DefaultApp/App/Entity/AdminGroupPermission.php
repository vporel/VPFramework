<?php

namespace VPFramework\DefaultApp\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use VPFramework\Utils\FlexibleClassTrait;
use VPFramework\Model\Entity\Annotations\{EnumField, RelationField, Field};

/**
 * @ORM\Entity
 * @ORM\Table(name="vpframework_admins_groups_permissions")
 */
class AdminGroupPermission
{
    use FlexibleClassTrait;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id = null;

    /**
     * @EnumField(label="Entité", class="VPFramework\DefaultApp\App\Entity\ManagedEntitiesEnum")
     * @ORM\Column(type="string", nullable = false)
     */
    private $entityClass = "";

    /**
     * @Field(label="Ajout")
    * @ORM\Column(type="boolean", nullable = false, options={"default":false})
    */
    private $canAdd = false;
    /**
     * @Field(label="Modification")
     * @ORM\Column(type="boolean", nullable = false, options={"default":false})
     */
    private $canUpdate = false;
    /**
     * @Field(label="Suppression")
     * @ORM\Column(type="boolean", nullable = false, options={"default":false})
     */
    private $canDelete = false;

    /**
     * @RelationField(label="Groupe", repositoryClass="VPFramework\DefaultApp\App\Repository\AdminGroupRepository")
     * @ORM\ManyToOne(targetEntity="VPFramework\DefaultApp\App\Entity\AdminGroup", inversedBy = "permissions")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable = false)
     */
    private $group;
    
    public function __construct(bool $canAdd, bool $canUpdate, bool $canDelete)
    {
        $this->canAdd = $canAdd;
        $this->canUpdate = $canUpdate;
        $this->canDelete = $canDelete;
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

    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @return AdminGroupPermission
     */ 
    public function setEntityClass(string $entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function canAdd(){
        return $this->canAdd;
    }

    public function canUpdate(){
        return $this->canUpdate;
    }

    public function canDelete(){
        return $this->canDelete;
    }

    /**
     * @return AdminGroup
     */
    public function getGroup(){
        return $this->group;
    }

    public function setGroup($group){
        $this->group = $group;
        return $this;
    }
    
    public function __toString()
    {
        return $this->group." - ".$this->entityClass." [".$this->canAdd.", ".$this->canUpdate.", ".$this->canDelete."]";
    }
}
