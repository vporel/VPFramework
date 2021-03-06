<?php

namespace VPFramework\DefaultApp\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use VPFramework\Model\Entity\Annotations\{RelationField, PasswordField};
use VPFramework\Model\Entity\EntityWithId;
use VPFramework\Service\Security\UserInterface;
use VPFramework\Utils\FlexibleClassTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="vpframework_admins")
 */
class Admin extends EntityWithId implements UserInterface
{
    use FlexibleClassTrait;
    /**
     * @ORM\Column(type="string", nullable = false)
     */
    private $userName = "";

    /**
     * @PasswordField()
     * @ORM\Column(type="string", nullable = false)
     */
    private $password ="";
    
    /**
     * @ORM\Column(type="boolean", nullable = true)
     */
    private $isSuperAdmin = false;

    /**
     * Si c'est un superAdmin, alors ce champ n'est pas indispensable
     * @RelationField(label="Groupe", repositoryClass="VPFramework\DefaultApp\App\Repository\AdminGroupRepository")
     * @ORM\ManyToOne(targetEntity="VPFramework\DefaultApp\App\Entity\AdminGroup", inversedBy = "admins")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable = true)
     */
    private $group; 

    public function __construct(bool $isSuperAdmin = false)
    {
        $this->isSuperAdmin = $isSuperAdmin;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    /**
     * @return Admin
     */ 
    public function setUserName(string $userName)
    {
        $this->userName = $userName;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return Admin
     */ 
    public function setPassword(string $password)
    {
        $this->password = $password;
        return $this;
    }

    public function isSuperAdmin(){
        return $this->isSuperAdmin;
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

    public function getRole(){
        return ($this->isSuperAdmin) ? "superAdmin" : "";
    }

    public function getKeyField(){
        return "userName";
    }

    public function __toString()
    {
        return $this->userName;
    }

    /**
     * Retourne la permission du groupe concernant l'enttit?? pass??e en param??tre
     * Si la valeur retourn??e est NULL, alors, le groupe n'a m??me pas le droit de lire cette entit??
     * @return AdminGroupPermission|null 
     */
    public function getPermission(string $entityClass): ?AdminGroupPermission
    {
        if($this->isSuperAdmin){
            return new AdminGroupPermission(true, true, true);
        }else{
            return $this->group->getPermission($entityClass);
        }
    }

}
