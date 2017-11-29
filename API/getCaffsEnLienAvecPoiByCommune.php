<?php
	require_once("fonctions.php");
	echo getCaffsEnLienAvecPoiByCommune($_POST["commune"], $_POST["ui"]);
	
?>