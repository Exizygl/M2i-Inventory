<?php

if (!empty($_POST)) {
    $email = trim(strip_tags($_POST["email"]));
    $password = trim(strip_tags($_POST["password"]));

    include("componant/config.php");
    $query = $db->prepare("SELECT * FROM agence WHERE email LIKE :email");
    $query->bindParam(":email", $email);
    $query->execute();
    $result = $query->fetch();

    if (!empty($result) && password_verify($password, $result["password"])) {

        session_start();

        $_SESSION["agence"] = $result["id_agence"];


        header("Location: liste.php");
    } else {
        $errors = "impossible de se connecter avec les infos saisie";
    }
}

include("componant/header.php");
?>

<h1>Connexion à l'espace utilisateur</h1>

<form action="" method="post">
    <div class="form-group">
        <label for="inputEmail">Email :</label>
        <input type="email" name="email" id="inputEmail">
    </div>
    <div class="form-group">
        <label for="inputEmail">Password :</label>
        <input type="password" name="password" id="inputPassword">
    </div>
    <div class="form-group">
        <input type="submit" value="Connexion" class="boutton-ajout">
    </div>
</form>
<div class="option">
    <a href="create_account.php"> Nouvelle agence</a>
</div>
<div class="option">
    <a href="forgotten_password.php"> Password oublié</a>
</div>
</body>

</html>