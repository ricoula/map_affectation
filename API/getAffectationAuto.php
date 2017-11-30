<?php
	require_once("fonctions.php");
	echo getAffectationAuto($_POST["poi_id"], $_POST["km"], $_POST["coef_poi_proxi"], $_POST["coef_poi_client"], $_POST["coef_charge"]);
	//echo getAffectationAuto(107494, 100, 0.5, 0.8, 0.5);
?>