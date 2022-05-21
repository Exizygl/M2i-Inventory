<?php
include("componant/config.php");
$id = $_POST['id'];
$x = $_POST['x'];

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
?>
<div id="tag_list">

    <div class="form-group">
        <label for="inputSend"> Tag <?=$x?>: </label>
        <select id="inputTag" name="tag[<?=$x?>]">

            <?php
            foreach ($listeTag as $key) {
            ?>


                <option value="<?= $key["code_bar"] ?>" <?= isset($tag[$x]) ? (($key["code_bar"] === $tag[$x]) ? "selected='selected'" : "") : "" ?>><?= $key["code_bar"] ?></option>


            <?php
            }
            ?>
        </select>

        <input type="button" value="X" id="remove" class="button-form">
    </div>
</div>
</select>