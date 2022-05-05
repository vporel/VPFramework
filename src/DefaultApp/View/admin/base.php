<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? "" ?> | Administration</title>
    <style>
        :root{
            --primary-color:rgb(150,150,0);
            --primary-color-lighten:rgb(180,180,0);
            --secondary-color:rgb(60,0,150);
        }
        a{text-decoration: none;}
        .btn{padding:5px 10px;background:var(--primary-color);border:0; color:white;
            font-size:16px;}
        .btn:hover{background:var(--primary-color-lighten);}
        .btn-bad{background:rgb(200,0, 0);}
        .btn-bad:hover{background:rgb(230,0, 0);}
        .btn-secondary{background:var(--secondary-color);}
        .btn-secondary:hover{background:rgb(80,0, 180);}
        .btn-disable{background:lightgray;}
        .btn-disable:hover{background:lightgray;}
        .cursor-pointer{cursor: pointer;}
        
        .alert{
            width:100%;margin:auto;padding:10px;margin-top:10px;
            border:2px solid var(--secondary-color);
            background:rgba(60,0,150,.3);
            border-radius:10px;
        }
        body{background:rgb(245,245,245);font-family:sans-serif, calibri, "century gothic";padding:0;margin:0;}
        #page{width:95%;margin:auto;margin-top:10px;display:flex;justify-content: space-between;}

        header{
            background:var(--primary-color);padding:0 15px;height:40px;display:flex;
            justify-content: space-between;align-items: center;position: sticky;top:0;
        }
        header h2{margin:0;color:white;text-shadow:1px 1px 1px black;}
        header h2 a{color:inherit;}
        
        header nav{display: flex;align-items: center;justify-content: flex-end;}
        header nav a{color:white;margin:0 5px;}
        header nav a:hover{color:var(--secondary-color);text-decoration: underline;}
        aside{width:25%;}
        main{width:74.5%;}
        aside section{
            background:white;margin-bottom:10px;min-height:100px;
        }
        aside section h3{
            background: var(--primary-color);margin:0;padding:5px;
            color:white;
        }
        aside section>div{
            padding:10px;
        }
        aside section .element{display: flex;justify-content: space-between;border-bottom:1px solid lightgray;padding:5px 0;}
        aside section .element:nth-child(2n){
            background:rgb(255,255,255,.5);
        }
        aside section .element a{
            
            color:var(--secondary-color);
        }
        aside section .element .title{
            
            color:var(--primary-color);
        }
    </style>
    <?= $_styles ?? "" ?>
</head>
<body>
    <header>
        <h2><a href="/admin">Administration VPFramework</a></h2>
        <nav>
            <h4>Bienvenu <?= $app->user ?>, </h4>
            <a href="/">Aller au site</a> / 
            <a href="<?= $url("admin-update-password"); ?>">Modifier mot de passe</a> /
            <a href="<?= $url("admin-logout"); ?>">Deconnexion</a>
        </nav>
    </header>
    <section id="page">
        <aside>
            <section>
                <h3>Application</h3>
                <div>
                    <?php if(count($entitiesAdmin) > 0) { ?>
                    <?php 
                        $haveOneReadPermission = false;
                        foreach($entitiesAdmin as $entityAdmin){ 
                            if(!$entityAdmin->isBuiltin()){
                                $permission = $app->user->getPermission($entityAdmin->getEntityClass());
                                if($permission != null){ //Droit de lecture
                                    $haveOneReadPermission = true;
                        ?>
                            <div class="element">
                                <a href="<?= $url("admin-entity-list", ["entityName" => $entityAdmin->getName()]) ?>" class="title"><?= $entityAdmin->getName() ?></a>
                                <?php 
                                    if($permission->canAdd) { 
                                ?>
                                    <a href="<?= $url("admin-entity-add", ["entityName" => $entityAdmin->getName()]) ?>">Ajouter</a>
                            </div>
                        <?php 
                                    }
                                }
                            }
                        } 
                        if(!$haveOneReadPermission)
                            echo "<div class='alert'>Vous n'avez le droit de lecture sur aucune des entités présentes</div>";
                        }else{
                            echo "<div class='alert'>Aucune entité enregistrée pour le service d'administration</div>";
                        }
                    ?>
                </div>
            </section>
            <?php 
                if($app->user->isSuperAdmin) { 
            ?>
            <section>
                <h3>Administration</h3>
                <div>
                    <div class="element">
                        <a href="<?= $url("admin-entity-list", ["entityName" => "Admin"]) ?>" class="title">Administrateurs</a>
                        <a href="<?= $url("admin-entity-add", ["entityName" => "Admin"]) ?>">Ajouter</a>
                    </div>
                    <div class="element">
                        <a href="<?= $url("admin-entity-list", ["entityName" => "AdminGroup"]) ?>" class="title">Groupes d'aministrateurs</a>
                        <a href="<?= $url("admin-entity-add", ["entityName" => "AdminGroup"]) ?>">Ajouter</a>
                    </div>
                    <div class="element">
                        <a href="<?= $url("admin-entity-list", ["entityName" => "AdminGroupPermission"]) ?>" class="title">Permissions des groupes</a>
                        <a href="<?= $url("admin-entity-add", ["entityName" => "AdminGroupPermission"]) ?>">Ajouter</a>
                    </div>
                </div>
            </section>
            <?php } ?>
        </aside>
        <main>
            <?= $_main ?? "" ?>
        </main>
    </section>
    <?= $_scripts ?? "" ?>
</body>
</html>