<?php
	require_once("fonctions.php");
	echo getCaffsEnLienAvecPoiByVoie($_POST["voie"], $_POST["commune"], $_POST["ui"]);
?>