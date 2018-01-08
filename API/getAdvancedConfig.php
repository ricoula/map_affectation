<?php
    require_once("fonctions.php");
    if(!isset($_POST["ui"]) || $_POST["ui"] == '')
    {
        $_POST["ui"] = null;
    }
    echo getAdvancedConfig($_POST["ui"]);
?>