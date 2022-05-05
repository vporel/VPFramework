# Doctrine
    [Semantical Error] The annotation "@..." in property .... was never imported.

    Ajouter la ligne suivante dans le fichier cli-config.php
    \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader("class_exists");