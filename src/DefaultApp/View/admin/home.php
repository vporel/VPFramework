
<?php
    $_title = "Accueil";
?>

<?php ob_start(); ?>
    <style>
        aside{width:100%;}
        main{width:0%}
    </style>
<?php $_styles = ob_get_clean(); ?>


<?php require $view("admin/base.php"); ?>