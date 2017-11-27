
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
  $.getJSON('API/getConfigById',function(data){
    config = JSON.parse(data);
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
              
              if(poi.domaine == 'Client'){
                color = config.filtercolorclient
              }
              if(poi.domaine == 'Immo'){
                color = config.filtercolorimmo
              }
              if(poi.domaine == 'Dissi'){
                color = config.filtercolordissi
              }
              if(poi.domaine == 'FO & CU'){
                color = config.filtercolorfocu
              }
              if(poi.domaine == 'Coordi'){
                color = config.filtercolorcoord
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
          '<a href="#" class="list-group-item" id="win_info_affecter_auto">Affecter auto. <span class="label label-info pull-right">RICOU Damien</span></a>' +
      '</div>' +
   '</div>');
        iw.open(map, marker);
        $.post("API/getNbPoiEnLien.php", {commune: poi.ft_libelle_commune, voie: poi.ft_libelle_de_voie, titulaire: poi.ft_titulaire_client, ui: poi.atr_ui}, function(data){
                  var nbPoi = JSON.parse(data);
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
            
          });

    }
    