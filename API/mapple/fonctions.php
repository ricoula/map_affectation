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
		
		$headers = "";
		//$headers .= "From: " . strip_tags($_POST['req-email']) . "\r\n";
		//$headers .= "Reply-To: ". strip_tags($_POST['req-email']) . "\r\n";
		//$headers .= "CC: susan@example.com\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=utf-8\r\n";
		
		if($obj->oeie == "")
		{
			mail($auteur, "Réponse: ".$objet, "Le mail que vous avez envoyé n'est pas conforme. Veuillez faire la manipulation manuellement.", $headers);
		}
		else{
			mail($auteur, "Réponse: ".$objet, "Réponse positive", $headers);
		}
		
		return json_encode($obj);
	}
	
	function getInfoInMail($message, $stringBefore, $stringAfter)
	{
		return trim(substr(strstr(strstr($message, $stringBefore), $stringAfter, true), strlen($stringBefore)));
	}
?>