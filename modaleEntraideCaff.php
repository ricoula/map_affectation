<?php
  include("API/fonctions.php");
  $listeSites = json_decode(getSites());
  $listeEntraides = json_decode(getProchainesEntraidesCaff($_GET["idCaff"]));
  $listeLibelleEntraides = array();
  foreach($listeEntraides as $entraide)
  {
    array_push($listeLibelleEntraides, strtoupper($entraide->site_entraide_libelle));
    ?>
    <input type="hidden" class="uneEntraide" dateDeb="<?php echo $entraide->date_debut ?>" dateFin="<?php echo $entraide->date_expiration ?>" />
    <?php
  }

  $entraidesEnCours = array();
  if(sizeof($listeEntraides) > 0)
  {
    foreach($listeEntraides as $entraide)
    {
        if(strtotime($entraide->date_debut) <= strtotime(date("Y-m-d")))
        {
            array_push($entraidesEnCours, $entraide);
        }
    }
  }
  $siteBase = urldecode($_GET["site"]);
  $idSiteBase = json_decode(getIdFromSite($siteBase));
  if(sizeof($entraidesEnCours) == 0)
  {
    $tauxSiteBase = 100;
  }
  else{
    $tauxSiteBase = 100;
    foreach($entraidesEnCours as $siteEntraide)
    {
      $tauxSiteBase -= $siteEntraide->taux;
      if($tauxSiteBase < 0)
      {
        $tauxSiteBase = 0;
      }
      if($tauxSiteBase > 100)
      {
        $tauxSiteBase = 100;
      }
    }
  }

  $competences = json_decode(getCompetenceByCaffId($_GET["idCaff"]));
?>

<input type="hidden" name="siteEntraide" id="siteEntraide" value="<?php echo urlencode($_GET["site"]) ?>" />
<input type="hidden" name="idSiteEntraideBase" id="idSiteEntraideBase" value="<?php echo $idSiteBase ?>" />
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">x</button>
  <h4 class="modal-title">Entraide</h4>
</div>
<div class="modal-body">
  <form id="formEntraideCaff" action="index.php" >
    <input type="hidden" name="idCaff" id="idCaffEntraide" value="<?php echo $_GET["idCaff"] ?>" />
    <div class="form-group">
      <label>Site d'entraide</label>
      <select class="form-control" id="idSiteEntraide">
        <?php
        foreach($listeSites as $site)
        {
          if( (strtoupper($siteBase) == strtoupper($site->libelle) && sizeof($listeLibelleEntraides) == 0) || in_array(strtoupper($site->libelle), $listeLibelleEntraides))
          {
            ?>
            <option disabled value="<?php echo $site->id ?>"><?php echo $site->libelle ?></option>
            <?php
          }
          else{
            ?>
            <option value="<?php echo $site->id ?>"><?php echo $site->libelle ?></option>
            <?php
          }
        }
        ?>
      </select>
    </div>
    <div class="form-group">
        <label>Choix des domaines</label>
        <div id="listeDomainesModaleEntraide">
          <?php 
          foreach($competences as $competence)
          {
            if(strtoupper($competence) == "FO & CU" || strtoupper($competence) == "FO&CU")
            {
              $idElt = "focu";
            }
            else{
              $idElt = strtolower($competence);
            }
            ?>
            <label><input class="checkDomainesEntraide" type="checkbox" name="<?php echo $idElt ?>" id="<?php echo $idElt ?>" checked /> <?php echo $competence ?></label>
            <?php
          }
          ?>
          
        </div>
    </div>
    <div class="form-group">
        <label>Date d'expiration</label>
        <input type="text" name="dateExpiration" id="dateExpiration" class="form-control" value="<?php echo date("Y-m-d")." ".date("Y-m-d") ?>" />
    </div>
    <div class="form-group">
          <label>Taux</label>
          <input class="form-control taux" type="number" name="tauxEntraide" id="tauxEntraide" value="0" min="1" max="100" placeholder="Min: 1, max: 100" />
    </div>
    <div>
      <div class="form-group">
        <label id="labelSiteBase"><?php echo $siteBase ?></label>
        <input class="form-control" type="number" id="tauxSiteBase" name="tauxSiteBase" min="1" max="100" placeholder="Min: 1, max: 100" value="<?php echo $tauxSiteBase ?>" disabled />
      </div>
    </div>
    <?php
    if(sizeof($listeEntraides) > 0)
    {
      ?>
      <div>
          <label class="label label-default">Autres taux</label>
          <small class="form-text text-muted">Attention: la somme des taux doit être égale à 100</small>
      <?php
      foreach($listeEntraides as $entraide)
      {
        ?>
          <div class="form-group">
            <label><?php echo $entraide->site_entraide_libelle ?></label>
            <input class="form-control taux autreTaux" type="number" id="tauxEntraide-<?php echo $entraide->site_entraide_id ?>" name="tauxEntraide-<?php echo $entraide->site_entraide_id ?>" value="<?php echo $entraide->taux ?>" min="1" max="100" placeholder="Min: 1, max: 100" />
          </div>
        <?php
      }
      ?>
      </div>
      <?php
    }
    ?>
    <hr/>
    
    
  </form>
  
  <div>
    <?php 
    if(sizeof($listeEntraides) > 0)
    {
        if(sizeof($entraidesEnCours > 0))
        {
            ?>
            <div id="divTableEntraideEnCours">
              <h4>En cours</h4>
              <table class="table table-striped table-hover table-condensed table-bordered">
                <tr>
                  <th>Site</th>
                  <th>Date Début</th>
                  <th>Date Fin</th>
                  <th>Domaines</th>
                  <th>Taux</th>
                  <th>Annuler</th>
                </tr>
                <?php
                $listeIdEntraidesEnCours = array();
                foreach($entraidesEnCours as $entraideEnCours)
                {
                  array_push($listeIdEntraidesEnCours, $entraideEnCours->id);
                  $domaines = implode(", ", $entraideEnCours->domaines);
                  ?>
                  <tr>
                    <td class="siteEntraideEnCours">
                      <?php echo $entraideEnCours->site_entraide_libelle ?>
                    </td>
                    <td>
                      <?php echo $entraideEnCours->date_debut ?>
                    </td>
                    <td>
                      <?php echo $entraideEnCours->date_expiration ?>
                    </td>
                    <td><?php echo $domaines ?></td>
                    <td class="entraideTaux" ><?php echo $entraideEnCours->taux.'%' ?></td>
                    <td style="text-align:center">
                      <a style="cursor:pointer" class="glyphicon glyphicon-trash supprEntraide entraideEnCours" entraideId="<?php echo $entraideEnCours->id ?>" ></a>
                    </td>
                  </tr>
                  <?php
                }
                ?>
              </table>
            </div>
            <?php
            if(sizeof($listeEntraides) > sizeof($entraidesEnCours))
            {
              ?>
              <div id="divTableEntraideAVenir">
                <h4>A venir</h4>
                <table class="table table-striped table-hover table-condensed table-bordered">
                <tr>
                  <th>Site</th>
                  <th>Date Début</th>
                  <th>Date Fin</th>
                  <th>Domaines</th>
                  <th>Taux</th>
                  <th>Annuler</th>
                </tr>
                <?php
                foreach($listeEntraides as $entraide)
                {
                  if(!in_array($entraide->id, $listeIdEntraidesEnCours))
                  {
                    $domaines = implode(", ", $entraide->domaines);
                    ?>
                    <tr>
                      <td>
                        <?php echo $entraide->site_entraide_libelle ?>
                      </td>
                      <td>
                        <?php echo $entraide->date_debut ?>
                      </td>
                      <td>
                        <?php echo $entraide->date_expiration ?>
                      </td>
                      <td><?php echo $domaines ?></td>
                      <td><?php echo $entraide->taux.'%' ?></td>
                      <td style="text-align:center">
                        <a style="cursor:pointer" class="glyphicon glyphicon-trash supprEntraide entraidesAVenir" entraideId="<?php echo $entraide->id ?>" ></a>
                      </td>
                    </tr>
                    <?php
                  }
                }
                ?>
                </table>
              </div>
              <?php
            }
        }
        else{
            ?>
              <div id="divTableEntraideAVenir">
                <h4>A venir</h4>
                <table class="table table-striped table-hover table-condensed table-bordered">
                <tr>
                  <th>Site</th>
                  <th>Date Début</th>
                  <th>Date Fin</th>
                  <th>Domaines</th>
                  <th>Taux</th>
                  <th>Annuler</th>
                </tr>
                <?php
                foreach($listeEntraides as $entraide)
                {
                    $domaines = implode(", ", $entraide->domaines);
                    ?>
                    <tr>
                      <td>
                        <?php echo $entraide->site_entraide_libelle ?>
                      </td>
                      <td>
                        <?php echo $entraide->date_debut ?>
                      </td>
                      <td>
                        <?php echo $entraide->date_expiration ?>
                      </td>
                      <td><?php echo $domaines ?></td>
                      <td><?php echo $entraide->taux.'%' ?></td>
                      <td style="text-align:center">
                        <a style="cursor:pointer" class="glyphicon glyphicon-trash supprEntraide entraidesAVenir" entraideId="<?php echo $entraide->id ?>" ></a>
                      </td>
                    </tr>
                    <?php
                }
                ?>
                </table>
              </div>
              <?php
        }

    }
    ?>
  </div>
</div>
<div class="modal-footer">
  <button class="btn btn-success" id="validerModaleEntraide">Valider</button>
  <button class="btn btn-info" data-dismiss="modal">Fermer</button>
</div>

<script>

  $(".taux").change(function(){
    if(isNaN($(this).val()))
    {
      $(this).val("100");
    }
    else{
      var val = parseInt($(this).val());
      if(isNaN(val))
      {
        $(this).val("100");
      }
      if(val < 1)
      {
        $(this).val("0");
      }
      else if(val > 100)
      {
        $(this).val("100");
      }
    }
    var tauxSiteBase = 100;
    $(".taux").each(function(i, elt){
      tauxSiteBase -= $(elt).val();
      if(tauxSiteBase < 0)
      {
        tauxSiteBase = 0;
      }
      else if(tauxSiteBase > 100)
      {
        tauxSiteBase = 100;
      }
    });
    $("#tauxSiteBase").val(tauxSiteBase);
  });

  $("#dateExpiration").daterangepicker({locale: {format: 'DD/MM/YYYY'}});

  $(".supprEntraide").click(function(){
    var elt = $(this);
    $(this).removeClass("supprEntraide entraidesAVenir");
     $.post("API/removeEntraideById.php", {entraide_id: $(this).attr("entraideId")}, function(){
       elt.closest("tr").hide();
       if(elt.hasClass("entraideEnCours"))
       {
        if($(".siteEntraideEnCours").length > 1)
        {
          var title = "";
          var siteSupprime = $.trim($(elt).closest("tr").find("td:first").text());
          $(".siteEntraideEnCours").each(function(i, elt){
            if($.trim($(elt).text()) != siteSupprime)
            {
              var taux = "";
              $(".entraideTaux").each(function(y, elmt){
                if(y == i)
                {
                  taux = " (" + $.trim($(elmt).text()) + ")";
                }
              });
              title += $.trim($(elt).text()) + taux + "\n";
            }
          });
          title += $("#labelSiteBase").text() + " (" + $("#tauxSiteBase").val() + "%)\n";

          //var siteEnCours = $(".siteEntraideEnCours:first").text();
          var siteEnCours =  '<label class="label label-info">Entraide <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top" title="' + title + '"  ></span></label>';
          var siteCaff = decodeURI($("#site-caff-" + $("#idCaffEntraide").val()).attr("siteCaff"));
          siteCaff = siteCaff.split("+");
          siteCaff = siteCaff.join(" ");
          $("#site-caff-" + $("#idCaffEntraide").val()).html(siteEnCours);
          $('[data-toggle="tooltip"]').tooltip(); 
        }
        else{
          $("#divTableEntraideEnCours").hide();
         var siteCaff = $("#labelSiteBase").text();
         $("#site-caff-" + $("#idCaffEntraide").val()).html(siteCaff);
        }
       }
       if($(".entraidesAVenir").length == 0)
       {
         $("#divTableEntraideAVenir").hide();
       }
       if($(".supprEntraide").length == 0)
        {
          $("#btnEntraideCaff-" + $("#idCaffEntraide").val()).css("color", "rgb(51, 122, 183)");
        }

        $("#divModaleEntraideCaff").load("modaleEntraideCaff.php?idCaff=" + $("#idCaffEntraide").val() + "&site=" + $("#siteEntraide").val(), function(){
          
        });

     });
  });

  $("#validerModaleEntraide").click(function(){
    if($("#idSiteEntraide").val() == "0")
    {
      alert("Veuillez sélectionner un site d'entraide");
    }
    else{
      var total = 0;
      $(".taux").each(function(i, elt){
        total += parseInt($(elt).val());
      });
      total += parseInt($("#tauxSiteBase").val());
      if(total != 100)
      {
        alert("Le total des taux des sites d'entraide n'est pas égal à 100. Merci de les vérifier");
      }
      else{
        var listeDomaines = [];
        $(".checkDomainesEntraide").each(function(){
          if($(this).prop("checked"))
          {
            switch($(this).attr("id"))
            {
              case 'focu': listeDomaines.push("FO & CU");
              break;
              case 'client': listeDomaines.push("Client");
              break;
              case 'dissi': listeDomaines.push("Dissi");
              break;
              case 'coordi': listeDomaines.push("Coordi");
              break;
              case 'immo': listeDomaines.push("Immo");
              break;
              case 'fors': listeDomaines.push("FORS");
              break;
            }
          }
        });
        if(listeDomaines.length > 0)
        {
          listeDomaines = JSON.stringify(listeDomaines);
          var dateDebut = $("#dateExpiration").val().split(" - ")[0];
          dateDebut = dateDebut.split("/");
          dateDebut = dateDebut[2] + "-" + dateDebut[1] + "-" + dateDebut[0];
          var dateFin = $("#dateExpiration").val().split(" - ")[1];
          dateFin = dateFin.split("/");
          dateFin = dateFin[2] + "-" + dateFin[1] + "-" + dateFin[0];

          /*console.log("caff_id: " + $("#idCaffEntraide").val());
          console.log("site_entraide_id: " + $("#idSiteEntraide").val());
          console.log("liste_domaines_json: " + listeDomaines);
          console.log("date_expiration: " + dateFin);
          console.log("date_debut: " + dateDebut);
          console.log("site_defaut_id: " + $("#idSiteEntraideBase").val());*/
          $(".autreTaux").each(function(i, elt){
            var idSite = elt.getAttribute('id').split("-")[1];
            var taux = $(elt).val();
          
            $.post("API/updateTauxCaffEntraideByCaffIdAndSiteId.php", {caff_id: $("#idCaffEntraide").val(), site_id: idSite, taux: taux});
          });
          
          $.post("API/entraideCaff.php", {caff_id: $("#idCaffEntraide").val(), site_entraide_id: $("#idSiteEntraide").val(), liste_domaines_json: listeDomaines, date_expiration: dateFin, date_debut: dateDebut, site_defaut_id: $("#idSiteEntraideBase").val(), taux: $("#tauxEntraide").val()}, function(data){
            var reponse = JSON.parse(data);
            if(reponse)
            {
              $("#divModaleEntraideCaff").load("modaleEntraideCaff.php?idCaff=" + $("#idCaffEntraide").val() + "&site=" + $("#siteEntraide").val(), function(){
                if($(".supprEntraide").length > 0)
                {
                  $("#btnEntraideCaff-" + $("#idCaffEntraide").val()).css("color", "orange");
                  if($(".siteEntraideEnCours").length > 0)
                  {
                    var title = "";
                    $(".siteEntraideEnCours").each(function(i, elt){
                      var taux = "";
                      $(".entraideTaux").each(function(y, elmt){
                        if(y == i)
                        {
                          taux = " (" + $.trim($(elmt).text()) + ")";
                        }
                      });
                      title += $.trim($(elt).text()) + taux + "\n";
                    });
                    title += $("#labelSiteBase").text() + " (" + $("#tauxSiteBase").val() + "%)\n";

                    //var siteEnCours = $(".siteEntraideEnCours:first").text();
                    var siteEnCours =  '<label class="label label-info">Entraide <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="top" title="' + title + '"  ></span></label>';
                    var siteCaff = decodeURI($("#site-caff-" + $("#idCaffEntraide").val()).attr("siteCaff"));
                    siteCaff = siteCaff.split("+");
                    siteCaff = siteCaff.join(" ");
                    $("#site-caff-" + $("#idCaffEntraide").val()).html(siteEnCours);
                    $('[data-toggle="tooltip"]').tooltip();
                  }
                }
              });
            }
            else{
              alert("Une erreur s'est produite, veuillez réeesayer plus tard");
            }
          });
        }
        else{
          alert("Veuillez sélectionner au moins un domaine");
        }
      }
    }
    
  });
</script>