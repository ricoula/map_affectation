
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
            poi_domaine: poi.domaine
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

          $.post("API/getAffectationAuto.php", {poi_id: marker.poi_id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_charge_reactive: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val(), limite_jour: $("#limiteAffectationJour").val(), limite_semaine: $("#limiteAffectationSemaine").val(), limite_max_calcul: $("#limiteMaxCalcul").val(), nb_jours_avant_conges_max: $("#nbJoursAvantCongesMax").val(), nb_jours_conges_max: $("#nbJoursCongesMax").val()}, function(data){
            console.log(data);
            var caff = JSON.parse(data);
            $("#rightClickPoi_" + marker.poi_id).removeClass("glyphicon glyphicon-refresh gly-spin").addClass("label label-info").text(caff.name_related);
            $("#win_info_affecter_auto").click(function(){
                var repUser = confirm("Attribuer " + marker.title + " à " + caff.name_related + "?");
                if(repUser)
                {
                    $.post("API/addPoiAffect.php", {poi_id: marker.poi_id, poi_num: marker.title, poi_domaine: marker.poi_domaine, caff_id: caff.id, caff_name: caff.name_related}, function(data2){
                        $("#cardBox-" + marker.poi_id).hide();
                        marker.setMap(null);
                    });
                }
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
        
                $.post("API/getPoiNAByUi.php", {ui: $(this).val()}, function(data){
                    $("#loadingChoixUi").hide();
                    var listePoi = JSON.parse(data);
                    $("#nbPoiNA").text(listePoi.length);
                    $("#labelNbPoiNA").show();
        
                    $("#progress_bar_affect").css({"width":"0%"});
                    $("#progress_bar_affect").html("0%");


        
                    $("#btnGenererPoiNA").click(function(){
                      $("#percent").text('0%').fadeIn();
                        var el = document.getElementById('btnGenererPoiNA'),
                        elClone = el.cloneNode(true);
                        el.parentNode.replaceChild(elClone, el);
                        $("#resultatsListePoiNA").html("").hide();
                       
                        $("#divListePoiNAUi").show();
                        var progress = 0;
                        var html = "<table class='table table-striped table-hover table-condensed table-responsive'><thead><tr><th>POI</th><th>Domaine</th><th>DRE</th><th>SJ</th><th>Caff</th></tr></thead><tbody>";
                        var i = 0;
                        var y = 0;
                        var listeCaffsSimulation = new Array();
                        var listeClientsAvecListePoiLiee = new Array();
                        listePoi.forEach(function(poi){
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
                              y++;

                            $.ajax({
                                type: 'POST',
                                url: "API/getAffectationAuto.php",
                                data: {poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_charge_reactive: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val(), limite_jour: $("#limiteAffectationJour").val(), limite_semaine: $("#limiteAffectationSemaine").val(), limite_max_calcul: $("#limiteMaxCalcul").val(), nb_jours_avant_conges_max: $("#nbJoursAvantCongesMax").val(), nb_jours_conges_max: $("#nbJoursCongesMax").val()},// liste_caffs_simulation: listeCaffsSimulation},
                                success: function(data2){
                                    i++;
                                    var clientDejaDansTab = false;


                                    listeClientsAvecListePoiLiee.forEach(function(client){
                                        if(client.libelle == poi.ft_titulaire_client)
                                        {
                                            clientDejaDansTab = true;
                                        }
                                    });
                                    if(clientDejaDansTab)
                                    {
                                        listeClientsAvecListePoiLiee.forEach(function(client){
                                            if(client.libelle == poi.ft_titulaire_client)
                                            {
                                                client.listePoi.push(poi.id);
                                            }
                                        });
                                    }
                                    else{
                                        var listePoiClient = new Array();
                                        listePoiClient.push(poi.id);
                                        if(poi.ft_titulaire_client != null && poi.ft_titulaire_client != "")
                                        {
                                            var client = {libelle: poi.ft_titulaire_client, listePoi: listePoiClient};
                                            listeClientsAvecListePoiLiee.push(client);
                                        }
                                    }

                                    progress = Math.round(((i) / listePoi.length) * 100);  
                                    $("#progress_bar_affect").attr("aria-valuenow", progress);
  
                                    $("#progress_bar_affect").css("width", progress+"%");
                                     $("#percent").html(progress+"%");

                                    //console.log(data2);
                                    poi.affectationAuto = JSON.parse(data2);
                                    /*if(poi.affectationAuto == null)
                                    {
                                        console.log(poi);
                                    }
                                    if(poi.affectationAuto.listePoiTitulaire != null)
                                    {
                                        console.log("////////////////////////////////");
                                        console.log(poi.affectationAuto);
                                        console.log(poi);
                                        console.log("////////////////////////////////\n\n");
                                    }*/

                                    var optionElt = "";
                                    
                                    poi.affectationAuto.listeAutresCaffs.forEach(function(caffSimu){
                                        caffSimu.chargeGlobale = caffSimu.charge_totale;
                                    });

                                    listeCaffsSimulation.forEach(function(caffSimulation){
                                            poi.affectationAuto.listeAutresCaffs.forEach(function(caffSimu){
                                                if(caffSimu.id == caffSimulation.id)
                                                {
                                                    
                                                    caffSimulation.listePoiSimulation.forEach(function(poiSimulation){
                                                    if(poiSimulation.reactive)
                                                    {
                                                        //console.log("AVANT +1 :", caffSimu.charge_totale);
                                                        caffSimu.charge_totale = Number(caffSimu.charge_totale);
                                                        caffSimu.charge_totale += Number($("#coefPoiClient").val());
                                                        //console.log("APRES +1 :", caffSimu.charge_totale);
                                                        if(caffSimu.charge_simu == null)
                                                        {
                                                            caffSimu.charge_simu = Number($("#coefPoiClient").val());
                                                        }
                                                        else{
                                                            caffSimu.charge_simu += Number($("#coefPoiClient").val());
                                                        }
                                                    }
                                                    else{
                                                        //console.log("AVANT +0.5 :", caffSimu.charge_totale);
                                                        caffSimu.charge_totale = Number(caffSimu.charge_totale);
                                                        caffSimu.charge_totale += Number($("#coefCharge").val());
                                                        //console.log("APRES +0.5 :", caffSimu.charge_totale)
                                                        if(caffSimu.charge_simu == null)
                                                        {
                                                            caffSimu.charge_simu = Number($("#coefCharge").val());
                                                        }
                                                        else{
                                                            caffSimu.charge_simu += Number($("#coefCharge").val());
                                                        }
                                                    }
                                                });
                                                //console.log(caffSimu.name_related + " = " + caffSimu.charge_totale);
                                                }
                                            });
                                    });

                                    poi.affectationAuto.listeAutresCaffs.forEach(function(unCaff){
                                        listeCaffsSimulation.forEach(function(caffSimul){
                                            if(unCaff.id == caffSimul.id)
                                            {
                                                caffSimul.listePoiSimulation.forEach(function(poiSim){
                                                    if(poiSim.domaine.toUpperCase() == "FO & CU" || poiSim.domaine.toUpperCase() == "CLIENT")
                                                    {
                                                        unCaff.nbAffectationsJour ++;
                                                        unCaff.nbAffectationsSemaine ++;
                                                    }
                                                });

                                                if(unCaff.nbAffectationsJour > $("#limiteAffectationJour").val() || unCaff.nbAffectationsSemaine > $("#limiteAffectationSemaine").val())
                                                {
                                                    unCaff.limiteAtteinte = true;
                                                }
                                            }
                                        });
                                    });

                                    var compare = function(a, b){
                                        if(clientDejaDansTab)
                                        {
                                            listeClientsAvecListePoiLiee.forEach(function(client){
                                                if(client.libelle == poi.ft_titulaire_client && poi.ft_titulaire_client != null && poi.ft_titulaire_client != "")
                                                {
                                                    if(a.id == client.caffAffecte)
                                                    {
                                                        console.log("Poi: " + poi.ft_titulaire_client + " | Client: " + client.caffAffecte + " = " + a.id + "(" + a.name_related + ")");
                                                        return -1;
                                                    }
                                                    else{
                                                        if(b.id == client.caffAffecte)
                                                        {
                                                            console.log("Client: " + client.caffAffecte + " = " + b.id + "(" + b.name_related + ")");
                                                            return 1;
                                                        }
                                                    }
                                                }
                                            });
                                        }
                                            if(poi.affectationAuto.listePoiTitulaire != null && (a.id == poi.affectationAuto.id || b.id == poi.affectationAuto.id))
                                            {
                                                if(a.id == poi.affectationAuto.id)
                                                {
                                                    return -1;
                                                }
                                                else{
                                                    return 1;
                                                }
                                            }
                                            else{
                                                    if(a.nbAffectationsJour <= $("#limiteAffectationJour").val() && a.nbAffectationsSemaine <= $("#limiteAffectationSemaine").val() && a.enConges == false)
                                                    {
                                                        if(b.nbAffectationsJour <= $("#limiteAffectationJour").val() && b.nbAffectationsSemaine <= $("#limiteAffectationSemaine").val() && b.enConges == false)
                                                        {
                                                            if(a.charge_totale < b.charge_totale)
                                                            {
                                                                return -1;
                                                            }
                                                            else if(a.charge_totale == b.charge_totale)
                                                            {
                                                                return 0;
                                                            }
                                                            else{
                                                                return 1;
                                                            }
                                                        }
                                                        else{
                                                            return -1;
                                                        }
                                                    }
                                                    else{
                                                        if(b.nbAffectationsJour <= $("#limiteAffectationJour").val() && b.nbAffectationsSemaine <= $("#limiteAffectationSemaine").val() && b.enConges == false)
                                                        {
                                                            return 1;
                                                        }
                                                        else{
                                                            if(a.charge_totale < b.charge_totale)
                                                            {
                                                                return -1;
                                                            }
                                                            else if(a.charge_totale == b.charge_totale)
                                                            {
                                                                return 0;
                                                            }
                                                            else{
                                                                return 1;
                                                            }
                                                        }
                                                    }
                                                }
                                        
                                    };
                                    

                                    poi.affectationAuto.listeAutresCaffs.sort(compare);
    
                                    cpt = 0;
                                    poi.affectationAuto.listeAutresCaffs.forEach(function(ceCaff){
                                        cpt++;
                                        if(ceCaff.enConges)
                                        {
                                            color = "orange";
                                        }
                                        else{
                                            if(ceCaff.limiteAtteinte)
                                            {
                                                color = "red";
                                            }
                                            else{
                                                color = "black";
                                            }
                                        }

                                        if(ceCaff.charge_simu == null)
                                        {
                                            ceCaff.charge_simu = 0;
                                        }
                                        //console.log("Global = " + ceCaff.chargeGlobale);
                                        //if(poi.affectationAuto.id == ceCaff.id)
                                        if(cpt == 1)
                                        {
                                            if(poi.affectationAuto.listePoiTitulaire != null)
                                            {
                                                optionElt += "<option poi_num='" + poi.ft_numero_oeie + "' poi_domaine='" + poi.domaine + "' poi_id='" + poi.id + "' caff_id='" + ceCaff.id + "' caff_name='" + ceCaff.name_related + "' value='caff-" + ceCaff.id + "' selected class='caffTitulaireAffectAuto' style='color:green' id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + Number(ceCaff.charge_totale).toFixed(1) + ") = ("+ Number(ceCaff.charge_initiale).toFixed(1) + ") + (" + Number(ceCaff.tauxRetard).toFixed(1) + ") - (" + Number(ceCaff.charge_rayon).toFixed(1) + ") + (" + Number(ceCaff.charge_simu).toFixed(1) + ")</option>";
                                                //console.log("Selected " + poi.ft_numero_oeie + ": " + ceCaff.name_related + " (" + poi.affectationAuto.name_related + ")");
                                            }
                                            else{
                                                optionElt += "<option poi_num='" + poi.ft_numero_oeie + "' poi_domaine='" + poi.domaine + "' poi_id='" + poi.id + "' caff_id='" + ceCaff.id + "' caff_name='" + ceCaff.name_related + "' value='caff-" + ceCaff.id + "' selected class='" + color + "' style='color:" + color + "' id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + Number(ceCaff.charge_totale).toFixed(1) + ") = ("+ Number(ceCaff.charge_initiale).toFixed(1) + ") + (" + Number(ceCaff.tauxRetard).toFixed(1) + ") - (" + Number(ceCaff.charge_rayon).toFixed(1) + ") + (" + Number(ceCaff.charge_simu).toFixed(1) + ")</option>";
                                                //console.log("Selected " + poi.ft_numero_oeie + ": " + ceCaff.name_related + " (" + poi.affectationAuto.name_related + ")");
                                            }
                                        }
                                        else{
                                            if(poi.affectationAuto.listePoiTitulaire != null)
                                            {
                                                optionElt += "<option poi_num='" + poi.ft_numero_oeie + "' poi_domaine='" + poi.domaine + "' poi_id='" + poi.id + "' caff_id='" + ceCaff.id + "' caff_name='" + ceCaff.name_related + "' value='caff-" + ceCaff.id + "' selected class='caffTitulaireAffectAuto' style='color:green' id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + Number(ceCaff.charge_totale).toFixed(1) + ") = ("+ Number(ceCaff.charge_initiale).toFixed(1) + ") + (" + Number(ceCaff.tauxRetard).toFixed(1) + ") - (" + Number(ceCaff.charge_rayon).toFixed(1) + ") + (" + Number(ceCaff.charge_simu).toFixed(1) + ")</option>";
                                                //console.log("Selected " + poi.ft_numero_oeie + ": " + ceCaff.name_related + " (" + poi.affectationAuto.name_related + ")");
                                            }
                                            else{
                                                optionElt += "<option poi_num='" + poi.ft_numero_oeie + "' poi_domaine='" + poi.domaine + "' poi_id='" + poi.id + "' caff_id='" + ceCaff.id + "' caff_name='" + ceCaff.name_related + "' value='caff-" + ceCaff.id + "' style='color:" + color + "' id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + Number(ceCaff.charge_totale).toFixed(1) + ") = ("+ Number(ceCaff.charge_initiale).toFixed(1) + ") + (" + Number(ceCaff.tauxRetard).toFixed(1) + ") - (" + Number(ceCaff.charge_rayon).toFixed(1) + ") + (" + Number(ceCaff.charge_simu).toFixed(1) + ")</option>";
                                            }
                                            
                                        }

                                    //}

                                    });
                                    if(poi.ft_titulaire_client != null && poi.ft_titulaire_client != "")
                                    {
                                        html += "<tr><td>" + poi.ft_numero_oeie + listeLiensPoi + "</td><td>" + poi.domaine + "</td><td>" + poi.ft_oeie_dre + "</td><td>" + poi.ft_sous_justification_oeie + "</td><td><select id='selectAffectationAutoPoi-" + poi.id + "' class='selectAffectationAutoPoi numAff numAffaire-" + poi.ft_titulaire_client + "'>" + optionElt + "</select></td></tr>";
                                    }
                                    else{
                                        html += "<tr><td>" + poi.ft_numero_oeie + listeLiensPoi + "</td><td>" + poi.domaine + "</td><td>" + poi.ft_oeie_dre + "</td><td>" + poi.ft_sous_justification_oeie + "</td><td><select id='selectAffectationAutoPoi-" + poi.id + "' class='selectAffectationAutoPoi'>" + optionElt + "</select></td></tr>";
                                    }
                                    
                                    $("#btnCaffAffectAuto-" + poi.id).click();
                                    if(i == listePoi.length)
                                    {
                                        html += "</tbody></table><div class='affect_btn' id='affect_btn'><button id='affectationListePoiNA' class='affectationListePoiNA'><span>Affecter </span></button></div>";
                                     
                                        document.getElementById("resultatsListePoiNA").innerHTML = html;

                                        $("#affectationListePoiNA").click(function(){
                                            $("#resultatsListePoiNA").find(".selectAffectationAutoPoi").each(function(){
                                              poi_id = $(this).find(":selected").attr("poi_id");
                                              poi_num = $(this).find(":selected").attr("poi_num");
                                              caff_id = $(this).find(":selected").attr("caff_id");
                                              caff_name = $(this).find(":selected").attr("caff_name");
                                              poi_domaine = $(this).find(":selected").attr("poi_domaine");
                                              postPoiAffect = 0;
                                              $.post("API/addPoiAffect.php",{poi_id: poi_id, poi_num: poi_num, poi_domaine: poi_domaine, caff_id: caff_id, caff_name: caff_name}, function(data){
                                                  postPoiAffect++;
                                                  if(postPoiAffect == $(".selectAffectationAutoPoi").length)
                                                  {
                                                      location.reload();
                                                  }
                                              });
                                              
                                          //    console.log(poi_id + " " + poi_num + " " + poi_domaine + " " + caff_id + " " + caff_name);
                                              
                                                        })
                                                        
                                        });

                                        $("#resultatsListePoiNA").show();
                                        $("#percent").fadeOut();
                                        $('[data-toggle="tooltip"]').tooltip();
                                        
                                        $(".selectAffectationAutoPoi").each(function(){
                                            if($(this).children("option:selected").hasClass("caffTitulaireAffectAuto"))
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
                                        /*if($(".selectAffectationAutoPoi option:selected").hasClass("caffTitulaireAffectAuto"))
                                        {
                                            console.log($(this).parent());
                                        }
                                        else{
                                            $(this).parent().css("color", "black");
                                        }*/
                                        
                                        $(".selectAffectationAutoPoi").change(function(elt){
                                            var valSelect = $(this).val();
                                            var listeClass = elt.target.classList.value;
                                            if($(this).hasClass("numAff"))
                                            {
                                                $(".selectAffectationAutoPoi").each(function(){
                                                    if($(this).hasClass(listeClass))
                                                    {                                                      
                                                        $(this).val(valSelect);

                                                        if($(this).children("option:selected").hasClass("caffTitulaireAffectAuto"))
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
                                            if($(this).children("option:selected").hasClass("caffTitulaireAffectAuto"))
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
                                    var caffSimulation = poi.affectationAuto.listeAutresCaffs[0];
                                    var trouve = false;
                                    if(listeCaffsSimulation.length > 0)
                                    {
                                        listeCaffsSimulation.forEach(function(caff){
                                            if(caff.id == caffSimulation.id)
                                            {
                                                trouve = true;
                                                caff.listePoiSimulation.push(poi);
                                                if(!clientDejaDansTab)
                                                {
                                                    listeClientsAvecListePoiLiee.forEach(function(client){
                                                        client.listePoi.forEach(function(poiClient){
                                                            if(poiClient == poi.id)
                                                            {
                                                                client.caffAffecte = caff.id;
                                                            }
                                                        });
                                                    });
                                                }
                                                //console.log("POI " + poi.ft_numero_oeie + " : " + caff.name_related);
                                            }
                                        });
                                    }
                                    if(!trouve)
                                    {
                                        var caff = poi.affectationAuto.listeAutresCaffs[0];
                                        caff.listePoiSimulation = new Array(poi);
                                        //console.log("POI " + poi.ft_numero_oeie + " : " + caff.name_related);
                                        listeCaffsSimulation.push(caff);
                                        if(!clientDejaDansTab)
                                        {
                                            listeClientsAvecListePoiLiee.forEach(function(client){
                                                client.listePoi.forEach(function(poiClient){
                                                    if(poiClient == poi.id)
                                                    {
                                                        client.caffAffecte = caff.id;
                                                    }
                                                });
                                            });
                                        }
                                    }
                                },
                                async:true
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
<<<<<<< HEAD

          $("#btnValiderModalSimulationAvance").click(function(){
            $.post("API/addListePoiSimuDomaines.php", {nbDissi: $("#dissiSimu").val(), nbClient: $("#clientSimu").val(), nbImmo: $("#immoSimu").val(), nbFocu: $("#focuSimu").val(), nbCoordi: $("#coordiSimu").val(), ui: $("#selectUiSimuAvance").val()}, function(){
                document.location.reload();
            });
        });
=======
          $("#advancedConfigUI").click(function(e){
              e.stopPropagation();
              console.log("ok2");
          })
          $("#advancedConfigAddUI").click(function(){
            var advancedSelectedUI = $('#advancedConfigUI').find(":selected").attr('value');
            if(advancedSelectedUI != "null"){
                console.log(advancedSelectedUI)
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
       
          })
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
>>>>>>> 54550070954ab096924747aa7a01f59ce05e8260
    }
    