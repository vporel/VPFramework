<?php
namespace VPFramework\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @author VPOREL-DEV
 *
 */
abstract class EntityWithId extends Entity {
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

	/**
	 * 
	 * @return int de l'identifiant
	 */
	public function getId():?int
    {
        return $this->id;
    }
	
	/**
	 * Modifier la valeur de l'identifiant
	 * @param $id
     * @return EntityWithId
	 */
	public function setId(int $id):EntityWithId
    {
        $this->id= $id;
        return $this;
    }

    public function getKeyProperty():string{
        return "id";
    }

    public function getNaturalOrderField():string
    {
        return "-id";
    }

}
