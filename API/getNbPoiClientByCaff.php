<?php
	require_once("fonctions.php");
	echo getNbPoiClientByCaff($_POST["caff_id"], $_POST["client"]);
?>