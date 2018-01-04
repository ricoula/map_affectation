<?php 
    include("header.php");
    include("API/fonctions.php");
    $_SESSION["user_id"] = 1;
    $config = json_decode(getAdvancedConfig(null));
    $listeUi = json_decode(getUi());
?>
<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="CSS/style.css">

  </head>
  <body>
    <?php 
    if($config != null)
    {
      ?>
      <input type="hidden" id="kmRadius" name="kmRadius" value="<?php echo $config->rayon_km_new ?>" />
      <input type="hidden" id="coefPoiProxi" name="coefPoiProxi" value="<?php echo $config->coef_rayon_new ?>" />
      <input type="hidden" id="coefPoiClient" name="coefPoiClient" value="<?php echo $config->coef_react ?>" />
      <input type="hidden" id="coefCharge" name="coefCharge" value="<?php echo $config->coef_non_react ?>" />
      <input type="hidden" id="limiteAffectationJour" name="limiteAffectationJour" value="<?php echo $config->max_day ?>" />
      <input type="hidden" id="limiteAffectationSemaine" name="limiteAffectationSemaine" value="<?php echo $config->max_week ?>" />
      <input type="hidden" id="limiteMaxCalcul" name="limiteMaxCalcul" value="<?php echo $config->max_rayon_new ?>" />
      <input type="hidden" id="nbJoursAvantCongesMax" name="nbJoursAvantCongesMax" value="<?php echo $config->jours_avant_conges ?>" />
      <input type="hidden" id="nbJoursCongesMax" name="nbJoursCongesMax" value="<?php echo $config->jours_conges ?>" />
      <?php
    }
    else{
      ?>
      <input type="hidden" id="kmRadius" name="kmRadius" value="20" />
      <input type="hidden" id="coefPoiProxi" name="coefPoiProxi" value="0.5" />
      <input type="hidden" id="coefPoiClient" name="coefPoiClient" value="0.8" />
      <input type="hidden" id="coefCharge" name="coefCharge" value="0.1" />
      <input type="hidden" id="limiteAffectationJour" name="limiteAffectationJour" value="3" />
      <input type="hidden" id="limiteAffectationSemaine" name="limiteAffectationSemaine" value="10" />
      <input type="hidden" id="limiteMaxCalcul" name="limiteMaxCalcul" value="20" />
      <input type="hidden" id="nbJoursAvantCongesMax" name="nbJoursAvantCongesMax" value="5" />
      <input type="hidden" id="nbJoursCongesMax" name="nbJoursCongesMax" value="5" />
      <?php
    }
    ?>

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
        <button data-toggle="modal" href="#modaleSimulationPOI" class="btn btn-info btn-lg">Simulation</button>
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
          <div class="modal-content">
          <div id="divAffecterA"></div>
          <div id="loadingDivAffecterA" style="text-align: center"><img src="img/loading.gif" /></div>
          </div>  
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


    <div class="modal" id="modaleSimulationPOI">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">x</button>
            <h4 class="modal-title">Ajouter des POI</h4>
          </div>
          <div class="modal-body">
            Ajouter <input type="number" name="nbPoiSimu" id="nbPoiSimu" value="0" /> POI sur <select id="selectUiSimu" name="selectUiSimu"><?php foreach($listeUi as $ui){ ?><option value="<?php echo $ui->ft_zone ?>"><?php echo $ui->libelle ?></option><?php } ?><select></select>
          </div>
          <div>
            <button class="btn btn-primary" data-toggle="modal" href="#modaleSimulationPOIAvance">Avancé</button>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success" id="btnValiderModalSimulation">Valider</button>
            <button class="btn btn-info" data-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>

    <div class="modal" id="modaleSimulationPOIAvance">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">x</button>
            <h4 class="modal-title">Ajouter des POI (Avancé)</h4>
          </div>
          <div class="modal-body">
            Ajouter <input type="number" name="nbPoiSimuAvance" id="nbPoiSimuAvance" value="0" /> POI sur <select id="selectUiSimuAvance" name="selectUiSimuAvance"><?php foreach($listeUi as $ui){ ?><option value="<?php echo $ui->ft_zone ?>"><?php echo $ui->libelle ?></option><?php } ?><select></select>
            <div class="form-group">
              <label>ft_sous_justification_oeie</label>
              <input type="text" class="form-control" name="ft_sous_justification_oeie" id="ft_sous_justification_oeie"/>
            </div>
            <!-- <div class="form-group">
              <label>atr_ui</label>
              <input type="text" class="form-control" name="atr_ui" id="atr_ui"/>
            </div> -->
            <div class="form-group">
              <label>ft_numero_oeie</label>
              <input type="text" class="form-control" name="ft_numero_oeie" id="ft_numero_oeie"/>
            </div>
            <div class="form-group">
              <label>ft_numero_demande_42C</label>
              <input type="text" class="form-control" name="ft_numero_demande_42C" id="ft_numero_demande_42C"/>
            </div>
            <div class="form-group">
              <label>ft_libelle_commune</label>
              <input type="text" class="form-control" name="ft_libelle_commune" id="ft_libelle_commune"/>
            </div>
            <div class="form-group">
              <label>ft_libelle_de_voie</label>
              <input type="text" class="form-control" name="ft_libelle_de_voie" id="ft_libelle_de_voie"/>
            </div>
            <div class="form-group">
              <label>ft_pg</label>
              <input type="text" class="form-control" name="ft_pg" id="ft_pg"/>
            </div>
            <div class="form-group">
              <label>ft_oeie_dre</label>
              <input type="text" class="form-control" name="ft_oeie_dre" id="ft_oeie_dre"/>
            </div>
            <div class="form-group">
              <label>ft_latitude</label>
              <input type="text" class="form-control" name="ft_latitude" id="ft_latitude"/>
            </div>
            <div class="form-group">
              <label>ft_longitude</label>
              <input type="text" class="form-control" name="ft_longitude" id="ft_longitude"/>
            </div>
            <div class="form-group">
              <label>insee_code</label>
              <input type="text" class="form-control" name="insee_code" id="insee_code"/>
            </div>
            <div class="form-group">
              <label>ft_libelle_affaire</label>
              <input type="text" class="form-control" name="ft_libelle_affaire" id="ft_libelle_affaire"/>
            </div>
            <div class="form-group">
              <label>ft_date_limite_realisation</label>
              <input type="text" class="form-control" name="ft_date_limite_realisation" id="ft_date_limite_realisation"/>
            </div>
            <div class="form-group">
              <label>create_date</label>
              <input type="text" class="form-control" name="create_date" id="create_date"/>
            </div>
            <div class="form-group">
              <label>ft_etat</label>
              <input type="text" class="form-control" name="ft_etat" id="ft_etat"/>
            </div>
            <div class="form-group">
              <label>atr_domaine_id</label>
              <input type="text" class="form-control" name="atr_domaine_id" id="atr_domaine_id"/>
            </div>
            <div class="form-group">
              <label>atr_caff_traitant_id</label>
              <input type="text" class="form-control" name="atr_caff_traitant_id" id="atr_caff_traitant_id"/>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-success" id="btnValiderModalSimulationAvance">Valider</button>
            <button class="btn btn-info" data-dismiss="modal">Fermer</button>
          </div>
        </div>
      </div>
    </div>


    <div class="modal fade" id="advancedsettings">
     <div class="modal-dialog modal-lg" id="modal_advanced_config">
       <div class="modal-content">
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal">x</button>
           <h2 class="modal-title">Paramètres avancés</h2>
         </div>
         <div class="modal-body">
            <h4>Algorithme de calcul de charge</h4>
            <p class="color-red">Attention la modification de ce calcul influencera les affectations futures</p>
            <div id="advancedConfig-defaut">
            <h3>Defaut</h3>
            <p>((NbPoiReactive * <label class="color-grey coef_react_defaut" id="coef_react_defaut"><?php echo $config->coef_react ?></label> + NbPoiNonReactive * <label class="color-purple coef_non_react_defaut" id="coef_non_react_defaut"><?php echo $config->coef_non_react ?></label>) * CoefCaff) + (%Retard * NbPoiReactive * <label class="color-grey coef_react_defaut" id="coef_react_defaut"><?php echo $config->coef_react ?></label>) - (NbPoiRayon(<label class="color-red" id="rayon_km_defaut"><?php echo $config->rayon_km_new ?></label>km) * <label class="color-yellow" id="coef_rayon_defaut"><?php echo $config->coef_rayon_new ?></label>)->MAX(<label class="color-green" id="max_rayon_defaut"><?php echo $config->max_rayon_new ?></label>) + (NbPoiReactiveSimu * <label class="color-grey coef_react_defaut" id="coef_react_defaut"><?php echo $config->coef_react ?></label> + NbPoiNonReactiveSimu * <label class="color-purple coef_non_react_defaut"><?php echo $config->coef_non_react ?></label>) </p>
            <p>Nombre de POI réactive MAX par jour: <label id="max_day_defaut"><?php echo $config->max_day ?></label></p>
            <p>Nombre de POI réactive MAX par semaine: <label id="max_week_defaut"><?php echo $config->max_week ?></label></p>
            <p>Nombre de jours MAX avant congés: <label id="max_avant_conges_defaut"><?php echo $config->jours_avant_conges ?></label></p>
            <p>Nombre de jours de congés MAX: <label id="max_conges_defaut"><?php echo $config->jours_conges ?></label></p>
            <button class="btn btn-primary pull-left config_modify" ui="defaut">Modifier</button>
         <button class="btn btn-success pull-left hide config_valid" ui="defaut">Valider</button>
         <button class="btn btn-primary pull-left hide config_cancel" ui="defaut">Annuler</button>
         </br></br></div>
            <?php 
              foreach($listeUi as $ui){
                $config = json_decode(getAdvancedConfig($ui->ft_zone));
                if($config != false){
                  ?> 
                  <div id="advancedConfig-<?php echo $ui->ft_zone ?>">
                  <h3 class="advanced_config_ui"><?php echo $ui->libelle; ?> </h3><span class="glyphicon glyphicon-remove remove_advanced_ui" ui="<?php echo $ui->ft_zone; ?>"></span>
            <p>((NbPoiReactive * <label class="color-grey coef_react_<?php echo $ui->ft_zone; ?>" id="coef_react_<?php echo $ui->ft_zone; ?>"><?php echo $config->coef_react ?></label> + NbPoiNonReactive * <label class="color-purple coef_non_react_<?php echo $ui->ft_zone; ?>" id="coef_non_react_<?php echo $ui->ft_zone; ?>"><?php echo $config->coef_non_react ?></label>) * CoefCaff) + (%Retard * NbPoiReactive * <label class="color-grey coef_react_<?php echo $ui->ft_zone; ?>" id="coef_react_<?php echo $ui->ft_zone; ?>"><?php echo $config->coef_react ?></label>) - (NbPoiRayon(<label class="color-red" id="rayon_km_<?php echo $ui->ft_zone; ?>"><?php echo $config->rayon_km_new ?></label>km) * <label class="color-yellow" id="coef_rayon_<?php echo $ui->ft_zone; ?>"><?php echo $config->coef_rayon_new ?></label>)->MAX(<label class="color-green" id="max_rayon_<?php echo $ui->ft_zone; ?>"><?php echo $config->max_rayon_new ?></label>) + (NbPoiReactiveSimu * <label class="color-grey coef_react_<?php echo $ui->ft_zone; ?>" id="coef_react_<?php echo $ui->ft_zone; ?>"><?php echo $config->coef_react ?></label> + NbPoiNonReactiveSimu * <label class="color-purple coef_non_react_<?php echo $ui->ft_zone; ?>"><?php echo $config->coef_non_react ?></label>) </p>
            <p>Nombre de POI réactive MAX par jour: <label id="max_day_<?php echo $ui->ft_zone; ?>"><?php echo $config->max_day ?></label></p>
            <p>Nombre de POI réactive MAX par semaine: <label id="max_week_<?php echo $ui->ft_zone; ?>"><?php echo $config->max_week ?></label></p>
            <p>Nombre de jours MAX avant congés: <label id="max_avant_conges_<?php echo $ui->ft_zone; ?>"><?php echo $config->jours_avant_conges ?></label></p>
            <p>Nombre de jours de congés MAX: <label id="max_conges_<?php echo $ui->ft_zone; ?>"><?php echo $config->jours_conges ?></label></p>
            <button class="btn btn-primary pull-left config_modify" ui="<?php echo $ui->ft_zone; ?>">Modifier</button>
         <button class="btn btn-success pull-left hide config_valid" ui="<?php echo $ui->ft_zone; ?>">Valider</button>
         <button class="btn btn-primary pull-left hide config_cancel" ui="<?php echo $ui->ft_zone; ?>">Annuler</button>
         </br></br></div>
                  <?php
                }
              }
            ?>
            <button class="btn btn-success" id="advancedConfigAddUI">Ajouter une configuration sur l'ui : <select id="advancedConfigUI">
            <option value="null" disabled selected ></option>
            <?php 
              foreach($listeUi as $ui){
                ?><option value="<?php echo $ui->ft_zone; ?>"><?php echo $ui->libelle; ?></option><?php
              }
            ?>
            </select></button>
         </div>
         <div class="modal-footer">


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
