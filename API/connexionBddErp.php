<?php
	@session_start();
	session_write_close();
	try{
		if(!isset($_SESSION['simu']) || $_SESSION['simu'] == false){
			$bddErp = new PDO('pgsql:host=192.168.30.240;dbname=ambigroup_prod8', 'admambigroup', '13jkgaUM8Um');
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
