<?php
    require_once("fonctions.php");
    echo getPoiLienByTitulaire($_POST["poi_json"]);
    /*$poi = (object) array();
    $poi->atr_ui = 'QFY';
    $poi->ft_titulaire_client = 'A1701627';
    $poi->id = 157002;
    $poi = json_encode($poi);
    echo getPoiLienByTitulaire($poi);*/
?>