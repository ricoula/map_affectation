<?php
	require_once("fonctions.php");
	echo getListePoiByCaffByVoie($_POST["caff_id"], $_POST["voie"], $_POST["commune"]);
?>