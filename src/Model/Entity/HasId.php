<?php
namespace VPFramework\Model\Entity;
/**
 * Cette interface est impl�ment�e par les entit�s ayant un identifiant de type Integer
 * @author VPOREL-DEV
 *
 */
interface HasId {
	/**
	 * 
	 * @return Valeur de l'identifiant
	 */
	public function getId();
	
	/**
	 * Modifier la valeur de l'identifiant
	 * @param $id
	 */
	public function setId($id);
}
