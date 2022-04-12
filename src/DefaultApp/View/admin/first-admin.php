
<?php $_title = "Premier administrateur"; ?>
<?php ob_start(); ?>
    <h2>Premier administrateur</h2>
    <div>
        <label for="username">Nom d'utilisateur</label>
        <input type="text" name="username" id="username"/>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password"/>
        <label for="confirm-password">Confirmer Mot de passe</label>
        <input type="password" name="confirm-password" id="confirm-password"/>
    </div>
    <span class="error"><?= $error ?? "" ?></span>
    <input type="submit"value="Créer"/>
<?php $_form = ob_get_clean(); ?>

<?php require $view("admin/login-first-admin-base.php"); ?>