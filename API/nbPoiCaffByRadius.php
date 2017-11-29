<?php
	require_once("fonctions.php");
	echo nbPoiCaffByRadius($_POST["latitude"], $_POST["longitude"], $_POST["km"]);
?>