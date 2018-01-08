<?php
    require_once("fonctions.php");
    echo entraideCaff($_POST["caff_id"], $_POST["site_entraide_id"], $_POST["liste_domaines_json"], $_POST["date_expiration"], $_POST["date_debut"]);
    //echo entraideCaff(1, 1, "TEST", "2018-02-02", "2019-08-08");
?>