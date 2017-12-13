<?php
    require_once("fonctions.php");
    
  //  echo addConfigById(1,'{"filterdre":"5","filtercolorurgent":"#000000","filtercolorclient":"#ffa500","filtercolorimmo":"#ffff00","filtercolordissi":"#008000","filtercolorfocu":"#0000ff","filtercolorcoord":"#800080","filtersj":[]}');
    echo changeAdvancedConfig($_POST["config"]);
?>