<?php
	require_once("fonctions.php");
	echo getInfoInMail($_POST["message"], $_POST["stringBefore"], $_POST["stringAfter"]);
?>