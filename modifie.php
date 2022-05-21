<?php
session_start();
include("componant/config.php");
include("componant/fonction.php");
if (!isset($_SESSION["agence"])) { // vérification de la connexion
    header("Location: index.php");
}
if (isset($_GET["id"])) {
    $id = $_GET["id"];
} else {
    $id = $_POST["id"];
}

$type = checkCategories($id);


if ($type == "ordinateur") {

    if (!empty($_POST)) {

        $nom = trim(strip_tags($_POST["produit"]));
        $nombre = trim(strip_tags($_POST["nombre"]));
        $dispo = trim(strip_tags($_POST["disponible"]));
        $modele = trim(strip_tags($_POST["modele"]));
        $ram = trim(strip_tags($_POST["ram"]));
        $processor = trim(strip_tags($_POST["processor"]));
        $hardDrive = trim(strip_tags($_POST["hardDrive"]));
        $formation = trim(strip_tags($_POST["formation"]));

        $errors = [];


        if (empty($nom)) {
            $errors["nom"] = "Le nom est obligatoire";
        }
        if (empty($hardDrive)) {
            $errors["disqueDur"] = "Les infos sur le disque dur doivent etre remplie";
        }

        if (empty($errors)) {


            $query = $db->prepare("UPDATE materiel SET
            nom = :nom, nombre_total = :nombre_total, lieu = :lieu, disponible = :disponible WHERE id LIKE :id");
            $query->bindParam(":id", $id);
            $query->bindParam(":nom", $nom);
            $query->bindParam(":lieu", $formation);
            $query->bindParam(":disponible", $dispo);

            $query->execute();

            $query = $db->prepare("UPDATE ordinateur SET
            modele = :modele, ram = :ram, processeur = :processeur, disque_dur = :disque_dur WHERE id_mat LIKE :id_mat");
            $query->bindParam(":id_mat", $id);
            $query->bindParam(":modele", $modele);
            $query->bindParam(":ram", $ram);
            $query->bindParam(":processeur", $processor);
            $query->bindParam(":disque_dur", $hardDrive);

            $query->execute();

            header("Location:liste.php");
        }
    } else {

        $query = $db->prepare("SELECT * from materiel 
            INNER JOIN ordinateur on materiel.id = ordinateur.id_mat WHERE materiel.id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        $item = $query->fetch();

        $code = $item["code_bar"];
        $nom = $item["nom"];
        $nombre = $item["nombre_total"];
        $dispo = $item["disponible"];
        $modele = $item["modele"];
        $ram = $item["ram"];
        $processor = $item["processeur"];
        $hardDrive = $item["disque_dur"];
    }
}
if ($type == "composant") {

    if (!empty($_POST)) {

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
        if (empty($errors)) {


            $query = $db->prepare("UPDATE materiel SET
            nom = :nom, lieu = :lieu, nombre_total = :nombre_total, disponible = :disponible WHERE id LIKE :id");

            $query->bindParam(":nom", $nom);
            $query->bindParam(":id", $id);
            $query->bindParam(":lieu", $formation);
            $query->bindParam(":nombre_total", $nombre);
            $query->bindParam(":disponible", $dispo);

            $query->execute();

            $query = $db->prepare("UPDATE materiel SET
            nom = :nom, code_bar = :code_bar, nombre_total = :nombre_total, disponible = :disponible WHERE id LIKE :id");
            $query->bindParam(":id", $id);
            $query->bindParam(":nom", $nom);
            $query->bindParam(":code_bar", $code);
            $query->bindParam(":nombre_total", $nombre);
            $query->bindParam(":disponible", $dispo);

            $query->execute();

            $query = $db->prepare("UPDATE composants SET
            type_composant = :type_composant, detail_composant = :detail_composant WHERE id_mat LIKE :id_mat");
            $query->bindParam(":id_mat", $id);
            $query->bindParam(":type_composant", $nom);
            $query->bindParam(":detail_composant", $detail);

            $query->execute();

            header("Location:liste.php");
        }
    } else {

        $query = $db->prepare("SELECT * from materiel 
            INNER JOIN composants on materiel.id = composants.id_mat WHERE materiel.id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        $item = $query->fetch();

        $code = $item["code_bar"];
        $nom = $item["nom"];
        $nombre = $item["nombre_total"];
        $dispo = $item["disponible"];
        $comp = $item["type_composant"];
        $detail = $item["detail_composant"];
    }
}
if ($type == "peripherique") {
    

    if (!empty($_POST)) {
        
        $nom = trim(strip_tags($_POST["produit"]));
        $nombre = trim(strip_tags($_POST["nombre"]));
        $dispo = trim(strip_tags($_POST["disponible"]));
        $peri = trim(strip_tags($_POST["peri"]));
        $objet = trim(strip_tags($_POST["produit"]));
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
        var_dump($errors);
        if (empty($errors)) {


            $query = $db->prepare("UPDATE materiel SET
            nom = :nom, lieu = :lieu, nombre_total = :nombre_total, disponible = :disponible WHERE id LIKE :id");

            $query->bindParam(":nom", $nom);
            $query->bindParam(":id", $id);
            $query->bindParam(":lieu", $formation);
            $query->bindParam(":nombre_total", $nombre);
            $query->bindParam(":disponible", $dispo);



            $query->execute();

            $query = $db->prepare("UPDATE peripheriques SET
            type_catalogue = :type_catalogue, detail_peripheriques = :detail_peripheriques, type_peripherique = :type_peripherique WHERE id_mat LIKE :id_mat");
            $query->bindParam(":id_mat", $id);
            $query->bindParam(":type_peripherique", $name);
            $query->bindParam(":type_catalogue", $peri);
            $query->bindParam(":detail_peripheriques", $detail);

            $query->execute();

            header("Location:liste.php");
        }
    } else {


        $query = $db->prepare("SELECT * from materiel 
            INNER JOIN peripheriques on materiel.id = peripheriques.id_mat WHERE materiel.id = :id_mat");
        $query->bindParam(":id_mat", $id);
        $query->execute();
        $item = $query->fetch();

        $code = $item["code_bar"];
        $nom = $item["nom"];
        $nombre = $item["nombre_total"];
        $dispo = $item["disponible"];
        $objet = $item["type_peripherique"];
        $detail = $item["detail_peripheriques"];
    }
}
if ($_SESSION["agence"] != $item["id_agence_actuelle"]) { // vérification que l'utilsateur a le droit de modifier l'objet
    header("Location: liste.php");
}

include("componant/header.php");
?>

<h1>Modification</h1>


<form action="" method="post">


    <input type="hidden" id="id" name="id" value=<?= isset($id) ? $id : "" ?>>


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


    <?php
    if ($type === "ordinateur") {
    ?>
        <div class="form-group">
            <label for="inputDiponible">Code bar/Disponible:</label>
            <input type="hidden" id="inputDisponible" value="0" name="disponible[1]" />
            <input type="checkbox" id="inputDisponible" value="1" name="disponible[1]" <?= isset($dispo) ? "checked" : "" ?> />
            <input type="button" value="ajout" id="ajout" class="button-form" value="1" />
        </div>
        <div class="form-group">
            <label for="inputModele"> Modèle: </label>
            <input type="text" id="inputModele" name="modele" value="<?= isset($modele) ? $modele : "" ?>">
        </div>

        <div class="form-group">
            <label for="inputRam"> Ram: </label>
            <input type="text" id="inputRam" name="ram" value="<?= isset($ram) ? $ram : "" ?>">
        </div>

        <div class="form-group">
            <label for="inputProcessor"> Processeur: </label>
            <input type="text" id="inputProcessor" name="processor" value="<?= isset($processor) ? $processor : "" ?>">
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
    }
    ?>

    <?php
    if ($type === "composant") {
    ?>

        <div class="form-group">
            <label for="inputNombre"> Nombre de produit: </label>
            <input id="inputNombre" name="nombre" value="<?= isset($nombre) ? $nombre : "" ?>" />
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
    <?php
    }
    ?>

    <?php
    if ($type === "peripherique") {
    ?>
        <div class="form-group">
            <label for="inputNombre"> Nombre de produit: </label>
            <input id="inputNombre" name="nombre" value="<?= isset($nombre) ? $nombre : "" ?>" />
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
    <?php
    }
    ?>
    <div class="form-group">
        <input type="submit" value="Modification" class="boutton-ajout">
    </div>
</form>
</form>
</body>

</html>