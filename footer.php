<!DOCTYPE html>
<html>
  <head>
    <title></title>
  </head>
  <body>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


    <script async defer src="https://maps.google.com/maps/api/js?v=3&callback=mapLibReadyHandler&key=AIzaSyAYvU3R9-LA0yn0B9UWY256WE0iTwWLBCw"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OverlappingMarkerSpiderfier/1.0.3/oms.min.js"></script>
    </script>
    <script>

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
                               '<a href="#" class="list-group-item" id="win_info_liens">Calculer les liens <span class="glyphicon glyphicon-refresh pull-right"></span></a>' +
                               '<a href="#" class="list-group-item" id="win_info_affecter_auto">Affecter auto. <span class="label label-info pull-right">RICOU Damien</span></a>' +
                           '</div>' +
                        '</div>');
      iw.open(map, marker);
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
      </script>
    <script src="JS/header.js"></script>
    <script src="JS/index.js"></script>

  
  </body>
</html>