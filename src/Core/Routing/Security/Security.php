<?php

namespace VPFramework\Core\Routing\Security;

use VPFramework\Core\AppGlobals;
use VPFramework\Core\Configuration\SecurityConfiguration;
use VPFramework\Core\DIC;

/**
 * Classe permettant de sécurisé certaines parties de l'applciation
 * La sécurité est appliquée aux routes
 * On peut définir des règles pour l'accès à certaines routes
 */
final class Security
{
    private $config;

    public function __construct(SecurityConfiguration $config)
    {
        $this->config = $config;
    }

    /**
     * Peut créer ou modifier la clé "before-login-url" de la variable globale $_SESSION
     * Cette clé aura pour valeur l'adresse la page précedente s'il y a redirection.
     */
    public function requireAccess($urlPath)
    {
        $rules = $this->config->getRules('security');
        foreach ($rules as $rule) {
            foreach ($rule->getSafeUrls() as $safeUrl) {
                if (preg_match("#$safeUrl#i", $urlPath)) { // Ajout des délimiteurs car dans le fichier de configuration ils ne sont pas mis
                    
                    $user = DIC::getInstance()->get(AppGlobals::class)->getUser();
                    foreach($rule->getEntitiesRoles() as $entity => $roles){
                        if ($user == null || !in_array($user->getRole(), $roles) || !($user instanceof $entity)) {
                            //Récupération de l'adresse précédente de la page avant la redirection
                            $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '') ? 'https' : 'http';
                            $fullUrl .= '://'.$_SERVER['HTTP_HOST'].$urlPath;
                            if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != $fullUrl) {
                                $_SESSION['URL-before-redirection'] = $_SERVER['HTTP_REFERER'];
                            } else {
                                $_SESSION['URL-before-redirection'] = $_SESSION['URL-before-redirection'] ?? '';
                            }
                            if ($rule->getRedirection() != null && $rule->getRedirection() != "") {
                                header('Location: '.$rule->getRedirection());
                            } else {
                                require FRAMEWORK_ROOT."/View/views/accessDenied.php";
                                return false;
                            }
                        }
                    }
                    break;
                }
            }
        }

        return true;
    }

    public static function getURLBeforeRedirection()
    {
        return $_SESSION['URL-before-redirection'] ?? "";
        
    }
}
