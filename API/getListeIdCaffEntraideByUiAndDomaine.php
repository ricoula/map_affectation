<?php
    require_once("fonctions.php");
    echo getListeIdCaffEntraideByUiAndDomaines($_POST["ui"], $_POST["domaines_json"]);
    //echo getListeIdCaffEntraideByUiAndDomaine('QFY', 'Immo');
?>