<?php
	try{
		$bddMail = new PDO('pgsql:host=192.168.30.218;dbname=mail_auto', 'postgres', 'postgres');
	}
	catch (Exception $e){
		die('Erreur : '.$e->getMessage());
	}
?>
