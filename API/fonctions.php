<?php
	function getPoiNA()
	{
		include("connexionBddErp.php");
		
		$listePoi = array();
		
		$req = $bddErp->query("select ag_poi.id,ag_poi.ft_sous_justification_oeie, ag_poi.atr_ui, ag_poi.ft_numero_oeie, account_analytic_account.name as domaine, ag_poi.ft_titulaire_client, ft_libelle_commune, ft_libelle_de_voie, ft_pg,ft_oeie_dre,ft_latitude,insee_code,ft_longitude,ft_libelle_affaire,ft_date_limite_realisation,ag_poi.create_date from ag_poi
		left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
		where hr_employee.name_related in ('MATHIASIN Celine','AFFECTATION') and ft_etat = '1'");
		
		while($data = $req->fetch())
		{
			$poi = (object) array();
			$poi->atr_ui = $data["atr_ui"];
			$poi->ft_numero_oeie = $data["ft_numero_oeie"];
			$poi->domaine = $data["domaine"];
			$poi->ft_titulaire_client = $data["ft_titulaire_client"];
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
		
		$req = $bddErp->query("select id,t3.name_related, t3.mobile_phone, t3.work_email, t3.site, t3.agence,case when t3.reactive is null then 0 else t3.reactive end,
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
		where res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE')) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
		group by t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, account_analytic_account.name) t2
		group by t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name ) t3
		where name_related is not null ORDER BY name_related");
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
		where res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE')) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
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
			ag_poi.ft_titulaire_client,
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
			$poi->ft_titulaire_client = $data["ft_titulaire_client"];
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
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=".$origins."&destinations=".$destinations."&key=".$key;
		$listeDistances = json_decode(file_get_contents($url));
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
		
		$req = $bddErp->prepare("SELECT COUNT(*) nb FROM ag_poi WHERE ((ft_titulaire_client = ? AND ft_titulaire_client != '' AND ft_titulaire_client IS NOT NULL) OR (ft_libelle_commune = ? AND ft_libelle_commune != '' AND ft_libelle_commune IS NOT NULL) OR (ft_libelle_commune = ? AND ft_libelle_de_voie = ? AND ft_libelle_commune != '' AND ft_libelle_de_voie != '' AND ft_libelle_commune IS NOT NULL AND ft_libelle_de_voie IS NOT NULL)) AND (atr_ui = ? AND atr_ui != '' AND atr_ui IS NOT NULL) AND atr_caff_traitant_id IN( select test2.employee_id from res_users  
				left join (select hr_job.name as job,ag_site.name as site,hr_employee.id as employee_id,hr_employee.name_related as employee_name,test.name_related as mana_name from hr_employee
				left join ag_site on hr_employee.ag_site_id = ag_site.id
				left join hr_job on hr_employee.job_id = hr_job.id
			   left join (select id,name_related,parent_id from hr_employee) test on hr_employee.parent_id = test.id
			   order by test.name_related)test2 on res_users.ag_employee_id = test2.employee_id
			   where active is true and login not in ('admin','import_portefeuille','CONGES') and job in ('CAFF FT','CAFF MIXTE')
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
		WHERE p.ft_titulaire_client = ?
		AND p.ft_titulaire_client != ''
		AND p.ft_titulaire_client IS NOT NULL
		AND p.atr_caff_traitant_id IN( select test2.employee_id from res_users  
				left join (select hr_job.name as job,ag_site.name as site,hr_employee.id as employee_id,hr_employee.name_related as employee_name,test.name_related as mana_name from hr_employee
				left join ag_site on hr_employee.ag_site_id = ag_site.id
				left join hr_job on hr_employee.job_id = hr_job.id
			   left join (select id,name_related,parent_id from hr_employee) test on hr_employee.parent_id = test.id
			   order by test.name_related)test2 on res_users.ag_employee_id = test2.employee_id
			   where active is true and login not in ('admin','import_portefeuille','CONGES') and job in ('CAFF FT','CAFF MIXTE')
		)
		GROUP BY p.atr_caff_traitant_id, e.name_related, e.ag_site_id
		ORDER BY nb DESC");
		$req->execute(array($titulaire));
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
		WHERE p.ft_libelle_de_voie = ?
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
			   where active is true and login not in ('admin','import_portefeuille','CONGES') and job in ('CAFF FT','CAFF MIXTE')
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
		WHERE p.ft_libelle_commune = ?
		AND p.ft_libelle_commune != ''
		AND p.ft_libelle_commune IS NOT NULL
		AND p.atr_caff_traitant_id IN( select test2.employee_id from res_users  
				left join (select hr_job.name as job,ag_site.name as site,hr_employee.id as employee_id,hr_employee.name_related as employee_name,test.name_related as mana_name from hr_employee
				left join ag_site on hr_employee.ag_site_id = ag_site.id
				left join hr_job on hr_employee.job_id = hr_job.id
			   left join (select id,name_related,parent_id from hr_employee) test on hr_employee.parent_id = test.id
			   order by test.name_related)test2 on res_users.ag_employee_id = test2.employee_id
			   where active is true and login not in ('admin','import_portefeuille','CONGES') and job in ('CAFF FT','CAFF MIXTE')
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
	
	function getListePoiByCaffByTitulaire($idCaff, $titulaire)
	{
		include("connexionBddErp.php");
		
		$listePoi = array();
		$req = $bddErp->prepare("SELECT id FROM ag_poi WHERE atr_caff_traitant_id = ? AND ft_titulaire_client = ? AND ft_titulaire_client != '' AND ft_titulaire_client IS NOT NULL ORDER BY ft_oeie_dre");
		$req->execute(array($idCaff, $titulaire));
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
		$req = $bddErp->prepare("SELECT id FROM ag_poi WHERE atr_caff_traitant_id = ? AND ft_libelle_de_voie = ? AND ft_libelle_commune = ? AND ft_libelle_de_voie != '' AND ft_libelle_de_voie IS NOT NULL AND ft_libelle_commune != '' AND ft_libelle_commune IS NOT NULL ORDER BY ft_oeie_dre");
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
		$req = $bddErp->prepare("SELECT id FROM ag_poi WHERE atr_caff_traitant_id = ? AND ft_libelle_commune = ? AND ft_libelle_commune != '' AND ft_libelle_commune IS NOT NULL ORDER BY ft_oeie_dre");
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
		$req = $bdd->query("SELECT distinct site FROM cds_transco_ui_site ORDER BY site");
		while($data = $req->fetch())
		{
			array_push($sites, $data["site"]);
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
	
	function getNbPoiClientByCaff($idCaff, $client)
	{
		include("connexionBddErp.php");
		$nbPoi = 0;
		$req = $bddErp->prepare("SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = ? AND ft_titulaire_client = ? AND ft_titulaire_client IS NOT NULL AND ft_titulaire_client != '' AND ft_titulaire_client != 'suppr. CNIL'");
		$req->execute(array($idCaff, $client));
		if($data = $req->fetch())
		{
			$nbPoi = $data["nb"];;
		}
		return json_encode($nbPoi);
	}
	
	/*function getAffectationAuto($idPoi, $km)
	{
		include("connexionBddErp.php");
		include("connexionBdd.php");
		
		$caffAuto = null;
		
		$coefNbPoiProimite = 0.5;
		$coefNbPoiClient = 0.8;
		
		$poi = json_decode(getPoiById($idPoi));
		$listeCaffs = json_decode(nbPoiCaffByRadius($poi->ft_latitude, $poi->ft_longitude, $km));
		foreach($listeCaffs as $caff)
		{
			$caff = json_decode(getCaffById($caff->caff->id));
			
			if($caff != null)
			{
				$charge = json_decode(getChargeCaff(json_encode($caff)));
			
				$nbPoiProximite = intval(json_decode(getNbPoiProximiteByCaffByPoi($idPoi, $caff->id, $km)));
				
				$nbPoiClient = intval(json_decode(getNbPoiClientByCaff($caff->id, $poi->ft_titulaire_client)));
				
				$listePoi = json_decode(getPoiAffecteByCaff($caff->name_related));
				$nbPoiEnRetard = 0;
				$dateAjd = new DateTime("now");
				foreach($listePoi as $poi)
				{
					$dre = new DateTime($poi->ft_oeie_dre);
					if($dateAjd > $dre)
					{
						$nbPoiEnRetard++;
					}
				}
				if(sizeof($listePoi) > 0)
				{
					$tauxDre = $nbPoiEnRetard / sizeof($listePoi);
				}
				else{
					$tauxDre = 0;
				}
				
				$chargeGlobale = $charge - ($nbPoiProximite * $coefNbPoiProimite) + ($tauxDre * $caff->reactive) + ($nbPoiClient * $coefNbPoiClient);
				
				if($caffAuto == null)
				{
					$caffAuto = $caff;
					$caffAuto->chargeGlobale = $chargeGlobale;
				}
				elseif($caffAuto->chargeGlobale > $chargeGlobale){
					$caffAuto = $caff;
					$caffAuto->chargeGlobale = $chargeGlobale;
				}
			}
		}
		
		return json_encode($caffAuto);
	}*/
	
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
		where res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE')) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
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
	
	function getAffectationAuto($idPoi, $km, $coefNbPoiProimite, $coefNbPoiClient, $coefCharge)//, $listeCaffsSimulation) // $listeCaffsSimulation (facultatif) = array en json
	{
		include("connexionBddErp.php");
		include("connexionBdd.php");
		
		$caffAuto = null;
		
		//$listeCaffsSimulation = json_decode($listeCaffsSimulation);
		
		/*$coefNbPoiProimite = 0.5;
		$coefNbPoiClient = 0.8;
		$coefCharge = 0.5;*/
		
		$poi = json_decode(getPoiById($idPoi));
		
		$listeSites = json_decode(getSitesByUi($poi->atr_ui));
		
		$listeIdSites = array();
		foreach($listeSites as $site)
		{
			array_push($listeIdSites, $site->id);
		}
		$listeIdSites = implode(", ", $listeIdSites);
		
		$listeCaffs = array();
		
		$req = $bddErp->query("SELECT id, name_related, mobile_phone, work_email, site, site_id, agence, reactive, non_reactive, ((reactive + (non_reactive * ".$coefCharge.")) 
        - ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND sqrt(power((ft_longitude - ".$poi->ft_longitude.")/0.0090808,2)+power((ft_latitude - ".$poi->ft_latitude.")/0.01339266,2)) < ".$km." AND ft_etat = '1') * ".$coefNbPoiProimite.")
		+ ((SELECT COUNT(*) nb FROM ag_poi WHERE atr_caff_traitant_id = caff.id AND ft_titulaire_client = '".$poi->ft_titulaire_client."' AND ft_titulaire_client IS NOT NULL AND ft_titulaire_client != '' AND ft_titulaire_client != 'suppr. CNIL') * ".$coefNbPoiClient.")
        )charge_totale 
		FROM (select id,t3.name_related, t3.mobile_phone, t3.work_email, t3.site, t3.site_id, t3.agence,case when t3.reactive is null then 0 else t3.reactive end,
		case when t3.non_reactive is null then 0 else t3.non_reactive end from
		(
		select t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.site_id, t2.name as agence, sum(t2.reactive) as reactive, sum(t2.non_reactive) as non_reactive from (
		 
		select t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site, t1.site_id,t1.name, case when account_analytic_account.name in ('Client', 'FO & CU') then count (ag_poi.id)
		end as reactive , case when account_analytic_account.name not in ('Client', 'FO & CU') then count (ag_poi.id) end as non_reactive
		from ag_poi
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id  
		full join
		(select hr_employee.id, hr_employee.name_related,hr_employee.mobile_phone,hr_employee.work_email,ag_site.name as site, ag_site.id as site_id,ag_agence.name from res_users
		full join hr_employee on res_users.ag_employee_id = hr_employee.id
		full join ag_site on hr_employee.ag_site_id = ag_site.id
		full join ag_agence on hr_employee.ag_agence_id = ag_agence.id
		full join hr_job on hr_employee.job_id = hr_job.id
		where res_users.active = true and hr_job.name in ('CAFF FT','CAFF MIXTE')) t1 on ag_poi.atr_caff_traitant_id = t1.id and ft_etat in ('1','5') and ag_poi.ft_numero_oeie not like '%MBB%'
		group by t1.id, t1.name_related,t1.mobile_phone,t1.work_email,t1.site,t1.name, account_analytic_account.name, t1.site_id) t2
		group by t2.id, t2.name_related, t2.mobile_phone, t2.work_email, t2.site, t2.name, t2.site_id ) t3
		where name_related is not null AND site_id IN(".$listeIdSites."))caff
        ORDER BY charge_totale");
		while($data = $req->fetch())
		{
			$req2 = $bdd->prepare("SELECT id FROM cds_formation WHERE caff_id = ?");
			$req2->execute(array($data["id"]));
			if(!$data2 = $req2->fetch())
			{
				/*$caffInSimulation = false;
				
				if($listeCaffsSimulation != null && sizeof($listeCaffsSimulation) > 0)
				{
					//var_dump($listeCaffsSimulation);
					//return json_encode($listeCaffsSimulation);
					foreach($listeCaffsSimulation as $caffSimulation)
					{
						//var_dump($caffSimulation);
						if($caffSimulation != null && $caffSimulation->id == $data["id"])
						{
							$caffInSimulation = true;
							$caff = $caffSimulation;
							
							$nbPoiSimulation = sizeof($caff->listePoiSimulation)-1;
							//var_dump($nbPoiSimulation);
							if($caff->listePoiSimulation[$nbPoiSimulation]->reactive == true)
							{
								$caff->charge_totale += 1;
							}
							else{
								$caff->charge_totale += $coefCharge;
							}
						}
					}
				}
				if(!$caffInSimulation)
				{*/
					$caff = (object) array();
					$caff->name_related = $data["name_related"];
					$caff->mobile_phone = $data["mobile_phone"];
					$caff->work_email = $data["work_email"];
					$caff->site = $data["site"];
					$caff->site_id = $data["site_id"];
					$caff->agence = $data["agence"];
					$caff->reactive = $data["reactive"];
					$caff->non_reactive = $data["non_reactive"];
					$caff->charge_totale = $data["charge_totale"];
					$caff->id = $data["id"];
					
					$listePoi = json_decode(getPoiAffecteByCaff($caff->name_related));
					$nbPoiEnRetard = 0;
					$dateAjd = new DateTime("now");
					foreach($listePoi as $poi)
					{
						$dre = new DateTime($poi->ft_oeie_dre);
						if($dateAjd > $dre)
						{
							$nbPoiEnRetard++;
						}
					}
					if(sizeof($listePoi) > 0)
					{
						$tauxDre = $nbPoiEnRetard / sizeof($listePoi);
					}
					else{
						$tauxDre = 0;
					}
					$caff->charge_totale += ($tauxDre * $caff->reactive);
				//}
				
				$ceCaff = (object) array();
				$ceCaff->id = $caff->id;
				$ceCaff->name_related = $caff->name_related;
				$ceCaff->charge_totale = $caff->charge_totale;
				array_push($listeCaffs, $ceCaff);
				
				if($caffAuto == null)
				{
					$caffAuto = $caff;
				}
				elseif($caffAuto->charge_totale > $caff->charge_totale){
					$caffAuto = $caff;
				}
			}
			
		}
		
		if($caffAuto != null)
		{
			function comparer($a, $b) {
				if($a->charge_totale < $b->charge_totale)
				{
					return -1;
				}
				elseif($a->charge_totale == $b->charge_totale)
				{
					return 0;
				}
				else{
					return 1;
				}
			}
			usort($listeCaffs, 'comparer');
			
			$caffAuto->listeAutresCaffs = $listeCaffs;
		}
		
		return json_encode($caffAuto);
	}
	
	function getPoiNAByUi($ui) //ft_zone
	{
		include("connexionBddErp.php");
		$listePoi = array();
		$req = $bddErp->prepare("select ag_poi.id,ag_poi.ft_sous_justification_oeie, ag_poi.atr_ui, ag_poi.ft_numero_oeie, account_analytic_account.name as domaine, ag_poi.ft_titulaire_client, ft_libelle_commune, ft_libelle_de_voie, ft_pg,ft_oeie_dre,ft_latitude,insee_code,ft_longitude,ft_libelle_affaire,ft_date_limite_realisation,ag_poi.create_date from ag_poi
		left join hr_employee on ag_poi.atr_caff_traitant_id = hr_employee.id
		left join account_analytic_account on ag_poi.atr_domaine_id = account_analytic_account.id
		where hr_employee.name_related in ('MATHIASIN Celine','AFFECTATION') and ft_etat = '1' AND ag_poi.atr_ui = ?");
		$req->execute(array($ui));
		while($data = $req->fetch())
		{
			$poi = (object) array();
			$poi->atr_ui = $data["atr_ui"];
			$poi->ft_numero_oeie = $data["ft_numero_oeie"];
			$poi->domaine = $data["domaine"];
			$poi->ft_titulaire_client = $data["ft_titulaire_client"];
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
?>