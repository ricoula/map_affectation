<?php
	//@session_start();
	
	try{
		if(!isset($_SESSION['simu']) || $_SESSION['simu'] == false){
			$bddErp = new PDO('pgsql:host=192.168.30.240;dbname=ambigroup_dev', 'admambigroup', '13jkgaUM8Um');
		}
		else{
			$bddErp = new PDO('pgsql:host=192.168.30.218;dbname=openerp', 'CYRRIC', 'cyril');
		}
		
		//$bddErp = new PDO('pgsql:host=192.168.30.218;dbname=openerp', 'CYRRIC', 'cyril');
		$bddErp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
	catch (Exception $e){
		die('Erreur : '.$e->getMessage());
	}
?>
