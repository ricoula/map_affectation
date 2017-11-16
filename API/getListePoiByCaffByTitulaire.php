<?php
	require_once("fonctions.php");
	echo getListePoiByCaffByTitulaire($_POST["caff_id"], $_POST["titulaire"]);
?>