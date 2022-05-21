<?php
namespace VPFramework\Routing\Annotations;

/**
 * Annotation permettant de définir des éléments supplémetaires pour une entité
 * Ex : Un label qui sera utilisé dans les formulaires
 * 
 * @Annotation
 * @Target({"METHOD"})
 * @NamedArgumentConstructor
 */
class Route
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $path;

    /**
    * @var string
    */
    public $requiredParameters;

    public function __construct(string $name, string $path, array $requiredParameters = [])
    {
        $this->name = $name;
        $this->path = $path;
        $this->requiredParameters = $requiredParameters;
    }
}
