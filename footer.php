<!DOCTYPE html>
<html>
  <head>
    <title></title>
  </head>
  <body>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAYvU3R9-LA0yn0B9UWY256WE0iTwWLBCw&callback=initMap">
    </script>
    <script>
        function initMap() {
          var infoWindow = new google.maps.InfoWindow({
                content: '<div class="container info_poi_modal" >'+
                            '<h2 id="win_info_numero_oeie">BOU705566</h2>' +
                            '<div class="list-group">' +
                              '<a href="#" class="list-group-item" id="win_info_affecter_a">Affecter à</a>' +
                              '<a href="#" class="list-group-item" id="win_info_liens">Calculer les liens <span class="glyphicon glyphicon-refresh pull-right"></span></a>' +
                              '<a href="#" class="list-group-item" id="win_info_affecter_auto">Affecter auto. <span class="label label-info pull-right">RICOU Damien</span></a>' +
                            '</div>' +
                          '</div>'
              
            });
        var uluru = {lat:45.6930369, lng: 4.9989082};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 10,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
        marker.addListener('rightclick',function(){
          infoWindow.open(map, marker);
          $("#win_info_liens").click(function(){

        $("#win_info_liens").children().addClass("gly-spin")
  
    });
        });
      }
      </script>
    <script src="JS/header.js"></script>
    <script src="JS/index.js"></script>

  
  </body>
</html>