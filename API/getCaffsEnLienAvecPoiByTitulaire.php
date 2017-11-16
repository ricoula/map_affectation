<?php
	require_once("fonctions.php");
	echo getCaffsEnLienAvecPoiByTitulaire($_POST["titulaire"], $_POST["ui"]);
?>