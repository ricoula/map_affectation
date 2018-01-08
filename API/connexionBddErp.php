<?php
	try{
		$bddErp = new PDO('pgsql:host=192.168.30.240;dbname=ambigroup_dev', 'admambigroup', '13jkgaUM8Um');
		//$bddErp = new PDO('pgsql:host=192.168.30.218;dbname=openerp', 'CYRRIC', 'cyril');
		$bddErp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (Exception $e){
		die('Erreur : '.$e->getMessage());
	}
?>
