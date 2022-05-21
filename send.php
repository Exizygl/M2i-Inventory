<?php
session_start();
if (!isset($_SESSION["agence"])) { // vérification de la connexion
    header("Location: index.php");
}
include("componant/config.php");
include("componant/fonction.php");
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}


$type = checkCategories($id);

if (!empty($_POST)) {

    $id = $_POST["id"];
    $dispo = $_POST["dispo"];
    $agenceSelect = trim(strip_tags($_POST["agence"]));
    $errors = [];
    if ($type === "ordinateur") {
        $tag = $_POST["tag"];

        foreach (array_count_values($tag) as $key => $data) {
            if ($data > 1) $errors["doublon"] = "Ils y a des tags en double";
        }
    } else {
        $nb = trim(strip_tags($_POST["send"]));

        if (empty($nb)) {
            $errors["envoieImposible"] = "il faut au envoyer au moins un article";
        }
        if ($dispo < $nb) {
            $errors["envoieImposible"] = "Manque de matériel disponible";
        }
    }



    if (empty($agenceSelect)) {
        $errors["envoieImposible"] = "Aucune destination selectionné";
    }


    if (empty($errors)) {

        if ($type === "ordinateur") {   //envoie des ordinateur

            foreach ($tag as $data) {

                $query = $db->prepare("UPDATE materiel SET id_agence_actuelle = :id_agence_actuelle, 
                disponible = 0 WHERE code_bar LIKE :code_bar");
                $query->bindParam(":code_bar", $data);
                $query->bindParam(":id_agence_actuelle", $agenceSelect);
                $query->execute();
            }
            header("Location:liste.php");
        }

        //mise a jour du matériel de l'envoyeur autre que les pc





        if ($type === "composant") {

            //mettre à jour les donnée d'origine
            $query = $db->prepare("UPDATE materiel SET nombre_total = nombre_total - :nb,  
            disponible = disponible - :nb  WHERE id = :id");
            $query->bindParam(":id", $id);
            $query->bindParam(":nb", $nb);
            $query->execute();


            $query = $db->prepare("SELECT * from materiel 
                    INNER JOIN composants on materiel.id = composants.id_mat WHERE materiel.id = :id");
            $query->bindParam(":id", $id);
            $query->execute();
            $item = $query->fetch();



            $query = $db->prepare("SELECT * from materiel WHERE id_origine = :id_origine
            and id_agence_actuelle =:id_agence_actuelle");

            $query->bindParam(":id_origine", $item['id_origine']);
            $query->bindParam(":id_agence_actuelle", $agenceSelect);
            $query->execute();
            $check = $query->fetch();



            if (!empty($check)) {
                $query = $db->prepare("UPDATE materiel SET nombre_total = nombre_total +:nb WHERE id LIKE :id");
                $query->bindParam(":id", $check['id']);
                $query->bindParam(":nb", $nb);

                $query->execute();
            } else {
                $query = $db->prepare("INSERT INTO materiel (nom, code_bar, nombre_total, disponible,
                                id_agence_origine, id_agence_actuelle, id_origine)
                            VALUES (:nom, :code_bar, :nombre_total, 0, :id_agence_origine, :id_agence_actuelle, :id_origine)");
                $query->bindParam(":nom", $item["nom"]);
                $query->bindParam(":code_bar", $item["code_bar"]);
                $query->bindParam(":nombre_total", $nb);
                $query->bindParam(":id_origine", $item["id_origine"]);

                $query->bindParam(":id_agence_origine", $item["id_agence_origine"]);
                $query->bindParam(":id_agence_actuelle", $agenceSelect);


                $query->execute();

                $idNewInsert = $db->lastInsertId();

                $type = checkCategories($id);

                $query = $db->prepare("SELECT * from composants WHERE id_mat = :id");
                $query->bindParam(":id", $id);
                $query->execute();
                $newItem = $query->fetch();

                $query = $db->prepare("INSERT INTO composants (type_composant, detail_composant, id_mat)
                            VALUES (:type_componant, :detail, :id_mat)");
                $query->bindParam(":id_mat", $idNewInsert);
                $query->bindParam(":type_componant", $newItem["type_composant"]);
                $query->bindParam(":detail", $newItem["detail_composant"]);



                $query->execute();
            }

            if ($item["nombre_total"] == 0) {
                $delete = "delete.php?id=".$item['id'];

                header("Location: $delete");
            }else{

            header("Location:liste.php");
            }
        }



        if ($type === "peripherique") {





            $query = $db->prepare("UPDATE materiel SET nombre_total = nombre_total - :nb,  disponible = disponible - :nb  WHERE id = :id");
            $query->bindParam(":id", $id);
            $query->bindParam(":nb", $nb);
            $query->execute();



            $query = $db->prepare("SELECT * from materiel 
                        INNER JOIN peripheriques on materiel.id = peripheriques.id_mat WHERE materiel.id = :id");
            $query->bindParam(":id", $id);
            $query->execute();
            $item = $query->fetch();


            $query = $db->prepare("SELECT * from materiel WHERE id_origine = :id_origine
                        and id_agence_actuelle =:id_agence_actuelle");

            $query->bindParam(":id_origine", $item['id_origine']);
            $query->bindParam(":id_agence_actuelle", $agenceSelect);
            $query->execute();
            $check = $query->fetch();

            if (!empty($check)) {

                $query = $db->prepare("UPDATE materiel SET nombre_total = nombre_total + :nb WHERE id LIKE :id");
                $query->bindParam(":id", $check['id']);
                $query->bindParam(":nb", $nb);

                $query->execute();
            } else {
                $query = $db->prepare("INSERT INTO materiel (nom, code_bar, nombre_total, disponible,
                                id_agence_origine, id_agence_actuelle, id_origine)
                            VALUES (:nom, :code_bar, :nombre_total, 0, :id_agence_origine, :id_agence_actuelle, :id_origine)");
                $query->bindParam(":nom", $item["nom"]);
                $query->bindParam(":code_bar", $item["code_bar"]);
                $query->bindParam(":nombre_total", $nb);
                $query->bindParam(":id_origine", $item["id_origine"]);

                $query->bindParam(":id_agence_origine", $item["id_agence_origine"]);
                $query->bindParam(":id_agence_actuelle", $agenceSelect);


               



                $query->execute();

                $idNewInsert = $db->lastInsertId();

                $type = checkCategories($id);
                $query = $db->prepare("SELECT * from peripheriques WHERE id_mat = :id");
                $query->bindParam(":id", $id);
                $query->execute();
                $newItem = $query->fetch();

                $query = $db->prepare("INSERT INTO peripheriques (id_mat, type_catalogue, type_peripherique, detail_peripheriques)
                        VALUES (:id, :type_catalogue, :type_peripherique, :detail_peripheriques)");
                $query->bindParam(":id", $idNewInsert);
                $query->bindParam(":type_catalogue", $newItem["type_catalogue"]);
                $query->bindParam(":type_peripherique", $newItem["type_peripherique"]);
                $query->bindParam(":detail_peripheriques", $newItem["detail_peripheriques"]);


                $query->execute();
            }
            if ($item["nombre_total"] == 0) {

                $delete = "delete.php?id=".$item['id'];

                header("Location: $delete");
            }else{

            header("Location:liste.php");
            }
        }
    }
}


if ($type === "ordinateur") {
    $query = $db->prepare("SELECT * from materiel inner join ordinateur on materiel.id = ordinateur.id_mat WHERE id = :id");
    $query->bindParam(":id", $id);
    $query->execute();
    $item = $query->fetch();



    $query = $db->prepare("SELECT id, code_bar from materiel 
    inner join ordinateur on materiel.id = ordinateur.id_mat 
    WHERE ram = :ram AND processeur = :processeur AND disque_dur = :disque_dur AND modele = :modele AND disponible = 1 AND id_agence_actuelle = :id_agence_actuelle");
    $query->bindParam(":ram", $item["ram"]);
    $query->bindParam(":processeur", $item["processeur"]);
    $query->bindParam(":disque_dur", $item["disque_dur"]);
    $query->bindParam(":modele", $item["modele"]);
    $query->bindParam(":id_agence_actuelle", $item["id_agence_actuelle"]);
    $query->execute();
    $listeTag = $query->fetchall();



    $code = $item["code_bar"];
    $nom = $item["nom"];
    $nombre = $item["nombre_total"];
    $dispo = $item["disponible"];



    $query = $db->prepare("SELECT * from agence WHERE id_agence <> :id");
    $query->bindParam(":id", $id);
    $query->execute();
    $listeAgence = $query->fetchAll();
} else {
    $query = $db->prepare("SELECT * from materiel WHERE id = :id");
    $query->bindParam(":id", $id);
    $query->execute();
    $item = $query->fetch();

    $code = $item["code_bar"];
    $nom = $item["nom"];
    $nombre = $item["nombre_total"];
    $dispo = $item["disponible"];
}
$query = $db->prepare("SELECT * from agence WHERE id_agence <> :id");
$query->bindParam(":id", $_SESSION["agence"]);
$query->execute();
$listeAgence = $query->fetchAll();


include("componant/header.php");
if ($_SESSION["agence"] != $item["id_agence_actuelle"]) { // vérification que l'utilsateur a le droit de modifier l'objet
  //  header("Location: liste.php");
}

?>
<h1>Envoie produit</h1>
<?php
if (isset($errors["infoManquante"])) {
?>
    <span class="info-error"><?= $errors["infoManquante"] ?></span>
<?php
}
?>
<?php
if (isset($errors["doublon"])) {
?>
    <span class="info-error"><?= $errors["doublon"] ?></span>
<?php
}
?>



<div class="infoMaterial">
    <div class="info">
        <?= $nom ?>
    </div>
    <?php
    if ($type != "ordinateur") {
    ?>

        <div class="info">
           Dispo: <?= $dispo?>
        </div>

    <?php
    }else{
    ?>
        <div class="info">
            <?=$item["ram"]?>Go
        </div>
    <?php
    }

    if ($type != "ordinateur") {
    ?>
        <div class="info">
           Total: <?= $nombre?>
        </div>
    <?php
    }else{
    ?>
        <div class="info">
            <?=$item["processeur"]?>
        </div>
    <?php
    }
    
    if ($type != "ordinateur") {
    ?>
        <div class="info">
            <?= $code ?>
        </div>
    <?php
    }else{
    ?>
        <div class="info">
            <?=$item["modele"]?>
        </div>
    <?php
    }
    
    if ($type != "ordinateur") {
    ?>

    <?php
    }else{
    ?>
        <div class="info">
            <?=$item["disque_dur"]?>
        </div>
    <?php
    }
    ?>
</div>
<form action="" method="post">


    <input type="hidden" id="id" name="id" value=<?= isset($id) ? $id : "" ?>>
    <input type="hidden" id="dispo" name="dispo" value=<?= isset($dispo) ? $dispo : "" ?>>

    <div class="form-group">
        <label for="inputAgence"> Les agences: </label>

        <select id="inputAgence" name="agence">

            <?php
            foreach ($listeAgence as $key) {


            ?>
                <option value="<?= $key["id_agence"] ?>" <?= isset($agenceSelect) ? (($key["id_agence"] === $agenceSelect) ? "selected='selected'" : "") : "" ?>><?= $key["nom_agence"] ?></option>
            <?php

            }


            ?>
        </select>
    </div>


    <?php
    if ($type === "ordinateur") {
    ?>
        <div id="tag_list">
            
            <div class="form-group">
            <label for="inputSend"> Tag 1: </label>
                <select id="inputTag" name="tag[1]">

                    <?php
                    foreach ($listeTag as $key) {
                    ?>
                    
                           
                            <option value="<?= $key["code_bar"] ?>" <?= isset($tag[1]) ? (($key["code_bar"] === $tag[1]) ? "selected='selected'" : "") : "" ?>><?= $key["code_bar"] ?></option>
                        

                    <?php
                    }
                    ?>
                </select>
               
                <input type="button" value="ajout" id="ajout" class="button-form" value="1" />
                </div>
            </div>
        

    <?PHP
    } else {

    ?>

        <div class="form-group">
            <label for="inputSend"> Envoyer combien: </label>
            <input type="text" id="inputSend" name="send" value="<?= isset($nb) ? $nb : "" ?>">
        </div>


    <?php
    }
    ?>

<div class="form-group">
        <input type="submit" value="Envoie" class="boutton-ajout">
    </div>
</form>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="js/send_envoie.js"> </script>
</body>

</html>