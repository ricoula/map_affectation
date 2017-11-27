<html>
    <body>
    <?php
    include("API/fonctions.php");
    $caffs = json_decode(getInfosCaff());
    $listeUi = json_decode(getUi());
    $listeCaffsConges = json_decode(getListIdEmployesConges());
    ?>
        <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span><br/>
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
                $caff->ui = json_decode(getUiBySite($caff->site));
                ?>
                <div class="input-group users-card-caff card-<?php echo $caff->ui->ft_zone ?>" id="<?php echo urlencode(json_encode($caff)) ?>" >
                    <label class="input-group-addon imageCaff" style='background-image: url("img/inconnu.jpg"); background-size: 100px 100px; width:100px; height:100px;' id="imageCaff-<?php echo $caff->id ?>" ></label>
                    <div class="card-block users-card-info" id="">
                        <h4 class="users-name"><?php echo $caff->name_related ?><?php if(in_array($caff->id, $listeCaffsConges)){ ?><span class="label label-warning pull-right users-state">Congé</span><?php }else{ ?><span class="label label-success pull-right users-state">Actif</span><?php } ?></h4>
                        <h6 class="users-site"><?php echo $caff->site ?></h6>
                        <h6 class="users-charge">Charge: <span class="label label-danger users-charge-count">123</span><button class="btn btn-info btn-xs pull-right users-button-poi" caff_id="<?php echo $caff->id ?>">Afficher POI</button></h6>
                        <!--<h6 class="users-charge">Charge: <span class="label label-danger users-charge-count">123</span><button id="btnAfficherPoiCaff-<?php /*echo urlencode($caff->name_related)*/ ?>" class="btn btn-info btn-xs pull-right btnAfficherPoiCaff">Afficher POI</button></h6>-->
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
        $("#slide-close").click(function(){
        $("#side_bar").animate({left:'-500px'},500);
        $("#glyph").animate({left:'0px'},500);
        $(".glyph_div").removeClass("active");
        $("#side_bar").html("");
    });
    $(".imageCaff").each(function(){
        var elt = $(this);
        var idCaff = $(this).attr("id").split("-")[1];
        $.post("API/getImageByCaff.php", {caff_id: idCaff}, function(data){
            var image = JSON.parse(data);
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
    console.log(caff_id);
    $.getJSON("get")
});
</script>