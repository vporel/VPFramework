<?php
namespace VPFramework\Routing\Annotations;

/**
 * Annotation permettant de définir des éléments supplémetaires pour une entité
 * Ex : Un label qui sera utilisé dans les formulaires
 * 
 * @Annotation
 * @Target({"CLASS"})
 */
class RouteGroup
{
    /**
     * Le debut de l'url pour toute les routes du group
     * @Requireds
     */
    public $pathStart;

}
