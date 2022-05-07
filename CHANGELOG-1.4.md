# CHANGELOG for 1.4.x
================
## 1.4.0
* Changement du système de routage
    * Changement du constructeur de la classe route
    * Création des groupes de routes (RouteGroup)
    * Création de la classe RouteInGroup
    * Les expressions régulières pour les param_tres sont définies directement dans le chemin
        * LEs paramètres sont entre chevrons < >
        * L'expression régulière est mise après le nom entre hastags (expression non obligatoire)
        * La valeur par défaut est mise après un signe =
            * Exemples de paramètres
                <page> ; juste le nom
                <page=1> ; sans expression
                <page#\d+#> ; sans valeur par défaut
                <page#\d+#=1> ; complet
    * Juste le nom du contrôleur est nécessaire, le namespace est déterminé automatiquement (App\Controller)
    * Le contrôleur et la méthode sont renseignés dans un seul paramètre, séparés par deux points(:)
