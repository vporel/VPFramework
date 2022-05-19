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

    public function __construct(string $name, string $path)
    {
        $this->name = $name;
        $this->path = $path;
    }
}
