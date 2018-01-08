<?php
	require_once("fonctions.php");
	
	/*if(!isset($_POST["liste_caffs_simulation"]) || $_POST["liste_caffs_simulation"] == null || $_POST["liste_caffs_simulation"] == "")
	{
		$_POST["liste_caffs_simulation"] = array();
		$_POST["liste_caffs_simulation"] = json_encode($_POST["liste_caffs_simulation"]);
	}*/
	
	echo getAffectationAuto($_POST["poi_id"], $_POST["km"], $_POST["coef_poi_proxi"], $_POST["coef_charge_reactive"], $_POST["coef_charge"], $_POST["limite_jour"], $_POST["limite_semaine"], $_POST["limite_max_calcul"], $_POST["nb_jours_avant_conges_max"], $_POST["nb_jours_conges_max"]);//, $_POST["liste_caffs_simulation"]);
	//echo getAffectationAuto(159188, 20, 0.5, 1, 0.1, 3, 10, 20, 5, 5);
	
	/*$listeCaffsSimulation = array();
	
	$caff1 = (object) array();
	$caff1->id = 1;
	$caff1->charge_totale = 40;
	$caff1->listePoiSimulation = array();
	$poi = (object) array();
	$poi->reactive = true;
	array_push($caff1->listePoiSimulation, $poi);
	$poi2 = (object) array();
	$poi2->reactive = true;
	array_push($caff1->listePoiSimulation, $poi2);
	$poi3 = (object) array();
	$poi3->reactive = false;
	array_push($caff1->listePoiSimulation, $poi3);
	array_push($listeCaffsSimulation, $caff1);
	
	$caff2 = (object) array();
	$caff2->id = 1;
	$caff2->charge_totale = 40;
	$caff2->listePoiSimulation = array();
	$poi = (object) array();
	$poi->reactive = true;
	array_push($caff2->listePoiSimulation, $poi);
	$poi2 = (object) array();
	$poi2->reactive = true;
	array_push($caff2->listePoiSimulation, $poi2);
	$poi3 = (object) array();
	$poi3->reactive = false;
	array_push($caff2->listePoiSimulation, $poi3);
	array_push($listeCaffsSimulation, $caff2);
	$listeCaffsSimulation = json_encode($listeCaffsSimulation);*/
	
	/*$listeCaffsSimulation = '[{"name_related":"FOUCAULT Thomas","mobile_phone":"07 77 16 26 71","work_email":"Thomas.FOUCAULT@ambitiontelecom.com","site":"St Priest Progrès","site_id":10,"agence":"RHONE","reactive":"0","non_reactive":"0","charge_totale":0,"id":329,"listeAutresCaffs":[{"id":329,"name_related":"FOUCAULT Thomas","charge_totale":0},{"id":360,"name_related":"SALEM Hatem","charge_totale":16},{"id":393,"name_related":"BRIDI Hamza","charge_totale":17},{"id":57,"name_related":"VINDRET Jean Francois","charge_totale":17},{"id":254,"name_related":"PHEMIUS Garry","charge_totale":25.5},{"id":253,"name_related":"FILIPOWSKI François","charge_totale":28.5},{"id":83,"name_related":"HUBERT Philippe","charge_totale":29},{"id":281,"name_related":"MIMOSO Ludovic","charge_totale":40},{"id":332,"name_related":"BOSSAN Pascal","charge_totale":44.5},{"id":44,"name_related":"TAMBELLINI Guy","charge_totale":48.5},{"id":91,"name_related":"PERRET Roland","charge_totale":56},{"id":316,"name_related":"FAYOLLE Peter","charge_totale":56.5},{"id":53,"name_related":"PLASSE Jean Louis","charge_totale":57.5},{"id":73,"name_related":"BRUGERE Michel","charge_totale":60},{"id":74,"name_related":"FLOTTE Michel","charge_totale":60},{"id":9,"name_related":"BRAZILIER Antoine","charge_totale":60},{"id":358,"name_related":"YOUSFI Hichem","charge_totale":65},{"id":127,"name_related":"SERVE Thomas","charge_totale":74},{"id":8,"name_related":"PETER Alexandre","charge_totale":75},{"id":159,"name_related":"MOREAU Nicolas","charge_totale":89},{"id":34,"name_related":"CLAIS Florian","charge_totale":105.5}],"listePoiSimulation":[{"atr_ui":"QFY","ft_numero_oeie":"LYO708430","domaine":"Client","ft_titulaire_client":"","ft_libelle_commune":"SAINT GENIS LES OLLIERES (69205)","ft_libelle_de_voie":"R MARIUS PONCET","ft_pg":"68","ft_sous_justification_oeie":"YF","ft_oeie_dre":"2017-11-28","ft_latitude":"4.73333","insee_code":"69205","ft_longitude":"45.75","ft_libelle_affaire":null,"ft_date_limite_realisation":null,"create_date":"2017-11-24 08:09:44.304704","id":154513,"reactive":true},{"atr_ui":"QFY","ft_numero_oeie":"BRG702566","domaine":"Immo","ft_titulaire_client":"","ft_libelle_commune":"MIRIBEL (01249)","ft_libelle_de_voie":"ALLEE DES TERRASSES ST MARTIN","ft_pg":"67","ft_sous_justification_oeie":"AO","ft_oeie_dre":"2017-12-12","ft_latitude":"4.95306","insee_code":"1249","ft_longitude":"45.8245","ft_libelle_affaire":null,"ft_date_limite_realisation":null,"create_date":"2017-11-30 14:05:59.923528","id":155375,"reactive":false,"affectationAuto":{"name_related":"FOUCAULT Thomas","mobile_phone":"07 77 16 26 71","work_email":"Thomas.FOUCAULT@ambitiontelecom.com","site":"St Priest Progrès","site_id":10,"agence":"RHONE","reactive":"0","non_reactive":"0","charge_totale":1,"id":329,"listeAutresCaffs":[{"id":329,"name_related":"FOUCAULT Thomas","charge_totale":1},{"id":393,"name_related":"BRIDI Hamza","charge_totale":17},{"id":57,"name_related":"VINDRET Jean Francois","charge_totale":17},{"id":360,"name_related":"SALEM Hatem","charge_totale":19},{"id":83,"name_related":"HUBERT Philippe","charge_totale":26.5},{"id":253,"name_related":"FILIPOWSKI François","charge_totale":30.5},{"id":281,"name_related":"MIMOSO Ludovic","charge_totale":38.5},{"id":332,"name_related":"BOSSAN Pascal","charge_totale":39},{"id":254,"name_related":"PHEMIUS Garry","charge_totale":42.5},{"id":127,"name_related":"SERVE Thomas","charge_totale":45.5},{"id":44,"name_related":"TAMBELLINI Guy","charge_totale":49.5},{"id":316,"name_related":"FAYOLLE Peter","charge_totale":58},{"id":74,"name_related":"FLOTTE Michel","charge_totale":60},{"id":53,"name_related":"PLASSE Jean Louis","charge_totale":60.5},{"id":73,"name_related":"BRUGERE Michel","charge_totale":65.5},{"id":358,"name_related":"YOUSFI Hichem","charge_totale":66.5},{"id":8,"name_related":"PETER Alexandre","charge_totale":69.5},{"id":9,"name_related":"BRAZILIER Antoine","charge_totale":78.5},{"id":91,"name_related":"PERRET Roland","charge_totale":84},{"id":159,"name_related":"MOREAU Nicolas","charge_totale":89},{"id":34,"name_related":"CLAIS Florian","charge_totale":92.5}],"listePoiSimulation":[null]}}]},null]';
	
	echo getAffectationAuto(154513, 100, 0.5, 0.8, 0.5, $listeCaffsSimulation);*/
	
?>