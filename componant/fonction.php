<?php
function checkCategories($id){

    include("componant/config.php");

    $query = $db->prepare("SELECT * from materiel 
    INNER JOIN ordinateur on materiel.id = ordinateur.id_mat WHERE materiel.id = :id");
    $query->bindParam(":id", $id);
    $query->execute();
    $existCheck = $query->fetch();

    if($existCheck){
        return "ordinateur";
    }else{

        $query = $db->prepare("SELECT * from materiel 
        INNER JOIN composants on materiel.id = composants.id_mat WHERE materiel.id = :id");
        $query->bindParam(":id", $id);
        $query->execute();
        $existCheck = $query->fetch();

        if($existCheck){
            return "composant";    
        }else{
            $query = $db->prepare("SELECT * from materiel 
            INNER JOIN peripheriques on materiel.id = peripheriques.id_mat WHERE materiel.id = :id");
            $query->bindParam(":id", $id);
            $query->execute();
            $existCheck = $query->fetch();
        }
        if($existCheck){
            return "peripherique"; 
        }else{
            return "existe pas";
        }
    }

}
