<?php
	require_once("fonctions.php");
	echo getCaffsEnLienAvecPoiByTitulaire($_POST["titulaire"], $_POST["ui"]);
	//echo getCaffsEnLienAvecPoiByTitulaire("A1701336", "FC4");
?>