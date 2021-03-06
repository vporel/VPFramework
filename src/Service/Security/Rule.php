<?php
namespace VPFramework\Service\Security;

use VPFramework\Core\Configuration\RouteConfiguration;
use VPFramework\Core\DIC;

class Rule{

    private $name;
    private $safeUrls;
    private $entitiesRoles;
    private $redirection;
        
    /**
     * __construct
     * 
     * $entityesRoles Exemple:
     *      [
     *          Entity::class => ["manager"]
     *      ]
     *
     * @param  string $name Nom de la règle (ce nom pourra être utilisé pour savoir quelle règle a provoqué la redirection)
     * @param  array $safeUrls Tableau des chemin protégés par la règles (chemin sont exprimé en expression régulières) Ex: "^/account" pour toutes les urls commencant par /account - "user" pour els urls contenant la chaine user 
     * @param  array $entitiesRoles tableau associatif contenant les entitées(implémentant l'interface VPFramework\Core\Routing\Security\UserInterface) et les roles nécessaires(dans des tableaux)
     * @param  string $redirection Le chemin de redirection si la règle n'est pas satisfaite, si aucun chemin n'est défini, un chemin par défaut sera suivi par le framework. Le chemin doit commencer par / au cas contraire, on cherchera dans le fichier routes.php la route portant le nom défini dans ce paramètre
     * Le chamin de redirection ne doit pas prendre de paramètres
     * @return void
     */
    public function __construct(string $name, array $safeUrls, array $entitiesRoles, string $redirection = null)
    {
        $this->name = $name;
        $this->safeUrls = $safeUrls;
        $this->entitiesRoles = $entitiesRoles;
        if($redirection == null || substr($redirection, 0,1) == "/"){
            $this->redirection = $redirection;
        }else{//C'est le nom d'une route qui a été passé en paramètre
            $routeConfig = DIC::getInstance()->get(RouteConfiguration::class);
            //Pour savoir si l'accès à la route est le résultat d'une redirection pour la sécurité, 
            // il suffira de vérifier que l paramètre GET "security-redirection" est présent dans l'url
            $this->redirection = $routeConfig->getRoute($redirection)->getPath(["security-redirection" => true]);
        }
        foreach($entitiesRoles as $entity => $roles){
            if(class_exists($entity, true)){
                if(!in_array(UserInterface::class, class_implements($entity, true)))
                    throw new SecurityException("La classe entité $entity n'implémente pas l'interface ".UserInterface::class);
            }else{
                throw new SecurityException("La classe entité $entity n'existe pas");
            }
        }
    }

    public function getName(){
        return $this->name;
    }

    public function getSafeUrls(){
        return $this->safeUrls;
    }

    public function getEntitiesRoles(){
        return $this->entitiesRoles;
    }

    public function getRedirection(){
        return $this->redirection;
    }
}