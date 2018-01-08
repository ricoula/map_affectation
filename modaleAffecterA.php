<?php
    include("API/fonctions.php");
    $poi = json_decode(getPoiById($_GET["poi_id"]));
    $closestSite = json_decode(getClosestSite($poi->id));
    $sitesUi = json_decode(getSitesByUi($poi->atr_ui));
    $caffs = json_decode(getInfosCaff());
    $tabPoiLien = json_decode(getPoiLienByTitulaire(json_encode($poi)));
    if(sizeof($tabPoiLien) > 0)
    {
        $tab = array();
        foreach($tabPoiLien as $cettePoi)
        {
          array_push($tab, $cettePoi->ft_numero_oeie);
        }
          $listePoiLien = implode(" <span class='glyphicon glyphicon-link'></span> ", $tab);
          $listePoiLien = " <span class='glyphicon glyphicon-link'></span> ".$listePoiLien;
    }
    else{
      $listePoiLien = "";
    }
?>
<input type="hidden" id="poiModaleAffecterA" name="poiModaleAffecterA" value="<?php echo urlencode(json_encode($poi)) ?>" />
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h1>Affectation de la POI: <?php echo $poi->ft_numero_oeie.$listePoiLien ?></h1>
</div>
<div class="modal-body">
  <div>
    <select type="search" id="searchCaff" class="form-control" >
      <option>Rechercher un caff</option>
      <?php
      $listeSites = json_decode(getSites());
      foreach($listeSites as $site)
      {
        ?>
        <optgroup label="<?php echo $site->libelle ?>">
          <?php
          $listeCaffs = json_decode(getCaffsBySite($site->libelle));
          foreach($listeCaffs as $caff)
          {
            ?>
            <option value="<?php echo urlencode(json_encode($caff)) ?>"><?php echo $caff->name_related ?></option>
            <?php
          }
          ?>
        </optgroup>
        <?php
      }
      ?>
    </select>
    <br/><br/>
  </div>
  <ul class="nav nav-pills nav-justified">
    <li class="active"><a href="#tabClosestSite" data-toggle="tab">Site le plus proche</a></li>
    <li><a href="#tabSitesUi" data-toggle="tab">Sites de <?php echo $poi->atr_ui ?></a></li>
    <li><a href="#tabAllCaff" data-toggle="tab">Tous les caffs</a></li>
  </ul>
  <div class="tab-content">

    <div class="tab-pane active" id="tabClosestSite">
      <h3 style="text-align: center"><?php echo $closestSite->libelle ?></h3>
      <div class="list-group">
        <?php
        foreach($caffs as $caff)
        {
          if($caff->site == $closestSite->libelle)
          {
            ?>
            <a href="#" id="caffClosestSite_<?php echo $caff->id ?>" class="list-group-item caffAffectation"><?php echo $caff->name_related ?><input type="hidden" class="caffjson" value="<?php echo urlencode(json_encode($caff)) ?>" /></a>
            <?php
          }
        }
        ?>
      </div>
    </div>

    <div class="tab-pane" id="tabSitesUi"  style="background-color: white">
    <br/>
      <div id="listeSitesUi" class="panel-group">

        <?php
        foreach($sitesUi as $site)
        {
          ?>
          <div class="panel panel-default">
            <div class="panel-heading"> 
              <h3 class="panel-title">
                <a href="#<?php echo str_replace(" ", "_", htmlspecialchars($site->libelle)) ?>" data-parent="#listeSitesUi" data-toggle="collapse"> <?php echo $site->libelle ?> </a> 
              </h3>
            </div>
            <div id="<?php echo str_replace(" ", "_", htmlspecialchars($site->libelle)) ?>" class="panel-collapse collapse">
              <div class="panel-body">
              <div class="list-group">
              <?php
              foreach($caffs as $caff)
              {
                if($caff->site == $site->libelle)
                {
                  ?>
                  <a href="#" class="list-group-item caffAffectation" id="caffSiteUi_<?php echo $caff->id ?>"><?php echo $caff->name_related ?><input type="hidden" class="caffjson" value="<?php echo urlencode(json_encode($caff)) ?>" /></a>
                  <?php
                }
              }
              ?>
              </div>
              
              </div>
            </div>
          </div>
          <?php
        }
        ?>

      </div>
    </div>

    <div class="tab-pane" id="tabAllCaff">
      <div class="list-group">
        <?php
        foreach($caffs as $caff)
        {
          ?>
          <a href="#" class="list-group-item caffAffectation" id="caffAll_<?php echo $caff->id ?>"><?php echo $caff->name_related ?><span class="badge"><?php echo $caff->site ?></span><input type="hidden" class="caffjson" value="<?php echo urlencode(json_encode($caff)) ?>" /></a>
          <?php
        }
        ?>
      </div>
    </div>

  </div>
</div>
<script>
  $(".caffAffectation").click(function(){
      var caff = $(this).children(".caffjson").first().val();
      $("#divInfosCaffAffectation").load("modaleInfosCaffAffectation.php?caff=" + caff  + "&poi=" + $("#poiModaleAffecterA").val(), function(){
          $('#modaleInfosCaffAffectation').modal('show');
      });
  });

  $("#searchCaff").chosen({width: "inherit", width: "100%",placeholder_text_multiple:"Tous contrats", placeholder_text_single: "Rechercher..."});
  $('#searchCaff').on('change', function(evt, params) {
    if($("#searchCaff").val() != "search")
    {
      $("#divInfosCaffAffectation").load("modaleInfosCaffAffectation.php?caff=" + $("#searchCaff").val() + "&poi=" + $("#poiModaleAffecterA").val(), function(){
          $('#modaleInfosCaffAffectation').modal('show');
      });
    }
  });

  /*objetAC = [];
  $.post("API/getSites.php", function(data){
    var sites = JSON.parse(data);
    var i = 0;
    sites.forEach(function(site){
      i++;
      $.post("API/getCaffsBySite.php", {site: site}, function(data2){
        var caffs = JSON.parse(data2);
        var listeNomsCaffs = [];
        caffs.forEach(function(caff){
          listeNomsCaffs.push(caff.name_related);
        });
        objetAC[site] = listeNomsCaffs;
      });
    });
  });*/
  /*$.post("API/getInfosCaff.php", function(data){
    var caffs = JSON.parse(data);
    var options = {
    data: caffs,
    getValue: "name_related",
    list: {
      onChooseEvent: function() {
      var caff = encodeURI(JSON.stringify($("#searchCaff").getSelectedItemData()));
      $("#divInfosCaffAffectation").load("modaleInfosCaffAffectation.php?caff=" + caff, function(){
          $('#modaleInfosCaffAffectation').modal('show');
      });
		}	
	}
};

$("#searchCaff").easyAutocomplete(options);
  });*/
</script>

