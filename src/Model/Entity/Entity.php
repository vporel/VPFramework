<?php
namespace VPFramework\Model\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author VPOREL-DEV
 *
 */
class Entity {
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
     * @return Entity
	 */
	public function setId(int $id):Entity
    {
        $this->id= $id;
        return $this;
    }
}
