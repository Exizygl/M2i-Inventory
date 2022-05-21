<?php
session_start();
if (!isset($_SESSION["agence"])) { // vérification de la connexion
    header("Location: index.php");
}
if (!empty($_POST)) {
    include("componant/config.php");
    $code = trim(strip_tags($_POST["codeBar"]));
    $nom = trim(strip_tags($_POST["produit"]));
    $nombre = trim(strip_tags($_POST["nombre"]));
    $dispo = trim(strip_tags($_POST["disponible"]));
    $detail = trim(strip_tags($_POST["detail"]));
    $formation = trim(strip_tags($_POST["formation"]));




    $errors = [];

    if (empty($nom)) {
        $errors["nom"] = "Le nom est obligatoire";
    }

    if (empty($nombre)) {
        $errors["nombre"] = "un nombre total est obligatoire";
    }

    if (empty($dispo)) {
        $dispo = 0;
    }


    if ($nombre < $dispo) {
        $errors["compte"] = "il a plus de composant disponible que de  qu'il y en a au total";
    }

    if (!empty($code)) {
        $query = $db->prepare("SELECT * FROM materiel WHERE code_bar = :code_bar");
        $query->bindParam(":code_bar", $value);
        $query->execute();
        $code_verif = $query->fetch();
        if ($code_verif) {
            $errors["codePris"] = "le tag existe déjà";
        }
    }

    if (empty($errors)) {


        $query = $db->prepare("INSERT INTO materiel (nom, code_bar, lieu, nombre_total, disponible, id_agence_origine, id_agence_actuelle)
            VALUES (:nom, :code_bar, :lieu, :nombre_total, :disponible, :id_agence_origine, :id_agence_actuelle)");
        $query->bindParam(":nom", $nom);
        $query->bindParam(":code_bar", $tag);
        $query->bindParam(":lieu", $formation);
        $query->bindParam(":nombre_total", $nombre);
        $query->bindParam(":disponible", $dispo);
        $query->bindParam(":id_agence_origine", $_SESSION["agence"]);
        $query->bindParam(":id_agence_actuelle", $_SESSION["agence"]);


        $query->execute();
        $id = $db->lastInsertId();

        
        $query = $db->prepare("UPDATE materiel SET id_origine = :id WHERE id LIKE :id");
        $query->bindParam(":id", $id);
        $query->execute();


        $query = $db->prepare("INSERT INTO composants (type_composant, detail_composant, id_mat)
        VALUES (:type_composant, :detail_composant, :id_mat)");
        $query->bindParam(":id_mat", $id);
        $query->bindParam(":type_composant", $nom);
        $query->bindParam(":detail_composant", $detail);


        $query->execute();

        header("Location:liste.php");
    }
}
include("componant/header.php");
?>


<h1>Ajout composant</h1>

<form action="" method="post">
    <div class="form-group">
        <label for="inputCodeBar"> Code bar: </label>
        <input type="text" id="inputCodeBar" name="codeBar" value="<?= isset($code) ? $code : "" ?>">
    </div>
    <?php
    if (isset($errors["codePris"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["codePris"] ?></div>
        </div>
    <?php
    } ?>

    <div class="form-group">
        <label for="inputProduit"> Nom Produit: </label>
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
        <label for="inputNombre"> Nombre de produit: </label>
        <input id="inputNombre" name="nombre" min="1" max="20" value="<?= isset($nombre) ? $nombre : "" ?>" />
    </div>

    <?php
    if (isset($errors["nombre"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["nombre"] ?></div>
        </div>
    <?php
    }
    ?>

    <?php
    if (isset($errors["compte"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["compte"] ?></div>
        </div>
    <?php
    }
    ?>

    <div class="form-group">
        <label for="inputDisponible"> Disponible: </label>
        <input id="inputDisponible" name="disponible" value="<?= isset($dispo) ? $dispo : "" ?>" />
    </div>

    <?php
    if (isset($errors["disponible"])) {
    ?>
        <div class="form-group">
            <div class="info-erreur"><?= $errors["disponible"] ?></div>
        </div>
    <?php
    }
    ?>

    <div class="form-group">
        <label for="inputDetail"> Detail: </label>
        <input type="text" id="inputDetail" name="detail" value="<?= isset($detail) ? $detail : "" ?>">
    </div>
    <div class="form-group">
        <label for="inputFormation"> Emplacement/Formation: </label>
        <textarea type="text" id="inputFormation" name="formation">
        <?= isset($formation) ? $formation : "" ?>
        </textarea>
    </div>
    <div class="form-group">
        <input type="submit" value="Ajout" class="boutton-ajout">
    </div>
</form>
</body>

</html>