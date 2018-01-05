<?php
  include("API/fonctions.php");
  $listeSites = json_decode(getSites());
  $listeEntraides = json_decode(getProchainesEntraidesCaff($_GET["idCaff"]));
?>

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
          ?>
          <option value="<?php echo $site->id ?>"><?php echo $site->libelle ?></option>
          <?php
        }
        ?>
      </select>
    </div>
    <div class="form-group">
        <label>Choix des domaines</label>
        <div id="listeDomainesModaleEntraide">
          <label><input class="checkDomainesEntraide" type="checkbox" name="focu" id="focu" /> FO & CU</label>
          <label><input class="checkDomainesEntraide" type="checkbox" name="client" id="client" /> Client</label>
          <label><input class="checkDomainesEntraide" type="checkbox" name="dissi" id="dissi" /> Dissi</label>
          <label><input class="checkDomainesEntraide" type="checkbox" name="coordi" id="coordi" /> Coordi</label>
          <label><input class="checkDomainesEntraide" type="checkbox" name="immo" id="immo" /> Immo</label>
        </div>
    </div>
    <div class="form-group">
        <label>Date d'expiration</label>
        <input type="text" name="dateExpiration" id="dateExpiration" class="form-control" value="<?php echo date("Y-m-d")." ".date("Y-m-d") ?>" />
    </div>
  </form>
  
  <div>
    <?php 
    if(sizeof($listeEntraides) > 0)
    {
        $entraideEnCours = null;
        foreach($listeEntraides as $entraide)
        {
            if(strtotime($entraide->date_debut) <= strtotime(date("Y-m-d")))
            {
                $entraideEnCours = $entraide;
            }
        }
        if($entraideEnCours != null)
        {
            ?>
            <div id="divTableEntraideEnCours">
              <h4>En cours</h4>
              <table class="table table-striped table-hover table-condensed table-bordered">
                <tr>
                  <th>Site</th>
                  <th>Date Début</th>
                  <th>Date Fin</th>
                  <th>Annuler</th>
                </tr>
                <tr>
                  <td>
                    <?php echo $entraideEnCours->site_entraide_libelle ?>
                  </td>
                  <td>
                    <?php echo $entraideEnCours->date_debut ?>
                  </td>
                  <td>
                    <?php echo $entraideEnCours->date_expiration ?>
                  </td>
                  <td style="text-align:center">
                    <a style="cursor:pointer" class="glyphicon glyphicon-trash supprEntraide entraideEnCours" entraideId="<?php echo $entraideEnCours->id ?>" ></a>
                  </td>
                </tr>
              </table>
            </div>
            <?php
            if(sizeof($listeEntraides) > 1)
            {
              ?>
              <div id="divTableEntraideAVenir">
                <h4>A venir</h4>
                <table class="table table-striped table-hover table-condensed table-bordered">
                <tr>
                  <th>Site</th>
                  <th>Date Début</th>
                  <th>Date Fin</th>
                  <th>Annuler</th>
                </tr>
                <?php
                foreach($listeEntraides as $entraide)
                {
                  if($entraide->id != $entraideEnCours->id)
                  {
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
                  <th>Annuler</th>
                </tr>
                <?php
                foreach($listeEntraides as $entraide)
                {
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
  $("#dateExpiration").daterangepicker({locale: {format: 'DD/MM/YYYY'}});

  $(".supprEntraide").click(function(){
    var elt = $(this);
    $(this).removeClass("supprEntraide entraidesAVenir");
     $.post("API/removeEntraideById.php", {entraide_id: $(this).attr("entraideId")}, function(){
       elt.closest("tr").hide();
       if(elt.hasClass("entraideEnCours"))
       {
         $("#divTableEntraideEnCours").hide();
         var siteCaff = $("#site-caff-" + $("#idCaffEntraide").val()).children("del").text();
         $("#site-caff-" + $("#idCaffEntraide").val()).html(siteCaff);
       }
       if($(".entraidesAVenir").length == 0)
       {
         $("#divTableEntraideAVenir").hide();
       }
       if($(".supprEntraide").length == 0)
        {
          $("#btnEntraideCaff-" + $("#idCaffEntraide").val()).css("color", "rgb(51, 122, 183)");
        }
     });
  });

  $("#validerModaleEntraide").click(function(){
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
        }
      }
    });
    listeDomaines = JSON.stringify(listeDomaines);
    var dateDebut = $("#dateExpiration").val().split(" - ")[0];
    dateDebut = dateDebut.split("/");
    dateDebut = dateDebut[2] + "-" + dateDebut[1] + "-" + dateDebut[0];
    var dateFin = $("#dateExpiration").val().split(" - ")[1];
    dateFin = dateFin.split("/");
    dateFin = dateFin[2] + "-" + dateFin[1] + "-" + dateFin[0];
    $.post("API/entraideCaff.php", {caff_id: $("#idCaffEntraide").val(), site_entraide_id: $("#idSiteEntraide").val(), liste_domaines_json: listeDomaines, date_expiration: dateFin, date_debut: dateDebut}, function(data){
      var reponse = JSON.parse(data);
      if(reponse)
      {
        document.location.reload();
      }
      else{
        alert("Une erreur s'est produite, veuillez réeesayer plus tard");
      }
    });
  });
</script>