
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

    form span{
        display:block;
        padding:10px 0;
        border-bottom:1px solid lightgray;
        padding-left:20px;
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
</style>
<?php $_styles = ob_get_clean(); ?>

<?php ob_start(); ?>
    <h3 id="element-title"><?= ($mode == "add") ? "Ajout d'un élément" : $element ?></h3> 
    <?php if($msg != ""){ ?> 
        <div class="alert"><?= $msg ?></div>
    <?php }?>
    <form method="post" enctype="multipart/form-data">
        <?php 
            $readonly = "";
            if(!$adminGroupPermission->canUpdate) 
                $readonly = " readonly"
        ?>
        <div>
            <?php foreach($fields as $field){
                $fieldName = $field["name"];
                $type = $field["type"];
                $value = $element->$fieldName ?? ""; ?>
            <span>
                <label><?= $field["label"] ?> : </label>
                <?php 
                    if($fieldName == "id"){
                        echo "<strong>$value</strong>";
                    }else{
                        switch($type){
                            case "integer": echo "<input type='number' name='$fieldName' value='$value' $readonly/>";break;
                            case "NumberField": echo "<input type='number' name='$fieldName' value='$value' $readonly/>";break;
                            case "PasswordField": echo "<input type='password' name='$fieldName' $readonly/>";break;
                            case "text": echo "<textarea name='$fieldName' $readonly>$value</textarea>";break;
                            case "FileField": 
                                if($readonly == "")
                                    echo "<input type='file' name='$fieldName'/><br>";
                                echo "<i>Actuel : $value</i>";
                            break;
                            case "RelationField": 
                                echo "<select name='$fieldName' $readonly>";
                                echo "<option value=''>Sélectionnez un élément</option>";
                                foreach($field["customAnnotation"]->getElements() as $optionElement){
                                    echo "<option value='".$optionElement->id."' ";
                                    if(is_object($value) and $value->getId() == $optionElement->id)
                                        echo "selected";
                                    echo ">$optionElement</option>";
                                }
                                echo "</select>";
                            break;
                            case "EnumField": 
                                echo "<select name='$fieldName' $readonly>";
                                echo "<option value=''>Sélectionnez un élément</option>";
                                foreach($field["customAnnotation"]->getElements() as $optionElement){
                                    echo "<option value='$optionElement' ";
                                    if($value == $optionElement)
                                        echo "selected";
                                    echo ">$optionElement</option>";
                                }
                                echo "</select>";
                            break;
                            case "boolean": echo "<input type='checkbox' name='$fieldName'".(($value==true) ? "checked":"")."/>";break;
                            default: echo "<input type='text' name='$fieldName' value='$value' $readonly/>";break;
                        }
                    }
                ?>
            </span>
            <?php } ?>
        </div>
        <div id="buttons">
            
            <?php if($mode == "add") {
                echo "<input name='continueAdd' type='checkbox'".(($continueAdd) ? "checked":"")."/><label>Continuer l'ajout</label>";
                } 
            ?>
            <?php if($adminGroupPermission->canUpdate) { ?>
                <input class="btn" value="<?= ($mode == "add") ? "Enregistrer" : "Modifier" ?>" type="submit"/>
            <?php } ?>
            <?php if($mode != "add" && $adminGroupPermission->canDelete) { ?>
                <a class="btn btn-bad" href="<?= $url("admin-entity-delete", ["entityName" => $entityAdmin->name, "id" => $element->getId()]) ?>">Supprimer</a>
            <?php } ?>
        </div>
    </form>
<?php $_main = ob_get_clean(); ?>


<?php require $view("admin/base.php"); ?>