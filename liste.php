<?php
session_start();

if (!isset($_SESSION["agence"])) { // vérification de la connexion
    header("Location: index.php");
}

include("componant/header.php");
include("componant/config.php");

//requete de l'affiche de base de l'ineventaire

$query = $db->prepare("SELECT *  , sum(materiel.disponible) as ordi_dispo, sum(materiel.nombre_total) as ordi_total,
IFNULL(modele,id) as groupe_null FROM materiel left join ordinateur on materiel.id = ordinateur.id_mat
left join peripheriques on materiel.id = peripheriques.id_mat
left join composants on materiel.id = composants.id_mat WHERE materiel.id_agence_origine = materiel.id_agence_actuelle 
AND materiel.id_agence_origine = :id
group by ram, processeur, disque_dur, modele, groupe_null");
$query->bindParam(":id", $_SESSION["agence"]);
$query->execute();
$materiel = $query->fetchAll();


?>

<!--  formulaire de recherche -->
<div class="recherche">
    <div>
        <input type="text" name="recherche" id="recherche" class="inputSearch" />
    </div>
</div>
<div class="selection">
    <input type="radio" name="typeListe" id="actuelle" value="inventaireActuelle" checked="checked" class="typeListe">
    <label for="actuelle">Actuelle</label>
    <input type="radio" name="typeListe" id="envoyer" value="inventaireEnvoyer" class="typeListe">
    <label for="envoyer">Envoyé</label>
    <input type="radio" name="typeListe" id="emprunter" value="inventaireEmprunter" class="typeListe">
    <label for="emprunter">Emprunté</label>
    <input type="radio" name="typeListe" id="disponible" value="inventaireDisponible" class="typeListe">
    <label for="disponible">Disponible</label>
</div>

<!-- partie dynamique de la page -->
<div id="resultat">
    <!-- partie filtre -->
    <div class="filter">

        <div class="containerCategorie">

            <div class="titreCategorie">Liste :</div>
            <div>
                <input type="radio" name="typeMateriel" value="All" checked="checked" class="typeMateriel">
                <label for="contactChoice1">All</label>
            </div>
            <div>
                <input type="radio" name="typeMateriel" value="Ordinateur" class="typeMateriel">
                <label for="contactChoice1">Ordinateur</label>
            </div>
            <div>
                <input type="radio" name="typeMateriel" value="Composant" class="typeMateriel">
                <label for="contactChoice1">Composant</label>
            </div>
            <div>
                <input type="radio" name="typeMateriel" value="Peripherique" class="typeMateriel">
                <label for="contactChoice1">Périphérique</label>
            </div>

            <div id="filterDetails"></div>
        </div>
    </div>

    <!-- partie affichage de la requète -->
    <div class="liste" id="liste">

        <div class="containerMaterial titre">
            <div class="MaterialNom">
                Nom
            </div>
            <div class="MaterialCodebar">
                Code Bar
            </div>
            <div class="MaterialNombre">
                Dispo/Total
            </div>

            <div class="MaterialDetail">
                Details
            </div>

            <div class="MaterialLieux">
                Lieux
            </div>

            <div class="MaterialDetail TitleDetail">
                Modification
            </div>
            <div class="MaterialDetail TitleDetail">
                Envoie
            </div>
            <div class="MaterialDetail TitleDetail">
                suppression
            </div>
        </div>
        <?php

        for ($i = 0; $i <= count($materiel) - 1; $i++) {
            if (isset($materiel[$i])) {
                $item = $materiel[$i];

                if($i % 2 == 0){
                    $classLine = "Even"; 
                }
                else{
                    $classLine = "Odd";
                }


        ?>
                <div class="containerMaterial <?=$classLine?>">
                    <div class="MaterialNom">
                        <?= $item["nom"] ?>
                    </div>
                    <div class="MaterialCodebar">
                        <?= $item["code_bar"] ?>
                    </div>

                    <div class="MaterialNombre">
                        <?php
                        if ($item["ordi_dispo"]) {
                            // si les ordianteur sont combiné
                            echo $item["ordi_dispo"];
                        } else {
                            // pour le reste ou et les ordi séparés
                            echo $item["disponible"];
                        }
                        ?>
                        /
                        <?php
                        if ($item["ordi_total"]) {
                            // si les ordianteurs sont combinés
                            echo $item["ordi_total"];
                        } else {
                            // pour le reste ou et les ordi séparés
                            echo $item["nombre_total"];
                        }
                        ?>
                    </div>
                    <div class="MaterialDetail">
                        <?php if ($item["id_ordi"]) { ?>
                            <!-- affiche des détail en fonction du type de matériel -->
                            ram : <?= $item["ram"] ?>
                            pro : <?= $item["processeur"] ?>
                            Disque : <?= $item["disque_dur"] ?>
                        <?php } ?>
                        <?php if ($item["id_comp"]) { ?>
                            <?= $item["detail_composant"] ?>
                        <?php } ?>
                        <?php if ($item["id_peri"]) { ?>
                            <?= $item["detail_peripheriques"] ?>
                        <?php } ?>
                    </div>
                    <div class="MaterialLieux">
                        <?php
                        if (isset($item["id_ordi"])) {
                            echo "Diver";
                        } else {

                            echo $item["lieu"];
                        }

                        ?>


                    </div>


                    <div class="option">
                        <a href="modifie.php?id=<?= $item["id"] ?>" class="btn">modifier</a>
                    </div>
                    <div class="option">
                        <a href="send.php?id=<?= $item["id"] ?>" class="btn">Envoyer</a>
                    </div>
                    <div class="option">
                        <a href="delete.php?id=<?= $item["id"] ?>" class="btn">suppr</a>
                    </div>
                </div>
        <?php
            }
        }

        ?>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script src="js/search.js"> </script>
</body>

</html>