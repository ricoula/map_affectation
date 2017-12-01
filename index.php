<?php 
    include("header.php");
    include("API/fonctions.php");
    $_SESSION["user_id"] = 1;
?>
<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="CSS/style.css">

  </head>
  <body>
      <input type="hidden" id="kmRadius" name="kmRadius" value="20" />
      <input type="hidden" id="coefPoiProxi" name="coefPoiProxi" value="0.5" />
      <input type="hidden" id="coefPoiClient" name="coefPoiClient" value="0.8" />
      <input type="hidden" id="coefCharge" name="coefCharge" value="0.5" />

      <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>" />
      <div id="divInfos" class="well" >
        <button data-toggle="modal" href="#modaleAffectationPoi" class="btn btn-primary btn-lg">Affecter POI</button>
      </div>
  <div id="side_bar">
    <div id="div-slide-users" class="slide"></div>
    <div id="div-slide-box" class="slide"></div>
    <div id="div-slide-filter" class="slide"></div>
    <div id="div-slide-home" class="slide"></div>
  </div>
    <div id="map_content">
      <div id="glyph">
        <div id="slide-home" class="glyph_div glyph_div_border"><span  class="glyphicon glyphicon-home font-glyph" aria-hidden="true"></span></div>
        <div id="slide-box" class="glyph_div glyph_div glyph_div_border"><span class="glyphicon glyphicon-inbox font-glyph" aria-hidden="true"></span></div>
        <div id="slide-users" class="glyph_div glyph_div_border"><span class="glyphicon glyphicon-user font-glyph" aria-hidden="true"></span></div>
        <div id="slide-filter" class="glyph_div"><span class="glyphicon glyphicon-cog font-glyph" aria-hidden="true"></span></div>
     </div>
         <div id="map"></div>
    </div>

    <div class="modal fade" id="modaleListeCaffsLienPoi">
        <div class="modal-dialog modal-lg"> 
          <div class="modal-content" id="divListeCaffsLienPoi"></div>  
        </div> 
    </div>
    <div class="modal fade" id="modaleListePoiLienByCaff">
        <div class="modal-dialog modal-lg"> 
          <div class="modal-content" id="divListePoiLienByCaff"></div>  
        </div> 
    </div>
    <div class="modal fade" id="modaleAffecterA">
        <div class="modal-dialog modal-lg"> 
          <div class="modal-content" id="divAffecterA"></div>  
        </div> 
    </div>
    <div class="modal fade" id="modaleInfosCaffAffectation">
        <div class="modal-dialog modal-lg"> 
          <div class="modal-content" id="divInfosCaffAffectation"></div>  
        </div> 
    </div>
    <div class="modal fade" id="listePoiCaff">
        <div class="modal-dialog modal-lg"> 
          <div class="modal-content" id="divlistePoiCaff"></div>  
        </div> 
    </div>
    
    <div class="modal" id="modaleAffectationPoi">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">x</button>
            <h4 class="modal-title">Affectation des POI</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Veuillez choisir une UI:</label>
              <select id="selectUi" name="selectUi">
                <option disabled selected value>Séléctionner une UI</option>
                <?php
                $listeUi = json_decode(getUi());
                foreach($listeUi as $ui)
                {
                  ?>
                  <option id="<?php echo $ui->ft_zone ?>" value="<?php echo $ui->ft_zone ?>"><?php echo $ui->libelle ?></option>
                  <?php
                }
                ?>
              </select><img id="loadingChoixUi" src="img/wait.gif" />
                <label id="labelNbPoiNA"> <span id="nbPoiNA">0</span> POI non-affectées<button class="btn btn-info" id="btnGenererPoiNA">Générer</button></label>
            </div>
            <div id="divListePoiNAUi">
              <hr/>
                <div id="divLoadingPoiNA" style="text-align: center">
                  <div><span id="nbPoiNaEffectue"></span>/<span id="nbPoiNaEffectueTotal"></span></div>
                  <img src="img/loading.gif" />
              </div>
                <div id="resultatsListePoiNA">
                </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  <?php include("footer.php") ?>
  </body>
</html>
