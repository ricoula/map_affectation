<?php
	require_once("fonctions.php");
	echo getNbPoiEnLien($_POST["commune"], $_POST["voie"], $_POST["titulaire"]);
?>