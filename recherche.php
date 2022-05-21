<?php
session_start();
include("componant/config.php");
$buildQuery = '';
if (!empty($_POST)) {
    
    $buildQuery = '';
    $recherche = trim(strip_tags($_POST['recherche']));
    $typeMateriel = $_POST['typeMateriel'];
    $typeListe = $_POST['typeListe'];
    if (!empty(isset($_POST['typeVille']))) $typeVille = $_POST['typeVille'];

    if (empty($recherche) && ($typeMateriel == 'Ordinateur' || $typeMateriel == 'All')) {
        $buildQuery .= 'SELECT *  , sum(materiel.disponible) as ordi_dispo, sum(materiel.nombre_total) as ordi_total, IFNULL(modele,id) as groupe_null FROM materiel ';
        $ordiCombine = true;
    } else {
        $buildQuery .= 'SELECT * FROM materiel ';
        $ordiCombine = false;
    }



    if ($typeMateriel === 'All') $buildQuery .= "left join ordinateur on materiel.id = ordinateur.id_mat left join peripheriques on materiel.id = peripheriques.id_mat left join composants on materiel.id = composants.id_mat ";
    if ($typeMateriel === 'Ordinateur') $buildQuery .= "inner join ordinateur on materiel.id = ordinateur.id_mat ";
    if ($typeMateriel === 'Peripherique') $buildQuery .= "inner join peripheriques on materiel.id = peripheriques.id_mat ";
    if ($typeMateriel === 'Composant') $buildQuery .= "inner join composants on materiel.id = composants.id_mat ";
    
    
    
    if ($typeListe === 'inventaireDisponible' || $typeListe === 'inventaireEnvoyer') $buildQuery .= "inner join agence on materiel.id_agence_actuelle = agence.id_agence ";
    if ($typeListe === 'inventaireEmprunter') $buildQuery .= "inner join agence on materiel.id_agence_origine = agence.id_agence ";

    


    $typeListe = $_POST['typeListe'];

    if ($typeListe === 'inventaireActuelle') {
        $buildQuery .= "WHERE materiel.id_agence_origine = materiel.id_agence_actuelle AND materiel.id_agence_origine = :id ";
    }
    if ($typeListe === 'inventaireEnvoyer') {
        $buildQuery .= "WHERE materiel.id_agence_origine <> materiel.id_agence_actuelle AND materiel.id_agence_origine = :id ";
    }
    if ($typeListe === 'inventaireEmprunter') {
        $buildQuery .= "WHERE materiel.id_agence_origine <> materiel.id_agence_actuelle AND materiel.id_agence_actuelle = :id ";
    }
    if ($typeListe === 'inventaireDisponible') {
        $buildQuery .= "WHERE materiel.id_agence_origine <> :id AND materiel.id_agence_actuelle <> :id AND materiel.disponible > 0 ";
    }

    if ($typeMateriel === 'Ordinateur') {
        if (!empty(isset($_POST['numberRam']))) {
            $numberRam = $_POST['numberRam'];
            $buildQuery .= "AND ram = :ram ";
        }

        if (!empty(isset($_POST['typeProcesseur']))) {
            $typeProcesseur = $_POST['typeProcesseur'];
            $buildQuery .= "AND processeur = :processeur ";
        }

        if (!empty(isset($_POST['typeDisqueDur']))) {
            $typeDisqueDur = $_POST['typeDisqueDur'];
            $buildQuery .= "AND disque_dur = :disque_dur ";
        }

        if (!empty(isset($_POST['typeModele']))) {
            $typeModele = $_POST['typeModele'];
            $buildQuery .= "AND modele = :modele ";
        }
    }

    if (!empty(isset($_POST['typeComposant'])) && $typeMateriel === 'Composant') {
        $typeComposant = $_POST['typeComposant'];
        $buildQuery .= "AND type_composant = :type_composant ";
    }

    if ($typeMateriel === 'Peripherique') {
        if (!empty(isset($_POST['typeCatalogue']))) {
            $typeCatalogue = $_POST['typeCatalogue'];
            $buildQuery .= "AND type_catalogue = :type_catalogue ";
        }

        if (!empty(isset($_POST['typePeripherique']))) {
            $typePeripherique = $_POST['typePeripherique'];
            $buildQuery .= "AND type_peripherique= :type_peripherique ";
        }
    }

    if ($typeListe != 'inventaireActuelle') {
        if (!empty($typeVille) && ($typeListe === 'inventaireDisponible' || $typeListe === 'inventaireEnvoyer')) {
            $buildQuery .= "AND materiel.id_agence_actuelle = :id_agence_select ";
        }
        if (!empty($typeVille) && $typeListe === 'inventaireEmprunter') {
            $buildQuery .= "AND materiel.id_agence_origine = :id_agence_select ";
        }
    }





    if (!empty($recherche)) {
        $buildQuery .= " AND (materiel.nom LIKE concat('%' ,:recherche, '%') 
                    OR materiel.code_bar LIKE concat('%' ,:recherche, '%')) ";
    } else if ($typeMateriel === 'Ordinateur' || $typeMateriel === 'All') {
        $buildQuery .= "group by ram, processeur, disque_dur, modele, groupe_null";
    }

    $query = $db->prepare($buildQuery);
    $query->bindParam(":id", $_SESSION["agence"]);

    if (!empty($recherche)) {
        $query->bindParam(":recherche", $recherche);
    }

    if (!empty($numberRam)) {
        $query->bindParam(":ram", $numberRam);
    }

    if (!empty($typeProcesseur)) {
        $query->bindParam(":processeur", $typeProcesseur);
    }

    if (!empty($typeDisqueDur)) {
        $query->bindParam(":disque_dur", $typeDisqueDur);
    }

    if (!empty($typeModele)) {
        $query->bindParam(":modele", $typeModele);
    }

    if (!empty($typeComposant)) {
        $query->bindParam(":type_composant", $typeComposant);
    }

    if (!empty($typeCatalogue)) {
        $query->bindParam(":type_catalogue", $typeCatalogue);
    }

    if (!empty($typePeripherique)) {
        $query->bindParam(":type_peripherique", $typePeripherique);
    }
    if (!empty($typeVille) && $typeListe != 'inventaireActuelle') {
        $query->bindParam(":id_agence_select", $typeVille);
    }


    // echo $buildQuery;
    $query->execute();
    $result = $query->fetchAll();

?>

    <?php
    if (!empty($result)) {
    ?>



        <div class="containerMaterial titre">
            <div class="MaterialNom">
                Nom
            </div>
            <div class="MaterialCodebar">
                Code Bar
            </div>
            <div class="MaterialNombre">
                <?php if ($typeListe != 'inventaireDisponible') { ?>
                    Dispo/Total
                <?php } else { ?>
                    Disponible
                <?php } ?>
            </div>

            <div class="MaterialDetail">
                Details
            </div>
            <?php if ($typeListe === 'inventaireEnvoyer' || $typeListe === 'inventaireEmprunter' || $typeListe === 'inventaireDisponible') { ?>
                <div class="MaterialVille">
                    Agence
                </div>
            <?php } ?>
            <?php
            if ($typeListe === 'inventaireActuelle' || $typeListe === 'inventaireEmprunter') {
            ?>
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
            <?php } ?>
        </div>
        <?php
        for ($i = 0; $i <= count($result) - 1; $i++) {
            if (isset($result[$i])) {
                $item = $result[$i];
            }

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
                    if ($typeListe != 'inventaireDisponible') {
                        if (isset($item["ordi_dispo"])) {
                            echo $item["ordi_dispo"];
                        } else {
                            echo $item["disponible"];
                        }
                    ?> / <?php
                        }
                            ?>

                    <?php
                    if (isset($item["ordi_total"])) {
                        echo $item["ordi_total"];
                    } else {
                        echo $item["nombre_total"];
                    }

                    ?>
                </div>

                <div class="MaterialDetail">
                    <?php if (isset($item["id_ordi"])) { ?>
                        ram : <?= $item["ram"] ?>
                        pro : <?= $item["processeur"] ?>
                        Disque : <?= $item["disque_dur"] ?>
                    <?php } ?>
                    <?php if (isset($item["id_comp"])) { ?>
                        <?= $item["detail_composant"] ?>
                    <?php } ?>
                    <?php if (isset($item["id_peri"])) { ?>
                        <?= $item["detail_peripheriques"] ?>
                    <?php } ?>
                </div>





                <?php if (isset($item["nom_agence"]) && ($typeListe === 'inventaireEnvoyer'
                    || $typeListe === 'inventaireEmprunter'
                    || $typeListe === 'inventaireDisponible')) { ?>
                    <div class="MaterialVille">
                        <?= $item["nom_agence"] ?>
                    </div>
                <?php  } ?>


                <?php if ($typeListe === 'inventaireActuelle' || $typeListe === 'inventaireEmprunter') { ?>
                    <div class="MaterialLieux">
                        <?php
                        if (isset($item["id_ordi"]) && $ordiCombine) {
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
                <?php } ?>
            </div>

<?php
        }
    }
}
?>