<?php
if (isset($_GET["token"])) {
    $token = trim(strip_tags($_GET["token"]));

    include("componant/config.php");

    $query = $db->prepare("SELECT email, timer FROM password_reset WHERE token LIKE :token");
    $query->bindParam(":token", $token);
    $query->execute();
    $result = $query->fetch();

    if (empty($result) || $result["timer"] < time()) {

        header("Location: ./");
    }

    if (isset($_POST["password"])) {

        $password = trim(strip_tags($_POST["password"]));

        $password = password_hash($password, PASSWORD_DEFAULT);


        $query = $db->prepare("UPDATE agence SET password = :password WHERE email LIKE :email");
        $query->bindParam(":password", $password);
        $query->bindParam(":email", $result["email"]);

        if ($query->execute()) {
            header("Location: ./index.php");
        }
    }
} else {

    header("Location: ./");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau mot de passe</title>
</head>

<body>
    <h1>Nouveau mot de passe</h1>

    <form action="" method="post">
        <div class="form-group">
            <label for="inputPassword">Nouveau mot de passe :</label>
            <input type="password" name="password" id="inputPassword">
        </div>

        <input type="submit" value="Envoyer">
    </form>
</body>

</html>