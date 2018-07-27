<html>
    <head>
        <style>
            .material-switch > input[type="checkbox"] {
                display: none;   
            }

            .material-switch > label {
                cursor: pointer;
                height: 0px;
                position: relative; 
                width: 40px;  
            }

            .material-switch > label::before {
                background: rgb(0, 0, 0);
                box-shadow: inset 0px 0px 10px rgba(0, 0, 0, 0.5);
                border-radius: 8px;
                content: '';
                height: 16px;
                margin-top: -8px;
                position:absolute;
                opacity: 0.3;
                transition: all 0.4s ease-in-out;
                width: 40px;
            }
            .material-switch > label::after {
                background: rgb(255, 255, 255);
                border-radius: 16px;
                box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
                content: '';
                height: 24px;
                left: -4px;
                margin-top: -8px;
                position: absolute;
                top: -4px;
                transition: all 0.3s ease-in-out;
                width: 24px;
            }
            .material-switch > input[type="checkbox"]:checked + label::before {
                background: inherit;
                opacity: 0.5;
            }
            .material-switch > input[type="checkbox"]:checked + label::after {
                background: inherit;
                left: 20px;
            }
        </style>
    </head>
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
            <div id="cardBox-<?php echo $poi->id ?>" class="card-box well cardBox-<?php echo $poi->atr_ui ?>">
                <input type="hidden" class="longitude" value="<?php echo $poi->ft_longitude ?>" />
                <input type="hidden" class="latitude" value="<?php echo $poi->ft_latitude ?>" />
                <div class="card-block">
                    <span class="label label-danger"><?php if($poi->ft_latitude != null && $poi->ft_latitude != '' && $poi->ft_longitude != null && $poi->ft_longitude != ''){ echo "lat: ".$poi->ft_latitude." | lon: ".$poi->ft_longitude; }else{ echo "Pas de référence GPS"; } ?></span><span class="label label-warning pull-right">Non affectée</span>
                    <h4 class="card-title"><?php echo $poi->ft_numero_oeie ?><span class="label label-primary pull-right"><?php echo $poi->domaine ?></span></h4>
                    <h6 class="card-subtitle mb-2 text-muted">DRE: <span><?php echo $poi->ft_oeie_dre ?></span></h6>
                    <h6 class="card-subtitle mb-2 text-muted">Commune: <span><?php echo $poi->ft_libelle_commune ?></span></h6>
                    <hr/>
                    <div>
                        Valid. Affect.
                        <div class="material-switch pull-right">
                            <input id="validAffect-<?php echo $poi->id ?>" name="validAffect-<?php echo $poi->id ?>" type="checkbox"/>
                            <label for="validAffect-<?php echo $poi->id ?>" class="label-success"></label>
                        </div>
                    </div>
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