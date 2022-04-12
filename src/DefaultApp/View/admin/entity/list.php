
<?php
    $_title = $entityAdmin." | Liste";
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
        }
    </style>
<?php $_styles = ob_get_clean(); ?>

<?php ob_start(); ?>
    <table id="list" cellspacing="0" cellpadding="3">
        <thead>
            <tr>
                <th></th><!-- check case -->
                <?php foreach($fields as $field){ ?>
                    <th><?= $field ?></th>
                <?php } ?>
                <th>Actions</th>
            </tr>
            <?php foreach($elements as $element){ ?>
                <tr>
                    <td><input type="checkbox"/></td>
                    <?php foreach($fields as $field){ $get = "get".ucfirst($field); ?>
                        <td><?= $element->$get() ?></td>
                    <?php } ?>
                    
                    <td>
                        <a href="<?= $url("admin-entity-update", ["entityName" => $entityAdmin, "id" => $element->getId()]) ?>" class="action">Modifier</a>
                        <a href="<?= $url("admin-entity-delete", ["entityName" => $entityAdmin, "id" => $element->getId()]) ?>" class="action">Supprimer</a>
                    </td>
                </tr>
            <?php } ?>
        </thead>
    </table>
<?php $_main = ob_get_clean(); ?>


<?php require $view("admin/base.php"); ?>