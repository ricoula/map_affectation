<?php
    function getSizeWidget($lien, $taille)
    {
        switch($lien)
        {
            default: $obj = null;
                break;

            case "test": $obj = (object) array();
                $obj->sm = (object) array();
                $obj->sm->x = 1;
                $obj->sm->y = 1;
                $obj->lg = (object) array();
                $obj->lg->x = 2;
                $obj->lg->y = 2;
                $obj->full = true;
                break;
        }

        return json_encode($obj);
    }

    function getGridsterByUserId($idUser)
    {
        include("connexionBdd.php");
        $gridster = null;
        $req = $bdd->prepare("SELECT gridster_json FROM cds_gridster WHERE user_id = ?");
        $req->execute(array($idUser));
        if($data = $req->fetch())
        {
            $gridster = $data["gridster_json"];
        }
        return $gridster;
    }

    function addGridster($idUser, $gridster)
    {
        include("connexionBdd.php");
        $reponse = false;
        try{
            $req = $bdd->prepare("SELECT id FROM cds_gridster WHERE user_id = ?");
            $req->execute(array($idUser));
            if($data = $req->fetch())
            {
                $req2 = $bdd->prepare("UPDATE cds_gridster SET gridster_json = ? WHERE user_id = ?");
                $reponse = $req2->execute(array($gridster, $idUser));
            }
            else{
                $req = $bdd->prepare("INSERT INTO cds_gridster(user_id, gridster_json) VALUES(?, ?)");
                $reponse = $req->execute(array($idUser, $gridster));
            }
        }catch(Exception $e){
            $reponse = false;
        }
        return json_encode($reponse);
    }
    
    function getNbAffectation(){
        include("connexionBdd.php");
        $listaffect = array();
        $lastmonday = date("Y-m-d",strtotime("last Monday"));
        $nextsunday = date("Y-m-d",strtotime("next Sunday"));
        $req= $bdd->prepare("SELECT * FROM cds_affectation WHERE cds_affectation_date >= ? AND cds_affectation_date <= ?");
        $req->execute(array($lastmonday,$nextsunday));
		while($data = $req->fetch())
		{
			$affect = (object) array();
			$affect->caff = $data["erp_caff_name"];
			$affect->poi = $data["erp_poi"];
			$affect->domaine = $data["erp_poi_domaine"];
            $affect->date_affect = $data["cds_affectation_date"];
            array_push($listaffect,$affect);
        }
        return json_encode($listaffect);
    }
?>