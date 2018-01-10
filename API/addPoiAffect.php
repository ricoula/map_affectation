<?php
	require_once("fonctions.php");
	echo addPoiAffect($_POST["poi_id"],$_POST["poi_num"],$_POST["poi_domaine"],$_POST["caff_id"],$_POST["caff_name"], $_POST["ui"]);
?>