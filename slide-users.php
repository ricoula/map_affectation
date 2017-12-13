<html>
    <head>
    </head>
    <body>
    <?php
    include("API/fonctions.php");
    $caffs = json_decode(getInfosCaff());
    $listeUi = json_decode(getUi());
    $listeCaffsConges = json_decode(getListIdEmployesConges());
    ?>
        <span class="glyphicon glyphicon-remove pull-right slide-close"></span><br/>
        <h1 id="home-poi" class="well">Information Caff</h1>
        <div class="btn-group" role="group" aria-label="Basic example" id="users-group">
            <?php
            foreach($listeUi as $ui)
            {
                ?>
                <button type="button" class="btn btn-default btn-primary btnUI" id="<?php echo $ui->ft_zone ?>" ><?php echo $ui->diminutif ?></button>
                <?php
            }
            ?>
            <button type="button" class="btn btn-default btn-success btnUIAll" id="allUi" >All</button>
        </div>
        <?php
        foreach($caffs as $caff)
        {
            if($caff->id != 326)
            {
                if($caff->ag_coeff_traitement != null)
                {
                    $caff->coefTraitement = $caff->ag_coeff_traitement;
                    $caff->chargeInit = round(getChargeCaff(json_encode($caff), $_GET["coefCharge"])*(1/$caff->ag_coeff_traitement), 1);
                    $caff->ag_coeff_traitement *= 100;
                    $caff->ag_coeff_traitement .= "%";
                }
                else{
                    $caff->coefTraitement = 1;
                    $caff->chargeInit = getChargeCaff(json_encode($caff), $_GET["coefCharge"]);
                    $caff->ag_coeff_traitement = "0%";
                }
                $formation = getFormationCaff($caff->id);
                $caff->ui = json_decode(getUiBySite($caff->site));
                ?>
                <div class="input-group users-card-caff card-<?php echo $caff->ui->ft_zone ?>" id="<?php echo urlencode(json_encode($caff)) ?>" >
                    <label class="input-group-addon imageCaff" style='background-image: url("img/inconnu.jpg"); background-size: 100px 100px; width:100px; height:100px;' id="imageCaff-<?php echo $caff->id ?>" ></label>
                    <div class="card-block users-card-info" id="">
                        <h4 class="users-name"><?php echo $caff->name_related ?></span><?php if(in_array($caff->id, $listeCaffsConges)){ ?><span class="label label-warning pull-right users-state">Congé</span><?php }else{ ?><span class="label label-success pull-right users-state">Actif</span><?php } ?></h4>
                        <h6 class="users-site"><?php echo $caff->site ?><span class="pull-right">Formation : <span class="label label-<?php if($formation == "OUI"){ echo "warning"; }else{ echo "default"; } ?> users-formation" caff_id ="<?php echo $caff->id ?>"><?php echo $formation; ?></span></span></h6>
                        <!--<h6 class="users-charge">Charge: <span class="label label-danger users-charge-count">123</span><button class="btn btn-info btn-xs pull-right users-button-poi" id="<?php /*echo $caff->id*/ ?>">Afficher POI</button></h6>-->
                        <h6 class="users-charge">Charge: <span data-toggle="tooltip" data-placement="top" title="(nb_poi_reactives + (nb_poi_non_reactives * coef_poi_non_reactives)) * (1 / coef_charge)
                        = (<?php echo $caff->reactive ?> + (<?php echo $caff->non_reactive ?> * <?php echo $_GET["coefCharge"] ?>)) * (1 / <?php echo $caff->coefTraitement ?>)" class="label label-danger users-charge-count"><?php echo $caff->chargeInit ?></span><span class="label label-info coef-charge" data-toggle="tooltip" data-placement="right" title="Coef. charge"><?php echo $caff->ag_coeff_traitement ?></span><button id="btnAfficherPoiCaff-<?php echo urlencode($caff->name_related) ?>" class="btn btn-info btn-xs pull-right btnAfficherPoiCaff">Afficher POI</button></h6>
                    </div>
                </div>
                <?php
            }
        }
        ?>
    </div>
    </body>
</html>
<script>
$('[data-toggle="tooltip"]').tooltip(); 
</script>
<script>
    $(".btnAfficherPoiCaff").click(function(){
        var caffName = $(this).attr("id").split("-")[1];
        $("#divlistePoiCaff").load("modaleAfficherAllPoiCaff.php?caff_name=" + caffName, function(){
            $("#listePoiCaff").modal("show");
        });
    });
    $("#allUi").click(function(){
        if($(this).hasClass("btn-success"))
        {
            $(this).removeClass("btn-success");
            $(".btnUI").removeClass("btn-primary");
            $(".users-card-caff").hide();
        }
        else{
            $(this).addClass("btn-success");
            $(".btnUI").addClass("btn-primary");
            $(".users-card-caff").show();
        }
    });
        $(".btnUI").click(function(){
            $(this).toggleClass("btn-primary");
            var idBtn = $(this).attr("id");
            if($(this).hasClass("btn-primary"))
            {
                $(".card-" +idBtn).show();
            }
            else{
                $(".card-" +idBtn).hide();
            }
        });
        $(".slide-close").click(function(){
        $("#side_bar").animate({left:'-500px'},500);
        $("#glyph").animate({left:'0px'},500);
        $(".glyph_div").removeClass("active");
    });
    $(".imageCaff").each(function(){
        var elt = $(this);
        var idCaff = $(this).attr("id").split("-")[1];
        $.post("API/getImageByCaff.php", {caff_id: idCaff}, function(data){
            var image = data;
            if(image != null && image != '')
            {
                elt.css("background-image", 'url("' + image + '")');
            }
        });
    });
</script>
<script>
$(".users-button-poi").click(function(){
    var caff_id = $(this).attr("caff_id");
    if($(this).hasClass("btn btn-info")){
        $(this).removeClass("btn btn-info");
        $(this).addClass("btn btn-default");
        $(this).text("Cacher POI");
        // var myLatLng = new google.maps.LatLng(28.617161,77.208111);
        // var marker = new google.maps.Marker({
        //   position:  myLatLng,
        //   map: map,
        //   title: 'Hello World!'
        // });
    }
    else
    {
        $(this).removeClass("btn btn-default");
        $(this).addClass("btn btn-info");
        $(this).text("Afficher POI");
    }
    //console.log(caff_id);
    $.getJSON("get")
});
</script>
<script>
$(".users-formation").click(function(){

    var caff_id = $(this).attr("caff_id");
    //console.log(caff_id)
    if($(this).hasClass("label label-default")){
    $(this).removeClass("label label-default");
        $(this).addClass("label label-warning");
        $(this).text("OUI");
        $.post("API/addRemoveFormationByCaffId.php", {caff_id: caff_id, state: "OUI"});
    }
        else
        {
            $(this).removeClass("label label-warning");
        $(this).addClass("label label-default");
        $(this).text("NON");
        $.post("API/addRemoveFormationByCaffId.php", {caff_id: caff_id, state:"NON"});
        }
})
</script>