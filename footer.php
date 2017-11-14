<!DOCTYPE html>
<html>
  <head>
    <title></title>
  </head>
  <body>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAYvU3R9-LA0yn0B9UWY256WE0iTwWLBCw&callback=initMap">
    </script>
    <script>
        function initMap() {
        var uluru = {lat: -25.363, lng: 131.044};
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 4,
          center: uluru
        });
        var marker = new google.maps.Marker({
          position: uluru,
          map: map
        });
      }
      </script>
    <script src="JS/header.js"></script>
    <script src="JS/index.js"></script>

  
  </body>
</html>