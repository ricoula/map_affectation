<?php
    require_once("fonctions.php");
    echo getTauxCaffEntraideByCaffIdAndSiteId($_POST["caff_id"], $_POST["site_id"]);
    //echo getTauxCaffEntraideByCaffIdAndSiteId(418, 12);
?>