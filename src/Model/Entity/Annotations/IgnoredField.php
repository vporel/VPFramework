<?php
namespace VPFramework\Model\Entity\Annotations;

/**
 * Annotation utilisé pour les champs qui ne devront pas être gérés le framework (ignorés)
 * Ils ne seront donc pas pris en compte par les formulaires
 * @Annotation
 * @Target({"PROPERTY"})
 */
class IgnoredField extends Field
{
    
}
