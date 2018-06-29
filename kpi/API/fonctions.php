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
                $obj->libelle = "Nombre Affect.";
                $obj->description = "Nombre d'affectations total ou par UI";
                break;
            case "kpi_charge_ui": $obj = (object) array();
                $obj->lien = $lien;
                $obj->sm = (object) array();
                $obj->sm->x = 1;
                $obj->sm->y = 1;
                $obj->lg = (object) array();
                $obj->lg->x = 2;
                $obj->lg->y = 2;
                $obj->full = true;
                $obj->libelle = "Charge UI";
                $obj->description = "Charge des ui en fonction du nombre de CAFF";
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

    function getChargeByUi(){
        include("connexionBddErp.php");
        $listUiCharge = array();
        $req = $bddErp->query("select site as agence,sum(reactive) as reactive,sum(non_reactive) as non_reactive,count(agence),round(((sum(reactive) + (sum(non_reactive)*0.1))/count(agence)),2) as charge from(
            select id, t3.ag_coeff_traitement, t3.name_related, t3.mobile_phone, t3.work_email, t3.site, t3.agence,case when t3.reactive is null then 0 else t3.reactive end,
                    case when t3.non_reactive is null then 0 else t3.non_reactive end from
                    (
                    select t2.ag_coeff_traitement, t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name as agence, sum(t2.reactive) as reactive, sum(t2.non_reactive) as non_reactive from (
                     
                    select t1.ag_coeff_traitement, t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, case when account_analytic_account.name in ('Client', 'FO & CU') then count (ag_poi.id)
                    end as reactive , case when account_analytic_account.name not in ('Client', 'FO & CU') then count (ag_poi.id) end as non_reactive
                    from ag_poi
                    left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id  
                    full join
                    (select hr_employee.ag_coeff_traitement, hr_employee.id, hr_employee.name_related,hr_employee.mobile_phone,hr_employee.work_email,ag_site.name as site,ag_agence.name from res_users
                    full join hr_employee on res_users.ag_employee_id = hr_employee.id
                    full join ag_site on hr_employee.ag_site_id = ag_site.id
                    full join ag_agence on hr_employee.ag_agence_id = ag_agence.id
                    full join hr_job on hr_employee.job_id = hr_job.id
                    where res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER')) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
                    group by t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, account_analytic_account.name, t1.ag_coeff_traitement) t2
                    group by t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name, t2.ag_coeff_traitement ) t3
                    where name_related is not null ORDER BY name_related)uicharge
                    group by site
                    order by charge desc");
                    while($data = $req->fetch())
                    {
                        $listui = (object) array();
                        $listui->ui = $data['agence'];
                        $listui->charge_reactive = $data['reactive'];
                        $listui->charge_non_reactive = $data['non_reactive'];
                        $listui->nb_caff = $data['count'];
                        array_push($listUiCharge,$listui);
                    }
                    return json_encode($listUiCharge);
    }

?>