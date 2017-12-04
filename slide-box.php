<html>
    <body>
    <?php
    include("API/fonctions.php");
    $poiNa = json_decode(getPoiNA());
    $listeUi = json_decode(getUi());
    ?>
    <span class="glyphicon glyphicon-remove pull-right slide-close"></span></br>        
    <h1 id="home-poi" class="well">Liste des POI</h1>
    <div class="btn-group" role="group" aria-label="Basic example" id="users-group">
        <?php
        foreach($listeUi as $ui)
        {
            ?>
            <button type="button" class="btn btn-default btn-primary btnUIBox" id="box-<?php echo $ui->ft_zone ?>" ><?php echo $ui->diminutif ?></button>
            <?php
        }
        ?>
        <button type="button" class="btn btn-default btn-success btnUIAll" id="allUiBox" >All</button>
    </div><br/><br/>
    <?php
        foreach($poiNa as $poi)
        {
            ?>
            <div class="card-box well cardBox-<?php echo $poi->atr_ui ?>">
                <input type="hidden" class="longitude" value="<?php echo $poi->ft_longitude ?>" />
                <input type="hidden" class="latitude" value="<?php echo $poi->ft_latitude ?>" />
                <div class="card-block">
                    <span class="label label-danger"><?php if($poi->ft_latitude != null && $poi->ft_latitude != '' && $poi->ft_longitude != null && $poi->ft_longitude != ''){ echo "lat: ".$poi->ft_latitude." | lon: ".$poi->ft_longitude; }else{ echo "Pas de référence GPS"; } ?></span><span class="label label-warning pull-right">Non affectée</span>
                    <h4 class="card-title"><?php echo $poi->ft_numero_oeie ?><span class="label label-primary pull-right"><?php echo $poi->domaine ?></span></h4>
                    <h6 class="card-subtitle mb-2 text-muted">DRE: <span><?php echo $poi->ft_oeie_dre ?></span></h6>
                    <h6 class="card-subtitle mb-2 text-muted">Commune: <span><?php echo $poi->ft_libelle_commune ?></span></h6>
                </div>
            </div>
            <?php
        }
    ?>
    </body>
</html>
<script>
    $("#allUiBox").click(function(){
        if($(this).hasClass("btn-success"))
        {
            $(this).removeClass("btn-success");
            $(".btnUIBox").removeClass("btn-primary");
            $(".card-box").hide();
        }
        else{
            $(this).addClass("btn-success");
            $(".btnUIBox").addClass("btn-primary");
            $(".card-box").show();
        }
    });
    $(".btnUIBox").click(function(){
        $(this).toggleClass("btn-primary");
        var idBtn = $(this).attr("id").split("-")[1];
        if($(this).hasClass("btn-primary"))
        {
            $(".cardBox-" +idBtn).show();
        }
        else{
            $(".cardBox-" +idBtn).hide();
        }
    });

    var marker = new google.maps.Marker({ map: map, optimized: false,
      zIndex:99999999, icon: {
                    path: google.maps.SymbolPath.BACKWARD_CLOSED_ARROW,
                    fillColor: "green",
                    fillOpacity: 0.8,
                    strokeColor: "black",
                    strokeWeight: 2,
                    scale: 7,
                  }
         });
    $(".card-box").click(function(){
        $(".card-box").css({"backgroundColor" : "#f5f5f5"});
        $(this).css({"backgroundColor" : "#d5d5d5"});
        var longitude = $(this).children(".longitude").first().val();
        var latitude = $(this).children(".latitude").first().val();
        myLatlng = new google.maps.LatLng(Number(longitude),Number(latitude));
        marker.setMap(map);
        marker.setPosition(myLatlng);
        marker.setZIndex(1000);
        marker.setAnimation(google.maps.Animation.BOUNCE);
       
        google.maps.event.addListener(marker, 'click', function(event) {
    this.setMap(null);
    $(".card-box").css({"backgroundColor" : "#f5f5f5"});
  });
    });

    $(".slide-close").click(function(){
        $("#side_bar").animate({left:'-500px'},500);
        $("#glyph").animate({left:'0px'},500);
        $(".glyph_div").removeClass("active");
    });
</script>