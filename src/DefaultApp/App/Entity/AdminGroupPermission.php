<?php

namespace VPFramework\DefaultApp\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use VPFramework\Utils\FlexibleClassTrait;

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
    private $id;

    /**
     * @ORM\Column(type="string", nullable = false)
     */
    private $entityClass;

    /**
     * @ORM\Column(type="bool", nullable = false, defaultValue=true)
     */
    private $canRead;
    /**
    * @ORM\Column(type="bool", nullable = false, defaultValue=false)
    */
    private $canAdd;
    /**
     * @ORM\Column(type="bool", nullable = false, defaultValue=false)
     */
    private $canUpdate;
    /**
     * @ORM\Column(type="bool", nullable = false, defaultValue=false)
     */
    private $canDelete;

    /**
     * @ORM\ManyToOne(targetEntity="VPFramework\DefaultApp\App\Entity\AdminGroup", inversedBy = "admingrouppermission")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable = false)
     */
    private $group;
    
    
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

    public function canRead(){
        return $this->canRead;
    }

    public function setCanRead(bool $val){
        $this->canRead = $val;
        return $this;
    }

    public function canAdd(){
        return $this->canAdd;
    }
    
    public function setCanAdd(bool $val){
        $this->canAdd = $val;
        return $this;
    }

    public function canUpdate(){
        return $this->canUpdate;
    }
    
    public function setCanUpdate(bool $val){
        $this->canUpdate = $val;
        return $this;
    }

    public function canDelete(){
        return $this->canDelete;
    }
    
    public function setCanDelete(bool $val){
        $this->canDelete = $val;
        return $this;
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
        return $this->entityClass+" [".$this->canRead.", ".$this->canAdd.", ".$this->canUpdate.", ".$this->canDelete."]";
    }
}
