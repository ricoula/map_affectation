<?php
    function getSizeWidget($lien, $taille)
    {
        $obj = null;
        if($lien == "test")
        {
            $obj = (object) array();
            $obj->sm = (object) array();
            $obj->sm->x = 1;
            $obj->sm->y = 1;
            $obj->lg = (object) array();
            $obj->lg->x = 2;
            $obj->lg->y = 2;
        }

        return json_encode($obj);
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