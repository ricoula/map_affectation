<?php
    include("API/fonctions.php");
    $poi = json_decode(getPoiById($_GET["poi_id"]));
    $closestSite = json_decode(getClosestSite($poi->id));
    $sitesUi = json_decode(getSitesByUi($poi->atr_ui));
    
?>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h1>Caffs en lien avec la POI: <?php echo $poi->ft_numero_oeie ?></h1>
</div>
<div class="modal-body">
    
</div>

