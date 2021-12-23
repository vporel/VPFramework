<?php

namespace VPFramework\Service\Security;

use VPFramework\Core\AppGlobals;
use VPFramework\Core\Configuration\ServiceConfiguration;
use VPFramework\Core\Configuration\ServiceNotFoundException;
use VPFramework\Core\DIC;

final class Security
{
    private $config;

    public function __construct(ServiceConfiguration $config)
    {
        $this->config = $config;
    }

    /**
     * Peut créer ou modifier la clé "before-login-url" de la variable globale $_SESSION
     * Cette clé aura pour valeur l'adresse la page précedente s'il y a redirection.
     */
    public function checkSecurity($urlPath)
    {
        try {
            $security = $this->config->getService('security');
            foreach ($security['safe_urls'] as $safeUrls => $definition) {
                $safeUrlsArray = preg_split('#, *#', $safeUrls);
                foreach ($safeUrlsArray as $safeUrl) {
                    if (preg_match($safeUrl, $urlPath)) {
                        /**
                         * unnamed.
                         */
                        $user = DIC::getInstance()->get(AppGlobals::class)->getUser();
                        if ($user == null || !in_array($user->getRole(), $definition['roles']) || !($user instanceof $definition['entity'])) {
                            // unset($_SESSION["user"]);
                            //Récupération de l'adresse précédente de la page avant la redirection
                            $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != '') ? 'https' : 'http';
                            $fullUrl .= '://'.$_SERVER['HTTP_HOST'].$urlPath;
                            if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] != $fullUrl) {
                                $_SESSION['URL-before-redirection'] = $_SERVER['HTTP_REFERER'];
                            } else {
                                $_SESSION['URL-before-redirection'] = $_SESSION['URL-before-redirection'] ?? '';
                            }
                            if (isset($definition['redirection']) && $definition['redirection'] != '') {
                                header('Location: '.$definition['redirection']);
                            } else {
                                echo '<h1>Access denied</h1>';

                                return false;
                            }
                        }
                        break;
                    }
                }
            }
        } catch (ServiceNotFoundException $e) {
            //Do nothing
        }

        return true;
    }

    public static function getURLBeforeRedirection()
    {
        if (isset($_SESSION['URL-before-redirection'])) {
            return $_SESSION['before-redirection'];
        } else {
            return '';
        }
    }
}
