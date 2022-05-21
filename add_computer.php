<?php
session_start();
if (!isset($_SESSION["agence"])) { // vérification de la connexion
    header("Location: index.php");
}
if (!empty($_POST)) {
    include("componant/config.php");


    $code = $_POST["codeBar"];
    $ram = $_POST["ram"];
    $processor = $_POST["processor"];
    $modele = $_POST["modele"];
    $nom = trim(strip_tags($_POST["produit"]));
    $dispo = $_POST["disponible"];
    $hardDrive = trim(strip_tags($_POST["hardDrive"]));
    $formation = trim(strip_tags($_POST["formation"]));



    $errors = [];

    if (empty($nom)) {
        $errors["nom"] = "Le nom est obligatoire";
    }
    if (empty($code)) {
        $errors["code"] = "Au moins un tag doit etre renseigner est obligatoire";
    }
    if (empty($hardDrive)) {
        $errors["disqueDur"] = "Les informations sur le disque dur doivent etre remplie";
    }
    if (count(array_unique($code)) < count($code)) {
        $errors["codeDouble"] = "Il y a des codes barres en double";
    }
    foreach ($code as $key => $value) {
        if (empty($value)) {
            $errors["code"] = "Tout les codes barres doivent être renseigner";
        } else {
            $tag = trim(strip_tags($value));
            $query = $db->prepare("SELECT * FROM materiel WHERE code_bar = :code_bar");
            $query->bindParam(":code_bar", $tag);
            $query->execute();
            $code_verif = $query->fetch();
            if ($code_verif) {
                $errors["codePris"] = "le tag existe déjà";
            }
        }
    }

    if (empty($errors)) {


        foreach ($code as $key => $value) {
            $tag = trim(strip_tags($value));


            if ($tag != "") {
                $query = $db->prepare("INSERT INTO materiel (nom, code_bar, lieu, nombre_total, disponible, id_agence_origine, id_agence_actuelle)
                VALUES (:nom, :code_bar, :lieu, 1, :disponible, :id_agence_origine, :id_agence_actuelle)");
                $query->bindParam(":nom", $nom);
                $query->bindParam(":code_bar", $tag);
                $query->bindParam(":lieu", $formation);
                $query->bindParam(":disponible", $dispo[$key]);
                $query->bindParam(":id_agence_origine", $_SESSION["agence"]);
                $query->bindParam(":id_agence_actuelle", $_SESSION["agence"]);

                $query->execute();
                
                $id = $db->lastInsertId();

                $query = $db->prepare("UPDATE materiel SET id_origine = :id WHERE id LIKE :id");
                $query->bindParam(":id", $id);
              
                $query->execute();

                $query = $db->prepare("INSERT INTO ordinateur (id_mat, modele, ram, processeur, disque_dur)
                VALUES (:id_mat, :modele, :ram, :processeur, :disque_dur)");
                $query->bindParam(":id_mat", $id);
                $query->bindParam(":modele", $modele);
                $query->bindParam(":ram", $ram);
                $query->bindParam(":processeur", $processor);
                $query->bindParam(":disque_dur", $hardDrive);

                $query->execute();
            }
        }

        header("Location:liste.php");
    }
}

include("componant/header.php");
?>



<h1>Ajout ordinateur</h1>

<form action="" method="post">
    <div class="form-group">
        <label for="inputProduit"> Nom matériel: </label>
        <input type="text" id="inputProduit" name="produit" value="<?= isset($nom) ? $nom : "" ?>">
    </div>
    <?php
    if (isset($errors["nom"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["nom"] ?></div>
        </div>
    <?php
    }
    ?>


    <div class="form-group">
        <label for="inputModele"> Type de poste: </label>
        <select id="inputModele" name="modele">
            <option value="Tour" <?= isset($modele) ? (($modele === "Tour") ? "selected='selected'" : "") : "" ?>>Tour</option>
            <option value="Mac" <?= isset($modele) ? (($modele === "Audio / Video") ? "selected='selected'" : "") : "" ?>>Mac</option>
            <option value="Portable" <?= isset($modele) ? (($modele === "Connectiques") ? "selected='selected'" : "") : "" ?>>Portable</option>
        </select>
    </div>

    <div class="form-group">
        <label for="inputRam"> Ram: </label>
        <select id="inputRam" name="ram">
            <option value="8Mo" <?= isset($ram) ? (($ram === "Tour") ? "selected='selected'" : "") : "" ?>>8G0</option>
            <option value="16Mo" <?= isset($ram) ? (($ram === "Audio / Video") ? "selected='selected'" : "") : "" ?>>16Go</option>
            <option value="32Mo" <?= isset($ram) ? (($ram === "Connectiques") ? "selected='selected'" : "") : "" ?>>32Go</option>
        </select>
    </div>

    <div class="form-group">
        <label for="inputProcessor"> Processeur: </label>
        <select id="inputProcessor" name="processor">
            <option value="I3" <?= isset($processor) ? (($processor === "Tour") ? "selected='selected'" : "") : "" ?>>I3</option>
            <option value="I5" <?= isset($processor) ? (($processor === "Audio / Video") ? "selected='selected'" : "") : "" ?>>I5</option>
            <option value="I7" <?= isset($processor) ? (($processor === "Connectiques") ? "selected='selected'" : "") : "" ?>>I7</option>
        </select>
    </div>

    <div class="form-group">
        <label for="inputHardDrive"> Disque dur: </label>
        <input type="text" id="inputHardDrive" name="hardDrive" value="<?= isset($hardDrive) ? $hardDrive : "" ?>">
    </div>


    <?php
    if (isset($errors["disqueDur"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["disqueDur"] ?></div>
        </div>
    <?php
    }
    ?>

    <div class="form-group">
        <label for="inputFormation"> Emplacement/Formation: </label>
        <input type="text" id="inputFormation" name="formation" value="<?= isset($formation) ? $formation : "" ?>">
    </div>



    <?php
    if (isset($errors["code"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["code"] ?></div>
        </div>
    <?php
    }
    if (isset($errors["codePris"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["codePris"] ?></div>
        </div>
    <?php
    }
    if (isset($errors["codeDouble"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["codeDouble"] ?></div>
        </div>
    <?php
    }
    ?>
    <div id="tag_list">
        <div class="form-group">
            <label for="inputCodeBar">Code bar/Disponible:</label>
            <input type="text" id="inputCodeBar" name="codeBar[1]">
            <input type="hidden" id="inputDisponible" value="0" name="disponible[1]" />
            <input type="checkbox" id="inputDisponible" value="1" name="disponible[1]" />
            <input type="button" value="ajout" id="ajout" class="button-form" value="1" />
        </div>
    </div>

    <div class="form-group">
        <input type="submit" value="Ajout" class="boutton-ajout">
    </div>


</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="js/number_computer.js"> </script>
</body>

</html>