<?php
	require_once("fonctions.php");
	echo getNbPoiProximiteByCaffByPoi($_POST["poi_id"], $_POST["caff_id"], $_POST["km"]);
?>