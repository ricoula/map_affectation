<?php
	require_once("fonctions.php");
	echo gestionMail($_POST["auteur"], $_POST["objet"], $_POST["message"]);
	/*echo gestionMail("spadaro.florian@outlook.fr", "Test", "Attention : l’envoi de ce mail est automatique, merci de ne pas répondre. 
Bonjour,
L’application Maple vous a affecté le dossier suivant :
AS n° :	1807661
OEIE n° :	LYO805133
  			
créés le 20/06/2018 sur l’Unité d'Intervention Lyon.	
  			
Commune :	VENISSIEUX	Code INSEE :	69259
Voie :	AV DE LA REPUBLIQUE	Code RIVOLI :	1480
N° dans la voie :	53		
  			
Centre :	PI4		
Zone :	MCH		
  			
DRE :	26/06/2018		
DLR :	04/07/2018		
Sous-justification :	QM / Production Wholesale
  			
Commentaire création AS :	Etude en ligne PC saturé  PC/MCH/012/131.2 ctc 0478700310		
Commentaire Réponse AS :			
  			
Le n° de demande 42c :			
Titulaire :	ENTREPRISE DUTEURTRE VENISSIEUX00000034		
Autre(s) demande(s) 42c associée(s) :			

Bonne journée,
Maple 
");*/
?>