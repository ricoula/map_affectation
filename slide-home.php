<html>
    <head>
      <style>
        .tooltip-inner {
            white-space:pre;
            max-width:none;
        }
      </style>
    </head>
    <body>

    <?php
    if(isset($_GET["poi_id"]) && $_GET["poi_id"] != null)
    {
    include("API/fonctions.php");
    $poi = json_decode(getPoiById($_GET["poi_id"]));
    $closestSite = json_decode(getClosestSite($poi->id));
    $tabPoiLien = json_decode(getPoiLienByTitulaire(json_encode($poi)));
    if(sizeof($tabPoiLien) > 0)
    {
        $tab = array();
        foreach($tabPoiLien as $cettePoi)
        {
          array_push($tab, $cettePoi->ft_numero_oeie);
        }
        $title = implode("\n", $tab);
        $listePoiLien = "<span class='glyphicon glyphicon-link' data-toggle='tooltip' data-placement='right' title='".$title."'></span><sup>".sizeof($tab)."</sup>";
    }
    else{
      $listePoiLien = "";
    }
    ?>
    <!-- <input type="hidden" id="poi_lat_lng" latlng='<?php /*echo $closestSite->poilatlng ;?>'>
    <input type="hidden" id="site_lat_lng" latlng='<?php echo $closestSite->sitelatlng ;?>'>
    <input type="hidden" id="site_nb" nb="<?php echo $closestSite->i*/ ?>"> -->
    <input type="hidden" id="idPoi" name="idPoi" value="<?php echo $_GET["poi_id"] ?>" />
    <span class="glyphicon glyphicon-remove pull-right slide-close"></span></br> 
    <h1 id="home-poi" class="well"><?php echo $poi->ft_numero_oeie.$listePoiLien ?><span class="badge badge-default pull-right" id="home-domaine"><?php echo $poi->domaine ?></span></h1>
    <h4>Information POI</h4>
    <form>
    <div class="form-group">
        <div class="list-group">
            <div class="list-group-item"><label>UI</label><label class="pull-right home-result " id="home-ui"><?php echo $poi->atr_ui ?></label></div>
           <div class="list-group-item"><label>Date de création</label><label class="pull-right home-result " id="home-creation"><?php echo $poi->ft_date_creation_oeie ?></label></div>
           <div class="list-group-item"><label>Date de retour d'étude</label><label class="pull-right home-result" id="home-dre"><?php echo $poi->ft_oeie_dre ?></label></div>
           <div class="list-group-item"><label>Date limite de réalisation</label><label class="pull-right home-result " id="home-dlr"><?php echo $poi->ft_date_limite_realisation ?></label></div>
           <div class="list-group-item"><label>Projet générique</label><label class="pull-right home-result" id="home-pg"><?php echo $poi->ft_pg ?></label></div>
           <div class="list-group-item"><label>Sous justification</label><label class="pull-right home-result" id="home-sj"><?php echo $poi->ft_sous_justification_oeie ?></label></div>
           <div class="list-group-item"><label>Commentaire</label><p class="list-group-item" id="home-commentaire"><?php echo $poi->ft_commentaire_creation_oeie ?></p></div>    
        </div>
        <h4>Information Client</h4>
        <div class="list-group">
            <div class="list-group-item"><label>Titulaire</label><label class="pull-right home-result" id="home-titulaire"><?php echo $poi->ft_titulaire_client ?></label></div>
            <div class="list-group-item"><label>Commune</label><label class="pull-right home-result" id="home-commune"><?php echo $poi->ft_libelle_commune ?></label></div>
            <div class="list-group-item"><label>Voie</label><label class="pull-right home-result" id="home-voie"><?php echo $poi->ft_libelle_de_voie ?></label></div>
        </div>
        <h4>Information d'affectation</h4>
        <div class="list-group">
            <div class="list-group-item"><label>Site le plus proche</label><label class="pull-right home-result" id="home-closest"><?php echo $closestSite->libelle ?></label></div>
            <div class="list-group-item"><label>Distance</label><label class="pull-right home-result" id="home-distance"><?php echo $closestSite->distance ?></label></div>
            <div class="list-group-item"><label>Temps</label><label class="pull-right home-result" id="home-temps"><?php echo $closestSite->duree ?></label></div>
            <div class="list-group-item"><label>Caff conseillé</label><span id="attenteCaffConseille" class="pull-right"><img src="img/wait.gif" /></span></div>
        </div>
        
      <!-- <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
      <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Password</label>
      <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
    </div>
    <div class="form-check">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input">
        Check me out
      </label>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button> -->
  </form>

    </div>
    <?php
    }else{
      ?>
      <label class="label label-primary" >Aucune POI sélectionnée</label>
      <?php
    }
    ?>
    </body>
</html>
<script>
      $('[data-toggle="tooltip"]').tooltip();
        //console.log("CETTE POI = " + $("#idPoi").val());
        if($("#idPoi").val() != undefined && $("#idPoi").val() != null)
        {
          $.post("API/getAffectationAuto.php", {poi_id: $("#idPoi").val(), km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_charge_reactive: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val(), limite_jour: $("#limiteAffectationJour").val(), limite_semaine: $("#limiteAffectationSemaine").val(), limite_max_calcul: $("#limiteMaxCalcul").val()}, function(data){
            var caff = JSON.parse(data);
            $("#attenteCaffConseille").replaceWith('<label class="pull-right home-result label label-success" id="home-caff">' + caff.name_related + '</label>');
          });
        }
        $(".slide-close").click(function(){
        $("#side_bar").animate({left:'-500px'},500);
        $("#glyph").animate({left:'0px'},500);
        $(".glyph_div").removeClass("active");
    });
</script>
<script>
                var d = new Date();
                var date_now = Number(d.getFullYear() +''+ (d.getMonth() + 1) +''+ d.getDate());
                var date_poi = Number($("#home-dre").text().split('-')[0] + $("#home-dre").text().split('-')[1] + $("#home-dre").text().split('-')[2]);
                if(date_poi <= (date_now + Number(config.filterdre))){
                  $("#home-dre").addClass("label label-danger");
                }
                config.filtersj.forEach(function(sj){
                var sj_oeie = sj.split("-")[1];
                var ui_oeie = sj.split("-")[2];
                //console.log("test:" + sj_oeie + " poi:" + $("#home-sj").text() + " test:" + ui_oeie + " poi:" + $("#home-ui").text());

                if($("#home-sj").text() == sj_oeie && $("#home-ui").text() == ui_oeie){
                  //console.log("testok");
                  $("#home-sj").addClass("label label-danger");
                }
            
              });
              if($("#home-domaine").text() == 'Client'){
                color_label_poi = config.filtercolorclient;
                //console.log("Color: " + color_label_poi);
              }
              else if($("#home-domaine").text() == 'Immo'){
                color_label_poi = config.filtercolorimmo;
                //console.log("Color: " + color_label_poi);
              }
              else if($("#home-domaine").text() == 'Dissi'){
                color_label_poi = config.filtercolordissi;
                //console.log("Color: " + color_label_poi);
              }
              else if($("#home-domaine").text() == 'FO & CU'){
                color_label_poi = config.filtercolorfocu;
                //console.log("Color: " + color_label_poi);
              }
              else if($("#home-domaine").text() == 'Coordi'){
                color_label_poi = config.filtercolorcoord;
                //console.log("Color: " + color_label_poi);
              }
              else{
                 color_label_poi = null;
                  //console.log("Color: " + color_label_poi);
              }
              $("#home-domaine").css({"backgroundColor":color_label_poi,"color":"black"});


</script>
<!-- <script>
//console.log($("#poi_lat_lng").attr('latlng'));
//console.log($("#site_lat_lng").attr('latlng'));
//console.log($("#site_nb").attr('nb'))
var a = $("#poi_lat_lng").attr('latlng').split("|")[$("#site_nb").attr('nb')];
var b = $("#site_lat_lng").attr('latlng')
var a_lng = Number(a.split(",")[0]);
var a_lat = Number(a.split(",")[1]);
var b_lng = Number(b.split(",")[0]);
var b_lat = Number(b.split(",")[1]);
//console.log(a_lng + " " + a_lat + " " +b_lng + " " + b_lat);

var pointA = new google.maps.LatLng(a_lng,a_lat);
var pointB = new google.maps.LatLng(b_lng,b_lat);
directionsService = new google.maps.DirectionsService,
directionsDisplay = new google.maps.DirectionsRenderer({
  preserveViewport: true,
map: map
})
calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB);
function calculateAndDisplayRoute(directionsService, directionsDisplay, pointA, pointB) {
    directionsService.route({
        origin: pointA,
        destination: pointB,
        avoidTolls: true,
        avoidHighways: false,
        travelMode: google.maps.TravelMode.DRIVING
    }, function (response, status) {
        if (status == google.maps.DirectionsStatus.OK) {
            directionsDisplay.setDirections(response);
        } else {
            window.alert('Directions request failed due to ' + status);
        }
    });
}
</script> -->