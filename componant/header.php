<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <title>Inventaire</title>
</head>

<body>
    <header>
        <div class="logo">
            <a href="liste.php">
                <img src="img/logo.jpg" alt="logo M2i" class="logo">
            </a>
        </div>

        <nav>
            <?php
            if (isset($_SESSION["agence"])) {
            ?>
                <a href="add_computer.php" class="ajout">AJOUT PC</a>
                <a href="add_componant.php" class="ajout">AJOUT COMPOSANT</a>
                <a href="add_peripherique.php" class="ajout">AJOUT PERIPHERIQUE</a>
                <a href="deconnexion.php" class="ajout">DECONNEXION</a>
            <?php
            }
            ?>
        </nav>
    </header>