<?php
session_start();
include("componant/config.php");
include("componant/fonction.php");
if (isset($_GET["id"])) {
    $id = $_GET["id"];


    $type = checkCategories($id);

    if ($type === "ordinateur") {

        $query = $db->prepare("DELETE FROM ordinateur WHERE id_mat = :id");
        $query->bindParam(":id", $_GET["id"], PDO::PARAM_INT);
        $query->execute();

    } else if ($type === "composant") {

        $query = $db->prepare("DELETE FROM composants WHERE id_mat = :id");
        $query->bindParam(":id", $_GET["id"], PDO::PARAM_INT);
        $query->execute();
    } else if ($type === "peripherique") {

        $query = $db->prepare("DELETE FROM peripheriques WHERE id_mat = :id");
        $query->bindParam(":id", $_GET["id"], PDO::PARAM_INT);
        $query->execute();
    }
    $query = $db->prepare("DELETE FROM materiel WHERE id = :id");
        $query->bindParam(":id", $_GET["id"], PDO::PARAM_INT);
        $query->execute();
       header("Location: liste.php");
} else {
  header("Location: liste.php");
}
