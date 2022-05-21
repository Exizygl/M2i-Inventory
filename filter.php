<?php
session_start();
include("componant/config.php");

if (!empty($_POST)) {
    
    $typeMateriel = $_POST['typeMateriel'];
    $typeListe = $_POST['typeListe'];

    if (!empty(isset($_POST['typeVille']))) {
        $typeVille = $_POST['typeVille'];
    }

    if ($typeMateriel === 'Ordinateur') {
        if (!empty(isset($_POST['numberRam']))) {
            $numberRam = $_POST['numberRam'];
        }

        if (!empty(isset($_POST['typeProcesseur']))) {
            $typeProcesseur = $_POST['typeProcesseur'];
        }

        if (!empty(isset($_POST['typeDisqueDur']))) {
            $typeDisqueDur = $_POST['typeDisqueDur'];
        }

        if (!empty(isset($_POST['typeModele']))) {
            $typeModele = $_POST['typeModele'];
        }
    }
    if (!empty(isset($_POST['typeComposant'])) && $typeMateriel === 'Composant') {
        $typeComposant = $_POST['typeComposant'];
    }
    if ($typeMateriel === 'Peripherique') {
        if (!empty(isset($_POST['typeCatalogue']))) {
            $typeCatalogue = $_POST['typeCatalogue'];
            
        }

        if (!empty(isset($_POST['typePeripherique']))) {
            $typePeripherique = $_POST['typePeripherique'];
        }
    }

    if ($typeListe === 'inventaireActuelle') {

        $typeQuery = "WHERE materiel.id_agence_origine = materiel.id_agence_actuelle AND materiel.id_agence_origine = :id ";
    }
    if ($typeListe === 'inventaireEnvoyer') {

        $typeQuery = "WHERE materiel.id_agence_origine <> materiel.id_agence_actuelle AND materiel.id_agence_origine = :id ";
    }
    if ($typeListe === 'inventaireEmprunter') {

        $typeQuery = "WHERE materiel.id_agence_origine <> materiel.id_agence_actuelle AND materiel.id_agence_actuelle = :id ";
    }
    if ($typeListe === 'inventaireDisponible') {

        $typeQuery = "WHERE materiel.id_agence_origine <> :id AND materiel.id_agence_actuelle <> :id AND materiel.disponible > 0 ";
    }
    if ($typeMateriel === 'Ordinateur') {
        $ramQuery = 'SELECT distinct ram from ordinateur inner join materiel on ordinateur.id_mat = materiel.id ' . $typeQuery;

        $query = $db->prepare($ramQuery);
        $query->bindParam(":id", $_SESSION["agence"]);
        $query->execute();
        $ram = $query->fetchAll();

        $proQuery = 'SELECT distinct  processeur from ordinateur inner join materiel on ordinateur.id_mat = materiel.id ' . $typeQuery;
        $query = $db->prepare($proQuery);
        $query->bindParam(":id", $_SESSION["agence"]);
        $query->execute();
        $processeur = $query->fetchAll();

        $disqueQuery = 'SELECT distinct  disque_dur from ordinateur inner join materiel on ordinateur.id_mat = materiel.id ' . $typeQuery;
        $query = $db->prepare($disqueQuery);
        $query->bindParam(":id", $_SESSION["agence"]);
        $query->execute();
        $disqueDur = $query->fetchAll();

        $modeleQuery = 'SELECT distinct  modele from ordinateur inner join materiel on ordinateur.id_mat = materiel.id ' . $typeQuery;
        $query = $db->prepare($modeleQuery);
        $query->bindParam(":id", $_SESSION["agence"]);
        $query->execute();
        $modele = $query->fetchAll();

        if (!empty($ram)) {


?>


            <div class="containerCategorie">
                <div class="titreCategorie">Ram :</div>
                <?php
                if (!isset($numberRam)) $numberRam = 0;

                foreach ($ram as $data) {
                ?>
                    <div>
                        <input type="radio" name="numberRam" value="<?= $data['ram'] ?>" id="<?= $data['ram'] ?>" <?= ($numberRam == $data['ram']) ? 'checked="checked"' : "" ?> class="numberRam">
                        <label for="<?= $data['ram'] ?>"><?= $data['ram'] ?></label>
                    </div>

                <?php
                }
                ?>

            </div>

            <div class="containerCategorie">
                <div class="titreCategorie">Processeur :</div>
                
                <?php
                if (!isset($typeProcesseur)) $typeProcesseur = 0;
                foreach ($processeur as $data) {
                ?>
                    <div>
                        <input type="radio" name="typeProcesseur" value="<?= $data['processeur'] ?>" id="<?= $data['processeur'] ?>" <?= ($typeProcesseur == $data['processeur']) ? 'checked="checked"' : "" ?> class="typeProcesseur">
                        <label for="<?= $data['processeur'] ?>"><?= $data['processeur'] ?></label>
                    </div>
                <?php
                }
                ?>

            </div>

            <div class="containerCategorie">
                <div class="titreCategorie">Disque dur :</div>
                <?php
                if (!isset($typeDisqueDur)) $typeDisqueDur = 0;
                foreach ($disqueDur as $data) {
                ?>
                    <div>
                        <input type="radio" name="typeDisqueDur" value="<?= $data['disque_dur'] ?>" id="<?= $data['disque_dur'] ?>" <?= ($typeDisqueDur ==  $data['disque_dur']) ? 'checked="checked"' : "" ?> class="typeDisqueDur">
                        <label for="<?= $data['disque_dur'] ?>"><?= $data['disque_dur'] ?></label>
                    </div>
                <?php
                }
                ?>

            </div>

            <div class="containerCategorie">
                <div class="titreCategorie">Type de poste: :</div>
                <?php
                if (!isset($typeModele)) $typeModele = 0;
                foreach ($modele as $data) {
                ?>
                    <div>
                        <input type="radio" name="typeModele" value="<?= $data['modele'] ?>" id="<?= $data['modele'] ?>" <?= ($typeModele ==  $data['modele']) ? 'checked="checked"' : "" ?>class="typeModele">
                        <label for="<?= $data['modele'] ?>"><?= $data['modele'] ?></label>
                    </div>
                <?php
                }
                ?>

            </div>
            </div>
        <?php
        }
    }
    if ($typeMateriel === 'Composant') {
        $compQuery = 'SELECT distinct type_composant from composants 
                    inner join materiel on composants.id_mat = materiel.id ' . $typeQuery;
        $query = $db->prepare($compQuery);
        $query->bindParam(":id", $_SESSION["agence"]);
        $query->execute();
        $comp = $query->fetchAll();

        if (!empty($comp)) {


        ?>
            <div class="containerCategorie">
                <div class="titreCategorie">Composant:</div>

                <?php

                if (!isset($typeComposant)) $typeComposant = 0;

                foreach ($comp as $data) {
                ?>
                    <div>
                        <input type="radio" name="typeComposant" value="<?= $data['type_composant']?>" id="<?= $data['type_composant']?>"
                        <?= ($typeComposant == $data['type_composant']) ? 'checked="checked"' : "" ?> class="typeComposant">
                        <label for="<?= $data['type_composant']?>"><?= $data['type_composant'] ?></label>
                    </div>

                <?php
                }
                ?>
            </div>
        <?php
        }
    }
    if ($typeMateriel === 'Peripherique') {
        $cataQuery = 'SELECT distinct type_catalogue from peripheriques inner join materiel on peripheriques.id_mat = materiel.id ' . $typeQuery;
        $query = $db->prepare($cataQuery);
        $query->bindParam(":id", $_SESSION["agence"]);
        $query->execute();
        $peri = $query->fetchAll();

        if (!empty($peri)) {


        ?>

            <div class="containerCategorie">
                <div class="titreCategorie">Catégorie:</div>
                <?php
                if (!isset($typeCatalogue)) $typeCatalogue = 0;

                foreach ($peri as $data) {
                ?>
                    <div>
                        <input type="radio" name="typeCatalogue" value="<?= $data['type_catalogue'] ?>" id="<?= $data['type_catalogue'] ?>" <?= ($typeCatalogue == $data['type_catalogue']) ? 'checked="checked"' : "" ?> class="typeCatalogue">
                        <label for="<?= $data['type_catalogue'] ?>"><?= $data['type_catalogue'] ?></label>
                    </div>

                <?php
                }
                ?>

            </div>

            <div class="containerCategorie">
                <div class="titreCategorie">Type:</div>
                <?php
                
                if ($typeCatalogue != 0) {
                    $peryQuery = 'SELECT distinct type_peripherique from peripheriques inner join materiel on peripheriques.id_mat = materiel.id ' . $typeQuery . ' AND type_catalogue = :type_catalogue';
                    $query = $db->prepare($peryQuery);
                    $query->bindParam(":type_catalogue", $typeCatalogue);
                    $query->bindParam(":id", $_SESSION["agence"]);

                    $query->execute();
                    $peri = $query->fetchAll();
                    if (!isset($typePeripherique)) $typePeripherique = 0;

                    foreach ($peri as $data) {
                ?>
                        <div>
                            <input type="radio" name="typePeripherique" value="<?= $data['type_peripherique'] ?>" id="<?= $data['type_peripherique'] ?>" <?= ($typePeripherique == $data['type_peripherique']) ? 'checked="checked"' : "" ?> class="typePeripherique">
                            <label for="<?= $data['type_peripherique'] ?>"><?= $data['type_peripherique'] ?></label>
                        </div>

                    <?php
                    } 
                    ?>

            </div>
            </div>

        <?php
                }
            }
        }

        if ($typeListe === 'inventaireEnvoyer' || $typeListe === 'inventaireEmprunter' || $typeListe === 'inventaireDisponible') {
            $compQuery = 'SELECT * from agence WHERE id_agence <> :id';
            $query = $db->prepare($compQuery);
            $query->bindParam(":id", $_SESSION["agence"]);

            $query->execute();
            $ville = $query->fetchAll();



            if (!empty($ville)) {




        ?>
        <div class="containerCategorie">
            <div class="titreCategorie">VILLE:</div>
            <?php
                if (!isset($typeVille)) $typeVille = 0;

                foreach ($ville as $data) {

            ?>
                <div>
                    <input type="radio" name="typeVille" value="<?= $data['id_agence'] ?>" id="<?= $data['id_agence'] ?>" <?= ($typeVille == $data['id_agence']) ? 'checked="checked"' : "" ?> class="typeVille">
                    <label for="<?= $data['id_agence'] ?>"><?= $data['nom_agence'] ?></label>
                </div>

            <?php
                }
            ?>

        </div>
<?php
            }
        }
    }
?>