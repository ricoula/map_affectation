<?php
	require_once("fonctions.php");
	echo getListeAffectationAuto($_POST["listeIdPoi_json"], $_POST["km"]);
	//echo getListeAffectationAuto(json_encode(array(154513,154686,154687)), 20);
?>