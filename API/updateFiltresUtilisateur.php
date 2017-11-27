<?php
	require_once("fonctions.php");
	echo updateFiltresUtilisateur($_POST["utilisateur_id"], $_POST["filtres_json"]);
?>