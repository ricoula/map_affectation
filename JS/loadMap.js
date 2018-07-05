
 window.onload = function() {
 
  var mapElement = document.getElementById('map');
  map = new google.maps.Map(mapElement, { center: new google.maps.LatLng(45.6930369, 4.9989082), zoom: 8 });
  //document.getElementById("mapJson").value = JSON.stringify(map);
  //$("#mapJson").val(JSON.stringify(map));
  var iw = new google.maps.InfoWindow();
  
  var oms = new OverlappingMarkerSpiderfier(map, {
    markersWontMove: true,
    markersWontHide: true
  });

  $.post('API/getConfigById.php', {utilisateur_id: $("#user_id").val()},function(data){
    config = JSON.parse(JSON.parse(data));
  });
  $.getJSON('API/getPoiNA.php',function(data){
   
            data.forEach(function(poi){

              var strokecolorpoi = 'black';
              var strokeweightpoi = 2;
              var scalepoi = 7;
              var d = new Date();
              var date_now = d.getTime() + (Number(config.filterdre) * 86400000);
            //   var date_now = Number(d.getFullYear() +''+ (d.getMonth() + 1) +''+ d.getDate());
            //   var date_dre = Number(poi.ft_oeie_dre.split('-')[0] + poi.ft_oeie_dre.split('-')[1] + poi.ft_oeie_dre.split('-')[2]);
              var date_dre = new Date(poi.ft_oeie_dre).getTime();

            //   console.log(test + " " + test2);
              if(date_dre <= date_now){
                strokecolorpoi = config.filtercolorurgent;
                // console.log(filtercolorurgent + " " + strokecolorpoi );
                strokeweightpoi = 3;
                scalepoi = 7;
              }
              config.filtersj.forEach(function(sj){
               
                var sj_oeie = sj.split("-")[1];
                var ui_oeie = sj.split("-")[2];
                

                if((poi.ft_sous_justification_oeie == sj_oeie && poi.atr_ui == ui_oeie)){
                //   strokecolorpoi = config.filtercolorurgent;
                    scalepoi = 11;
                    strokeweightpoi = 4;
                }
                  
              });
              $("#leg_client").css({"backgroundColor":config.filtercolorclient});
              $("#leg_immo").css({"backgroundColor":config.filtercolorimmo});
              $("#leg_focu").css({"backgroundColor":config.filtercolorfocu});
              $("#leg_dissi").css({"backgroundColor":config.filtercolordissi});
              $("#leg_coord").css({"backgroundColor":config.filtercolorcoord});
              $("#leg_fors").css({"backgroundColor":config.filtercolorfors});
              $("#leg_dre").css({"borderColor":config.filtercolorurgent});
              $("#leg_dre_txt").text("DRE < J+"+config.filterdre);
              if(poi.domaine == 'Client'){
                color = config.filtercolorclient
              }
              else if(poi.domaine == 'Immo'){
                color = config.filtercolorimmo
              }
              else if(poi.domaine == 'Dissi'){
                color = config.filtercolordissi
              }
              else if(poi.domaine == 'FO & CU'){
                color = config.filtercolorfocu
              }
              else if(poi.domaine == 'Coordi'){
                color = config.filtercolorcoord
              }
              else if(poi.domaine == 'FORS'){
                color = config.filtercolorfors
              }
              else{
                color = null;
              }
              
              var marker = new google.maps.Marker({
                
            position: {lat: Number(poi.ft_longitude), lng: Number(poi.ft_latitude)},
            map: map,
            icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    fillColor: color,
                    fillOpacity: 1,
                    strokeColor: strokecolorpoi,
                    strokeWeight: strokeweightpoi,
                    scale: scalepoi,
                  },
            poi_id: poi.id,
            title: poi.ft_numero_oeie,
            poi_domaine: poi.domaine,
            poi_ui: poi.atr_ui
          });
          
          google.maps.event.addListener(map, 'click', function(e){
            iw.close();
          });
          google.maps.event.addListener(marker, 'rightclick', function(e) {  // 'spider_rightclick', not plain 'click'
          
          $.post("API/getPoiLienByTitulaire.php", {poi_json: JSON.stringify(poi)}, function(data){
            var tabPoiLien = JSON.parse(data);
            var title = "";
            if(tabPoiLien.length > 0)
            {
                var tab = new Array();
                tabPoiLien.forEach(function(cettePoi){
                    tab.push(cettePoi.ft_numero_oeie);
                  });
                  title = tab.join("\n");
                  var listePoiLien = " <span class='glyphicon glyphicon-link' data-toggle='tooltip' data-placement='right' title='" + title + "'></span><sup>" + tab.length + "</sup>";
                $("#listeLiens-" + marker.poi_id).html(listePoiLien);
                $('[data-toggle="tooltip"]').tooltip();
            }
        });
          iw.setContent('<div class="container info_poi_modal" >'+
                '<h2 id="win_info_numero_oeie">'+marker.title+ '<span id="listeLiens-' + marker.poi_id +'"></span>' + 
                    //+'<span class="meta"><span class="glyphicon glyphicon-link"></span>' + marker.poi_lien + '</span>'+
                '</h2>' +
                '<div class="list-group">' +
                    '<a href="#" class="list-group-item" id="win_info_affecter_a">Affecter à</a>' +
                    '<a class="list-group-item testClass" id="win_info_liens" style="cursor: pointer">POI en lien <span class="badge" id="badgeNbPoi"></span></a>' +
                    '<a href="#" class="list-group-item" id="win_info_affecter_auto">Affecter auto. <span id="rightClickPoi_' + marker.poi_id + '" class="glyphicon glyphicon-refresh gly-spin pull-right"></span></a>' +
                '</div>' +
            '</div>');
            iw.open(map, marker);
            $.post("API/getNbPoiEnLien.php", {commune: poi.ft_libelle_commune, voie: poi.ft_libelle_de_voie, titulaire: poi.ft_titulaire_client, ui: poi.atr_ui}, function(data2){ 
                    var nbPoi = JSON.parse(data2);
                      $("#badgeNbPoi").text(nbPoi);
                  });
                  $("#win_info_liens").click(function(){
                      $("#divListeCaffsLienPoi").load("modaleListeCaffsLienPoi.php?poi=" + poi.id, function(){
                          $('#modaleListeCaffsLienPoi').modal('show');
                      });
                  });
                  $("#win_info_affecter_a").click(function(){
                    $("#divAffecterA").html("");
                    $("#loadingDivAffecterA").show();
                    $('#modaleAffecterA').modal('show');
                    $("#divAffecterA").load("modaleAffecterA.php?poi_id=" + poi.id, function(){
                        $("#loadingDivAffecterA").hide();
                        $('#modaleAffecterA').modal('show');
                    });
                });
                



                $.post("API/getAdvancedConfig.php", {ui: marker.poi_ui}, function(data){
                    var config = JSON.parse(data);
                    if(config == null)
                    {
                      //console.log("DEFAUT UI");
                      var obj = {poi_id: marker.poi_id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_charge_reactive: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val(), limite_jour: $("#limiteAffectationJour").val(), limite_semaine: $("#limiteAffectationSemaine").val(), limite_max_calcul: $("#limiteMaxCalcul").val(), nb_jours_avant_conges_max: $("#nbJoursAvantCongesMax").val(), nb_jours_conges_max: $("#nbJoursCongesMax").val()};
                    }
                    else{
                        //console.log("UI: " + marker.poi_ui);
                      var obj = {poi_id: marker.poi_id, km: config.rayon_km_new, coef_poi_proxi: config.coef_rayon_new, coef_charge_reactive: config.coef_react, coef_charge: config.coef_non_react, limite_jour: config.max_day, limite_semaine: config.max_week, limite_max_calcul: config.max_rayon_new, nb_jours_avant_conges_max: config.jours_avant_conges, nb_jours_conges_max: config.jours_conges};
                    }
        
                    $.post("API/getAffectationAuto.php", obj, function(data){
                        //console.log(data);
                        var caff = JSON.parse(data);
                        $("#rightClickPoi_" + marker.poi_id).removeClass("glyphicon glyphicon-refresh gly-spin").addClass("label label-info").text(caff.name_related);
                        $("#win_info_affecter_auto").click(function(){
                            var repUser = confirm("Attribuer " + marker.title + " à " + caff.name_related + "?");
                            if(repUser)
                            {
                                $.post("API/addPoiAffect.php", {poi_id: marker.poi_id, poi_num: marker.title, poi_domaine: marker.poi_domaine, caff_id: caff.id, caff_name: caff.name_related, ui: marker.poi_ui}, function(data2){
                                    $("#cardBox-" + marker.poi_id).hide();
                                    marker.setMap(null);
                                });
                            }
                        });
                    });
                  });
  
          });
          google.maps.event.addListener(marker, 'spider_click', function(e) {  // 'spider_rightclick', not plain 'click'
          var myUrl = window.location.href;
          myUrl = myUrl.split("?")[0];
          var newUrl = myUrl + "?poi=" + marker.poi_id;
          history.pushState(null, null, newUrl);

          $("#side_bar").animate({left:'0px'},500);
          $("#glyph").animate({left:'500px'},500);
          
          $("#div-slide-home").load('slide-home.php?poi_id=' + marker.poi_id);
          $(".slide").hide();
          $("#div-slide-home").show();
          $(".glyph_div").removeClass("active");
          $("#slide-home").addClass("active");
          
            });

          oms.addMarker(marker);
            });
            $("#div-slide-home").load('slide-home.php');
            $("#div-slide-box").load('slide-box.php');
            $("#div-slide-users").load('slide-users.php?coefCharge=' + $("#coefCharge").val());
            $("#div-slide-filter").load('slide-filter.php?utilisateur_id=' + $("#user_id").val());
        
            $("#slide-box").click(function(){
                $("#side_bar").animate({left:'0px'},500);
                $("#glyph").animate({left:'500px'},500);
                $(".slide").hide();
                $("#div-slide-box").show();
                $(".glyph_div").removeClass("active");
                $("#slide-box").addClass("active");
            });
            $("#slide-filter").click(function(){
                $("#side_bar").animate({left:'0px'},500);
                $("#glyph").animate({left:'500px'},500);
                $(".slide").hide();
                $("#div-slide-filter").show();
                $(".glyph_div").removeClass("active");
                $("#slide-filter").addClass("active");
            });
            $("#slide-home").click(function(){
                var poi = getUrlParameter('poi');
                $("#side_bar").animate({left:'0px'},500);
                $("#glyph").animate({left:'500px'},500);
                $(".slide").hide();
                $("#div-slide-home").show();
                $(".glyph_div").removeClass("active");
                $("#slide-home").addClass("active");
                if(poi != undefined){
                    $("#div-slide-home").load('slide-home.php?poi_id=' + poi);
                }
            });
            
            $("#slide-users").click(function(){
                $("#side_bar").animate({left:'0px'},500);
                $("#glyph").animate({left:'500px'},500);
                $(".slide").hide();
                $("#div-slide-users").show();
                $(".glyph_div").removeClass("active");
                $("#slide-users").addClass("active");
            });
            var getUrlParameter = function getUrlParameter(sParam) {
                var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                    sURLVariables = sPageURL.split('&'),
                    sParameterName,
                    i;
            
                for (i = 0; i < sURLVariables.length; i++) {
                    sParameterName = sURLVariables[i].split('=');
            
                    if (sParameterName[0] === sParam) {
                        return sParameterName[1] === undefined ? true : sParameterName[1];
                    }
                }
            };
        
            $("#selectUi").change(function(){
                var el = document.getElementById('btnGenererPoiNA'),
                elClone = el.cloneNode(true);
                el.parentNode.replaceChild(elClone, el);

                $("#loadingChoixUi").show();
                $("#labelNbPoiNA").hide();
                $("#divListePoiNAUi").hide();

                var cetteUI = $(this).val();
                $.post("API/getPoiNAByUi.php", {ui: $(this).val()}, function(data){
                    //console.log(cetteUI);
                    $("#loadingChoixUi").hide();
                    var listePoi = JSON.parse(data);
                    $("#nbPoiNA").text(listePoi.length);
                    $("#labelNbPoiNA").show();
        
                    $("#progress_bar_affect").css({"width":"0%"});
                    $("#progress_bar_affect").html("0%");
                    
                    
                    $("#btnGenererPoiNA").click(function(){
                        //J'initialise la barre de chargement à 0%
                        $("#percent").text('0%').fadeIn();
                        //Je masque les précédents résultats
                        $("#resultatsListePoiNA").html("").hide();

                        //tableau nécessaire à retenir quels CAFF se sont vu affecter une POI suite à cet algorithme, afin de calculer le coeff de simulation
                        var listeCaffsSimulation = new Array();

                        //Je récupère la config de l'UI
                        $.post("API/getAdvancedConfig.php", {ui: cetteUI}, function(data5){
                            var config = JSON.parse(data5);

                            $("#divListePoiNAUi").show();
                            //J'initialise la variable de la barre de progression à 0
                            var progress = 0;
                            //Je crée une variable qui va permettre de savoir où en est le chargement
                            var progressionAlgoCaffAuto = 0;
                            //Début de la création du tableau d'affichage des POI à affecter
                            var html = "<table class='table table-striped table-hover table-condensed table-responsive'><thead><tr><th>POI</th><th>Domaine</th><th>DRE</th><th>SJ</th><th>Caff</th></tr></thead><tbody>";

                            //Je parcours chaque POI pour affecter au bon caff chacune d'entre elles
                            listePoi.forEach(function(poi){

                                //Je crée l'objet de paramètres pour la fonction getAffectationAuto (API)
                                if(config == null)
                                {
                                    var obj = {poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_charge_reactive: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val(), limite_jour: $("#limiteAffectationJour").val(), limite_semaine: $("#limiteAffectationSemaine").val(), limite_max_calcul: $("#limiteMaxCalcul").val(), nb_jours_avant_conges_max: $("#nbJoursAvantCongesMax").val(), nb_jours_conges_max: $("#nbJoursCongesMax").val()};
                                }
                                else{
                                    var obj = {poi_id: poi.id, km: config.rayon_km_new, coef_poi_proxi: config.coef_rayon_new, coef_charge_reactive: config.coef_react, coef_charge: config.coef_non_react, limite_jour: config.max_day, limite_semaine: config.max_week, limite_max_calcul: config.max_rayon_new, nb_jours_avant_conges_max: config.jours_avant_conges, nb_jours_conges_max: config.jours_conges};
                                }

                                $.post("API/getAffectationAuto.php", obj, function(data2){
                                    
                                    //J'incrémente la variable pour la barre de progresion
                                    progressionAlgoCaffAuto++;
                                    //J'actualise la barre de progression
                                    progress = Math.round(((progressionAlgoCaffAuto) / listePoi.length) * 100);  
                                    $("#progress_bar_affect").attr("aria-valuenow", progress);

                                    $("#progress_bar_affect").css("width", progress+"%");
                                    $("#percent").html(progress+"%");
                                    
                                    //Je crée le titre de la POI avec les liens de cette POI avec d'autres POI
                                    var listeLiensPoi = "";
                                    var nbPoiLien = 0;
                                    var tab = new Array();
                                    var thisTitle = "";
                                    if(poi.ft_titulaire_client != null && poi.ft_titulaire_client != "")
                                    {
                                        listePoi.forEach(function(cettePoi){
                                            if(cettePoi.id != poi.id)
                                            {
                                                if(cettePoi.ft_titulaire_client == poi.ft_titulaire_client)
                                                {
                                                    if(nbPoiLien < 2)
                                                    {
                                                        listeLiensPoi += " <span class='glyphicon glyphicon-link'></span> " + cettePoi.ft_numero_oeie;
                                                    }
                                                    else{
                                                        tab.push(cettePoi.ft_numero_oeie);
                                                    }

                                                    nbPoiLien++;
                                                }
                                            }
                                        });
                                    }
                                    if(tab.length > 0)
                                    {
                                        thisTitle = tab.join("\n");
                                    }
                                    if(nbPoiLien > 2)
                                    {
                                        listeLiensPoi += " <span class='glyphicon glyphicon-link' data-toggle='tooltip' title='" + thisTitle + "' data-placement='right'></span><sup>" + tab.length + "</sup>";
                                    }

                                    //Je récupère le caff recommandé par getAffectationAuto, et je l'ajoute comme attribut de la POI
                                    poi.affectationAuto = JSON.parse(data2);

                                    //J'affecte les valeurs de simulation aux caffs de la liste des affections AUTO
                                    poi.affectationAuto.listeAutresCaffs.forEach(function(ceCaff){
                                        listeCaffsSimulation.forEach(function(caffSimulation){
    
                                            //Je vérifie si le caff est présent dans listeCaffsSimulation pour lui affecter sa charge simulation
                                            if(caffSimulation.id == ceCaff.id)
                                            {
                                                ceCaff.nbAffectationsJour = caffSimulation.nbAffectationsJour;
                                                ceCaff.nbAffectationsSemaine = caffSimulation.nbAffectationsSemaine;
                                                ceCaff.limiteAtteinte = caffSimulation.limiteAtteinte;
                                                ceCaff.charge_simu = caffSimulation.charge_simu;
                                                ceCaff.charge_totale = caffSimulation.charge_totale;
                                            }
                                        });
                                    });

                                    var compare = function(a, b)
                                    {
                                        //On compare d'abord le nombre de POI à l'état 1 lié au titulaire qui appartiennent au CAFF
                                        if(a.nbPoiTitulaireEtat1 > b.nbPoiTitulaireEtat1)
                                        {
                                            return -1;
                                        }
                                        else if(a.nbPoiTitulaireEtat1 < b.nbPoiTitulaireEtat1)
                                        {
                                            return 1;
                                        }
                                        else{
                                            //S'ils ont le même nombre de POI à l'état 1 lié au titulaire, alors on compare leur nombre de POI hors état 1 en lien avec le titulaire
                                            if(a.nbPoiTitulaireNotEtat1 > b.nbPoiTitulaireNotEtat1)
                                            {
                                                return -1;
                                            }
                                            else if(a.nbPoiTitulaireNotEtat1 < b.nbPoiTitulaireNotEtat1)
                                            {
                                                return 1;
                                            }
                                            else{
                                                //On compare ensuite leur site, pour savoir lequel est le plus proche de la POI
                                                if(a.numSite < b.numSite)
                                                {
                                                    return -1;
                                                }
                                                else if(a.numSite > b.numSite)
                                                {
                                                    return 1;
                                                }
                                                else{
                                                    if(a.enConges)
                                                    {
                                                        if(b.enConges)
                                                        {
                                                            if(a.limiteAtteinte)
                                                            {
                                                                if(b.limiteAtteinte)
                                                                {
                                                                    if(a.charge_totale > b.charge_totale)
                                                                    {
                                                                        return 1;
                                                                    }
                                                                    else if(a.charge_totale < b.charge_totale)
                                                                    {
                                                                        return -1;
                                                                    }
                                                                    else{
                                                                        return 0;
                                                                    }
                                                                }
                                                                else{
                                                                    return 1;
                                                                }
                                                            }
                                                            else{
                                                                if(b.limiteAtteinte)
                                                                {
                                                                    return -1;
                                                                }
                                                                else{
                                                                    if(a.charge_totale > b.charge_totale)
                                                                    {
                                                                        return 1;
                                                                    }
                                                                    else if(a.charge_totale < b.charge_totale)
                                                                    {
                                                                        return -1;
                                                                    }
                                                                    else{
                                                                        return 0;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                        else{
                                                            return 1;
                                                        }
                                                    }
                                                    else{
                                                        if(b.enConges)
                                                        {
                                                            return -1;
                                                        }
                                                        else{
                                                            if(a.limiteAtteinte)
                                                            {
                                                                if(b.limiteAtteinte)
                                                                {
                                                                    if(a.charge_totale > b.charge_totale)
                                                                    {
                                                                        return 1;
                                                                    }
                                                                    else if(a.charge_totale < b.charge_totale)
                                                                    {
                                                                        return -1;
                                                                    }
                                                                    else{
                                                                        return 0;
                                                                    }
                                                                }
                                                                else{
                                                                    return 1;
                                                                }
                                                            }
                                                            else{
                                                                if(b.limiteAtteinte)
                                                                {
                                                                    return -1;
                                                                }
                                                                else{
                                                                    if(a.charge_totale > b.charge_totale)
                                                                    {
                                                                        return 1;
                                                                    }
                                                                    else if(a.charge_totale < b.charge_totale)
                                                                    {
                                                                        return -1;
                                                                    }
                                                                    else{
                                                                        return 0;
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    //Je tri la liste des CAFF avec la fonction que nous venons de créer
                                    poi.affectationAuto.listeAutresCaffs = poi.affectationAuto.listeAutresCaffs.sort(compare);

                                    //J'initialise les options du Select (qui représente la liste des Caff possibles à affecter, triés du plus probable au moins probable)
                                    var optionElt = "";

                                    poi.affectationAuto.listeAutresCaffs.forEach(function(ceCaff){

                                        //traitement pour savoir de quelle couleur doit apparître le caff
                                        if(ceCaff.nbPoiTitulaireEtat1 > 0 || ceCaff.nbPoiTitulaireNotEtat1 > 0)
                                        {
                                            color = "green";
                                        }
                                        else if(ceCaff.enConges){
                                            color = "orange";
                                        }
                                        else if(ceCaff.limiteAtteinte){
                                            color = "red";
                                        }
                                        else{
                                            color = "black";
                                        }

                                        if(ceCaff.charge_simu == null)
                                        {
                                            ceCaff.charge_simu = 0;
                                        }

                                        optionElt += "<option poi_ui='" + poi.atr_ui + "' poi_num='" + poi.ft_numero_oeie + "' poi_domaine='" + poi.domaine + "' poi_id='" + poi.id + "' caff_id='" + ceCaff.id + "' caff_name='" + ceCaff.name_related + "' value='caff-" + ceCaff.id + "' selected class='" + color + "' style='color:" + color + "' id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + "[" + ceCaff.site + "]" + ceCaff.name_related + " (" + Number(ceCaff.charge_totale).toFixed(1) + ") = ("+ Number(ceCaff.charge_initiale).toFixed(1) + ") + (" + Number(ceCaff.tauxRetard).toFixed(1) + ") - (" + Number(ceCaff.charge_rayon).toFixed(1) + ") + (" + Number(ceCaff.charge_simu).toFixed(1) + ")</option>";
                                    });

                                    //Je termine la création de la ligne (différente si la poi a un titulaire ou non)
                                    if(poi.ft_titulaire_client != null && poi.ft_titulaire_client != "")
                                    {
                                        html += "<tr><td>" + poi.ft_numero_oeie + listeLiensPoi + "</td><td>" + poi.domaine + "</td><td>" + poi.ft_oeie_dre + "</td><td>" + poi.ft_sous_justification_oeie + "</td><td><select id='selectAffectationAutoPoi-" + poi.id + "' class='selectAffectationAutoPoi numAff numAffaire-" + poi.ft_titulaire_client + "'>" + optionElt + "</select></td></tr>";
                                    }
                                    else{
                                        html += "<tr><td>" + poi.ft_numero_oeie + listeLiensPoi + "</td><td>" + poi.domaine + "</td><td>" + poi.ft_oeie_dre + "</td><td>" + poi.ft_sous_justification_oeie + "</td><td><select id='selectAffectationAutoPoi-" + poi.id + "' class='selectAffectationAutoPoi'>" + optionElt + "</select></td></tr>";
                                    }

                                    //S'il s'agit de la dernière POI à traiter, alors il faut refermer le tableau (et le créer)
                                    if(progressionAlgoCaffAuto == listePoi.length)
                                    {
                                        html += "</tbody></table><div class='affect_btn' id='affect_btn'><button id='affectationListePoiNA' class='affectationListePoiNA'><span>Affecter </span></button></div>";
                                        document.getElementById("resultatsListePoiNA").innerHTML = html;

                                        //Je sélectionne le premier caff de la liste des options (car il s'agit du caff le plus probable à affecter, vu que nous avon trier cette liste plus tôt)
                                        $(".selectAffectationAutoPoi").each(function(i, select){
                                            $(select).children("option:first").each(function(i, option){
                                                $(select).val($(option).val());
                                            });
                                        });

                                        //J'ajoute l'évènement lors du clique sur le bouton "Affecter"
                                        $("#affectationListePoiNA").click(function(){
                                            //Pour chaque CAFF sélectionné, je lui affecte la POI en base de données
                                            $("#resultatsListePoiNA").find(".selectAffectationAutoPoi").each(function(){
                                                poi_id = $(this).find(":selected").attr("poi_id");
                                                poi_num = $(this).find(":selected").attr("poi_num");
                                                caff_id = $(this).find(":selected").attr("caff_id");
                                                caff_name = $(this).find(":selected").attr("caff_name");
                                                poi_domaine = $(this).find(":selected").attr("poi_domaine");
                                                poi_ui = $(this).find(":selected").attr("poi_ui");
                                                postPoiAffect = 0;
                                                $.post("API/addPoiAffect.php",{poi_id: poi_id, poi_num: poi_num, poi_domaine: poi_domaine, caff_id: caff_id, caff_name: caff_name, ui: poi_ui}, function(data){
                                                    postPoiAffect++;
                                                    //Si toutes les POI ont été affectées, alors on recharge la page
                                                    if(postPoiAffect == $(".selectAffectationAutoPoi").length)
                                                    {
                                                        location.reload();
                                                    }
                                                });
                                            });
                                        });
                                            
                                        //On affiche le tableau que l'on vient de créer
                                        $("#resultatsListePoiNA").show();
                                        //On cache le pourcentage de la barre de progression
                                        $("#percent").fadeOut();
                                        //On active les tooltips
                                        $('[data-toggle="tooltip"]').tooltip();

                                        //Lors de l'affichage du select, la couleur de l'option sélectionnée n'apparait pas. Je modifie donc manuellement la couleur du select en fonction de la couleur de l'option choisie
                                        $(".selectAffectationAutoPoi").each(function(){
                                            if($(this).children("option:selected").hasClass("green"))
                                            {
                                                $(this).css("color", "green");
                                            }
                                            else{
                                                if($(this).children("option:selected").hasClass("red"))
                                                {
                                                    $(this).css("color", "red");
                                                }
                                                else{
                                                    if($(this).children("option:selected").hasClass("orange"))
                                                    {
                                                        $(this).css("color", "orange");
                                                    }
                                                    else{
                                                        $(this).css("color", "black");
                                                    }
                                                }
                                            }
                                        });

                                        //J'ajoute un évènement lorsque l'utilisateur choisi un autre CAFF que celui par défaut (s'il change un des select)
                                        $(".selectAffectationAutoPoi").change(function(elt){
                                            var valSelect = $(this).val();
                                            var listeClass = elt.target.classList.value;
                                            if($(this).hasClass("numAff"))
                                            {
                                                $(".selectAffectationAutoPoi").each(function(){
                                                    
                                                    if($(this).hasClass(listeClass))
                                                    {
                                                        //S'il l'un des select est lié au select qui vient d'être modifié, alors la valeur de cet autre select sera égal à la valeur du select qui vient d'être modifié                               
                                                        $(this).val(valSelect);

                                                        //Lors de l'affichage du select, la couleur de l'option sélectionnée n'apparait pas. Je modifie donc manuellement la couleur du select en fonction de la couleur de l'option choisie
                                                        if($(this).children("option:selected").hasClass("green"))
                                                        {
                                                            $(this).css("color", "green");
                                                        }
                                                        else{
                                                            if($(this).children("option:selected").hasClass("red"))
                                                            {
                                                                $(this).css("color", "red");
                                                            }
                                                            else{
                                                                if($(this).children("option:selected").hasClass("orange"))
                                                                {
                                                                    $(this).css("color", "orange");
                                                                }
                                                                else{
                                                                    $(this).css("color", "black");
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            }
                                            //Lors de l'affichage du select, la couleur de l'option sélectionnée n'apparait pas. Je modifie donc manuellement la couleur du select en fonction de la couleur de l'option choisie
                                            if($(this).children("option:selected").hasClass("green"))
                                            {
                                                $(this).css("color", "green");
                                            }
                                            else{
                                                if($(this).children("option:selected").hasClass("red"))
                                                {
                                                    $(this).css("color", "red");
                                                }
                                                else{
                                                    if($(this).children("option:selected").hasClass("orange"))
                                                    {
                                                        $(this).css("color", "orange");
                                                    }
                                                    else{
                                                        $(this).css("color", "black");
                                                    }
                                                }
                                            }
                                            
                                        });
                                    }

                                    //variable qui va me permettre de savoir si le caff est présent dans listeCaffsSimulation
                                    var caffDansListeCaffsSimulation = false;
                                    //Caff à qui l'algorithme recommande d'affecter la POI
                                    var caffAffecter = poi.affectationAuto.listeAutresCaffs[0];
                                    //Je parcours la liste des CAFF afin de vérifier si ce CAFF est déjà dans listeCaffsSimulation.
                                    //S'il y est déjà, alors on lui ajoute à sa charge de simulation
                                    //Sinon, on l'ajoute dans listeCaffsSimulation et on lui ajoute à sa charge de simulation
                                    listeCaffsSimulation.forEach(function(caff){

                                        //S'il existe déjà dans listeCaffsSimulation, alors on lui ajoute à sa charge de simulation
                                        if(caff.id == caffAffecter.id)
                                        {
                                            caffDansListeCaffsSimulation = true;

                                            //Si le domaine de la POI est FO & CU ou client, alors il faut incrémenter le nombre de POI du CAFF affectées au jour et à la semaine
                                            if(poi.domaine.toUpperCase() == "FO & CU" || poi.domaine.toUpperCase() == "CLIENT")
                                            {
                                                caff.nbAffectationsJour++;
                                                caff.nbAffectationsSemaine++;

                                                //Si le CAFF a atteint la limite d'affectation de la journee/semaine
                                                if( caff.nbAffectationsJour >= $("#limiteAffectationJour").val() || caff.nbAffectationsSemaine >= $("#limiteAffectationSemaine").val())
                                                {
                                                    caff.limiteAtteinte = true;
                                                }
                                            }

                                            if(poi.reactive)
                                            {
                                                caff.charge_totale += Number($("#coefPoiClient").val());
                                                if(caff.charge_simu == null)
                                                {
                                                    caff.charge_simu = Number($("#coefPoiClient").val());
                                                }
                                                else{
                                                    caff.charge_simu += Number($("#coefPoiClient").val());
                                                }
                                            }
                                            else{
                                                caff.charge_totale += Number($("#coefCharge").val());
                                                if(caff.charge_simu == null)
                                                {
                                                    caff.charge_simu = Number($("#coefCharge").val());
                                                }
                                                else{
                                                    caff.charge_simu += Number($("#coefCharge").val());
                                                }
                                            }
                                        }
                                    });
                                    if(!caffDansListeCaffsSimulation)
                                    {
                                        //Si le domaine de la POI est FO & CU ou client, alors il faut incrémenter le nombre de POI du CAFF affectées au jour et à la semaine
                                        if(poi.domaine.toUpperCase() == "FO & CU" || poi.domaine.toUpperCase() == "CLIENT")
                                        {
                                            caffAffecter.nbAffectationsJour++;
                                            caffAffecter.nbAffectationsSemaine++;

                                            //Si le CAFF a atteint la limite d'affectation de la journee/semaine
                                            if( caffAffecter.nbAffectationsJour >= $("#limiteAffectationJour").val() || caffAffecter.nbAffectationsSemaine >= $("#limiteAffectationSemaine").val())
                                            {
                                                caffAffecter.limiteAtteinte = true;
                                            }
                                        }

                                        if(poi.reactive)
                                        {
                                            caffAffecter.charge_totale += Number($("#coefPoiClient").val());
                                            if(caffAffecter.charge_simu == null)
                                            {
                                                caffAffecter.charge_simu = Number($("#coefPoiClient").val());
                                            }
                                            else{
                                                caffAffecter.charge_simu += Number($("#coefPoiClient").val());
                                            }
                                        }
                                        else{
                                            caffAffecter.charge_totale += Number($("#coefCharge").val());
                                            if(caffAffecter.charge_simu == null)
                                            {
                                                caffAffecter.charge_simu = Number($("#coefCharge").val());
                                            }
                                            else{
                                                caffAffecter.charge_simu += Number($("#coefCharge").val());
                                            }
                                        }

                                        listeCaffsSimulation.push(caffAffecter);
                                    }

                                });
                            });
                        });
                    });
                })
            });
          });


          $("#nbPoiSimu").blur(function(){
            if($(this).val() < 0 || $(this).val() == '' || $(this).val() == null)
            {
                $(this).val(0);
            }
          });
          
          $(".numberNegatif").blur(function(){
            if($(this).val() < 0 || $(this).val() == '' || $(this).val() == null)
            {
                $(this).val(0);
            }
        });

          $("#btnValiderModalSimulation").click(function(){
              $.post("API/addListePoiSimu.php", {nb_poi: $("#nbPoiSimu").val(), ui: $("#selectUiSimu").val()}, function(){
                  document.location.reload();
              });
          });

          $("#btnValiderModalSimulationAvance").click(function(){
            $.post("API/addListePoiSimuDomaines.php", {nbDissi: $("#dissiSimu").val(), nbClient: $("#clientSimu").val(), nbImmo: $("#immoSimu").val(), nbFocu: $("#focuSimu").val(), nbCoordi: $("#coordiSimu").val(), nbFors: $("#forsSimu").val(), ui: $("#selectUiSimuAvance").val()}, function(){
                document.location.reload();
            });
        });
          $("#advancedConfigUI").click(function(e){
              e.stopPropagation();
              //console.log("ok2");
          });
          $("#advancedConfigAddUI").click(function(){
            var advancedSelectedUI = $('#advancedConfigUI').find(":selected").attr('value');
            if(advancedSelectedUI != "null"){
                $.post("API/addAdvancedConfigUI.php",{ui : advancedSelectedUI}, function(data){
                    if(data == true){
                        document.location.reload();
                    }
                    else
                    {
                        alert("La configuration de l'ui existe déjà");
                    }
                })
            }
       
          });
          $(".remove_advanced_ui").click(function(){
              var ui = $(this).attr('ui');
              var ui_libelle = $(this).attr('ui_libelle');
              var result = confirm("Voulez vous vraiment supprimer la configuration de l'ui "+ui_libelle);
              if(result==true){
                  $.post("API/removeAdvancedConfigUI.php",{ui: ui},function(){
                    $("#advancedConfig-"+ui).html("");
                  })
              }
          })
          $("#btnResetSimu").click(function(){
            $.post("API/resetSimulation.php", function(){
                document.location.reload();
            });
        });
    }
    