<?php
	try{
		$bdd = new PDO('pgsql:host=192.168.30.218;dbname=cds', 'CYRRIC', 'cyril');
	}
	catch (Exception $e){
		die('Erreur : '.$e->getMessage());
	}
?>
