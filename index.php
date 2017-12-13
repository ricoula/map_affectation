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
      <input type="hidden" id="coefCharge" name="coefCharge" value="0.1" />
      <input type="hidden" id="limiteAffectationJour" name="coefCharge" value="3" />
      <input type="hidden" id="limiteAffectationSemaine" name="coefCharge" value="10" />

      <input type="hidden" id="user_id" name="user_id" value="<?php echo $_SESSION["user_id"] ?>" />
      <div id="divInfos" class="well" >
        <button data-toggle="modal" href="#modaleAffectationPoi" class="btn btn-primary btn-lg">Affecter POI</button>
        <div class="panel-group"></br>
          <div class="panel panel-default">
            <div class="panel-heading">
              <h4 class="panel-title">
                <a data-toggle="collapse" href="#collapse1">Légende</a>
              </h4>
            </div>
            <div id="collapse1" class="panel-collapse collapse">
              <div class="panel-body"><span>Client: <span id="leg_client" class="leg_poi pull-right"></span></span></div>
              <div class="panel-body"><span>FO & CU:</span><span id="leg_focu" class="leg_poi pull-right"></span></div>
              <div class="panel-body"><span>Immo:</span><span id="leg_immo" class="leg_poi pull-right"></span></div>
              <div class="panel-body"><span>Dissi:</span><span id="leg_dissi" class="leg_poi pull-right"></span></div>
              <div class="panel-body"><span>Coordi:</span><span id="leg_coord" class="leg_poi pull-right"></span></div>
              <div class="panel-body"><span id="leg_dre_txt">DRE < à :</span><span id="leg_dre" class="pull-right"></span></div>
            </div>
          </div>
        </div>

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
    <div class="modal fade" id="advancedsettings">
     <div class="modal-dialog modal-lg" id="modal_advanced_config">
       <div class="modal-content">
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">x</button>
           <h4 class="modal-title">Paramètres avancés</h4>
         </div>
         <div class="modal-body">
            <h4>Algorithme de calcul de charge</h4>
            <p class="color-red">Attention la modification de ce calcul influancera les affectations futures</p>
            <p>((NbPoiReactive * <label class="color-grey coef_react" id="coef_react">0.8</label> + NbPoiNonReactive * <label class="color-purple coef_non_react" id="coef_non_react">0.1</label>) * CoefCaff) - (%Retard * NbPoiReactive) - (NbPoiRayon(<label class="color-red" id="rayon_km">20</label>km) * <label class="color-yellow" id="coef_rayon">0.5</label>)->MAX(<label class="color-green" id="max_rayon">20</label>) + (NbPoiReactiveSimu * <label class="color-grey coef_react" id="coef_react">0.8</label> + NbPoiNonReactiveSimu * <label class="color-purple coef_non_react">0.1</label>) </p>
            

         </div>
         <div class="modal-footer">
         <button class="btn btn-primary pull-left" id="config_modify">Modifier</button>
         <button class="btn btn-success pull-left hide" id="config_valid">Valider</button>
         <button class="btn btn-primary pull-left hide" id="config_cancel">Annuler</button>

           <button class="btn btn-info" data-dismiss="modal">Fermer</button>
         </div>
       </div>
     </div>
   </div>
    <div class="modal" id="modaleAffectationPoi">
      <div class="modal-dialog modal-lg">
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
                <label id="labelNbPoiNA"> <span id="nbPoiNA">0</span> POI non-affectées<button class="btn btn-info pull-right" id="btnGenererPoiNA">Générer</button></label></br>
                <div class="progress">
                <div class="progress-bar" id="progress_bar_affect" role="progressbar" aria-valuenow="70"
                aria-valuemin="0" aria-valuemax="100" style="width:0%">
                  0%
                </div>
                
              </div>
              <div id="percent">0%</div>
              
            </div>
            <div id="divListePoiNAUi">
              

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
