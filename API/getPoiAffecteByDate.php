<?php
	require_once("fonctions.php");
	echo getPoiAffecteByDate($_POST["date_debut"], $_POST["date_fin"]);
	//echo getPoiAffecteByDate("2018-06-14 00:00:00", "2018-06-19 23:59:59");
?>