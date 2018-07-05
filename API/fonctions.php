<?php
	function getProchainesEntraidesCaff($idCaff)
	{
		include("connexionBdd.php");
		$entraides = array();
		$req = $bdd->prepare("SELECT * FROM cds_entraide WHERE DATE_PART('day', date_expiration - NOW()) >= 0 AND caff_id = ? ORDER BY date_debut");
		$req->execute(array($idCaff));
		while($data = $req->fetch())
		{
			$entraide = (object) array();
			$entraide->id = $data["id"];
			$entraide->caff_id = $data["caff_id"];
			$entraide->site_entraide_id = $data["site_entraide_id"];
			$entraide->site_entraide_libelle = json_decode(getLibelleSiteById($data["site_entraide_id"]));
			$entraide->date_expiration = $data["date_expiration"];
			$entraide->domaines = json_decode($data["domaines"]);
			$entraide->date_debut = $data["date_debut"];
			$entraide->site_defaut_id = $data["site_defaut_id"];
			$entraide->taux = $data["taux"];
			array_push($entraides, $entraide);
		}
		return json_encode($entraides);
	}
	
	function getIdFromSite($site)
	{
		include("connexionBdd.php");
		$id = null;
		$req = $bdd->prepare("SELECT erp_site_id FROM cds_transco_ui_site WHERE LOWER(site) = ? LIMIT 1");
		$req->execute(array(strtolower($site)));
		if($data = $req->fetch())
		{
			$id = $data["erp_site_id"];
		}
		return json_encode($id);
	}
	
	function getPoiNA()
	{
		include("connexionBddErp.php");
		
		$listePoiAffect = json_decode(getPoiAffect());
		if(sizeof($listePoiAffect) > 0)
		{
			$listePoiAffect = implode(",", $listePoiAffect);
			$listePoiAffect = " AND ag_poi.id NOT IN(".$listePoiAffect.")";
		}
		else{
			$listePoiAffect = "";
		}
		
		
		$listePoi = array();
		
		$req = $bddErp->query("select ag_poi.id,ag_poi.ft_sous_justification_oeie, ag_poi.atr_ui, ag_poi.ft_numero_oeie, account_analytic_account.name as domaine, ag_poi.\"ft_numero_demande_42C\" numero_demande, ft_libelle_commune, ft_libelle_de_voie, ft_pg,ft_oeie_dre,ft_latitude,insee_code,ft_longitude,ft_libelle_affaire,ft_date_limite_realisation,ag_poi.create_date from ag_poi
		left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
		where hr_employee.name_related in ('MATHIASIN Celine','AFFECTATION') and ft_etat = '1'".$listePoiAffect);
		
		while($data = $req->fetch())
		{
			$poi = (object) array();
			$poi->atr_ui = $data["atr_ui"];
			$poi->ft_numero_oeie = $data["ft_numero_oeie"];
			$poi->domaine = $data["domaine"];
			$poi->ft_titulaire_client = $data["numero_demande"];
			$poi->ft_libelle_commune = $data["ft_libelle_commune"];
			$poi->ft_libelle_de_voie = $data["ft_libelle_de_voie"];
			$poi->ft_pg = $data["ft_pg"];
			$poi->ft_sous_justification_oeie = $data["ft_sous_justification_oeie"];
			$poi->ft_oeie_dre = $data["ft_oeie_dre"];
			$poi->ft_latitude = $data["ft_latitude"];
			$poi->insee_code = $data["insee_code"];
			$poi->ft_longitude = $data["ft_longitude"];
			$poi->ft_libelle_affaire = $data["ft_libelle_affaire"];
			$poi->ft_date_limite_realisation = $data["ft_date_limite_realisation"];
			$poi->create_date = $data["create_date"];
			$poi->id = $data["id"];
			if($poi->domaine == 'Client' || $poi->domaine == 'FO & CU')
			{
				$poi->reactive = true;
			}
			else{
				$poi->reactive = false;
			}
			
			array_push($listePoi, $poi);
		}
		
		return json_encode($listePoi);
	}

	function getPoiLienByTitulaire($poi) //$poi = format JSON
	{
		include("connexionBddErp.php");

		$poi = json_decode($poi);

		$listePoi = array();
		$req = $bddErp->prepare("SELECT ag_poi.id FROM ag_poi left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
		where hr_employee.name_related in ('MATHIASIN Celine','AFFECTATION') and ft_etat = '1' AND ag_poi.atr_ui = ? AND ag_poi.\"ft_numero_demande_42C\" = ? AND ag_poi.\"ft_numero_demande_42C\" IS NOT NULL AND ag_poi.\"ft_numero_demande_42C\" != '' AND ag_poi.id != ?");
		$req->execute(array($poi->atr_ui, $poi->ft_titulaire_client, $poi->id));
		while($data = $req->fetch())
		{
			array_push($listePoi, json_decode(getPoiById($data["id"])));
		}
		return json_encode($listePoi);
	}
	
	function getListeSites()
	{
		include("connexionBdd.php");
		
		$listeSites = array();
		
		$req = $bdd->query("SELECT * FROM cds_transco_ui_site");
		while($data = $req->fetch())
		{
			$site = (object) array();
			$site->id = $data["id"];
			$site->site = $data["site"];
			$site->site_longitude = $data["site_longitude"];
			$site->site_latitude = $data["site_latitude"];
			$site->ui = $data["ui"];
			$site->ft_zone = $data["ft_zone"];
			
			array_push($listeSites, $site);
		}
		
		return json_encode($listeSites);
	}
	
	function getInfosCaff()
	{
		include("connexionBddErp.php");
		
		$listeCaff = array();
		
		$req = $bddErp->query("select id, t3.ag_coeff_traitement, t3.name_related, t3.mobile_phone, t3.work_email, t3.site, t3.agence,case when t3.reactive is null then 0 else t3.reactive end,
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
		where name_related is not null ORDER BY name_related");
		while($data = $req->fetch())
		{
			$caff = (object) array();
			$caff->id = $data["id"];
			$caff->ag_coeff_traitement = $data["ag_coeff_traitement"];
			$caff->name_related = $data["name_related"];
			$caff->mobile_phone = $data["mobile_phone"];
			$caff->work_email = $data["work_email"];
			$caff->site = $data["site"];
			$caff->agence = $data["agence"];
			$caff->reactive = $data["reactive"];
			$caff->non_reactive = $data["non_reactive"];
			$caff->conges = json_decode(getProchainesConges($caff->id));
			$caff->entraides = json_decode(getProchainesEntraidesCaff($caff->id));
			
			if($caff->conges != null && $caff->conges->temps_avant <= 0)
			{
				$caff->enConge = true;
			}
			else{
				$caff->enConge = false;
			}
			
			array_push($listeCaff, $caff);
		}
		
		return json_encode($listeCaff);
	}
	
	function getCaffsBySite($site)
	{
		include("connexionBddErp.php");
		
		$listeCaff = array();
		
		$req = $bddErp->prepare("select id,t3.name_related, t3.mobile_phone, t3.work_email, t3.site, t3.agence,case when t3.reactive is null then 0 else t3.reactive end,
		case when t3.non_reactive is null then 0 else t3.non_reactive end from
		(
		select t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name as agence, sum(t2.reactive) as reactive, sum(t2.non_reactive) as non_reactive from (
		 
		select t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, case when account_analytic_account.name in ('Client', 'FO & CU') then count (ag_poi.id)
		end as reactive , case when account_analytic_account.name not in ('Client', 'FO & CU') then count (ag_poi.id) end as non_reactive
		from ag_poi
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id  
		full join
		(select hr_employee.id, hr_employee.name_related,hr_employee.mobile_phone,hr_employee.work_email,ag_site.name as site,ag_agence.name from res_users
		full join hr_employee on res_users.ag_employee_id = hr_employee.id
		full join ag_site on hr_employee.ag_site_id = ag_site.id
		full join ag_agence on hr_employee.ag_agence_id = ag_agence.id
		full join hr_job on hr_employee.job_id = hr_job.id
		where res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER')) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
		group by t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, account_analytic_account.name) t2
		group by t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name ) t3
		where name_related is not null AND site = ?");
		$req->execute(array($site));
		while($data = $req->fetch())
		{
			$caff = (object) array();
			$caff->id = $data["id"];
			$caff->name_related = $data["name_related"];
			$caff->mobile_phone = $data["mobile_phone"];
			$caff->work_email = $data["work_email"];
			$caff->site = $data["site"];
			$caff->agence = $data["agence"];
			$caff->reactive = $data["reactive"];
			$caff->non_reactive = $data["non_reactive"];
			
			array_push($listeCaff, $caff);
		}
		
		return json_encode($listeCaff);
	}
	
	function getPoiAffecteByCaff($caff)
	{
		include("connexionBdd.php");
		
		$listePoi = array();
		
		$req = $bdd->prepare("SELECT erp_poi_id FROM cds_affectation WHERE erp_caff_name = ?");
		$req->execute(array($caff));
		while($data = $req->fetch())
		{
			$poi = json_decode(getPoiById($data["erp_poi_id"]));
			array_push($listePoi, $poi);
		}
		return json_encode($listePoi);
	}
	
	function getPoiById($id)
	{
		include("connexionBddErp.php");
		
		$poi = null;
		$req = $bddErp->prepare("select
			ag_poi.id,
			ag_poi.ft_numero_oeie,
			account_analytic_account.name,
			ag_poi.ft_sous_justification_oeie,
			ag_poi.ft_oeie_dre,
			ag_poi.ft_commentaire_creation_oeie,
			hr_employee.name_related,
			ag_poi.\"ft_numero_demande_42C\" numero_demande,
			ag_poi.ft_numero_de_voie,
			ag_poi.ft_libelle_de_voie,
			ag_poi.ft_libelle_commune,
			ag_poi.insee_code,
			ag_poi.ft_latitude,
			ag_poi.ft_longitude,
			ag_poi.ft_etat,
			ag_poi.atr_ui,
			ag_poi.ft_pg,
			ag_poi.ft_date_creation_oeie,
			ag_poi.ft_date_limite_realisation,
			ag_poi.ft_numero_as,
			ag_poi.ft_date_creation_as,
			ag_poi.ft_commentaire_creation_as,
			ag_poi.ft_zone_as
			from ag_poi
		 
			left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
			left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
			where ag_poi.id = ?");
		$req->execute(array($id));
		if($data = $req->fetch())
		{
			$poi = (object) array();
			$poi->id = $data["id"];
			$poi->ft_numero_oeie = $data["ft_numero_oeie"];
			$poi->domaine = $data["name"];
			$poi->ft_sous_justification_oeie = $data["ft_sous_justification_oeie"];
			$poi->ft_oeie_dre = $data["ft_oeie_dre"];
			$poi->ft_commentaire_creation_oeie = $data["ft_commentaire_creation_oeie"];
			$poi->employee_name = $data["name_related"];
			$poi->ft_titulaire_client = $data["numero_demande"];
			$poi->ft_numero_de_voie = $data["ft_numero_de_voie"];
			$poi->ft_libelle_de_voie = $data["ft_libelle_de_voie"];
			$poi->ft_libelle_commune = $data["ft_libelle_commune"];
			$poi->insee_code = $data["insee_code"];
			$poi->ft_latitude = $data["ft_latitude"];
			$poi->ft_longitude = $data["ft_longitude"];
			$poi->ft_etat = $data["ft_etat"];
			$poi->atr_ui = $data["atr_ui"];
			$poi->ft_pg = $data["ft_pg"];
			$poi->ft_date_creation_oeie = $data["ft_date_creation_oeie"];
			$poi->ft_date_limite_realisation = $data["ft_date_limite_realisation"];
			$poi->ft_numero_as = $data["ft_numero_as"];
			$poi->ft_date_creation_as = $data["ft_date_creation_as"];
			$poi->ft_commentaire_creation_as = $data["ft_commentaire_creation_as"];
			$poi->ft_zone_as = $data["ft_zone_as"];
			if($poi->domaine == 'Client' || $poi->domaine == 'FO & CU')
			{
				$poi->reactive = true;
			}
			else{
				$poi->reactive = false;
			}
		}
		
		return json_encode($poi);
	}
	
	function getRandomColor()
	{
		$rbg = array();
		$rgb[0] = rand(100, 200);
		$rgb[1] = rand(100, 200);
		$rgb[2] = rand(100, 200);
		
		return json_encode($rgb);
	}
	
	function getSitesByUi($ui)
	{
		include("connexionBdd.php");
		
		$listeSites = array();
		$req = $bdd->prepare("SELECT site, erp_site_id, site_longitude, site_latitude FROM cds_transco_ui_site WHERE ft_zone = ?");
		$req->execute(array($ui));
		while($data = $req->fetch())
		{
			$site = (object) array();
			$site->libelle = $data["site"];
			$site->id = $data["erp_site_id"];
			$site->longitude = $data["site_longitude"];
			$site->latitude = $data["site_latitude"];
			
			array_push($listeSites, $site);
		}
		
		return json_encode($listeSites);
	}
	
	function getClosestSite($poi_id)
	{
		$key = "AIzaSyDKM2Ymk3PwN3uZVowTr7gLvyNVmROOD0E";
		
		$poi = json_decode(getPoiById($poi_id));
		$listeSite = json_decode(getSitesByUi($poi->atr_ui));
		$origins = $poi->ft_longitude.",".$poi->ft_latitude;
		$destinations = array();
		foreach($listeSite as $site)
		{
			array_push($destinations, $site->longitude . "," . $site->latitude);
		}
		$destinations = implode("|", $destinations);

		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);
		
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$origins."&destinations=".$destinations."&key=".$key;
		$listeDistances = json_decode(file_get_contents($url, false, stream_context_create($arrContextOptions)));
		$duree = null;
		$nb = 0;
		$i = 0;
		foreach($listeDistances->rows[0]->elements as $distance)
		{
			if($duree == null)
			{
				// $listeSite[$nb]->i = $i;
				$duree = $distance->duration->value;
				// $listeSite[$nb]->poilatlng = $destinations;
				// $listeSite[$nb]->sitelatlng = $origins;
				$listeSite[$nb]->duree = $distance->duration->text;
				$listeSite[$nb]->distance = $distance->distance->text;
			}
			else{
				if($distance->duration->value < $duree)
				{
					$duree = $distance->duration->value;
					$nb = $i;
					// $listeSite[$nb]->i = $i;
					// $listeSite[$nb]->poilatlong = $destinations;
					// $listeSite[$nb]->duree = $origins;
					$listeSite[$nb]->duree = $distance->duration->text;
					$listeSite[$nb]->distance = $distance->distance->text;
				}
			}
			
			$i++;
		}
		$closestSite = $listeSite[$nb];
		
		return json_encode($closestSite);
	}
	
	function getListClosestSites($poi_id)
	{
		$key = "AIzaSyDKM2Ymk3PwN3uZVowTr7gLvyNVmROOD0E";
		
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);
		
		$poi = json_decode(getPoiById($poi_id));
		$listeSite = json_decode(getSitesByUi($poi->atr_ui));
		$origins = $poi->ft_longitude.",".$poi->ft_latitude;
		
		foreach($listeSite as $site)
		{
			$destination = $site->longitude . "," . $site->latitude;
			$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$origins."&destinations=".$destination."&key=".$key;
			$site->distance = json_decode(file_get_contents($url, false, stream_context_create($arrContextOptions)))->rows[0]->elements[0]->distance->value;
		}
		
		function cmp($a, $b)
		{
			if($a->distance < $b->distance)
			{
				return -1;
			}
			elseif($a->distance == $b->distance)
			{
				return 0;
			}
			else{
				return 1;
			}
		}
		
		usort($listeSite, "cmp");
		
		return json_encode($listeSite);
	}

	function poiProxi($latitude, $longitude, $km)
	{
		include("connexionBddErp.php");
		$liste = array();
		
		$req = $bddErp->prepare("select ft_numero_oeie,ft_commentaire_creation_oeie,ft_libelle_commune,ag_poi.id,atr_caff_traitant_id, name_related,round(sqrt(power((ft_longitude - ?)/0.0090808,2)+power((ft_latitude - ?)/0.01339266,2))::numeric,1) as algo,account_analytic_account.name as domaine,hr_employee.mobile_phone from ag_poi
		left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
		where sqrt(power((ft_longitude - ?)/0.0090808,2)+power((ft_latitude - ?)/0.01339266,2)) < ? and ft_etat = '1' and name_related is not null
		order by algo");
		$req->execute(array($longitude, $latitude,$longitude, $latitude, $km));
		while($data = $req->fetch())
		{
			$obj = (object) array();
			$obj->oeie_id = $data["id"];
			$obj->oeie = $data["ft_numero_oeie"];
			$obj->caff_id = $data["atr_caff_traitant_id"];
			$obj->caff_name = $data["name_related"];
			$obj->algo = $data["algo"];
			$obj->ft_libelle_commune = $data["ft_libelle_commune"];
			$obj->domaine = $data["domaine"];
			$obj->mobile = $data["mobile_phone"];
			$obj->ft_commentaire_creation_oeie = $data['ft_commentaire_creation_oeie'];
			array_push($liste, $obj);
		}
		
		return json_encode($liste);

	}
	
	function nbPoiCaffByRadius($latitude, $longitude, $km)
	{
		include("connexionBddErp.php");
		$liste = array();
		
		$req = $bddErp->prepare("select atr_caff_traitant_id, name_related, count(ft_numero_oeie) nb from ag_poi
		left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
		where sqrt(power((ft_longitude - ?)/0.0090808,2)+power((ft_latitude - ?)/0.01339266,2)) < ? and ft_etat = '1' and name_related is not null
		group by name_related, atr_caff_traitant_id");
		$req->execute(array($longitude, $latitude, $km));
		while($data = $req->fetch())
		{
			$obj = (object) array();
			$obj->caff = (object) array();
			$obj->caff->id = $data["atr_caff_traitant_id"];
			$obj->caff->name = $data["name_related"];
			$obj->nbPoi = $data["nb"];
			array_push($liste, $obj);
		}
		
		return json_encode($liste);
	}
	
	function getNbPoiEnLien($commune, $voie, $titulaire, $ui)
	{
		include("connexionBddErp.php");
		$nbPoi = 0;
		
		$req = $bddErp->prepare("SELECT COUNT(*) nb FROM ag_poi WHERE ((\"ft_numero_demande_42C\" = ? AND \"ft_numero_demande_42C\" != '' AND \"ft_numero_demande_42C\" IS NOT NULL) OR (ft_etat = '1' AND ft_libelle_commune = ? AND ft_libelle_commune != '' AND ft_libelle_commune IS NOT NULL) OR (ft_etat = '1' AND ft_libelle_commune = ? AND ft_libelle_de_voie = ? AND ft_libelle_commune != '' AND ft_libelle_de_voie != '' AND ft_libelle_commune IS NOT NULL AND ft_libelle_de_voie IS NOT NULL)) AND (atr_ui = ? AND atr_ui != '' AND atr_ui IS NOT NULL) AND atr_caff_traitant_id IN( select test2.employee_id from res_users  
				left join (select hr_job.name as job,ag_site.name as site,hr_employee.id as employee_id,hr_employee.name_related as employee_name,test.name_related as mana_name from hr_employee
				left join ag_site on hr_employee.ag_site_id = ag_site.id
				left join hr_job on hr_employee.job_id = hr_job.id
			   left join (select id,name_related,parent_id from hr_employee) test on hr_employee.parent_id = test.id
			   order by test.name_related)test2 on res_users.ag_employee_id = test2.employee_id
			   where active is true and login not in ('admin','import_portefeuille','CONGES') and job in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER')
		)");
		$req->execute(array($titulaire, $commune, $commune, $voie, $ui));
		if($data = $req->fetch())
		{
			$nbPoi = $data["nb"];
		}
		return json_encode($nbPoi);
	}
	
	function getCaffsEnLienAvecPoiByTitulaire($titulaire, $ui)
	{
		include("connexionBddErp.php");
		include("connexionBdd.php");
		$listeCaffs = array();
		
		$req = $bddErp->prepare("SELECT p.atr_caff_traitant_id, e.name_related, COUNT(p.atr_caff_traitant_id) nb, e.ag_site_id 
		FROM ag_poi p 
		JOIN hr_employee e 
		ON p.atr_caff_traitant_id = e.id 
		WHERE p.\"ft_numero_demande_42C\" = ?
		AND p.\"ft_numero_demande_42C\" != ''
		AND p.atr_ui = ?
		AND p.\"ft_numero_demande_42C\" IS NOT NULL
		AND p.atr_caff_traitant_id IN( select test2.employee_id from res_users  
				left join (select hr_job.name as job,ag_site.name as site,hr_employee.id as employee_id,hr_employee.name_related as employee_name,test.name_related as mana_name from hr_employee
				left join ag_site on hr_employee.ag_site_id = ag_site.id
				left join hr_job on hr_employee.job_id = hr_job.id
			   left join (select id,name_related,parent_id from hr_employee) test on hr_employee.parent_id = test.id
			   order by test.name_related)test2 on res_users.ag_employee_id = test2.employee_id
			   where active is true and login not in ('admin','import_portefeuille','CONGES') and job in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER')
		)
		GROUP BY p.atr_caff_traitant_id, e.name_related, e.ag_site_id
		ORDER BY nb DESC");
		$req->execute(array($titulaire, $ui));
		while($data = $req->fetch())
		{
			$req2 = $bdd->prepare("SELECT ft_zone FROM cds_transco_ui_site WHERE erp_site_id = ?");
			$req2->execute(array($data["ag_site_id"]));
			while($data2 = $req2->fetch())
			{
				$caff = (object) array();
				$caff->id = $data["atr_caff_traitant_id"];
				$caff->name = $data["name_related"];
				$caff->nb_poi = $data["nb"];
				$caff->site_id = $data["ag_site_id"];
				if($data2["ft_zone"] == $ui)
				{
					$caff->entraide = false;
				}
				else{
					$caff->entraide = true;
				}
				array_push($listeCaffs, $caff);
			}
		}
		
		return json_encode($listeCaffs);
	}
	
	function getCaffsEnLienAvecPoiByVoie($voie, $commune, $ui)
	{
		include("connexionBddErp.php");
		include("connexionBdd.php");
		$listeCaffs = array();
		
		$req = $bddErp->prepare("SELECT p.atr_caff_traitant_id, e.name_related, COUNT(p.ft_libelle_de_voie) nb, e.ag_site_id 
		FROM ag_poi p 
		JOIN hr_employee e 
		ON p.atr_caff_traitant_id = e.id 
		WHERE p.ft_libelle_de_voie = ? and p.ft_etat = '1'
		AND p.ft_libelle_de_voie != ''
		AND p.ft_libelle_de_voie IS NOT NULL
		AND p.ft_libelle_commune = ?
		AND p.ft_libelle_commune != ''
		AND p.ft_libelle_commune IS NOT NULL
		AND p.atr_caff_traitant_id IN( select test2.employee_id from res_users  
				left join (select hr_job.name as job,ag_site.name as site,hr_employee.id as employee_id,hr_employee.name_related as employee_name,test.name_related as mana_name from hr_employee
				left join ag_site on hr_employee.ag_site_id = ag_site.id
				left join hr_job on hr_employee.job_id = hr_job.id
			   left join (select id,name_related,parent_id from hr_employee) test on hr_employee.parent_id = test.id
			   order by test.name_related)test2 on res_users.ag_employee_id = test2.employee_id
			   where active is true and login not in ('admin','import_portefeuille','CONGES') and job in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER')
		)
		GROUP BY p.atr_caff_traitant_id, e.name_related, e.ag_site_id
		ORDER BY nb DESC");
		$req->execute(array($voie, $commune));
		while($data = $req->fetch())
		{
			$req2 = $bdd->prepare("SELECT ft_zone FROM cds_transco_ui_site WHERE erp_site_id = ?");
			$req2->execute(array($data["ag_site_id"]));
			while($data2 = $req2->fetch())
			{
				$caff = (object) array();
				$caff->id = $data["atr_caff_traitant_id"];
				$caff->name = $data["name_related"];
				$caff->nb_poi = $data["nb"];
				$caff->site_id = $data["ag_site_id"];
				if($data2["ft_zone"] == $ui)
				{
					$caff->entraide = false;
				}
				else{
					$caff->entraide = true;
				}
				array_push($listeCaffs, $caff);
			}
		}
		
		return json_encode($listeCaffs);
	}
	
	function getCaffsEnLienAvecPoiByCommune($commune, $ui)
	{
		include("connexionBddErp.php");
		include("connexionBdd.php");
		$listeCaffs = array();
		
		$req = $bddErp->prepare("SELECT p.atr_caff_traitant_id, e.name_related, COUNT(p.ft_libelle_commune) nb, e.ag_site_id 
		FROM ag_poi p 
		JOIN hr_employee e 
		ON p.atr_caff_traitant_id = e.id 
		WHERE p.ft_libelle_commune = ? and p.ft_etat = '1'
		AND p.ft_libelle_commune != ''
		AND p.ft_libelle_commune IS NOT NULL
		AND p.atr_caff_traitant_id IN( select test2.employee_id from res_users  
				left join (select hr_job.name as job,ag_site.name as site,hr_employee.id as employee_id,hr_employee.name_related as employee_name,test.name_related as mana_name from hr_employee
				left join ag_site on hr_employee.ag_site_id = ag_site.id
				left join hr_job on hr_employee.job_id = hr_job.id
			   left join (select id,name_related,parent_id from hr_employee) test on hr_employee.parent_id = test.id
			   order by test.name_related)test2 on res_users.ag_employee_id = test2.employee_id
			   where active is true and login not in ('admin','import_portefeuille','CONGES') and job in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER')
		)
		GROUP BY p.atr_caff_traitant_id, e.name_related, e.ag_site_id
		ORDER BY nb DESC");
		$req->execute(array($commune));
		while($data = $req->fetch())
		{
			$req2 = $bdd->prepare("SELECT ft_zone FROM cds_transco_ui_site WHERE erp_site_id = ?");
			$req2->execute(array($data["ag_site_id"]));
			while($data2 = $req2->fetch())
			{
				$caff = (object) array();
				$caff->id = $data["atr_caff_traitant_id"];
				$caff->name = $data["name_related"];
				$caff->nb_poi = $data["nb"];
				$caff->site_id = $data["ag_site_id"];
				if($data2["ft_zone"] == $ui)
				{
					$caff->entraide = false;
				}
				else{
					$caff->entraide = true;
				}
				array_push($listeCaffs, $caff);
			}
		}
		
		return json_encode($listeCaffs);
	}
	
	function getListePoiByCaffByTitulaire($idCaff, $titulaire, $ui)
	{
		include("connexionBddErp.php");
		
		$listePoi = array();
		$req = $bddErp->prepare("SELECT id FROM ag_poi WHERE atr_caff_traitant_id = ? AND \"ft_numero_demande_42C\" = ? AND \"ft_numero_demande_42C\" != '' AND \"ft_numero_demande_42C\" IS NOT NULL AND atr_ui = ? ORDER BY ft_oeie_dre");
		$req->execute(array($idCaff, $titulaire, $ui));
		while($data = $req->fetch())
		{
			$poi = json_decode(getPoiById($data["id"]));
			array_push($listePoi, $poi);
		}
		
		return json_encode($listePoi);
	}
	
	function getListePoiByCaffByVoie($idCaff, $voie, $commune)
	{
		include("connexionBddErp.php");
		
		$listePoi = array();
		$req = $bddErp->prepare("SELECT id FROM ag_poi WHERE ft_etat = '1' AND atr_caff_traitant_id = ? AND ft_libelle_de_voie = ? AND ft_libelle_commune = ? AND ft_libelle_de_voie != '' AND ft_libelle_de_voie IS NOT NULL AND ft_libelle_commune != '' AND ft_libelle_commune IS NOT NULL ORDER BY ft_oeie_dre");
		$req->execute(array($idCaff, $voie, $commune));
		while($data = $req->fetch())
		{
			$poi = json_decode(getPoiById($data["id"]));
			array_push($listePoi, $poi);
		}
		
		return json_encode($listePoi);
	}
	
	function getListePoiByCaffByCommune($idCaff, $commune)
	{
		include("connexionBddErp.php");
		
		$listePoi = array();
		$req = $bddErp->prepare("SELECT id FROM ag_poi WHERE ft_etat = '1' AND atr_caff_traitant_id = ? AND ft_libelle_commune = ? AND ft_libelle_commune != '' AND ft_libelle_commune IS NOT NULL ORDER BY ft_oeie_dre");
		$req->execute(array($idCaff, $commune));
		while($data = $req->fetch())
		{
			$poi = json_decode(getPoiById($data["id"]));
			array_push($listePoi, $poi);
		}
		
		return json_encode($listePoi);
	}
	
	function getSites()
	{
		include("connexionBdd.php");
		$sites = array();
		$req = $bdd->query("SELECT distinct site, erp_site_id FROM cds_transco_ui_site ORDER BY site");
		while($data = $req->fetch())
		{
			$site = (object) array();
			$site->libelle = $data["site"];
			$site->id = $data["erp_site_id"];
			array_push($sites, $site);
		}
		return json_encode($sites);
	}
	
	function getIdCaffByName($name)
	{
		include("connexionBddErp.php");
		$idCaff = null;
		$req = $bddErp->prepare("SELECT id FROM hr_employee WHERE name_related = ?");
		$req->execute(array($name));
	}
	
	function getUi()
	{
		include("connexionBddErp.php");
		include("connexionBdd.php");
		$listeUi = array();
		$req = $bddErp->query("SELECT DISTINCT atr_ui FROM ag_poi WHERE atr_ui IS NOT NULL AND atr_ui != ''");
		while($data = $req->fetch())
		{
			$ui = (object) array();
			$ui->ft_zone = $data["atr_ui"];
			
			$req2 = $bdd->prepare("SELECT ui FROM cds_transco_ui_site WHERE ft_zone = ?");
			$req2->execute(array($data["atr_ui"]));
			if($data2 = $req2->fetch())
			{
				$ui->libelle = substr($data2["ui"], 3);
				if($ui->libelle == "Provence Cote D'Azur")
				{
					$ui->diminutif = "PCA";
				}
				elseif($ui->libelle == "Midi Pyrennees")
				{
					$ui->diminutif = "MPY";
				}
				else{
					$ui->diminutif = strtoupper(substr($ui->libelle, 0, 3));
				}
			}
			array_push($listeUi, $ui);
		}
		return json_encode($listeUi);
	}
	
	function getListIdEmployesConges()
	{
		include("connexionBddErp.php");
		$listeEmployes = array();
		
		$req = $bddErp->query("SELECT employee_id FROM hr_holidays WHERE date_to >= NOW() AND date_from <= NOW()");
		while($data = $req->fetch())
		{
			array_push($listeEmployes, $data["employee_id"]);
		}
		return json_encode($listeEmployes);
	}
	
	function getUiBySite($site)
	{
		include("connexionBdd.php");
		$ui = null;
		$req = $bdd->prepare("SELECT ui, ft_zone FROM cds_transco_ui_site WHERE site = ?");
		$req->execute(array($site));
		if($data = $req->fetch())
		{
			$ui = (object) array();
			$ui->libelle = $data["ui"];
			$ui->ft_zone = $data["ft_zone"];
		}
		return json_encode($ui);
	}
	
	function getImageByCaff($idCaff)
	{
		include("connexionBddErp.php");
		$image = null;
		$req = $bddErp->prepare("SELECT image FROM hr_employee WHERE id = ?");
		$req->execute(array($idCaff));
		if($data = $req->fetch())
		{
			$image = "data:image/jpeg;base64, ".stream_get_contents($data['image']);
		}
		return $image;
	}

	function getConfigById($id){
		include("connexionBdd.php");
		
		//configuration par défaut
		$config = (object) array();
        $config->filtercolorurgent = 'red';
        $config->filtercolorclient = 'orange';
        $config->filtercolorimmo = 'yellow';
        $config->filtercolordissi = 'green';
        $config->filtercolorfocu = 'blue';
		$config->filtercolorcoord = 'purple';
		$config->filtercolorfors = 'pink';
        $config->filterdre = 1;
        $config->filtersj = array();
		$config = json_encode($config);
		
		$req = $bdd->prepare("SELECT * FROM cds_config WHERE caff_id = ?");
		$req->execute(array($id));
		if($data = $req->fetch())
		{
			$config = $data["config"];
		}
		return json_encode($config);
	}
	
	function addConfigById($id,$json_code){
		include("connexionBdd.php");
		$reponse = false;
		try{
			$req = $bdd->prepare("SELECT id FROM cds_config WHERE caff_id = ?");
			$req->execute(array($id));
			if($data = $req->fetch())
			{
				$req = $bdd->prepare("UPDATE cds_config SET config = ? WHERE caff_id = ?");
				$reponse = $req->execute(array($json_code,$id));
			}
			else{
				$req = $bdd->prepare("INSERT INTO cds_config(config, caff_id) VALUES(?, ?)");
				$reponse = $req->execute(array($json_code,$id));
			}
		}catch(Exception $e){
			$reponse = false;
		}
		return json_encode($reponse);
	}
	function changeAdvancedConfig($json_code,$ui){
		include("connexionBdd.php");
		$reponse = false;
		if($ui=="defaut"){
			try{
				$req = $bdd->prepare("UPDATE cds_advanced_config SET config = ? WHERE ui is null");
				$reponse = $req->execute(array($json_code));
				/*$req = $bdd->prepare("SELECT id FROM cds_advanced_config WHERE id = 1");
				$req->execute(array($id));
				if($data = $req->fetch())
				{
					$req = $bdd->prepare("UPDATE cds_advanced_config SET config = ? WHERE id = 1");
					$reponse = $req->execute(array($json_code));
				}
				else{
					$req = $bdd->prepare("UPDATE cds_advanced_config SET config = ? WHERE id = 1");
					$reponse = $req->execute(array($json_code));
				}*/
			}catch(Exception $e){
				$reponse = false;
			}
		}
		else{
			try{
				$req = $bdd->prepare("UPDATE cds_advanced_config SET config = ? WHERE ui = ?");
				$reponse = $req->execute(array($json_code,$ui));
				/*$req = $bdd->prepare("SELECT id FROM cds_advanced_config WHERE id = 1");
				$req->execute(array($id));
				if($data = $req->fetch())
				{
					$req = $bdd->prepare("UPDATE cds_advanced_config SET config = ? WHERE id = 1");
					$reponse = $req->execute(array($json_code));
				}
				else{
					$req = $bdd->prepare("UPDATE cds_advanced_config SET config = ? WHERE id = 1");
					$reponse = $req->execute(array($json_code));
				}*/
			}catch(Exception $e){
				$reponse = false;
			}
		}
		
		return json_encode($reponse);
	}

	function getAdvancedConfig($ui)
	{
		include("connexionBdd.php");
		$config = json_encode(null);
		if(!isset($ui) || $ui == null || $ui == '')
		{
			$req = $bdd->query("SELECT config FROM cds_advanced_config WHERE ui is null");
		}
		else
		{
			$req = $bdd->prepare("SELECT config FROM cds_advanced_config WHERE ui = ?");
			$req->execute(array($ui));
		}
		if($data = $req->fetch())
		{
			$config = $data["config"];
		}
		return $config;
	}
	
	function getChargeCaff($caff, $coef) //$caff = object caff en json
	{
		$caff = json_decode($caff);
		//$coef = 0.1;
		$charge = intval($caff->reactive) + (intval($caff->non_reactive) * $coef);
		return json_encode($charge);
	}
	
	function getNbPoiProximiteByCaffByPoi($idPoi, $idCaff, $km) //$caff = json
	{
		include("connexionBddErp.php");
		$poi = json_decode(getPoiById($idPoi));
		
		$nbPoi = 0;
		$req = $bddErp->prepare("SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = ? AND sqrt(power((ft_longitude - ?)/0.0090808,2)+power((ft_latitude - ?)/0.01339266,2)) < ? AND ft_etat = '1'");
		$req->execute(array($idCaff, $poi->ft_longitude, $poi->ft_latitude, $km));
		if($data = $req->fetch())
		{
			$nbPoi = $data["nb"];
		}
		return json_encode($nbPoi);
	}
	
	function getNbPoiClientByCaff($idCaff, $client, $ui)
	{
		include("connexionBddErp.php");
		$nbPoi = 0;
		$req = $bddErp->prepare("SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = ? AND \"ft_numero_demande_42C\" = ? AND \"ft_numero_demande_42C\" IS NOT NULL AND \"ft_numero_demande_42C\" != '' AND \"ft_numero_demande_42C\" != 'suppr. CNIL' AND atr_ui = ?");
		$req->execute(array($idCaff, $client, $ui));
		if($data = $req->fetch())
		{
			$nbPoi = $data["nb"];;
		}
		return json_encode($nbPoi);
	}
	
	function getCaffById($id)
	{
		include("connexionBddErp.php");
		
		$caff = null;
		$req = $bddErp->prepare("select id,t3.name_related, t3.mobile_phone, t3.work_email, t3.site, t3.agence,case when t3.reactive is null then 0 else t3.reactive end,
		case when t3.non_reactive is null then 0 else t3.non_reactive end from
		(
		select t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name as agence, sum(t2.reactive) as reactive, sum(t2.non_reactive) as non_reactive from (
		 
		select t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, case when account_analytic_account.name in ('Client', 'FO & CU') then count (ag_poi.id)
		end as reactive , case when account_analytic_account.name not in ('Client', 'FO & CU') then count (ag_poi.id) end as non_reactive
		from ag_poi
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id  
		full join
		(select hr_employee.id, hr_employee.name_related,hr_employee.mobile_phone,hr_employee.work_email,ag_site.name as site,ag_agence.name from res_users
		full join hr_employee on res_users.ag_employee_id = hr_employee.id
		full join ag_site on hr_employee.ag_site_id = ag_site.id
		full join ag_agence on hr_employee.ag_agence_id = ag_agence.id
		full join hr_job on hr_employee.job_id = hr_job.id
		where res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER')) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
		group by t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, account_analytic_account.name) t2
		group by t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name ) t3
		WHERE id = ?");
		$req->execute(array($id));
		if($data = $req->fetch())
		{
			$caff = (object) array();
			$caff->id = $data["id"];
			$caff->name_related = $data["name_related"];
			$caff->mobile_phone = $data["mobile_phone"];
			$caff->work_email = $data["work_email"];
			$caff->site = $data["site"];
			$caff->agence = $data["agence"];
			$caff->reactive = $data["reactive"];
			$caff->non_reactive = $data["non_reactive"];
		}
		return json_encode($caff);
	}

	function getListeSitesByUi($ui) //$ui --> JR4, FC4...
	{
		include("connexionBdd.php");
		$liste = array();
		$req = $bdd->prepare("SELECT erp_site_id FROM cds_transco_ui_site WHERE ft_zone = ?");
		$req->execute(array($ui));
		while($data = $req->fetch())
		{
			array_push($liste, $data["erp_site_id"]);
		}
		return json_encode($liste);
	}

	function getListeIdCaffEntraideByUiAndDomaine($ui, $domaine)
	{
		include("connexionBdd.php");
		$liste = array();
		$listeIdsSites = json_decode(getListeSitesByUi($ui));
		$listeIdsSites = implode(',', $listeIdsSites);
		$req = $bdd->query("SELECT id, caff_id, site_entraide_id, taux FROM cds_entraide WHERE site_entraide_id IN(".$listeIdsSites.") AND date_debut <= current_date AND date_expiration >= current_date");
		while($data = $req->fetch())
		{
			$req2 = $bdd->prepare("SELECT domaines FROM cds_entraide WHERE id = ?");
			$req2->execute(array($data["id"]));
			if($data2 = $req2->fetch())
			{
				$domainesCaff = json_decode($data2["domaines"]);
				foreach($domainesCaff as $dom)
				{
					if(strtoupper($dom) == strtoupper($domaine))
					{
						$entraide = (object) array();
						$entraide->caff_id = $data["caff_id"];
						$entraide->site_entraide_id = $data["site_entraide_id"];
						$entraide->taux = $data["taux"];
						array_push($liste, $entraide);
					}
				}
			}
		}
		return json_encode($liste);
	}

	function getTauxCaffEntraideByCaffIdAndSiteId($caff_id, $site_id)
	{
		include("connexionBdd.php");

		$taux = null;

		$req = $bdd->prepare("SELECT taux FROM cds_entraide WHERE caff_id = ? AND site_entraide_id = ? AND date_debut <= current_date AND date_expiration >= current_date");
		$req->execute(array($caff_id, $site_id));
		if($data = $req->fetch())
		{
			$taux = $data["taux"];
		}

		return json_encode($taux);
	}
	
	function getListeIdCaffEntraideExclureUi($ui)
	{
		include("connexionBdd.php");
		$liste = array();
		$listeIdsSites = json_decode(getListeSitesByUi($ui));
		$listeIdsSites = implode(',', $listeIdsSites);
		$req = $bdd->query("SELECT caff_id FROM cds_entraide WHERE site_defaut_id IN(".$listeIdsSites.") AND date_debut <= current_date AND date_expiration >= current_date");
		while($data = $req->fetch())
		{
			array_push($liste, $data["caff_id"]);
		}
		return json_encode($liste);
	}
	
	// function getAffectationAuto($idPoi, $km, $coefNbPoiProimite, $coefChargeReactive, $coefCharge, $limiteJour, $limiteSemaine, $limiteMaxCalcul, $nbJoursAvantConges, $nbJoursConges)//, $listeCaffsSimulation) // $listeCaffsSimulation (facultatif) = array en json
	// {
	// 	include("connexionBddErp.php");
	// 	include("connexionBdd.php");
	// 	include("connexionBddMailAuto.php");
		
	// 	$caffAuto = null;
		
	// 	//$listeCaffsSimulation = json_decode($listeCaffsSimulation);
		
	// 	/*$coefNbPoiProimite = 0.5;
	// 	$coefNbPoiClient = 0.8;
	// 	$coefCharge = 0.5;*/
		
	// 	$listePoiBleues = array();
    //     $req = $bddMail->query("select poi from relance where date_expiration >= NOW()");
    //     while($data = $req->fetch())
    //     {
    //         array_push($listePoiBleues, $data["poi"]);
    //     }
        
    //     $listePoiBleues = implode(", ", $listePoiBleues);
		
	// 	$poi = json_decode(getPoiById($idPoi));
	// 	if($poi->domaine == "Focu" || $poi->domaine == "FO & CU")
	// 	{
	// 		$competence = "Fo & Cu";
	// 	}
	// 	else{
	// 		$competence = $poi->domaine;
	// 	}
		
	// 	$listeSites = json_decode(getSitesByUi($poi->atr_ui));
		
	// 	$listeIdSites = array();
	// 	foreach($listeSites as $site)
	// 	{
	// 		array_push($listeIdSites, $site->id);
	// 	}
	// 	$listeIdSites = implode(", ", $listeIdSites);
		
	// 	$listeCaffs = array();
		
	// 	$caffTitulaireAuto = false; //Pour savoir si un caff est affilié ou non au titulaire de la poi
		
	// 	$globalDebut = "select atr.atr_caff_traitant_id, atr.atr_sous_domaine_id,atr.id,atr.atr_ui,atr.partner,atr.ft_numero_oeie,atr.ft_oeie_dre,atr.name as domaine,account_analytic_account.name as sous_domaine,atr.ft_pg, CASE WHEN LENGTH(ft_sous_justification_oeie) = 2 THEN ft_sous_justification_oeie ELSE 'Pas de SJ' END AS ft_sous_justification_oeie, atr.ft_libelle_commune,atr.ft_libelle_de_voie,atr.name_related,atr.work_email,atr.mobile_phone,atr.ft_commentaire_creation_oeie from(
    //             select ag_poi.atr_caff_traitant_id, ag_poi.atr_sous_domaine_id,ag_poi.id,ag_poi.atr_ui,res_partner.name as partner,ag_poi.ft_oeie_dre,ag_poi.ft_numero_oeie,account_analytic_account.name,ag_poi.ft_pg,ag_poi.ft_sous_justification_oeie,ag_poi.ft_libelle_commune,ag_poi.ft_libelle_de_voie,hr_employee.name_related,hr_employee.work_email,hr_employee.mobile_phone,ag_poi.ft_commentaire_creation_oeie from ag_poi
    //             left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
    //             left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
    //             left join res_partner on ag_poi.res_partner_id = res_partner.id
    //             where ft_etat = '1' and name_related is not null and ag_poi.atr_caff_traitant_id = ";
	// 	//idCaff entre
	// 	$globalFin = " and work_email is not null and ag_poi.ft_numero_oeie not like '%MBB%')atr
    //             left join account_analytic_account on atr.atr_sous_domaine_id = account_analytic_account.id
    //             order by ft_oeie_dre";
		
	// 	if($listePoiBleues != null && $listePoiBleues != "")
    //     {
    //         /*$requeteRetard = "SELECT (dre_ko / (dre_ko + dre_ok)) retard (select atr_caff_traitant_id,count(dre_ko) as dre_ko,count(dre_ok) as dre_ok from(select atr_caff_traitant_id,ft_oeie_dre,case when (ft_oeie_dre IS NULL OR ft_oeie_dre <= NOW()) and id not in (".$listePoiBleues.") then 1 end as dre_ko,case when ft_oeie_dre > NOW() or id in (".$listePoiBleues.") then 1 end as dre_ok from (".$global.")dre)dre2 group by atr_caff_traitant_id)";*/
	// 		$debRequete = "SELECT (dre_ko / (dre_ko + dre_ok)) retard (select atr_caff_traitant_id,count(dre_ko) as dre_ko,count(dre_ok) as dre_ok from(select atr_caff_traitant_id,ft_oeie_dre,case when (ft_oeie_dre IS NULL OR ft_oeie_dre <= NOW()) and id not in (".$listePoiBleues.") then 1 end as dre_ko,case when ft_oeie_dre > NOW() or id in (".$listePoiBleues.") then 1 end as dre_ok from (";
	// 		//global entre
	// 		$finRequete = ")dre)dre2 group by atr_caff_traitant_id)";
    //     }
    //     else{
    //         /*$requeteRetard = "SELECT (dre_ko / (dre_ko + dre_ok)) retard (select atr_caff_traitant_id,count(dre_ko) as dre_ko,count(dre_ok) as dre_ok from(select atr_caff_traitant_id,ft_oeie_dre,case when (ft_oeie_dre IS NULL OR ft_oeie_dre <= NOW()) then 1 end as dre_ko,case when ft_oeie_dre > NOW() then 1 end as dre_ok from (".$global.")dre)dre2 group by atr_caff_traitant_id)";*/
	// 		$debRequete = "SELECT (dre_ko / (dre_ko + dre_ok)) retard (select atr_caff_traitant_id,count(dre_ko) as dre_ko,count(dre_ok) as dre_ok from(select atr_caff_traitant_id,ft_oeie_dre,case when (ft_oeie_dre IS NULL OR ft_oeie_dre <= NOW()) then 1 end as dre_ko,case when ft_oeie_dre > NOW() then 1 end as dre_ok from (";
	// 		//global entre
	// 		$finRequete = ")dre)dre2 group by atr_caff_traitant_id)";
    //     }
		
	// 	$cesCaffs = array();
	// 	$listeCaffsUi = json_decode(getListeIdCaffEntraideByUiAndDomaine($poi->atr_ui, $poi->domaine));
	// 	if(sizeof($listeCaffsUi) > 0)
	// 	{
	// 		$listeCaffsUi = implode(",", $listeCaffsUi);
	// 	}
	// 	else{
	// 		$listeCaffsUi = "0";
	// 	}
	// 	$listeCaffsExclure = json_decode(getListeIdCaffEntraideExclureUi($poi->atr_ui));
	// 	if(sizeof($listeCaffsExclure) > 0)
	// 	{
	// 		$listeCaffsExclure = implode(",", $listeCaffsExclure);
	// 	}
	// 	else{
	// 		$listeCaffsExclure = "0";
	// 	}
		
	// 	$req = $bddErp->query("SELECT id, name_related, mobile_phone, work_email, site, site_id, agence, reactive, non_reactive, (((reactive * ".$coefChargeReactive.") + (non_reactive * ".$coefCharge.")) * (1 / caff.ag_coeff_traitement)) charge_initiale, (CASE WHEN ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.") > ".$limiteMaxCalcul." THEN ".$limiteMaxCalcul." ELSE ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.") END)charge_rayon, ((((reactive * ".$coefChargeReactive.") + (non_reactive * ".$coefCharge.")) * (1 / caff.ag_coeff_traitement))
    //     - CASE WHEN ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.") > ".$limiteMaxCalcul." THEN ".$limiteMaxCalcul." ELSE ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.") END
    //     )charge_totale 
	// 	FROM (select id, t3.ag_coeff_traitement, t3.name_related, t3.mobile_phone, t3.work_email, t3.site, t3.site_id, t3.agence,case when t3.reactive is null then 0 else t3.reactive end,
	// 	case when t3.non_reactive is null then 0 else t3.non_reactive end from
	// 	(
	// 	select t2.id, t2.ag_coeff_traitement, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.site_id, t2.name as agence, sum(t2.reactive) as reactive, sum(t2.non_reactive) as non_reactive from (
		 
	// 	select t1.id, t1.ag_coeff_traitement, t1.name_related,t1.mobile_phone,t1.work_email,t1.site, t1.site_id,t1.name, case when account_analytic_account.name in ('Client', 'FO & CU') then count (ag_poi.id)
	// 	end as reactive , case when account_analytic_account.name not in ('Client', 'FO & CU') then count (ag_poi.id) end as non_reactive
	// 	from ag_poi
	// 	left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id  
	// 	full join
	// 	(select hr_employee.id, hr_employee.ag_coeff_traitement, hr_employee.name_related,hr_employee.mobile_phone,hr_employee.work_email,ag_site.name as site, ag_site.id as site_id,ag_agence.name from res_users
	// 	full join hr_employee on res_users.ag_employee_id = hr_employee.id
	// 	full join ag_site on hr_employee.ag_site_id = ag_site.id
	// 	full join ag_agence on hr_employee.ag_agence_id = ag_agence.id
	// 	full join hr_job on hr_employee.job_id = hr_job.id
	// 	FULL JOIN m2m__hr_employee__ag_competence cmp ON cmp.employee_id = hr_employee.id
	// 	FULL JOIN ag_competence ON ag_competence.id = cmp.competence_id
	// 	where ((res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER') and ag_competence.name = '".$competence."') OR hr_employee.id IN(".$listeCaffsUi.")) AND hr_employee.id NOT IN(".$listeCaffsExclure.")) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
	// 	group by t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, account_analytic_account.name, t1.site_id, t1.ag_coeff_traitement) t2
	// 	group by t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name, t2.site_id, t2.ag_coeff_traitement ) t3
	// 	where ((name_related is not null AND site_id IN(".$listeIdSites.")) OR id IN(".$listeCaffsUi.")) AND id NOT IN(".$listeCaffsExclure."))caff
	// 	ORDER BY charge_totale");
	// 	while($data = $req->fetch())
	// 	{
	// 		$req2 = $bdd->prepare("SELECT id FROM cds_formation WHERE caff_id = ?");
	// 		$req2->execute(array($data["id"]));
	// 		if(!$data2 = $req2->fetch())
	// 		{
	// 			$enConges = false; // = en conges actuellement ou bientot en conges (moins de 5 jours)
				
	// 			if(strtoupper($poi->domaine) == "FO & CU" || strtoupper($poi->domaine) == "CLIENT")
	// 			{
	// 				$conges = json_decode(getProchainesConges($data["id"]));
	// 				if($conges != null)
	// 				{
	// 					if($conges->temps_avant <= strtotime(60*60*24*$nbJoursAvantConges) && $conges->nbJoursCongesRestant >= $nbJoursConges) // Si le caff est en congé dans moins de 5 jours et que ces congés durent au moins 5 jours (sans compter les week_end) ou si le caff est actuellement en congés et que le nombre de jours de conges restant est d'au moins 5 jours
	// 					{
	// 						$enConges = true;
	// 					}
	// 				}
	// 				if(!$enConges)
	// 				{
	// 					$enConges = false;
	// 					if(array_search($data["id"], json_decode(getListIdEmployesConges())) != false)
	// 					{
	// 						$enConges = true;
	// 					}
	// 				}
	// 			}
				
	// 			$caff = (object) array();
	// 			$caff->name_related = $data["name_related"];
	// 			$caff->mobile_phone = $data["mobile_phone"];
	// 			$caff->work_email = $data["work_email"];
	// 			$caff->site = $data["site"];
	// 			$caff->site_id = $data["site_id"];
	// 			$caff->agence = $data["agence"];
	// 			$caff->reactive = $data["reactive"];
	// 			$caff->non_reactive = $data["non_reactive"];
	// 			$caff->charge_rayon = $data["charge_rayon"];
	// 			$caff->charge_totale = $data["charge_totale"];
	// 			$caff->tauxRetard = (json_decode(getStatsCaff($data["id"])) * $caff->reactive * $coefChargeReactive);
	// 			$caff->charge_totale += $caff->tauxRetard;
	// 			$caff->charge_initiale = $data["charge_initiale"];
	// 			$caff->id = $data["id"];
				
	// 			$listePoi = json_decode(getPoiAffecteByCaff($caff->name_related));
	// 			/*$nbPoiEnRetard = 0;
	// 			$dateAjd = new DateTime("now");
	// 			foreach($listePoi as $lPoi)
	// 			{
	// 				$dre = new DateTime($lPoi->ft_oeie_dre);
	// 				if($dateAjd > $dre)
	// 				{
	// 					$nbPoiEnRetard++;
	// 				}
	// 			}
	// 			if(sizeof($listePoi) > 0)
	// 			{
	// 				$tauxDre = $nbPoiEnRetard / sizeof($listePoi);
	// 			}
	// 			else{
	// 				$tauxDre = 0;
	// 			}
	// 			$caff->charge_totale += ($tauxDre * $caff->reactive);*/

	// 			$listeCaffsTitulaireAffectationAuto = array();
	// 			$listeCaffsTitulaireAffectationAutoNot1 = array();
	// 			$listeCaffsTitulaire = json_decode(getCaffsEnLienAvecPoiByTitulaire($poi->ft_titulaire_client, $poi->atr_ui));
	// 			foreach($listeCaffsTitulaire as $caffTitulaire)
	// 			{
	// 				if($caffTitulaire->id == $data["id"])
	// 				{
	// 					$caffTitulaireAuto = true;

	// 					$listePoiTitulaire = array();
	// 					$poiTitulaireEtat1 = false;
	// 					foreach($listePoi as $cettePoi)
	// 					{
	// 						if($cettePoi->ft_titulaire_client == $poi->ft_titulaire_client)
	// 						{
	// 							if($cettePoi->ft_etat == 1)
	// 							{
	// 								$poiTitulaireEtat1 = true;
	// 							}
	// 							array_push($listePoiTitulaire, $cettePoi);
	// 						}
	// 					}
	// 					$caff->listePoiTitulaire = $listePoiTitulaire;
	// 					array_push($listeCaffsTitulaireAffectationAutoNot1, $caff);
	// 					if($poiTitulaireEtat1)
	// 					{
	// 						array_push($listeCaffsTitulaireAffectationAuto, $caff);
	// 						if(sizeof($listeCaffsTitulaireAffectationAuto) == 1)
	// 						{
	// 							$caffAuto = $caff;
	// 						}
	// 						else{
	// 							$poiTituCaff = null;
	// 							foreach($listeCaffsTitulaireAffectationAuto as $caffTitulaireAffectationAuto)
	// 							{
	// 								foreach($caffTitulaireAffectationAuto->listePoiTitulaire as $poiTitu)
	// 								{
	// 									if($poiTituCaff == null)
	// 									{
	// 										$poiTituCaff = (object) array();
	// 										$poiTituCaff->caff = $caffTitulaireAffectationAuto;
	// 										$poiTituCaff->poi = $poiTitu;
	// 									}
	// 									else{
	// 										if($poiTitu->ft_date_creation_oeie < $poiTituCaff->poi->ft_date_creation_oeie)
	// 										{
	// 											$poiTituCaff->caff = $caffTitulaireAffectationAuto;
	// 											$poiTituCaff->poi = $poiTitu;
	// 										}
	// 									}
	// 								}
	// 							}
	// 							$caffAuto = $poiTituCaff->caff;
	// 						}
	// 					}
	// 					else{
	// 						if(sizeof($listeCaffsTitulaireAffectationAuto) == 0)
	// 						{
	// 							$poiTituCaff = null;
								
	// 							foreach($listeCaffsTitulaireAffectationAutoNot1 as $caffTitulaireAffectationAuto)
	// 							{
	// 								foreach($caffTitulaireAffectationAuto->listePoiTitulaire as $poiTitu)
	// 								{
	// 									if($poiTituCaff == null)
	// 									{
											
	// 										$poiTituCaff = (object) array();
	// 										$poiTituCaff->caff = $caffTitulaireAffectationAuto;
	// 										$poiTituCaff->poi = $poiTitu;
	// 									}
	// 									else{
	// 										if($poiTitu->ft_date_creation_oeie < $poiTituCaff->poi->ft_date_creation_oeie)
	// 										{
	// 											$poiTituCaff->caff = $caffTitulaireAffectationAuto;
	// 											$poiTituCaff->poi = $poiTitu;
	// 										}
	// 									}
	// 								}
	// 							}
	// 							if(isset($poiTituCaff) && $poiTituCaff != null)
	// 							{
	// 								$caffAuto = $poiTituCaff->caff;
	// 							}
	// 							else{
	// 								$caffAuto = $caff;
	// 							}
	// 						}
	// 					}
	// 				}
	// 			}
					
									
	// 				$req3 = $bdd->prepare("SELECT COUNT(*) nb_affectations_jour FROM cds_affectation WHERE caff_id = ? AND (DATE_PART('day', NOW()) - DATE_PART('day', cds_affectation_date)) < 1 AND UPPER(erp_poi_domaine) IN('CLIENT', 'FO & CU')");
	// 				$req3->execute(array($data["id"]));
	// 				if($data3 = $req3->fetch())
	// 				{
	// 					if($data3["nb_affectations_jour"] < $limiteJour)
	// 					{
	// 						$lastMonday = date("Y-m-d",strtotime("last Monday"));
							
	// 						$req4 = $bdd->prepare("SELECT COUNT(*) nb_affectations_semaine FROM cds_affectation WHERE caff_id = ? AND cds_affectation_date >= ? AND UPPER(erp_poi_domaine) IN('CLIENT', 'FO & CU')");
	// 						$req4->execute(array($data["id"], $lastMonday));
	// 						if($data4 = $req4->fetch())
	// 						{
	// 							if($data4["nb_affectations_semaine"] < $limiteSemaine)
	// 							{
	// 								if(!$enConges)
	// 								{
	// 									$ceCaff = (object) array();
	// 									$ceCaff->id = $caff->id;
	// 									$ceCaff->charge_initiale = $caff->charge_initiale;
	// 									$ceCaff->name_related = $caff->name_related;
	// 									$ceCaff->charge_totale = $caff->charge_totale;
	// 									$ceCaff->tauxRetard = $caff->tauxRetard;
	// 									$ceCaff->charge_rayon = $caff->charge_rayon;
	// 									$ceCaff->limiteAtteinte = false;
	// 									$ceCaff->nbAffectationsJour = intval($data3["nb_affectations_jour"]);
	// 									$ceCaff->nbAffectationsSemaine = intval($data4["nb_affectations_semaine"]);
	// 									$ceCaff->enConges = $enConges;
	// 									array_push($listeCaffs, $ceCaff);

	// 									/*$caff = (object) array();
	// 									$caff->name_related = $data["name_related"];
	// 									$caff->mobile_phone = $data["mobile_phone"];
	// 									$caff->work_email = $data["work_email"];
	// 									$caff->site = $data["site"];
	// 									$caff->site_id = $data["site_id"];
	// 									$caff->agence = $data["agence"];
	// 									$caff->reactive = $data["reactive"];
	// 									$caff->non_reactive = $data["non_reactive"];
	// 									$caff->charge_totale = $data["charge_totale"];
	// 									$caff->charge_initiale = $data["charge_initiale"];
	// 									$caff->id = $data["id"];
										
	// 									$listePoi = json_decode(getPoiAffecteByCaff($caff->name_related));
	// 									$nbPoiEnRetard = 0;
	// 									$dateAjd = new DateTime("now");
	// 									foreach($listePoi as $poi)
	// 									{
	// 										$dre = new DateTime($poi->ft_oeie_dre);
	// 										if($dateAjd > $dre)
	// 										{
	// 											$nbPoiEnRetard++;
	// 										}
	// 									}
	// 									if(sizeof($listePoi) > 0)
	// 									{
	// 										$tauxDre = $nbPoiEnRetard / sizeof($listePoi);
	// 									}
	// 									else{
	// 										$tauxDre = 0;
	// 									}
	// 									$caff->charge_totale += ($tauxDre * $caff->reactive);*/
	// 									if($caffTitulaireAuto == false)
	// 									{
	// 										if($caffAuto == null)
	// 										{
	// 											$caffAuto = $caff;
	// 										}
	// 										elseif($caffAuto->charge_totale > $caff->charge_totale){
	// 											$caffAuto = $caff;
	// 										}
	// 									}
	// 								}
	// 								else{
	// 									$ceCaff = (object) array();
	// 									$ceCaff->id = $caff->id;
	// 									$ceCaff->charge_initiale = $caff->charge_initiale;
	// 									$ceCaff->name_related = $caff->name_related;
	// 									$ceCaff->charge_totale = $caff->charge_totale;
	// 									$ceCaff->tauxRetard = $caff->tauxRetard;
	// 									$ceCaff->charge_rayon = $caff->charge_rayon;
	// 									$ceCaff->limiteAtteinte = true;
	// 									$ceCaff->nbAffectationsJour = intval($data3["nb_affectations_jour"]);
	// 									$ceCaff->nbAffectationsSemaine = intval($data4["nb_affectations_semaine"]);
	// 									$ceCaff->enConges = $enConges;
	// 									array_push($listeCaffs, $ceCaff);
	// 								}
									
	// 							}
	// 							else{
	// 								$ceCaff = (object) array();
	// 								$ceCaff->id = $caff->id;
	// 								$ceCaff->charge_initiale = $caff->charge_initiale;
	// 								$ceCaff->name_related = $caff->name_related;
	// 								$ceCaff->charge_totale = $caff->charge_totale;
	// 								$ceCaff->tauxRetard = $caff->tauxRetard;
	// 								$ceCaff->charge_rayon = $caff->charge_rayon;
	// 								$ceCaff->limiteAtteinte = true;
	// 								$ceCaff->nbAffectationsJour = intval($data3["nb_affectations_jour"]);
	// 								$ceCaff->nbAffectationsSemaine = intval($data4["nb_affectations_semaine"]);
	// 								$ceCaff->enConges = $enConges;
	// 								array_push($listeCaffs, $ceCaff);
	// 							}
	// 						}
	// 					}
	// 					else{
	// 						$lastMonday = date("Y-m-d",strtotime("last Monday"));
							
	// 						$req4 = $bdd->prepare("SELECT COUNT(*) nb_affectations_semaine FROM cds_affectation WHERE caff_id = ? AND cds_affectation_date >= ? AND UPPER(erp_poi_domaine) IN('CLIENT', 'FO & CU')");
	// 						$req4->execute(array($data["id"], $lastMonday));
	// 						if($data4 = $req4->fetch())
	// 						{
	// 							$ceCaff = (object) array();
	// 							$ceCaff->id = $caff->id;
	// 							$ceCaff->charge_initiale = $caff->charge_initiale;
	// 							$ceCaff->name_related = $caff->name_related;
	// 							$ceCaff->charge_totale = $caff->charge_totale;
	// 							$ceCaff->tauxRetard = $caff->tauxRetard;
	// 							$ceCaff->charge_rayon = $caff->charge_rayon;
	// 							$ceCaff->limiteAtteinte = true;
	// 							$ceCaff->nbAffectationsJour = intval($data3["nb_affectations_jour"]);
	// 							$ceCaff->nbAffectationsSemaine = intval($data4["nb_affectations_semaine"]);
	// 							$ceCaff->enConges = $enConges;
	// 							array_push($listeCaffs, $ceCaff);
	// 						}
	// 					}
	// 				}
	// 			if($caffTitulaireAuto == false)
	// 			{
	// 				if($caffAuto == null)
	// 				{
	// 					$caffAuto = $caff;
	// 				}
	// 			}
	// 		}
			
	// 	}

		
	// 	if($caffAuto != null)
	// 	{
	// 		function comparer($a, $b) {
	// 			if($a->limiteAtteinte)
	// 			{
	// 				if($b->limiteAtteinte)
	// 				{
	// 					if($a->charge_totale < $b->charge_totale)
	// 					{
	// 						return -1;
	// 					}
	// 					elseif($a->charge_totale == $b->charge_totale)
	// 					{
	// 						return 0;
	// 					}
	// 					else{
	// 						return 1;
	// 					}
	// 				}
	// 				else{
	// 					return 1;
	// 				}
	// 			}
	// 			else{
	// 				if($a->charge_totale < $b->charge_totale)
	// 				{
	// 					return -1;
	// 				}
	// 				elseif($a->charge_totale == $b->charge_totale)
	// 				{
	// 					return 0;
	// 				}
	// 				else{
	// 					return 1;
	// 				}
	// 			}
				
	// 		}
	// 		usort($listeCaffs, 'comparer');
			
	// 		$caffAuto->listeAutresCaffs = $listeCaffs;
	// 	}
		
	// 	return json_encode($caffAuto);
	// }

	function getAffectationAuto($idPoi, $km, $coefNbPoiProimite, $coefChargeReactive, $coefCharge, $limiteJour, $limiteSemaine, $limiteMaxCalcul, $nbJoursAvantConges, $nbJoursConges)//, $listeCaffsSimulation) // $listeCaffsSimulation (facultatif) = array en json
	{
		include("connexionBddErp.php");
		include("connexionBdd.php");
		
		//variable qui sera retournée en fin de fonction
		$caffAuto = null;
		
		//liste des caffs triés du plus probable à affecter au moins probable
		$listeCaffs = array();

		//Je récupère toutes les infos de la POI grâce à son ID passé en paramètre
		$poi = json_decode(getPoiById($idPoi));

		//Liste des sites de l'UI de la POI. Ces sites sont triés du plus proche de la POI à la plus éloignée
		$listeSites = json_decode(getListClosestSites($poi->id));
		//String des id des sites (chaque element est séparé par une virgule)
		$listeIdSites = array();
		foreach($listeSites as $site)
		{
			array_push($listeIdSites, $site->id);
		}
		$listeIdSites = implode(", ", $listeIdSites);

		//liste des caffs qui ont des POI à l'état 1 dont le titulaire est identique à cette POI
		$listeCaffsTitulaireEtat1 = array();
		//liste des caffs qui ont des POI (hors état 1) dont le titulaire est identique à cette POI
		$listeCaffsTitulaireNotEtat1 = array();
		//Ce tableau représente la liste des Caffs, eux-même triés en fonction de leur site. Ainsi, la première valeur de ce tableau représente la liste des Caff du site le plus proche de la POI, la seconde valeur = liste des Caffs du second site le plus proche...
		$listeCaffsBySite = array();
		for($i = 0; $i < sizeof($listeSites); $i++)
		{
			$listeCaffsBySite[$i] = array();
		}
		
		//Je récupère la compétence nécessaire à la réalisation de la POI
		if($poi->domaine == "Focu" || $poi->domaine == "FO & CU")
		{
			$competence = "Fo & Cu";
		}
		else{
			$competence = $poi->domaine;
		}
		
		//Je récupère la liste des caffs de l'UI, afin de l'ajouter ensuite à la requête sql (c'est pour cela que je transforme cette liste en String, dont chaque valeur est séparée par une virgule. Si la liste est vide, alors le String contiendra 0, car il s'agit d'un ID affilié à aucun CAFF)
		$listeCaffsEntraide = json_decode(getListeIdCaffEntraideByUiAndDomaine($poi->atr_ui, $poi->domaine));
		if(sizeof($listeCaffsEntraide) > 0)
		{
			$listeCaffsUi = array();
			foreach($listeCaffsEntraide as $caffEntraide)
			{
				array_push($listeCaffsUi, $caffEntraide->caff_id);
			}
			$listeCaffsUi = implode(",", $listeCaffsUi);
		}
		else{
			$listeCaffsUi = "0";
		}
		//Je récupère la liste des caffs à exclure de l'UI, afin de l'ajouter ensuite à la requête sql (c'est pour cela que je transforme cette liste en String, dont chaque valeur est séparée par une virgule. Si la liste est vide, alors le String contiendra 0, car il s'agit d'un ID affilié à aucun CAFF)
		$listeCaffsExclure = json_decode(getListeIdCaffEntraideExclureUi($poi->atr_ui));
		if(sizeof($listeCaffsExclure) > 0)
		{
			$listeCaffsExclure = implode(",", $listeCaffsExclure);
		}
		else{
			$listeCaffsExclure = "0";
		}

		//J'effectue la requête
		$req = $bddErp->query("SELECT id, name_related, mobile_phone, work_email, site, site_id, agence, reactive, non_reactive, 
		(((reactive * ".$coefChargeReactive.") + (non_reactive * ".$coefCharge.")) * (1 / caff.ag_coeff_traitement)) charge_initiale, 
		(CASE WHEN ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.") > ".$limiteMaxCalcul." THEN ".$limiteMaxCalcul." ELSE ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.") END)charge_rayon, 
		((((reactive * ".$coefChargeReactive.") + (non_reactive * ".$coefCharge.")) * (1 / caff.ag_coeff_traitement))
        - CASE WHEN ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.") > ".$limiteMaxCalcul." THEN ".$limiteMaxCalcul." ELSE ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.") END
        )charge_totale 
		FROM (select id, t3.ag_coeff_traitement, t3.name_related, t3.mobile_phone, t3.work_email, t3.site, t3.site_id, t3.agence,case when t3.reactive is null then 0 else t3.reactive end,
		case when t3.non_reactive is null then 0 else t3.non_reactive end from
		(
		select t2.id, t2.ag_coeff_traitement, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.site_id, t2.name as agence, sum(t2.reactive) as reactive, sum(t2.non_reactive) as non_reactive from (
		 
		select t1.id, t1.ag_coeff_traitement, t1.name_related,t1.mobile_phone,t1.work_email,t1.site, t1.site_id,t1.name, case when account_analytic_account.name in ('Client', 'FO & CU') then count (ag_poi.id)
		end as reactive , case when account_analytic_account.name not in ('Client', 'FO & CU') then count (ag_poi.id) end as non_reactive
		from ag_poi
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id  
		full join
		(select hr_employee.id, hr_employee.ag_coeff_traitement, hr_employee.name_related,hr_employee.mobile_phone,hr_employee.work_email,ag_site.name as site, ag_site.id as site_id,ag_agence.name from res_users
		full join hr_employee on res_users.ag_employee_id = hr_employee.id
		full join ag_site on hr_employee.ag_site_id = ag_site.id
		full join ag_agence on hr_employee.ag_agence_id = ag_agence.id
		full join hr_job on hr_employee.job_id = hr_job.id
		FULL JOIN m2m__hr_employee__ag_competence cmp ON cmp.employee_id = hr_employee.id
		FULL JOIN ag_competence ON ag_competence.id = cmp.competence_id
		where ((res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE','ASSISTANT MANAGER') and ag_competence.name = '".$competence."') OR hr_employee.id IN(".$listeCaffsUi.")) AND hr_employee.id NOT IN(".$listeCaffsExclure.")) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
		group by t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, account_analytic_account.name, t1.site_id, t1.ag_coeff_traitement) t2
		group by t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name, t2.site_id, t2.ag_coeff_traitement ) t3
		where ((name_related is not null AND site_id IN(".$listeIdSites.")) OR id IN(".$listeCaffsUi.")) AND id NOT IN(".$listeCaffsExclure."))caff
		ORDER BY charge_totale");
		while($data = $req->fetch())
		{
			//liste des POI à l'état 1 du CAFF en lien avec le titulaire
			$listePoiTitulaireEtat1 = array();
			//liste des POI hors état 1 du CAFF en lien avec le titulaire
			$listePoiTitulaireNotEtat1 = array();

			//Je ne récupère que les CAFF qui ne sont actuellement pas en formation
			$req2 = $bdd->prepare("SELECT id FROM cds_formation WHERE caff_id = ?");
			$req2->execute(array($data["id"]));
			if(!$data2 = $req2->fetch())
			{
				//Je vérifie maintenant si le caff est actuellement, ou bientôt en congé (cette info n'a de l'importance que si la POI est du domaine FO & CU ou Client)
				$enConges = false;
				if(strtoupper($poi->domaine) == "FO & CU" || strtoupper($poi->domaine) == "CLIENT")
				{
					$conges = json_decode(getProchainesConges($data["id"]));
					if($conges != null)
					{
						if($conges->temps_avant <= strtotime(60*60*24*$nbJoursAvantConges) && $conges->nbJoursCongesRestant >= $nbJoursConges) // Si le caff est en congé dans moins de X jours et que ces congés durent au moins X jours (sans compter les week_end) ou si le caff est actuellement en congés et que le nombre de jours de conges restant est d'au moins X jours
						{
							$enConges = true;
						}
					}
					if(!$enConges)
					{
						if(array_search($data["id"], json_decode(getListIdEmployesConges())) != false)
						{
							$enConges = true;
						}
					}
				}

				//Je crée mon objet CAFF
				$caff = (object) array();
				$caff->name_related = $data["name_related"];
				$caff->mobile_phone = $data["mobile_phone"];
				$caff->work_email = $data["work_email"];
				$caff->agence = $data["agence"];
				$caff->reactive = $data["reactive"];
				$caff->non_reactive = $data["non_reactive"];
				$caff->charge_rayon = $data["charge_rayon"];
				$caff->charge_totale = $data["charge_totale"];
				$caff->tauxRetard = (json_decode(getStatsCaff($data["id"])) * $caff->reactive * $coefChargeReactive);
				$caff->charge_totale += $caff->tauxRetard;
				$caff->charge_initiale = $data["charge_initiale"];
				$caff->id = $data["id"];
				$caff->enConges = $enConges;
				$caff->listePoiTitulaire = null;
				//Si le caff est en entraide, alors il faut lui donner les informations de son site d'entraide, et non pas de son site de base
				if(in_array($caff->id, explode(",", $listeCaffsUi)))
				{
					foreach($listeCaffsEntraide as $entraide)
					{
						if($entraide->caff_id == $caff->id)
						{
							$caff->site_id = $entraide->site_entraide_id;
							$caff->site = json_decode(getLibelleSiteById($caff->site_id));
							$caff->enEntraide = true;
						}
					}
				}
				else{
					$caff->site = $data["site"];
					$caff->site_id = $data["site_id"];
					$caff->enEntraide = false;
				}
				//Je récupère le numéro du site du caff (plus ce numéro est bas, plus le site du CAFF est proche de la POI)
				$caff->numSite = null;
				for($i = 0; $i < sizeof($listeSites); $i++)
				{
					if($listeSites[$i]->id == $caff->site_id)
					{
						$caff->numSite = $i;
					}
				}
				//Je récupère le nombre d'affectations du caff dans la journée
				$req3 = $bdd->prepare("SELECT COUNT(*) nb_affectations_jour FROM cds_affectation WHERE caff_id = ? AND (DATE_PART('day', NOW()) - DATE_PART('day', cds_affectation_date)) < 1 AND UPPER(erp_poi_domaine) IN('CLIENT', 'FO & CU')");
				$req3->execute(array($data["id"]));
				if($data3 = $req3->fetch())
				{
					$caff->nbAffectationsJour = intval($data3["nb_affectations_jour"]);
				}
				else{
					$caff->nbAffectationsJour = null;
				}
				//Je récupère le nombre d'affectations du caff dans la semaine
				$lastMonday = date("Y-m-d",strtotime("last Monday")); //correspond à la date du dernier lundi
				$req3 = $bdd->prepare("SELECT COUNT(*) nb_affectations_semaine FROM cds_affectation WHERE caff_id = ? AND cds_affectation_date >= ? AND UPPER(erp_poi_domaine) IN('CLIENT', 'FO & CU')");
				$req3->execute(array($data["id"], $lastMonday));
				if($data3 = $req3->fetch())
				{
					$caff->nbAffectationsSemaine = intval($data3["nb_affectations_semaine"]);
				}
				else{
					$caff->nbAffectationsSemaine = null;
				}
				//variable qui permet de savoir si ce caff a atteint la limite d'affectation dans la journée/semaine
				$caff->limiteAtteinte = false;
				if(!$caff->nbAffectationsSemaine < $limiteSemaine || !$caff->nbAffectationsJour < $limiteJour)
				{
					$caff->limiteAtteinte = true;
				}
				//On vérifie si ce caff est en entraide, car si oui il faudra prendre en compte son taux sur ce site
				if(in_array($caff->id, explode(",", $listeCaffsUi)))
				{
					$taux = json_decode(getTauxCaffEntraideByCaffIdAndSiteId($caff->id, $caff->site_id));
					if($taux != null)
					{
						$caff->charge_totale *= ( 100 / $taux );
					}
				}

				//Je récupère la liste des POI affectées au CAFF
				$listePoi = json_decode(getPoiAffecteByCaff($caff->name_related));

			//Je commence par vérifier si le CAFF est en lien avec le titulaire de la POI (car dans ce cas il sera prioritaire)
				//Je récupère la liste des CAFF qui sont en lien avec le titulaire et l'UI de la POI
				$listeCaffsTitulaire = json_decode(getCaffsEnLienAvecPoiByTitulaire($poi->ft_titulaire_client, $poi->atr_ui));
				foreach($listeCaffsTitulaire as $caffTitulaire)
				{
					//Si le caff possède une POI en lien avec le titulaire
					if($caffTitulaire->id == $data["id"])
					{
						//Je parcours la liste de ces POI pour voir s'il s'agit de POI à l'état 1 ou non, puis j'ajoute ces POI à la liste des POI du caff qui sont à l'état 1, ou à la liste des POI qui ne sont pas à l'état 1
						foreach($listePoi as $cettePoi)
						{
							if($cettePoi->ft_titulaire_client == $poi->ft_titulaire_client)
							{
								if($cettePoi->ft_etat == 1)
								{
									array_push($listePoiTitulaireEtat1, $cettePoi);
								}
								else{
									array_push($listePoiTitulaireNotEtat1, $cettePoi);
								}
							}
						}
						
						$caff->listePoiTitulaire = (object) array();
						$caff->listePoiTitulaire->etat1 = $listePoiTitulaireEtat1;
						$caff->listePoiTitulaire->notEtat1 = $listePoiTitulaireNotEtat1;
						
						if(sizeof($caff->listePoiTitulaire->etat1) > 0)
						{
							array_push($listeCaffsTitulaireEtat1, $caff);
						}
						else{
							array_push($listeCaffsTitulaireNotEtat1, $caff);
						}
					}
				}

				//Si le caff n'a aucune POI en lien avec le titulaire, alors on l'ajoute à la liste des caffs de son site
				if($caff->listePoiTitulaire == null)
				{
					array_push($listeCaffsBySite[$caff->numSite], $caff);
				}
			}
		}

		//Je tri les caffs qui ont une POI à l'état 1 en lien avec le titulaire
		function cmpEtat1($a, $b)
		{
			//On affecte à celui qui a le plus de POI à l'état 1 en lien avec le titulaire
			if(sizeof($a->listePoiTitulaire->etat1) > sizeof($b->listePoiTitulaire->etat1))
			{
				return -1;
			}
			elseif(sizeof($a->listePoiTitulaire->etat1) < sizeof($b->listePoiTitulaire->etat1))
			{
				return 1;
			}
			else{
				//S'ils ont le même nombre de POI à l'état 1 en lien avec le titulaire, on vérifie celui qui a le site le plus proche
				if($a->numSite < $b->numSite)
				{
					return -1;
				}
				elseif($a->numSite > $b->numSite)
				{
					return 1;
				}
				else{
					//S'ils appartiennent tous les 2 au même site, on affecte à celui qui a la plus petite charge totale
					if($a->charge_totale < $b->charge_totale)
					{
						return -1;
					}
					elseif($a->charge_totale > $b->charge_totale)
					{
						return 1;
					}
					else{
						return 0;
					}
				}
			}
		}
		usort($listeCaffsTitulaireEtat1, 'cmpEtat1');
		foreach($listeCaffsTitulaireEtat1 as $cf)
		{
			array_push($listeCaffs, $cf);
		}

		//Je tri les caffs qui ont une POI (hors état 1) en lien avec le titulaire
		function cmpNotEtat1($a, $b)
		{
			//On affecte à celui qui a le plus de POI (hors état 1) en lien avec le titulaire
			if(sizeof($a->listePoiTitulaire->notEtat1) > sizeof($b->listePoiTitulaire->notEtat1))
			{
				return -1;
			}
			elseif(sizeof($a->listePoiTitulaire->notEtat1) < sizeof($b->listePoiTitulaire->notEtat1))
			{
				return 1;
			}
			else{
				//S'ils ont le même nombre de POI (hors état 1) en lien avec le titulaire, on vérifie celui qui a le site le plus proche
				if($a->numSite < $b->numSite)
				{
					return -1;
				}
				elseif($a->numSite > $b->numSite)
				{
					return 1;
				}
				else{
					//S'ils appartiennent tous les 2 au même site, on affecte à celui qui a la plus petite charge totale
					if($a->charge_totale < $b->charge_totale)
					{
						return -1;
					}
					elseif($a->charge_totale > $b->charge_totale)
					{
						return 1;
					}
					else{
						return 0;
					}
				}
			}
		}
		usort($listeCaffsTitulaireNotEtat1, 'cmpNotEtat1');
		foreach($listeCaffsTitulaireNotEtat1 as $cf)
		{
			array_push($listeCaffs, $cf);
		}

		//Je tri les caffs restant, en fonction de leur site (d'abord ceux du site le plus proche...)
		function cmpCaff($a, $b)
		{
			if($a->enConges)
			{
				if($b->enConges)
				{
					if($a->limiteAtteinte)
					{
						if($b->limiteAtteinte)
						{
							//On affecte à celui qui a le site le plus proche
							if($a->numSite < $b->numSite)
							{
								return -1;
							}
							elseif($a->numSite > $b->numSite)
							{
								return 1;
							}
							else{
								//S'ils appartiennent tous les 2 au même site, on affecte à celui qui a la plus petite charge totale
								if($a->charge_totale < $b->charge_totale)
								{
									return -1;
								}
								elseif($a->charge_totale > $b->charge_totale)
								{
									return 1;
								}
								else{
									return 0;
								}
							}
			
						}
						else{
							return 1;
						}
					}
					else{
						if($b->limiteAtteinte)
						{
							return -1;
						}
						else{
							//On affecte à celui qui a le site le plus proche
							if($a->numSite < $b->numSite)
							{
								return -1;
							}
							elseif($a->numSite > $b->numSite)
							{
								return 1;
							}
							else{
								//S'ils appartiennent tous les 2 au même site, on affecte à celui qui a la plus petite charge totale
								if($a->charge_totale < $b->charge_totale)
								{
									return -1;
								}
								elseif($a->charge_totale > $b->charge_totale)
								{
									return 1;
								}
								else{
									return 0;
								}
							}
						}
					}
				}
				else{
					return 1;
				}
			}else{
				if($b->enConges)
				{
					return -1;
				}
				else{
					if($a->limiteAtteinte)
					{
						if($b->limiteAtteinte)
						{
							//On affecte à celui qui a le site le plus proche
							if($a->numSite < $b->numSite)
							{
								return -1;
							}
							elseif($a->numSite > $b->numSite)
							{
								return 1;
							}
							else{
								//S'ils appartiennent tous les 2 au même site, on affecte à celui qui a la plus petite charge totale
								if($a->charge_totale < $b->charge_totale)
								{
									return -1;
								}
								elseif($a->charge_totale > $b->charge_totale)
								{
									return 1;
								}
								else{
									return 0;
								}
							}
			
						}
						else{
							return 1;
						}
					}
					else{
						if($b->limiteAtteinte)
						{
							return -1;
						}
						else{
							//On affecte à celui qui a le site le plus proche
							if($a->numSite < $b->numSite)
							{
								return -1;
							}
							elseif($a->numSite > $b->numSite)
							{
								return 1;
							}
							else{
								//S'ils appartiennent tous les 2 au même site, on affecte à celui qui a la plus petite charge totale
								if($a->charge_totale < $b->charge_totale)
								{
									return -1;
								}
								elseif($a->charge_totale > $b->charge_totale)
								{
									return 1;
								}
								else{
									return 0;
								}
							}
						}
					}
				}
			}
		}
		foreach($listeCaffsBySite as $caffsBySite)
		{
			usort($caffsBySite, 'cmpCaff');
			foreach($caffsBySite as $cf)
			{
				array_push($listeCaffs, $cf);
			}
		}

		//Le caff à privilégier pour l'affectation est donc le premier de la liste (puisque nous venons de les trier)
		$caffAuto = $listeCaffs[0];
		//print(json_encode($caffAuto));

		//J'utilise une autre liste contenant uniquement les infos nécessaires, car sinon le JSON plante
		$autreListe = array();
		foreach($listeCaffs as $ceCaff)
		{
			$autreCaff = (object) array();
			$autreCaff->id = $ceCaff->id;
			$autreCaff->charge_initiale = $ceCaff->charge_initiale;
			$autreCaff->name_related = $ceCaff->name_related;
			$autreCaff->charge_totale = $ceCaff->charge_totale;
			$autreCaff->tauxRetard = $ceCaff->tauxRetard;
			$autreCaff->charge_rayon = $ceCaff->charge_rayon;
			$autreCaff->limiteAtteinte = $ceCaff->limiteAtteinte;
			$autreCaff->nbAffectationsJour = $ceCaff->nbAffectationsJour;
			$autreCaff->nbAffectationsSemaine = $ceCaff->nbAffectationsSemaine;
			$autreCaff->enConges = $ceCaff->enConges;
			$autreCaff->site = $ceCaff->site;
			$autreCaff->nbPoiTitulaireEtat1 = 0;
			$autreCaff->nbPoiTitulaireNotEtat1 = 0;
			$autreCaff->numSite = $ceCaff->numSite;

			if($ceCaff->listePoiTitulaire != null)
			{
				if(sizeof($ceCaff->listePoiTitulaire->etat1))
				{
					$autreCaff->nbPoiTitulaireEtat1 = sizeof($ceCaff->listePoiTitulaire->etat1);
				}
				if(sizeof($ceCaff->listePoiTitulaire->notEtat1))
				{
					$autreCaff->nbPoiTitulaireNotEtat1 = sizeof($ceCaff->listePoiTitulaire->notEtat1);
				}
			}

			array_push($autreListe, $autreCaff);
		}

		//Je lui donne également la liste des autres caffs
		$caffAuto->listeAutresCaffs = $autreListe;

		return json_encode($caffAuto);
	}
	
	function getNbAffectationsCaffById($idCaff)
	{
		include("connexionBdd.php");
		
		$nbAffectations = (object) array();
		
		$req = $bdd->prepare("SELECT COUNT(*) nb_affectations_jour FROM cds_affectation WHERE caff_id = ? AND cds_affectation_date >= (NOW() - interval '1 day')");
		$req->execute(array($idCaff));
		if($data = $req->fetch())
		{
			$nbAffectations->jour = $data["nb_affectations_jour"];
		}

		$lastMonday = date("Y-m-d",strtotime("last Monday"));				
		$req = $bdd->prepare("SELECT COUNT(*) nb_affectations_semaine FROM cds_affectation WHERE caff_id = ? AND cds_affectation_date >= ?");
		$req->execute(array($idCaff, $lastMonday));
		if($data = $req->fetch())
		{
			$nbAffectations->semaine = $data["nb_affectations_semaine"];
		}
		
		return json_encode($nbAffectations);
	}
	
	function getPoiNAByUi($ui) //ft_zone
	{
		include("connexionBddErp.php");
		
		$listePoiAffect = json_decode(getPoiAffect());
		if(sizeof($listePoiAffect) > 0)
		{
			$listePoiAffect = implode(",", $listePoiAffect);
			$listePoiAffect = " AND ag_poi.id NOT IN(".$listePoiAffect.")";
		}
		else{
			$listePoiAffect = "";
		}
		
		$listePoi = array();
		
		$req = $bddErp->prepare("select ag_poi.id,ag_poi.ft_sous_justification_oeie, ag_poi.atr_ui, ag_poi.ft_numero_oeie, account_analytic_account.name as domaine, ag_poi.\"ft_numero_demande_42C\" numero_demande, ft_libelle_commune, ft_libelle_de_voie, ft_pg,ft_oeie_dre,ft_latitude,insee_code,ft_longitude,ft_libelle_affaire,ft_date_limite_realisation,ag_poi.create_date from ag_poi
		left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
		where hr_employee.name_related in ('MATHIASIN Celine','AFFECTATION') and ft_etat = '1' AND ag_poi.atr_ui = ?".$listePoiAffect);
		$req->execute(array($ui));
		while($data = $req->fetch())
		{
			$poi = (object) array();
			$poi->atr_ui = $data["atr_ui"];
			$poi->ft_numero_oeie = $data["ft_numero_oeie"];
			$poi->domaine = $data["domaine"];
			$poi->ft_titulaire_client = $data["numero_demande"];
			$poi->ft_libelle_commune = $data["ft_libelle_commune"];
			$poi->ft_libelle_de_voie = $data["ft_libelle_de_voie"];
			$poi->ft_pg = $data["ft_pg"];
			$poi->ft_sous_justification_oeie = $data["ft_sous_justification_oeie"];
			$poi->ft_oeie_dre = $data["ft_oeie_dre"];
			$poi->ft_latitude = $data["ft_latitude"];
			$poi->insee_code = $data["insee_code"];
			$poi->ft_longitude = $data["ft_longitude"];
			$poi->ft_libelle_affaire = $data["ft_libelle_affaire"];
			$poi->ft_date_limite_realisation = $data["ft_date_limite_realisation"];
			$poi->create_date = $data["create_date"];
			$poi->id = $data["id"];

			if($poi->domaine == 'Client' || $poi->domaine == 'FO & CU')
			{
				$poi->reactive = true;
			}
			else{
				$poi->reactive = false;
			}
			
			array_push($listePoi, $poi);
		}
		
		return json_encode($listePoi);
	}
	
	function getListeAffectationAuto($listeIdPoi, $km) //$listeIdPoi = array en json
	{
		$listeIdPoi = json_decode($listeIdPoi);
		
		$listeCaffsAuto = array();
		
		foreach($listeIdPoi as $idPoi)
		{
			$obj = (object) array();
			$obj->caff = json_decode(getAffectationAuto($idPoi, $km));
			$obj->idPoi = $idPoi;
			
			array_push($listeCaffsAuto, $obj);
		}
		
		return json_encode($listeCaffsAuto);
	}
	function addRemoveFormationByCaffId($caff_id,$state){
		include("connexionBdd.php");
		if($state == "OUI"){
			$req = $bdd->prepare("INSERT INTO cds_formation (caff_id) VALUES (?)");
			$req->execute(array($caff_id));
		}
		else
		{
			$req = $bdd->prepare("DELETE FROM cds_formation WHERE caff_id = ?");
			$req->execute(array($caff_id));
		}
	}

	function getFormationCaff($idCaff)
	{
		include("connexionBdd.php");
		$req = $bdd->prepare("SELECT id FROM cds_formation WHERE caff_id = ?");
		$req->execute(array($idCaff));
		if($data = $req->fetch())
		{
			return "OUI";
		}
		else{
			return "NON";
		}
	}
	
	
	function getStatsCaff($idCaff)
    {
        include("connexionBddMailAuto.php");
        include("connexionBddErp.php");
		$statistique = 0;
        $global = "select atr.atr_caff_traitant_id, atr.atr_sous_domaine_id,atr.id,atr.atr_ui,atr.partner,atr.ft_numero_oeie,atr.ft_oeie_dre,atr.name as domaine,account_analytic_account.name as sous_domaine,atr.ft_pg, CASE WHEN LENGTH(ft_sous_justification_oeie) = 2 THEN ft_sous_justification_oeie ELSE 'Pas de SJ' END AS ft_sous_justification_oeie, atr.ft_libelle_commune,atr.ft_libelle_de_voie,atr.name_related,atr.work_email,atr.mobile_phone,atr.ft_commentaire_creation_oeie from(
                select ag_poi.atr_caff_traitant_id, ag_poi.atr_sous_domaine_id,ag_poi.id,ag_poi.atr_ui,res_partner.name as partner,ag_poi.ft_oeie_dre,ag_poi.ft_numero_oeie,account_analytic_account.name,ag_poi.ft_pg,ag_poi.ft_sous_justification_oeie,ag_poi.ft_libelle_commune,ag_poi.ft_libelle_de_voie,hr_employee.name_related,hr_employee.work_email,hr_employee.mobile_phone,ag_poi.ft_commentaire_creation_oeie from ag_poi
                left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
                left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
                left join res_partner on ag_poi.res_partner_id = res_partner.id
                where ft_etat = '1' and name_related is not null and ag_poi.atr_caff_traitant_id = ".$idCaff." and work_email is not null and ag_poi.ft_numero_oeie not like '%MBB%' AND account_analytic_account.name IN ('Client', 'FO & CU'))atr
                left join account_analytic_account on atr.atr_sous_domaine_id = account_analytic_account.id
                order by ft_oeie_dre";
        
        $listePoiBleues = array();
        $req = $bddMail->query("select poi from relance where date_expiration >= NOW()");
        while($data = $req->fetch())
        {
            array_push($listePoiBleues, $data["poi"]);
        }
        
        $listePoiBleues = implode(", ", $listePoiBleues);

        if($listePoiBleues != null && $listePoiBleues != "")
        {
            $req = $bddErp->query("select atr_caff_traitant_id,count(dre_ko) as dre_ko,count(dre_ok) as dre_ok from(select atr_caff_traitant_id,ft_oeie_dre,case when (ft_oeie_dre IS NULL OR ft_oeie_dre <= NOW()) and id not in (".$listePoiBleues.") then 1 end as dre_ko,case when ft_oeie_dre > NOW() or id in (".$listePoiBleues.") then 1 end as dre_ok from (".$global.")dre)dre2 group by atr_caff_traitant_id");
            while($data = $req->fetch())
            {
                $statistique = round($data["dre_ko"]/($data["dre_ko"] + $data["dre_ok"]), 2);
            }
        }
        else{
            $req = $bddErp->query("select atr_caff_traitant_id,count(dre_ko) as dre_ko,count(dre_ok) as dre_ok from(select atr_caff_traitant_id,ft_oeie_dre,case when (ft_oeie_dre IS NULL OR ft_oeie_dre <= NOW()) then 1 end as dre_ko,case when ft_oeie_dre > NOW() then 1 end as dre_ok from (".$global.")dre)dre2 group by atr_caff_traitant_id");
            while($data = $req->fetch())
            {
                $statistique = round($data["dre_ko"]/($data["dre_ko"] + $data["dre_ok"]), 2);
            }
        }
        
        return json_encode($statistique);
    }
	
	

	function getCompetenceByCaffId($idCaff)
	{
		include("connexionBddErp.php");
		$listcomp = array();
		$req = $bddErp->prepare("SELECT employee_id,competence_id,name as competence_name FROM m2m__hr_employee__ag_competence
		left join ag_competence on m2m__hr_employee__ag_competence.competence_id = ag_competence.id
		where employee_id = ?");
		$req->execute(array($idCaff));
		while($data = $req->fetch())
		{
			if($data['competence_name'] == 'Fo & Cu'){
				$data['competence_name'] = 'Focu';
			}
			array_push($listcomp,$data['competence_name']);
		}
		return json_encode($listcomp);
	}

	function getActivePoiByCaffId($idCaff)
	{
		$color = dechex(mt_rand(0,16777215));
		$color = str_pad($color,6,'0');
		include("connexionBddErp.php");
		$listpoi = array();
		$req = $bddErp->prepare("select ag_poi.id,ft_commentaire_creation_oeie,ft_numero_oeie,ft_longitude,ft_latitude,account_analytic_account.name from ag_poi
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
		where ft_etat = '1' and atr_caff_traitant_id = ? and ft_longitude is not null and ft_longitude != 0
		");
		$req->execute(array($idCaff));
		while($data = $req->fetch())
		{

			$poi = (object) array();
			$poi->position = (object) array();
			$poi->position->lat = floatval($data['ft_longitude']);
			$poi->position->lng = floatval($data['ft_latitude']);
			$poi->title = $data['ft_numero_oeie'];
			$poi->commentaire = htmlspecialchars($data['ft_commentaire_creation_oeie']);
			$poi->icon = (object) array();
			$poi->icon->path = 0;
			$poi->icon->fillColor = '#'.$color;
			$poi->icon->fillOpacity = 1;
			$poi->icon->strokeColor = 'black';
			$poi->icon->strokeWeight = 1;
			$poi->icon->scale = 5;

			// path: google.maps.SymbolPath.CIRCLE,
			// fillColor: color,
			// fillOpacity: 1,
			// strokeColor: strokecolorpoi,
			// strokeWeight: strokeweightpoi,
			// scale: scalepoi,

			array_push($listpoi,$poi);
		}
		return json_encode($listpoi);
	}
	
	function getProchainesConges($idEmploye)
	{
		include("connexionBddErp.php");
		$conges = null;
		
		//$req = $bddErp->prepare("SELECT date_to, date_from, (date_from - NOW()) jours_av_conges, (NOW() - date_to) jours_restant FROM hr_holidays WHERE employee_id = ? AND date_to >= NOW() OR date_from >= NOW() ORDER BY date_to LIMIT 1");
		$req = $bddErp->prepare("SELECT date_from, date_to FROM hr_holidays WHERE employee_id = ? AND date_to >= NOW() ORDER BY date_to LIMIT 1");
		$req->execute(array($idEmploye));
		if($data = $req->fetch())
		{
			$conges = (object) array();
			$conges->temps_avant = strtotime($data["date_from"]) - time();
			$conges->dateDebut = $data["date_from"];
			$conges->dateFin = $data["date_to"];
			
			$continue = true;
			$nbJoursConges = 0;
			if($conges->temps_avant <= 0)
			{
				$dateDebutSimu = date("Y-m-d H:i:s");
			}
			else{
				$dateDebutSimu = $conges->dateDebut;
			}
			while($continue)
			{
				//echo $nbJoursConges;
				if(strtotime($dateDebutSimu) > strtotime($conges->dateFin))
				{
					$conges->dateFin = $dateDebutSimu;
				}
				$dateDebutSimu = new DateTime($dateDebutSimu);
				$req2 = $bddErp->prepare("SELECT name FROM training_holiday_period WHERE ? >= date_start AND ? <= date_stop");
				$req2->execute(array($dateDebutSimu->format('Y-m-d H:i:s'), $dateDebutSimu->format('Y-m-d H:i:s')));
				if($data2 = $req2->fetch())
				{
					if(strstr(strtoupper($data2["name"]), "WEEK") == false) //si ce n'est pas un jour de week-end (c'est donc un jour férié)
					{
						//echo "Ferie";
						$nbJoursConges++;
					}
					//echo "WEEK";
				}
				else{
					/*$dateDbut = $dateDebutSimu;
					$dateDbut = $dateDbut->modify("+20 hour"); //J'ajoute 23h à la date car sinon il y a un problème d'heure
					var_dump($dateDebutSimu);
					var_dump($dateDbut);*/

					//echo $dateDebutSimu->format('Y-m-d H:i:s')."\n\n";
					//echo "SELECT id FROM hr_holidays WHERE date_from <= ".$dateDbut->format('Y-m-d H:i:s')." AND date_to >= ".$dateDebutSimu->format('Y-m-d H:i:s')." AND employee_id = 387 \n\n";
					$req2 = $bddErp->query("SELECT id FROM hr_holidays WHERE DATE_PART('day', '".$dateDebutSimu->format('Y-m-d H:i:s')."' - date_from)  >= 0  AND DATE_PART('day', date_to - '".$dateDebutSimu->format('Y-m-d H:i:s')."') >= 0 AND employee_id = ".$idEmploye);
					//$req2->execute(array($dateDebutSimu->format('Y-m-d H:i:s'), $idEmploye));
					if($data2 = $req2->fetch())
					{
						//echo "conge";
						$nbJoursConges++;
					}
					else{
						$continue = false;
					}
				}
				
				if($continue)
				{
					$dateDebutSimu = $dateDebutSimu->modify("+1 day");
					$dateDebutSimu = $dateDebutSimu->format('Y-m-d H:i:s');
				}
				else{
					$conges->dateRetour = $dateDebutSimu->format('Y-m-d');
					$dateDebutSimu = $dateDebutSimu->modify("-1 day");
					$dateDebutSimu = $dateDebutSimu->format('Y-m-d');
					$conges->dateFin = $dateDebutSimu;
				}
			}
			$conges->nbJoursCongesRestant = $nbJoursConges;
			
			/*$conges = (object) array();
			$conges->date_debut = $data["date_from"];
			$conges->date_fin = $data["date_to"];
			$conges->temps_avant = strtotime($data["jours_av_conges"]);
			$conges->temps_restant = strtotime($data["jours_restant"]);
			
			$joursApresFinConges = true;
			$newDateFin = $conges->date_fin;
			$nbJoursConges = 0; //jours de conges + jours feries = +1, mais jour Week-End = +0
			$jourDebut = new DateTime($conges->date_debut);
			while($joursApresFinConges)
			{
				$jourFin = $jourFin->modify('+1 day');
				$timestampJourFin = strtotime($jourFin);
				$req2 = $bddErp->prepare("SELECT (? + interval '1 day') jour_suivant)");
				$req2->execute($newDateFin);
				if($data2 = $req2->fetch())
				{
					
				}
			}*/
		}
		
		return json_encode($conges);
	}
	
	
	
	function getPoiAffect(){
		include("connexionBdd.php");
		$liste_poi = array();
		$req = $bdd->query("SELECT erp_poi_id from cds_affectation");
		while($data = $req->fetch()){
			array_push($liste_poi,$data["erp_poi_id"]);
		}
		return json_encode($liste_poi);
	}

	function addPoiAffect($poi_id,$poi_num,$poi_domaine,$caff_id,$caff_name, $ui){
		
		$state = 1;
		$pilote = 'unknow';
		include("connexionBdd.php");
		$req = $bdd->prepare("INSERT INTO cds_affectation (erp_poi_id,erp_caff_name,erp_pilote_name,cds_affectation_date,cds_affectation_state_id,erp_poi,caff_id,erp_poi_domaine, erp_ui) VALUES (?,?,?,NOW(),?,?,?,?, ?)");
		$req->execute(array($poi_id,$caff_name,$pilote,$state,$poi_num,$caff_id,$poi_domaine, $ui));
		
	}
	
	function addPoiSimu($ft_sous_justification_oeie, $atr_ui, $ft_numero_oeie, $ft_numero_demande_42C, $ft_libelle_commune, $ft_libelle_de_voie, $ft_pg, $ft_oeie_dre, $ft_latitude, $insee_code, $ft_longitude, $ft_libelle_affaire, $ft_date_limite_realisation, $create_date, $ft_etat, $atr_domaine_id, $atr_caff_traitant_id)
	{
		include("connexionBddErp.php");
		$reponse = false;
		
		try{
			$req = $bddErp->prepare("INSERT INTO ag_poi(ft_sous_justification_oeie, atr_ui, ft_numero_oeie, \"ft_numero_demande_42C\", ft_libelle_commune, ft_libelle_de_voie, ft_pg, ft_oeie_dre, ft_latitude, insee_code, ft_longitude, ft_libelle_affaire, ft_date_limite_realisation, create_date, ft_etat, atr_domaine_id, atr_caff_traitant_id) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$reponse = $req->execute(array($ft_sous_justification_oeie, $atr_ui, $ft_numero_oeie, $ft_numero_demande_42C, $ft_libelle_commune, $ft_libelle_de_voie, $ft_pg, $ft_oeie_dre, $ft_latitude, $insee_code, $ft_longitude, $ft_libelle_affaire, $ft_date_limite_realisation, $create_date, $ft_etat, $atr_domaine_id, $atr_caff_traitant_id));
		}catch(Exception $e){
			$reponse = false;
		}
		return json_encode($reponse);
	}
	
	function addListePoiSimu($nb, $ui) //$ui = ft_zone (FC4, JR4...)
	{
		include("connexionBddErp.php");
		for($i = 0; $i < $nb; $i++)
		{
			$req = $bddErp->prepare("UPDATE ag_poi SET ft_etat = '1', atr_caff_traitant_id = (SELECT id FROM hr_employee WHERE UPPER(name_related) = 'AFFECTATION' LIMIT 1) WHERE id = (SELECT id from ag_poi WHERE ft_etat != '1' and atr_ui = ? AND ft_longitude is not null ORDER BY RANDOM()  LIMIT 1)");
			$req->execute(array($ui));
		}
	}

	function addListePoiSimuDomaines($nbDissi, $nbClient, $nbImmo, $nbFocu, $nbCoordi, $nbFors, $ui) //$ui = ft_zone (FC4, JR4...)
	{
		include("connexionBddErp.php");
		for($i = 0; $i < $nbDissi; $i++)
		{
			$req = $bddErp->prepare("UPDATE ag_poi SET ft_etat = '1', atr_caff_traitant_id = (SELECT id FROM hr_employee WHERE UPPER(name_related) = 'AFFECTATION' LIMIT 1) WHERE id = (SELECT id from ag_poi WHERE ft_etat != '1' and atr_ui = ? AND ft_longitude is not null AND atr_domaine_id = (SELECT id FROM account_analytic_account WHERE UPPER(name) = 'DISSI' LIMIT 1)  ORDER BY RANDOM()  LIMIT 1)");
			$req->execute(array($ui));
		}
		for($i = 0; $i < $nbClient; $i++)
		{
			$req = $bddErp->prepare("UPDATE ag_poi SET ft_etat = '1', atr_caff_traitant_id = (SELECT id FROM hr_employee WHERE UPPER(name_related) = 'AFFECTATION' LIMIT 1) WHERE id = (SELECT id from ag_poi WHERE ft_etat != '1' and atr_ui = ? AND ft_longitude is not null AND atr_domaine_id = (SELECT id FROM account_analytic_account WHERE UPPER(name) = 'CLIENT' LIMIT 1)  ORDER BY RANDOM()  LIMIT 1)");
			$req->execute(array($ui));
		}
		for($i = 0; $i < $nbImmo; $i++)
		{
			$req = $bddErp->prepare("UPDATE ag_poi SET ft_etat = '1', atr_caff_traitant_id = (SELECT id FROM hr_employee WHERE UPPER(name_related) = 'AFFECTATION' LIMIT 1) WHERE id = (SELECT id from ag_poi WHERE ft_etat != '1' and atr_ui = ? AND ft_longitude is not null AND atr_domaine_id = (SELECT id FROM account_analytic_account WHERE UPPER(name) = 'IMMO' LIMIT 1)  ORDER BY RANDOM()  LIMIT 1)");
			$req->execute(array($ui));
		}
		for($i = 0; $i < $nbFocu; $i++)
		{
			$req = $bddErp->prepare("UPDATE ag_poi SET ft_etat = '1', atr_caff_traitant_id = (SELECT id FROM hr_employee WHERE UPPER(name_related) = 'AFFECTATION' LIMIT 1) WHERE id = (SELECT id from ag_poi WHERE ft_etat != '1' and atr_ui = ? AND ft_longitude is not null AND atr_domaine_id = (SELECT id FROM account_analytic_account WHERE UPPER(name) = 'FO & CU' LIMIT 1)  ORDER BY RANDOM()  LIMIT 1)");
			$req->execute(array($ui));
		}
		for($i = 0; $i < $nbCoordi; $i++)
		{
			$req = $bddErp->prepare("UPDATE ag_poi SET ft_etat = '1', atr_caff_traitant_id = (SELECT id FROM hr_employee WHERE UPPER(name_related) = 'AFFECTATION' LIMIT 1) WHERE id = (SELECT id from ag_poi WHERE ft_etat != '1' and atr_ui = ? AND ft_longitude is not null AND atr_domaine_id = (SELECT id FROM account_analytic_account WHERE UPPER(name) = 'COORDI' LIMIT 1)  ORDER BY RANDOM()  LIMIT 1)");
			$req->execute(array($ui));
		}
		for($i = 0; $i < $nbFors; $i++)
		{
			$req = $bddErp->prepare("UPDATE ag_poi SET ft_etat = '1', atr_caff_traitant_id = (SELECT id FROM hr_employee WHERE UPPER(name_related) = 'AFFECTATION' LIMIT 1) WHERE id = (SELECT id from ag_poi WHERE ft_etat != '1' and atr_ui = ? AND ft_longitude is not null AND atr_domaine_id = (SELECT id FROM account_analytic_account WHERE UPPER(name) = 'FORS' LIMIT 1)  ORDER BY RANDOM()  LIMIT 1)");
			$req->execute(array($ui));
		}
		
	}
	
	function getPositionAleatoireByUi($ui) //$ui = ft_zone (FC4, JR4...)
	{
		include("connexionBddErp.php");
		$position = null;
		$req = $bddErp->prepare("SELECT ft_libelle_commune, ft_libelle_de_voie, ft_latitude, ft_longitude FROM ag_poi 
								WHERE atr_ui = ?
								AND ft_etat != '1' 
								AND ft_libelle_commune != '' AND ft_libelle_commune IS NOT NULL 
								AND  ft_libelle_de_voie != '' AND ft_libelle_de_voie IS NOT NULL
								AND ft_latitude IS NOT NULL
								AND ft_longitude IS NOT NULL
								ORDER BY random() LIMIT 1");
		$req->execute(array($ui));
		if($data = $req->fetch())
		{
			$position = (object) array();
			$position->commune = $data["ft_libelle_commune"];
			$position->voie = $data["ft_libelle_de_voie"];
			$position->longitude = $data["ft_longitude"];
			$position->latitude = $data["ft_latitude"];
		}
		
		return json_encode($position);
	}
	function addAdvancedConfigUI($ui){
		include("connexionBdd.php");

		$req = $bdd->prepare("SELECT ui from cds_advanced_config where ui = ?");
		$req->execute(array($ui));
		if($req->fetch()){
			return false;
		}
		else
		{
			$req2 = $bdd->prepare("INSERT INTO cds_advanced_config (ui) VALUES (?)");
			$req2->execute(array($ui));
			return true;
		}
	}
	function entraideCaff($idCaff, $idSite, $listeDomaines, $dateExpiration, $dateDebut, $idSiteDefaut, $taux)
	{
		include("connexionBdd.php");

		$reponse = false;
		try{
			$req = $bdd->prepare("INSERT INTO cds_entraide(caff_id, site_entraide_id, domaines, date_expiration, date_debut, site_defaut_id, taux) VALUES(?, ?, ?, ?, ?, ?, ?)");
			$reponse = $req->execute(array($idCaff, $idSite, $listeDomaines, $dateExpiration, $dateDebut, $idSiteDefaut, $taux));
		}catch(Exception $e){
			$reponse = false;
		}
		return json_encode($reponse);
	}



	function getLibelleSiteById($idSite)
	{
		include("connexionBddErp.php");

		$libelle = null;
		$req = $bddErp->prepare("SELECT name FROM ag_site WHERE id = ?");
		$req->execute(array($idSite));
		if($data = $req->fetch())
		{
			$libelle = $data["name"];
		}
		return json_encode($libelle);
	}

	function removeEntraideById($idEntraide)
	{
		include("connexionBdd.php");

		$reponse = false;
		try{
			$req = $bdd->prepare("DELETE FROM cds_entraide WHERE id = ?");
			$reponse = $req->execute(array($idEntraide));
		}catch(Exception $e){
			$reponse = false;
		}
		return json_encode($reponse);
	}

	function removeAdvancedConfigUI($ui){
		include("connexionBdd.php");
		
		$req = $bdd->prepare("DELETE FROM cds_advanced_config where ui = ?");
		$req->execute(array($ui));
	}

	function getListeCaffFormation(){
		include("connexionBdd.php");
		$listCaffFormation = array();
		$req = $bdd->query("SELECT * FROM cds_formation");
		while($data = $req->fetch()){
			array_push($listCaffFormation, $data['caff_id']);
		}
		return json_encode($listCaffFormation);
	}
	function resetSimulation()
	{
		include("connexionBddErp.php");
		$reponse = false;
		try{
			$reponse = $bddErp->exec("update ag_poi set atr_caff_traitant_id = 1, ft_etat = '6' where atr_caff_traitant_id = 357");
		}catch(Exception $e){
			$reponse = false;
		}
		return json_encode($reponse);
	}
?>