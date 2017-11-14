<?php
	try{
		$bddErp = new PDO('pgsql:host=192.168.30.240;dbname=ambigroup_dev', 'admambigroup', '13jkgaUM8Um');
	}
	catch (Exception $e){
		die('Erreur : '.$e->getMessage());
	}
?>
