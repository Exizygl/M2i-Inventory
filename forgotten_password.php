<?php
require("./vendor/autoload.php");

use PHPMailer\PHPMailer\PHPMailer;


define("HOST", "http://localhost/inventory/");

if (isset($_POST["email"])) {
    $email = trim(strip_tags($_POST["email"]));
    include("componant/config.php");
    $query = $db->prepare("SELECT * FROM agence WHERE email LIKE :email");
    $query->bindParam(":email", $email);
    $query->execute();
    $result = $query->fetch();


    if ($result) {

        $token = bin2hex(random_bytes(50));
        $timer= time() + 3600;
        $query = $db->prepare("INSERT INTO password_reset (email, token, timer) VALUES (:email, :token, :timer)");
        $query->bindParam(":email", $email);
        $query->bindParam(":token", $token);
        $query->bindParam(":timer", $timer, PDO::PARAM_INT);

        if ($query->execute()) {
            $phpmailer = new PHPMailer();
            //on indique que l'on utilse le protocol SMTP
            $phpmailer->isSMTP();
            
            // $phpmailer->Host = 'smtp.gmail.com';
            // $phpmailer->SMTPAuth = true;
            // $phpmailer->SMTPSecure = 'tls';
            // $phpmailer->Port = 587;
            // $phpmailer->Username = 
            // $phpmailer->Password = 


            //Expéditeur
            // $phpmailer->From = 
            // $phpmailer->FromName = ;
            // $phpmailer->setFrom();


            $phpmailer->addAddress($email);
            $phpmailer->isHTML();



            $phpmailer->Subject = "Réinitialistation du mot de passe";

            $phpmailer->Body = "<a href=\"http://".HOST."/new_password.php?token={$token}\">Réinitialisation</a>";

            $phpmailer->CharSet = "UTF-8";
            if($phpmailer->send()){
                echo "Email envoyé.";

            }else{
                echo $phpmailer->ErrorInfo;
            };
        }
    }else{
        echo "Email existe pas.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>

<body>
 

    <form action="" method="post">
        <div class="form-group">
            <label for="inputEmail">Email :</label>
            <input type="email" name="email" id="inputEmail">
        </div>

        <input type="submit">
    </form>
</body>

</html>