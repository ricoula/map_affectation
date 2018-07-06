<?php
    require_once("fonctions.php");
    echo updateTauxCaffEntraideByCaffIdAndSiteId($_POST["caff_id"], $_POST["site_id"], $_POST["taux"]);
?>