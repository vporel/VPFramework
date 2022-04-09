<?php
namespace VPFramework\Core\Routing\Security;

use VPFramework\Core\Routing\Security\UserInterface;

class Rule{
        
    /**
     * __construct
     * 
     * $entityesRoles Exemple:
     *      [
     *          Entity::class => ["manager"]
     *      ]
     *
     * @param  array $safeUrls Tableau des chemin protégés par la règles (chemin sont exprimé en expression régulières) Ex: "^/account" pour toutes les urls commencant par /account - "user" pour els urls contenant la chaine user 
     * @param  array $entitiesRoles tableau associatif contenant les entitées(implémentant l'interface VPFramework\Core\Routing\Security\UserInterface) et les roles nécessaires(dans des tableaux)
     * @param  string $redirection Le chemin de redirection si la règle n'est pas satisfaite, si aucun chemin n'est défini, un chemin par défaut sera suivi par le framework. Le chemin doit commencer par / au cas contraire, on cherchera dans le fichier routes.php la route portant le nom défini dans ce paramètre
     * @return void
     */
    public function __construct($safeUrls, $entitiesRoles, $redirection)
    {
        $this->safeUrls = $safeUrls;
        $this->entitiesRoles = $entitiesRoles;
        $this->redirection = $redirection;
        foreach($entitiesRoles as $entity => $roles){
            if(!class_exists($entity) || !in_array(UserInterface::class, class_implements($entity, true))){
                throw new SecurityException("La classe entité $entity est inexistante ou n'implémente pas l'interface ".UserInterface::class);
            }
        }
    }

    public function getSafeUrls(){
        return $this->safeUrls;
    }
}