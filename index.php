<?php include("header.php")?>
<!DOCTYPE html>
<html>
  <head>
    <title></title>
    <link rel="stylesheet" type="text/css" href="CSS/style.css">


  </head>
  <body>
  <div id="side_bar"></div>
    <div id="map_content">
      <div id="glyph">
        <div id="slide-home" class="glyph_div glyph_div_border"><span  class="glyphicon glyphicon-home font-glyph" aria-hidden="true"></span></div>
        <div id="slide-filter" class="glyph_div glyph_div_border"><span class="glyphicon glyphicon-filter font-glyph" aria-hidden="true"></span></div>
        <div id="slide-users" class="glyph_div glyph_div_border"><span class="glyphicon glyphicon-user font-glyph" aria-hidden="true"></span></div>
        <div id="slide-box" class="glyph_div"><span class="glyphicon glyphicon-inbox font-glyph" aria-hidden="true"></span></div>
     </div>
         <div id="map"></div>
    </div>

    <div class="modal fade" id="modaleListeCaffsLienPoi">
        <div class="modal-dialog modal-lg"> 
          <div class="modal-content" id="divListeCaffsLienPoi"></div>  
        </div> 
    </div>
    <div class="modal fade" id="modaleListePoiLienByCaff">
        <div class="modal-dialog modal-lg"> 
          <div class="modal-content" id="divListePoiLienByCaff"></div>  
        </div> 
    </div>
    <div class="modal fade" id="modaleAffecterA">
        <div class="modal-dialog modal-lg"> 
          <div class="modal-content" id="divAffecterA"></div>  
        </div> 
    </div>
    
  <?php include("footer.php") ?>
  </body>
</html>
