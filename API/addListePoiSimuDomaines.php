<?php
    require_once("fonctions.php");
    echo addListePoiSimuDomaines($_POST["nbDissi"], $_POST["nbClient"], $_POST["nbImmo"], $_POST["nbFocu"], $_POST["nbCoordi"], $_POST["nbFors"], $_POST["ui"]); //$ui = ft_zone (FC4, JR4...)
?>