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
    $peri = trim(strip_tags($_POST["peri"]));
    $detail = trim(strip_tags($_POST["detail"]));




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

        $query = $db->prepare("INSERT INTO peripheriques (type_catalogue, type_peripherique, detail_peripheriques, id_mat)
        VALUES (:type_catalogue, :type_peripherique, :detail_peripheriques, :id_mat)");
        $query->bindParam(":id_mat", $id);
        $query->bindParam(":type_catalogue", $peri);
        $query->bindParam(":type_peripherique", $nom);
        $query->bindParam(":detail_peripheriques", $detail);


        $query->execute();

        header("Location:liste.php");
    }
}
include("componant/header.php");
?>


<h1>Ajout produit</h1>
<?php
if (isset($errors["infoManquante"])) {
?>
    <span class="info-error"><?= $errors["infoManquante"] ?></span>
<?php
}
?>
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
    }
    ?>

    <div class="form-group">
        <label for="inputProduit"> Nom Produit: </label>
        <input type="text" id="inputProduit" name="produit" value="<?= isset($nom) ? $nom : "" ?>">
    </div>

    <div class="form-group">
        <label for="inputNombre"> Nombre de produit: </label>
        <input type="number" id="inputNombre" name="nombre" value="<?= isset($nombre) ? $nombre : "" ?>" />
    </div>

    <div class="form-group">
        <label for="inputDisponible"> Disponible: </label>
        <input id="inputDisponible" name="disponible" value="<?= isset($dispo) ? $dispo : "" ?>" />
    </div>

    <div class="form-group">
        <label for="inputPeri"> Type de périphérique: </label>

        <select id="inputPeri" name="peri">
            <option value="Equipement bureau" <?= isset($peri) ? (($peri === "Equipement bureau") ? "selected='selected'" : "") : "" ?>>Equipement bureau</option>
            <option value="Audio / Video" <?= isset($peri) ? (($peri === "Audio / Video") ? "selected='selected'" : "") : "" ?>>Audio / Video</option>
            <option value="Connectiques" <?= isset($peri) ? (($peri === "Connectiques") ? "selected='selected'" : "") : "" ?>>Connectiques</option>
            <option value="Equipements Reseaux" <?= isset($peri) ? (($peri === "Equipements Reseaux") ? "selected='selected'" : "") : "" ?>>Equipements Reseaux</option>
        </select>
    </div>

    <div class="form-group">
        <label for="inputDetail"> Detail: </label>
        <input type="text" id="inputDetail" name="detail" value="<?= isset($detail) ? $detail  : "" ?>">
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
<script src="node_modules/jquery/dist/jquery.min.js"></script>
</body>

</html>