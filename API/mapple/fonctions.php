<?php
	function gestionMail($auteur, $objet, $message)
	{
		$obj = (object) array();
		$obj->numAs = getInfoInMail($message, "AS n° :", "OEIE n° :");
		$obj->oeie = getInfoInMail($message, "OEIE n° :", "créés le ");
		$obj->commune = getInfoInMail($message, "Commune :", "Code INSEE :");
		$obj->insee = getInfoInMail($message, "Code INSEE :", "Voie :");
		$obj->voie = getInfoInMail($message, "Voie :", "Code RIVOLI :");
		$obj->rivoli = getInfoInMail($message, "Code RIVOLI :", "N° dans la voie :");
		$obj->numVoie = getInfoInMail($message, "N° dans la voie :", "Centre :");
		$obj->centre = getInfoInMail($message, "Centre :", "Zone :");
		$obj->zone = getInfoInMail($message, "Zone :", "DRE :");
		$obj->dre = getInfoInMail($message, "DRE :", "DLR :");
		$obj->dlr = getInfoInMail($message, "DLR :", "Sous-justification :");
		$obj->sousJustification = getInfoInMail($message, "Sous-justification :", "Commentaire création AS :");
		$obj->creationAs = getInfoInMail($message, "Commentaire création AS :", "Commentaire Réponse AS :");
		$obj->reponseAs = getInfoInMail($message, "Commentaire Réponse AS :", "Le n° de demande 42c :");
		$obj->numDemande42c = getInfoInMail($message, "Le n° de demande 42c :", "Titulaire :");
		$obj->titulaire = getInfoInMail($message, "Titulaire :", "Autre(s) demande(s) 42c associée(s) :");
		$obj->autresDemandes42c = getInfoInMail($message, "Autre(s) demande(s) 42c associée(s) :", "Bonne journée,");
		$obj->ui = json_decode(getUiByOeie($obj->oeie));

		$headers = "";
		//$headers .= "From: " . strip_tags($_POST['req-email']) . "\r\n";
		//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
		//$headers .= "CC: susan@example.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";
		
		if($obj->oeie == "")
		{
			mail($auteur, "Réponse: ".$objet, "Le mail que vous avez envoyé n'est pas conforme. Veuillez faire la manipulation manuellement.", $headers);
		
			$obj->isMappleMail = false;
		}
		else{
			mail($auteur, "Réponse: ".$objet, "Réponse positive", $headers);

			// include("odoo/odoo.php");
			// include("../connexionBddErp.php");

			// $req = $bddErp->prepare("SELECT id FROM ag_poi WHERE ft_numero_oeie = ? ORDER BY id DESC");
			// $req->execute(array($obj->oeie));
			// if($data = $req->fetch())
			// {
			// 	$models->execute_kw($db, $uid, $password, 'ag.poi', 'write',
			// 		array(array($data["id"]), array('ft_numero_as'=>$obj->numAs, 
			// 									'ft_numero_oeie'=>$obj->oeie, 
			// 									"ft_libelle_commune"=>$obj->commune, 
			// 									"insee_code"=>$obj->insee, 
			// 									"ft_libelle_de_voie"=>$obj->voie,
			// 									"agilis_code_rivoli"=>$obj->rivoli,
			// 									"ft_numero_de_voie"=>$obj->numVoie,
			// 									"ft_code_centre_oeie"=>$obj->centre,
			// 									"ft_zone_as"=>$obj->zone,
			// 									"ft_oeie_dre"=>$obj->dre,
			// 									"ft_date_limite_realisation"=>$obj->dlr,
			// 									"ft_sous_justification_oeie"=>$obj->sousJustification,
			// 									"ft_commentaire_creation_as"=>$obj->creationAs,
			// 									"agilis_as_commentaire_reponse"=>$obj->reponseAs,
			// 									"ft_numero_demande_42C"=>$obj->numDemande42c,
			// 									"ft_titulaire_client"=>$obj->titulaire,
			//									"atr_ui"=>$obj->ui,
			// 									"mail_affectation"=>true
			// 								)));
			// 	$obj->id = $data["id"];
			// 	$obj->isMappleMail = true;
			// }
			// else{
			// 	$id = $models->execute_kw($db, $uid, $password,
			// 		'ag.poi', 'create',
			// 		array(array('ft_numero_as'=>$obj->numAs, 
			// 					'ft_numero_oeie'=>$obj->oeie, 
			// 					"ft_libelle_commune"=>$obj->commune, 
			// 					"insee_code"=>$obj->insee, 
			// 					"ft_libelle_de_voie"=>$obj->voie,
			// 					"agilis_code_rivoli"=>$obj->rivoli,
			// 					"ft_numero_de_voie"=>$obj->numVoie,
			// 					"ft_code_centre_oeie"=>$obj->centre,
			// 					"ft_zone_as"=>$obj->zone,
			// 					"ft_oeie_dre"=>$obj->dre,
			// 					"ft_date_limite_realisation"=>$obj->dlr,
			// 					"ft_sous_justification_oeie"=>$obj->sousJustification,
			// 					"ft_commentaire_creation_as"=>$obj->creationAs,
			// 					"agilis_as_commentaire_reponse"=>$obj->reponseAs,
			// 					"ft_numero_demande_42C"=>$obj->numDemande42c,
			// 					"ft_titulaire_client"=>$obj->titulaire,
			//					"atr_ui"=>$obj->ui,
			// 					"mail_affectation"=>true
			// 				)));
			// 	$obj->id = $id;
			// 	$obj->isMappleMail = true;
			// }
		}
		
		return json_encode($obj);
	}
	
	function getInfoInMail($message, $stringBefore, $stringAfter)
	{
		return trim(substr(strstr(strstr($message, $stringBefore), $stringAfter, true), strlen($stringBefore)));
	}

	function getUiByOeie($oeie)
	{
		include("../connexionBddErp.php");
		$ui = null;

		$oeie = substr($oeie, 0, 3)."%";

		$req = $bddErp->prepare("SELECT atr_ui FROM ag_poi WHERE ft_numero_oeie LIKE ? AND atr_ui IS NOT NULL AND atr_ui != '' ORDER BY id DESC LIMIT 1");
		$req->execute(array($oeie));
		if($data = $req->fetch())
		{
			$ui = $data["atr_ui"];
		}

		return json_encode($ui);
	}
?>