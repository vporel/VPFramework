<?php
namespace VPFramework\Service\Admin\Annotations;

/**
 * This annotation should be used on entities properties
 * The properties with this annotation will be shown in the list page of Admin
 * If none of the entity properties has this annotation, all the properties will be shown
 * @Annotation
 * @Target({"PROPERTY"})
 */
class ShowInList
{

}
