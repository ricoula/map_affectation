window.onload = function() {
    var mapElement = document.getElementById('map');
    var map = new google.maps.Map(mapElement, { center: new google.maps.LatLng(50, 0), zoom: 6 });
    var iw = new google.maps.InfoWindow();
    var oms = new OverlappingMarkerSpiderfier(map, {
      markersWontMove: true,
      markersWontHide: true,
      basicFormatEvents: true
    });
            
    $.getJSON('API/getPoiNA.php',function(data){
              data.forEach(function(poi){
                var marker = new google.maps.Marker({
              position: {lat: Number(poi.ft_longitude), lng: Number(poi.ft_latitude)},
              map: map,
              poi_id: poi.id,
              title: poi.ft_numero_oeie
            });
            google.maps.event.addListener(marker, 'rightclick', function(e) {  // 'spider_rightclick', not plain 'click'
          iw.setContent('<div class="container info_poi_modal" >'+
                                 '<h2 id="win_info_numero_oeie">'+marker.title+'</h2>' +
                                '<div class="list-group">' +
                                   '<a href="#" class="list-group-item" id="win_info_affecter_a">Affecter à</a>' +
                                   '<a data-target="#infos" class="list-group-item testClass" id="win_info_liens">POI en lien <span class="badge" id="badgeNbPoi"></span></a>' +
                                   '<a href="#" class="list-group-item" id="win_info_affecter_auto">Affecter auto. <span class="label label-info pull-right">RICOU Damien</span></a>' +
                               '</div>' +
                            '</div>');
          iw.open(map, marker);


            //FONCTIONS
            $.post("API/getNbPoiEnLien.php", {commune: poi.ft_libelle_commune, voie: poi.ft_libelle_de_voie, titulaire: poi.ft_titulaire_client}, function(data){
                var nbPoi = JSON.parse(data);
                $("#badgeNbPoi").text(nbPoi);
            });
            $("#win_info_liens").click(function(){
                $("#divListeCaffsLienPoi").load("modaleListeCaffsLienPoi.php?poi=" + poi.id, function(){
                    $('.modal').modal('show');
                });
            });


        });
            oms.addMarker(marker);
              });
              


              
            });

     // make a closure over the marker and marker data
        // var markerData = {lat: 50.123, lng: 0.123};  
        // // e.g. { lat: 50.123, lng: 0.123, text: 'XYZ' }
        // var marker = new google.maps.Marker({ position: markerData });  // markerData works here as a LatLngLiteral
    
        // oms.addMarker(marker);  // adds the marker to the spiderfier _and_ the map
        // var markerData = {lat: 50.123, lng: 0.103};  
        // // e.g. { lat: 50.123, lng: 0.123, text: 'XYZ' }
        // var marker = new google.maps.Marker({ position: markerData });  // markerData works here as a LatLngLiteral
    
        // oms.addMarker(marker);
    
        //     function initMap() {
        //       var infoWindow = new google.maps.InfoWindow({
        //             content: '<div class="container info_poi_modal" >'+
        //                         '<h2 id="win_info_numero_oeie">BOU705566</h2>' +
        //                         '<div class="list-group">' +
        //                           '<a href="#" class="list-group-item" id="win_info_affecter_a">Affecter à</a>' +
        //                           '<a href="#" class="list-group-item" id="win_info_liens">Calculer les liens <span class="glyphicon glyphicon-refresh pull-right"></span></a>' +
        //                           '<a href="#" class="list-group-item" id="win_info_affecter_auto">Affecter auto. <span class="label label-info pull-right">RICOU Damien</span></a>' +
        //                         '</div>' +
        //                       '</div>'
                  
        //         });
          
        //     var lyon = {lat:45.6930369, lng: 4.9989082};
            
        //     var map = new google.maps.Map(document.getElementById('map'), {
        //       zoom: 8,
        //       center: lyon
        //     });
    
            
            
        //   }
      }