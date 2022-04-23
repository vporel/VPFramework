
<?php
    $_title = $entityAdmin->name." | Liste";
?>

<?php ob_start(); ?>
    <style>
        #list{
            width:100%;
        }
        #list th{
            background:rgb(200,200,200);
            border-bottom:1px solid rgb(170,170,170);
            text-align:left;
            text-transform: uppercase;
        }
        #list .action{
            display:inline-block;
            background:var(--secondary-color);
            color:white;
            padding:5px;
            margin:2px;
        }
    </style>
<?php $_styles = ob_get_clean(); ?>

<?php ob_start(); ?>
    <table id="list" cellspacing="0" cellpadding="3">
        <thead>
            <tr><!-- Top -->
                <tr colspan="<?= count($fields)+2 ?>">
                    <button id="deleteSelectionBtn" class="btn btn-disable cursor-pointer" style="margin: 3px 0">Supprimer la selection</button>
                </tr>
            </tr>
            <tr>
                <th></th><!-- check case -->
                <?php foreach($fields as $field){ ?>
                    <th><?= $field["label"] ?></th>
                <?php } ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($elements as $element){ ?>
                <tr>
                    <td><input type="checkbox" class="line-checkbox" data-id="<?= $element->id ?>"/></td>
                    <?php foreach($fields as $field){ $fieldName = $field["name"]; ?>
                        <td><?= $element->$fieldName ?></td>
                    <?php } ?>
                    
                    <td>
                        <a href="<?= $url("admin-entity-update", ["entityName" => $entityAdmin->name, "id" => $element->getId()]) ?>" class="action">Afficher</a>
                        <?php if($adminGroupPermission->canDelete){ ?>
                            <a href="<?= $url("admin-entity-delete", ["entityName" => $entityAdmin->name, "id" => $element->getId()]) ?>" class="action">Supprimer</a>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php $_main = ob_get_clean(); ?>

<?php ob_start(); ?>
    <script type="text/javascript">
        <?php include ASSETS."/js/jquery.js"; ?>
    </script>
    <script type="text/javascript">
        let nbCheckedLines = 0;
        $('.line-checkbox').each(function(){
            $(this).click(function(){
                if($(this).prop("checked")){
                    nbCheckedLines++;
                }else{
                    nbCheckedLines--;
                }
                if(nbCheckedLines <= 0){
                    $("#deleteSelectionBtn").removeClass("btn-secondary").addClass("btn-disable")
                }else{
                    $("#deleteSelectionBtn").addClass("btn-secondary").removeClass("btn-disable")
                }
            });
        });
        $("#deleteSelectionBtn").click(function(){
            if(nbCheckedLines <= 0){
                alert("Aucune ligne sélectionnée");
            }else{
                if(confirm("Les lignes sélectionnées vont être supprimées")){
                    let selectedIds = [];
                    $('.line-checkbox').each(function(){
                        if($(this).prop("checked")){
                            selectedIds.push($(this).attr("data-id"));
                        }
                    });
                    window.location="<?= $url("admin-entity-delete-many", ["entityName" => $entityAdmin->name]) ?>?ids="+selectedIds.join("-");
            
                }    
            }
        });
    </script>
<?php $_scripts = ob_get_clean(); ?>


<?php require $view("admin/base.php"); ?>