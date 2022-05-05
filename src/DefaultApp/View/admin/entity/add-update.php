
<?php
    $_title = ($mode == "add") ? $entityAdmin->name." | Ajout" : $element." | Modification";
?>

<?php ob_start(); ?>
<style>
    main{
        padding:0 30px;
    }
    #element-title{
        margin:0;
        border-bottom:1px solid black;
        padding:5px 0;
    }

    form .form-group{
        display:flex;
        padding:10px 0;
        border-bottom:1px solid lightgray;
        padding-left:20px;
    }
    form .input-div *{
        margin-bottom:2px;
    }
    form .input-div *:not(:first-child) {
        margin-top:2px;
    }
    form label{
        display:inline-block;width:150px;vertical-align: top;
    }
    form select, form input[type="text"], form input[type="password"], form input[type="number"], form textarea{
        height: 30px;
        font-family:calibri;
        width:500px;
    }
    form textarea{
        min-height: 100px;
    }

    form select:hover, form input[type="text"]:hover, form input[type="password"]:hover, form input[type="number"]:hover, form textarea:hover,
    form select:focus, form input[type="text"]:focus, form input[type="password"]:focus, form input[type="number"]:focus, form textarea:focus{
        color:var(--primary-color);
    }

    form #buttons{
        display:flex;
        justify-content: flex-end;
        margin-top:10px;
        align-items:center;
    }
    form #buttons *{
        margin:0 3px;
    }
    .form-field-error{
        display:block;
        color:red;
        font-size:.85em;
        width:auto;
    }
</style>
<?php $_styles = ob_get_clean(); ?>

<?php ob_start(); ?>
    <h3 id="element-title"><?= ($mode == "add") ? "Ajout d'un élément" : $element ?></h3> 
    <?php if($msg != ""){ ?> 
        <div class="alert"><?= $msg ?></div>
    <?php }?>
    <form method="post" enctype="multipart/form-data">
        <div>
            <?= $form->createHTML() ?>
        </div>
        <div id="buttons">
            
            <?php if($mode == "add") {
                echo "<input name='continueAdd' id='continueAdd' type='checkbox'".(($continueAdd) ? "checked":"")."/><label for='continueAdd'>Continuer l'ajout</label>";
                } 
            ?>
            <?php if($adminGroupPermission->canUpdate) { ?>
                <input class="btn" value="<?= ($mode == "add") ? "Enregistrer" : "Modifier" ?>" type="submit"/>
            <?php } ?>
            <?php if($mode != "add" && $adminGroupPermission->canDelete) { ?>
                <a class="btn btn-bad" href="<?= $url("admin-entity-delete", ["entityName" => $entityAdmin->name, "key" => $element->getId()]) ?>">Supprimer</a>
            <?php } ?>
        </div>
    </form>
<?php $_main = ob_get_clean(); ?>


<?php require $view("admin/base.php"); ?>