<?php
	require_once("fonctions.php");
	echo nbPoiCaffByRadius($_POST["poi_id"], $_POST["km"]);
?>