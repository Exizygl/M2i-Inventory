<?php
if (!empty($_POST)) {
    $agenceName = trim(strip_tags($_POST["agence"]));
    $email = trim(strip_tags($_POST["email"]));
    $password = trim(strip_tags($_POST["password"]));
    $retypePassword = trim(strip_tags($_POST["retypePassword"]));

    $errors = [];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "l'email n'est pas valide";
    }
    if ($password != $retypePassword) {
        $errors["retypePassword"] = "les mots de passe doivent etre identique";
    }

    $uppercase = preg_match("/[A-Z]/", $password);
    $lowercase = preg_match("/[a-z]/", $password);
    $number = preg_match("/[0-9]/", $password);
    $havespace = preg_match("/ /", $password);
    $specialCharacter = preg_match("/[^a-zA-Z0-9]/", $password);

    if (strlen($password) < 12 || !$uppercase || !$lowercase || !$number || $havespace || !$specialCharacter) {
        $errors["password"] = "le mots de passe doit avoir au moins 12 characters, une maj, une minucule, un nombre et un charctère spécial";
    }

    if (empty($errors)) {
        include("componant/config.php");

        $query = $db->prepare("SELECT * FROM agence WHERE email = :email");
        $query->bindParam(":email", $email);
        $query->execute();
        $emailCheck = $query->fetch();

        if ($emailCheck) {
            $errors["email"] = "Cette email est déjà pris";
        } else {

            $password = password_hash($password, PASSWORD_DEFAULT);

            $query = $db->prepare("INSERT INTO agence (nom_agence, email, password)
            VALUES (:nom, :email, :password)");
            $query->bindParam(":nom", $agenceName);
            $query->bindParam(":email", $email);
            $query->bindParam(":password", $password);

            $query->execute();

            header("Location:index.php");
        }
    }
}
include("componant/header.php");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Création d'un compte</h1>

    <form action="" method="post">
        <div class="form-group">
            <label for="inputAgence"> Nom agence: </label>
            <input type="text" id="inputAgence" name="agence" value="<?= isset($agenceName) ? $agenceName : "" ?>">
        </div>


        <div class="form-group">
            <label for="inputEmail"> Votre email : </label>
            <input type="email" id="inputEmail" name="email" value="<?= isset($email) ? $email : "" ?>">
            <?php
            if (isset($errors["email"])) {
            ?>
                <p><?= $errors["email"] ?></p>
            <?php
            }
            ?>
        </div>
        <div class="form-group">
            <label for="inputPassword"> Votre mot de passe: </label>
            <input type="password" id="inputPassword" name="password">
        </div>
        <?php
        if (isset($errors["password"])) {
        ?>
            <p><?= $errors["password"] ?></p>
        <?php
        }
        ?>
        <div class="form-group">
            <label for="inputRetypePassword"> Confirmation de mot de passe: </label>
            <input type="password" id="inputRetypePassword" name="retypePassword">
            <?php
            if (isset($errors["retypePassword"])) {
            ?>
                <p><?= $errors["retypePassword"] ?></p>
            <?php
            }
            ?>
        </div>
        <div class="form-group">
            <input type="submit" value="Creation du compte" class="boutton-ajout">
        </div>
    </form>
</body>

</html>