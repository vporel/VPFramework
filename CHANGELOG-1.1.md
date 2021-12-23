# CHANGELOG for 1.1.x
================
## 1.1.1
* Change how the security service is defined in services.json file
    * remove the activated rule
    * only one option **'safe_urls'**
        "url_regex":{
            "roles": [],
            "entity": "EntityClass",
            "redirection": "url if the access is denied"
        }
    * Move the security folter to a new one "service"
    * Create a new class Security
    * Make the Router to use an instance of this class for checking Security
* Creation of unique Class for each configuration file (routes, services)
