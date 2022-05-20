<?php

namespace VPFramework\InternalApp\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use VPFramework\Utils\FlexibleClassTrait;
use VPFramework\Form\Annotations\{EnumField, RelationField, Field};
use VPFramework\Model\Entity\EntityWithId;

/**
 * @ORM\Entity
 * @ORM\Table(name="vpframework_admins_groups_permissions")
 */
class AdminGroupPermission extends EntityWithId
{
    use FlexibleClassTrait;
    /**
     * @EnumField(label="Entité", class="VPFramework\InternalApp\App\Entity\ManagedEntitiesEnum")
     * @ORM\Column(type="string", nullable = false)
     */
    private $entityClass = "";

    /**
     * @Field(label="Ajout")
    * @ORM\Column(type="boolean", nullable = true, options={"default":false})
    */
    private $canAdd;
    /**
     * @Field(label="Modification")
     * @ORM\Column(type="boolean", nullable = true, options={"default":false})
     */
    private $canUpdate;
    /**
     * @Field(label="Suppression")
     * @ORM\Column(type="boolean", nullable = true, options={"default":false})
     */
    private $canDelete;

    /**
     * @RelationField(label="Groupe", repositoryClass="VPFramework\InternalApp\App\Repository\AdminGroupRepository")
     * @ORM\ManyToOne(targetEntity="VPFramework\InternalApp\App\Entity\AdminGroup", inversedBy = "permissions")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable = false)
     */
    private $group;
    
    public function __construct(bool $canAdd = false, bool $canUpdate = false, bool $canDelete = false)
    {
        $this->canAdd = $canAdd;
        $this->canUpdate = $canUpdate;
        $this->canDelete = $canDelete;
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
