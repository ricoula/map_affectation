
 window.onload = function() {

  var mapElement = document.getElementById('map');
  map = new google.maps.Map(mapElement, { center: new google.maps.LatLng(45.6930369, 4.9989082), zoom: 8 });
  //document.getElementById("mapJson").value = JSON.stringify(map);
  //console.log(document.getElementById("mapJson").value);
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
              var date_now = Number(d.getFullYear() +''+ (d.getMonth() + 1) +''+ d.getDate());
              var date_dre = Number(poi.ft_oeie_dre.split('-')[0] + poi.ft_oeie_dre.split('-')[1] + poi.ft_oeie_dre.split('-')[2]);
              if(date_dre <= (date_now + Number(config.filterdre))){
                strokecolorpoi = config.filtercolorurgent;
                strokeweightpoi = 3;
                scalepoi = 7;
              }
              config.filtersj.forEach(function(sj){
               
                var sj_oeie = sj.split("-")[1];
                var ui_oeie = sj.split("-")[2];
                

                if((poi.ft_sous_justification_oeie == sj_oeie && poi.atr_ui == ui_oeie)){
                  // strokecolorpoi = config.filtercolorurgent;
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
            title: poi.ft_numero_oeie
          });
          google.maps.event.addListener(map, 'click', function(e){
            iw.close();
          });
          google.maps.event.addListener(marker, 'rightclick', function(e) {  // 'spider_rightclick', not plain 'click'

          iw.setContent('<div class="container info_poi_modal" >'+
                '<h2 id="win_info_numero_oeie">'+marker.title+'</h2>' +
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
                    $("#divAffecterA").load("modaleAffecterA.php?poi_id=" + poi.id, function(){
                        $('#modaleAffecterA').modal('show');
                    });
                });

          $.post("API/getAffectationAuto.php", {poi_id: marker.poi_id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_poi_client: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val()}, function(data){
            //console.log(data);
            var caff = JSON.parse(data);
            $("#rightClickPoi_" + marker.poi_id).removeClass("glyphicon glyphicon-refresh gly-spin").addClass("label label-info").text(caff.name_related);
          });
  
          });
          google.maps.event.addListener(marker, 'spider_click', function(e) {  // 'spider_rightclick', not plain 'click'
          var myUrl = window.location.href;
          myUrl = myUrl.split("?")[0];
          var newUrl = myUrl + "?poi=" + marker.poi_id;
          console.log(newUrl);
          history.pushState(null, null, newUrl);
          
          
          console.log(marker.poi_id);
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
                      $("#percent").fadeIn();
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
                        listePoi.forEach(function(poi){
                              y++;
                              
                       //     console.log(progress);
                       
                            //  progress = (i / listePoi.length) * 100;
                            // console.log(progress);
                            // $("#progress_bar_affect").css({"width":""+progress+"%"});
                            // $("#progress_bar_affect").html({progress+"%"});
                            //console.log(listeCaffsSimulation);
                            //listeCaffsSimulation = JSON.stringify(listeCaffsSimulation);
                            /*var seen = [];
                            
                            listeCaffsSimulation = JSON.stringify(listeCaffsSimulation, function(key, val) {
                               if (val != null && typeof val == "object") {
                                    if (seen.indexOf(val) >= 0) {
                                        return;
                                    }
                                    seen.push(val);
                                }
                                return val;
                            });
                            console.log(listeCaffsSimulation);
                            console.log("ENCODE");*/
        
        
                          //  console.log(y + "=" + poi.ft_numero_oeie);
        
                            /*$(document).ajaxStart(function() {
                                console.log('Méthode ajaxStart exécutée<br>');
                              });
                              $(document).ajaxSend(function(ev, req, options){
                                console.log('Méthode ajaxSend exécutée, ');
                                console.log('nom du fichier : ' + options.url + '<br>');
                              });
                              $(document).ajaxStop(function(){
                                console.log('Méthode ajaxStop exécutée<br>');
                              });
                              $(document).ajaxSuccess(function(ev, req, options){
                                console.log('Méthode ajaxSuccess exécutée<br>');
                              });
                              $(document).ajaxComplete(function(ev, req, options){
                                console.log('Méthode ajaxComplete exécutée<br>');
                              });
                              $(document).ajaxError(function(ev, req, options, erreur){
                                console.log('Méthode ajaxError exécutée, ');
                                console.log('erreur : ' + erreur + '<br>');
                              });*/
                            
                            //  console.log("Objet:");
                             // console.log({poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_poi_client: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val()});
                            
        
                            $.ajax({
                                type: 'POST',
                                url: "API/getAffectationAuto.php",
                                data: {poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_poi_client: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val()},// liste_caffs_simulation: listeCaffsSimulation},
                                success: function(data2){
                                    i++;
                                   // console.log(i + "=" + poi.ft_numero_oeie);
                                 
                                    progress = Math.round(((i) / listePoi.length) * 100);  
                                    $("#progress_bar_affect").attr("aria-valuenow", progress)
                                    // $("#progress_bar_affect").css({"width":""+progress+"%"});
                                    $("#progress_bar_affect").css("width", progress+"%");
                                     $("#percent").html(progress+"%");
                                     console.log(progress);
                                  //  console.log("Data: " + data2);
                                    poi.affectationAuto = JSON.parse(data2);
                                    
                                    //listeCaffsSimulation = JSON.parse(listeCaffsSimulation);
                                    // console.log("DECODE" + i);
            
                                    var optionElt = "";
                                    

                                    listeCaffsSimulation.forEach(function(caffSimulation){
                                        if(caffSimulation.id == poi.affectationAuto.id)
                                        {
                                            poi.affectationAuto.listeAutresCaffs.forEach(function(caffSimu){
                                                if(caffSimu.id == caffSimulation.id)
                                                {
                                                 //   console.log("SECONDAIRE");
                                                    caffSimulation.listePoiSimulation.forEach(function(poiSimulation){
                                                    if(poiSimulation.reactive)
                                                    {
                                                        caffSimu.chargeGlobale =  caffSimu.charge_totale;
                                                        caffSimu.charge_totale += 1;
                                                        if(caffSimu.charge_simu == null)
                                                        {
                                                            caffSimu.charge_simu = 1;
                                                        }
                                                        else{
                                                            caffSimu.charge_simu += 1;
                                                        }
                                                    }
                                                    else{
                                                        caffSimu.charge_totale += parseFloat($("#coefCharge").val());
                                                        if(caffSimu.charge_simu == null)
                                                        {
                                                            caffSimu.charge_simu = 0.5;
                                                        }
                                                        else{
                                                            caffSimu.charge_simu += 0.5;
                                                        }
                                                    }
                                                });
                                                }
                                            });
                                        }
                                    });
        
                                    var compare = function(a, b){
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
                                    };
        
                                    poi.affectationAuto.listeAutresCaffs.sort(compare);
        
                                    poi.affectationAuto.listeAutresCaffs.forEach(function(ceCaff){
                                        
                                       // if(ceCaff.id == poi.affectationAuto.id)
                                        //{
                                            //optionElt += "<option id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info' selected>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + ceCaff.charge_totale + ")" + "</option>";
                                        //}
                                        //else{
                                            if(ceCaff.chargeGlobale == null)
                                            {
                                                ceCaff.chargeGlobale = ceCaff.charge_totale;
                                            }
                                            if(ceCaff.charge_simu == null)
                                            {
                                                ceCaff.charge_simu = 0;
                                            }
                                            optionElt += "<option id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + ceCaff.charge_totale + ") (init:" + ceCaff.charge_initiale + ")(global:" + ceCaff.chargeGlobale + ")(simu:" + ceCaff.charge_simu + ")</option>";
                                        //}
                                    });
                                    html += "<tr><td>" + poi.ft_numero_oeie + "</td><td>" + poi.domaine + "</td><td>" + poi.ft_oeie_dre + "</td><td>" + poi.ft_sous_justification_oeie + "</td><td><select>" + optionElt + "</select></td></tr>";
                                    $("#btnCaffAffectAuto-" + poi.id).click()
                                    if(i == listePoi.length)
                                    {
                                        html += "</tbody></table>";
                                     
                                        document.getElementById("resultatsListePoiNA").innerHTML = html;
                                        $("#resultatsListePoiNA").show();
                                        $("#percent").fadeOut();
                                    }
                                    var caffSimulation = poi.affectationAuto;
                                    var trouve = false;
                                    if(listeCaffsSimulation.length > 0)
                                    {
                                        listeCaffsSimulation.forEach(function(caff){
                                            if(caff.id == caffSimulation.id)
                                            {
                                                trouve = true;
                                                caff.listePoiSimulation.push(poi);
                                            }
                                        });
                                    }
                                    if(!trouve)
                                    {
                                        var caff = poi.affectationAuto;
                                        caff.listePoiSimulation = new Array(poi);+
                                        listeCaffsSimulation.push(caff);
                                    }
                                },
                                async:true
                              });
                              
                            /*$.post("API/getAffectationAuto.php", {poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_poi_client: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val(), liste_caffs_simulation: listeCaffsSimulation}, function(data2){
                                i++;
                                //console.log(data2);
                                poi.affectationAuto = JSON.parse(data2);
                                listeCaffsSimulation = JSON.parse(listeCaffsSimulation);
                                var caffSimulation = poi.affectationAuto;
                                var trouve = false;
                                if(listeCaffsSimulation.length > 0)
                                {
                                    listeCaffsSimulation.forEach(function(caff){
                                        if(caff.id == caffSimulation.id)
                                        {
                                            caff.listePoiSimulation.push(poi);
                                        }
                                    });
                                }
                                if(!trouve)
                                {
                                    var caff = poi.affectationAuto;
                                    caff.listePoiSimulation = new Array(poi);
                                    listeCaffsSimulation.push(caff);
                                }
        
                                var optionElt = "";
                                poi.affectationAuto.listeAutresCaffs.forEach(function(ceCaff){
                                    
                                    if(ceCaff.id == poi.affectationAuto.id)
                                    {
                                        optionElt += "<option id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info' selected>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + ceCaff.charge_totale + ")" + "</option>";
                                    }
                                    else{
                                        optionElt += "<option id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + ceCaff.charge_totale + ")" + "</option>";
                                    }
                                });
                                html += "<tr><td>" + poi.ft_numero_oeie + "</td><td>" + poi.domaine + "</td><td>" + poi.ft_oeie_dre + "</td><td>" + poi.ft_sous_justification_oeie + "</td><td><select>" + optionElt + "</select></td></tr>";
                                $("#btnCaffAffectAuto-" + poi.id).click()
                                if(i == listePoi.length)
                                {
                                    html += "</tbody></table>";
                                    $("#divLoadingPoiNA").hide();
                                    document.getElementById("resultatsListePoiNA").innerHTML = html;
                                    $("#resultatsListePoiNA").show();
                                }
                            });*/
                        });
                        
                    });
                })
            });
          });

    }
    