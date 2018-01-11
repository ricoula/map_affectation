<?php
    function getInfosWidget($lien)
    {
        switch($lien)
        {
            default: $obj = null;
                break;

            case "test": $obj = (object) array();
                $obj->lien = $lien;
                $obj->sm = (object) array();
                $obj->sm->x = 1;
                $obj->sm->y = 1;
                $obj->lg = (object) array();
                $obj->lg->x = 2;
                $obj->lg->y = 2;
                $obj->full = true;
                $obj->libelle = "test";
                $obj->description = "widget de test";
                break;
            
            case "kpi_nb_affectation": $obj = (object) array();
                $obj->lien = $lien;
                $obj->sm = (object) array();
                $obj->sm->x = 1;
                $obj->sm->y = 1;
                $obj->lg = (object) array();
                $obj->lg->x = 2;
                $obj->lg->y = 2;
                $obj->full = true;
                $obj->libelle = "nb affectations";
                $obj->description = "KPI qui calcule le nombre d'affectations";
                break;
        }

        return json_encode($obj);
    }

    function getGridsterByUserId($idUser)
    {
        include("connexionBdd.php");
        $req = $bdd->prepare("SELECT gridster_json FROM cds_gridster WHERE user_id = ?");
        $req->execute(array($idUser));
        if($data = $req->fetch())
        {
            $gridster = $data["gridster_json"];
        }
        else{
            $gridster = json_encode(null);
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
    function getNbAffectationByUi(){
        include("connexionBdd.php");
        $listeaffectui = array();
        $lastmonday = date("Y-m-d",strtotime("last Monday"));
        $nextsunday = date("Y-m-d",strtotime("next Sunday"));
        $req= $bdd->prepare("select erp_ui,count(id) from cds_affectation where cds_affectation_date >= ? AND cds_affectation_date <= ? group by erp_ui");
        $req->execute(array($lastmonday,$nextsunday));
		while($data = $req->fetch())
		{
			$affect = (object) array();
			$affect->ui = $data["erp_ui"];
			$affect->nb_poi = $data["count"];
            array_push($listeaffectui,$affect);
        }
        return json_encode($listeaffectui);
    }

    function getListeWidgets()
    {
        $liste = array();
        $dossier = scandir("kpi/widgets");
        foreach($dossier as $fichier)
        {
            if($fichier != "." && $fichier != "..")
            {
                $tab = explode(".", $fichier);
                $obj = json_decode(getInfosWidget($tab[0]));
                array_push($liste, $obj);
            }
        }
        return json_encode($liste);
    }

    function getListeWidgetsByUserId($idUser)
    {
        include("connexionBdd.php");
        $listeWidgets = array();
        $listeWidgetsUser = array();
        $req = $bdd->prepare("SELECT gridster_json FROM cds_gridster WHERE user_id = ?");
        $req->execute(array($idUser));
        if($data = $req->fetch())
        {
            $gridster = json_decode($data["gridster_json"]);
            foreach($gridster as $wdg)
            {
                $widget = json_decode(getInfosWidget($wdg->lien));
                array_push($listeWidgetsUser, $widget);
            }
        }
        $listeWidgets = json_decode(getListeWidgets());
        if(sizeof($listeWidgets) > 0 && sizeof($listeWidgetsUser) > 0)
        {
            foreach($listeWidgets as $wdg)
            {
                $existe = false;
                foreach($listeWidgetsUser as $wdgUser)
                {
                    if($wdg->libelle == $wdgUser->libelle)
                    {
                        $existe = true;
                    }
                }
                if($existe)
                {
                    $wdg->disponible = false;
                }
                else{
                    $wdg->disponible = true;
                }
            }
        }

        return json_encode($listeWidgets);
    }
?>