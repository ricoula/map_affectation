<?php
  include("API/fonctions.php");
  $listeSites = json_decode(getSites());
  $listeEntraides = json_decode(getProchainesEntraidesCaff($_GET["idCaff"]));
  foreach($listeEntraides as $entraide)
  {
    ?>
    <input type="hidden" class="uneEntraide" dateDeb="<?php echo $entraide->date_debut ?>" dateFin="<?php echo $entraide->date_expiration ?>" />
    <?php
  }

  $siteBase = urldecode($_GET["site"]);
  $idSiteBase = json_decode(getIdFromSite($siteBase));
?>

<input type="hidden" name="siteEntraide" id="siteEntraide" value="<?php echo $_GET["site"] ?>" />
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
          if(strtoupper($siteBase) == strtoupper($site->libelle))
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
          $domaines = implode(", ", $entraideEnCours->domaines);
            ?>
            <div id="divTableEntraideEnCours">
              <h4>En cours</h4>
              <table class="table table-striped table-hover table-condensed table-bordered">
                <tr>
                  <th>Site</th>
                  <th>Date Début</th>
                  <th>Date Fin</th>
                  <th>Domaines</th>
                  <th>Annuler</th>
                </tr>
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
                  <th>Domaines</th>
                  <th>Annuler</th>
                </tr>
                <?php
                foreach($listeEntraides as $entraide)
                {
                  if($entraide->id != $entraideEnCours->id)
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
/*var w = 0;
var v = 0;
var premiere_date = null;
var compteur = 0;
premiere_date_comp = null;
var nbEvent = 0;
var event = false;
$("#dateExpiration").on("showCalendar.daterangepicker", function(){
  if(nbEvent > 0)
  {
    event = true;
    console.log("ghthtezhezqe");
  }
  nbEvent++;
});
  $("#dateExpiration").daterangepicker({locale: {format: 'DD/MM/YYYY'}, isInvalidDate: function(date){
    
    w++;
    premiere_date = date._d;
    if(w == 1)
    {
      console.log("CLICK");
      if(premiere_date_comp == null){
        premiere_date_comp = premiere_date;
      }else if(new Date(premiere_date_comp).getTime() == new Date(premiere_date).getTime() || event == true){ // || ev = true

        event = false;
        if(compteur == 0){
          compteur++;
          console.log("premier click");
        }else
        {
          compteur = 0; 
          console.log("second click");
        }
      }else{
        premiere_date_comp = premiere_date;
      }
console.log(compteur);


     
    }else if(w == 84)
    {
    w=0;
  
    }
    var trouve = false;
   //console.log(date.length);
    
   
    $(".uneEntraide").each(function(){
      var dateDeb = new Date($(this).attr("dateDeb"));
      var dateFin = new Date($(this).attr("dateFin"));

      if(date._d.getTime() >= dateDeb.getTime() && date._d.getTime() <= dateFin.getTime())
      {
        trouve = true;
      }
    });
    if(!trouve)
    {
      return false;
    }
    else{
      return true;
    }
  } }, function(start, end, label) {

});*/

/* var w = 1;
var v = 0;
var nonValable = false;
  $("#dateExpiration").daterangepicker({locale: {format: 'DD/MM/YYYY'}, isInvalidDate: function(date){
    // if(v >= 84)
    //   {
    //     w = 1;
    //     v = 0;
    //   }
    var trouve = false;
      v++;
      if(v == 84)
      {
        v = 0;
      }
    //if($(".start-date").length > 0 && w == 1 && v == 84)
    if($(".start-date").length > 0 && v == 0)
    {
      //w++;
      v = 0;
      var jour = parseInt($(".start-date").text()) + 1;
      var monthYear = $(".start-date").closest("table").children("thead").children("tr").children(".month").text();
      var tabMonth = monthYear.split(" ");
      var annee = tabMonth[1];
      var mois = tabMonth[0];
      var dateDebutChoisie = new Date(mois + " " + jour + ", " + annee)
      if(date._d.getTime() >= dateDebutChoisie.getTime() && nonValable)
      {
        trouve = true;
      }
    }
    $(".uneEntraide").each(function(){
      var dateDeb = new Date($(this).attr("dateDeb"));
      var dateFin = new Date($(this).attr("dateFin"));

      if(date._d.getTime() >= dateDeb.getTime() && date._d.getTime() <= dateFin.getTime() && trouve == false)
      {
        console.log("FFFFFFFFFFF");
        trouve = true;
        return true;
      }
    });

    if(!trouve)
    {
      return false;
    }
    else{
      
      nonValable = true;
      return true;
    }
  }
  });*/

  $("#dateExpiration").daterangepicker({locale: {format: 'DD/MM/YYYY'}, isInvalidDate: function(date){
    var trouve = false;
    $(".uneEntraide").each(function(){
      var dateDeb = new Date($(this).attr("dateDeb"));
      dateDeb = dateDeb.getTime() - (23 * 60 * 60 * 1000) - (30 * 60 * 1000); //J'enleve 23h30 à la date pour que la date de debut ne soit pas valide à la selection
      dateDeb = new Date(dateDeb);
      var dateFin = new Date($(this).attr("dateFin"));

      if(date._d.getTime() >= dateDeb.getTime() && date._d.getTime() <= dateFin.getTime())
      {
        trouve = true;
      }
    });

    if(!trouve)
    {
      return false;
    }
    else{
      
      nonValable = true;
      return true;
    }
  }});


  $("#dateExpiration").on("apply.daterangepicker", function(ev, obj){
    var dateDebutChoisi = obj.startDate._d;
    var dateFinChoisi = obj.endDate._d;
    var valActuelle = $("#dateExpiration").val();
    var dateValide = true;
    $(".uneEntraide").each(function(){
      var dateDeb = new Date($(this).attr("dateDeb"));
      var dateFin = new Date($(this).attr("dateFin"));
      if(dateDebutChoisi.getTime() <= dateDeb.getTime() && dateFinChoisi.getTime() >= dateDeb.getTime())
      {
        dateValide = false;
      }
      /*if((dateDebutChoisi.getTime() >= dateDeb.getTime() && dateDebutChoisi.getTime() <= dateFin.getTime()) || (dateFinChoisi.getTime() >= dateDeb.getTime() && dateFinChoisi.getTime() <= dateFin.getTime()))
      {
        dateValide = false;
      }
      else{
        console.log("non");
      }*/
    });
    if(!dateValide)
    {
      alert("Cette période n'est pas valide car une enraide est déjà prévu");
      $("#divModaleEntraideCaff").load("modaleEntraideCaff.php?idCaff=" + $("#idCaffEntraide").val() + "&site=" + $("#siteEntraide").val(), function(){
          
        });
    }
    else{
      $("#validerModaleEntraide").prop("disabled", false);
    }
  });

  var fonctionVerifDate = function(valDate){ //prends en parametres la valeur du input daterangepicker et renvoit true si la date est valide, false sinon
    var tabValDate = valDate.split(" - ");
    var dateDb = tabValDate[0].split("/")[2] + "-" + tabValDate[0].split("/")[1] + "-" + tabValDate[0].split("/")[0];
    var dateFn = tabValDate[1].split("/")[2] + "-" + tabValDate[1].split("/")[1] + "-" + tabValDate[1].split("/")[0];
    var dateDebutChoisi = new Date(dateDb);
    var dateFinChoisi = new Date(dateFn);
    var dateValide = true;
    $(".uneEntraide").each(function(){
      var dateDeb = new Date($(this).attr("dateDeb"));
      var dateFin = new Date($(this).attr("dateFin"));
      console.log(dateDebutChoisi.getTime() + " <= " + dateDeb.getTime() + " && " + dateFinChoisi.getTime() + " >= " + dateDeb.getTime() +"\n\n");
      if(dateDebutChoisi.getTime() <= dateDeb.getTime() && dateFinChoisi.getTime() >= dateDeb.getTime())
      {
        dateValide = false;
      }
      else if(dateDebutChoisi.getTime() >= dateDeb.getTime() && dateDebutChoisi.getTime() <= dateFin.getTime())
      {
        dateValide = false;
      }

    });
    return dateValide;
  };

  
  if(!fonctionVerifDate($("#dateExpiration").val()))
  {
    $("#validerModaleEntraide").prop("disabled", true);
  }
  else{
    $("#validerModaleEntraide").prop("disabled", false);
  }

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

        $("#divModaleEntraideCaff").load("modaleEntraideCaff.php?idCaff=" + $("#idCaffEntraide").val() + "&site=" + $("#siteEntraide").val(), function(){
          
        });

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
    $.post("API/entraideCaff.php", {caff_id: $("#idCaffEntraide").val(), site_entraide_id: $("#idSiteEntraide").val(), liste_domaines_json: listeDomaines, date_expiration: dateFin, date_debut: dateDebut, site_defaut_id: $("#idSiteEntraideBase").val()}, function(data){
      var reponse = JSON.parse(data);
      if(reponse)
      {
        $("#divModaleEntraideCaff").load("modaleEntraideCaff.php?idCaff=" + $("#idCaffEntraide").val() + "&site=" + $("#siteEntraide").val(), function(){
          $("#btnEntraideCaff-" + $("#idCaffEntraide").val()).css("color", "orange");
          if($(".siteEntraideEnCours").length > 0)
          {
            var siteEnCours = $(".siteEntraideEnCours:first").text();
            var siteCaff = $("#site-caff-" + $("#idCaffEntraide").val()).attr("siteCaff");
            $("#site-caff-" + $("#idCaffEntraide").val()).html("<del>" + siteCaff + "</del> " + siteEnCours);
          }
        });
      }
      else{
        alert("Une erreur s'est produite, veuillez réeesayer plus tard");
      }
    });
  });
</script>