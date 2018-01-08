<html>
<?php
    include("API/fonctions.php");
    $listeUi = json_decode(getUi());
    $config = json_decode(getConfigById($_GET["utilisateur_id"]));
    $config_default = json_decode($config);
?>
    <body>
    <span class="glyphicon glyphicon-remove pull-right slide-close"></span></br>
        <h1 class="well">Paramètres </h1><button class="btn btn-primary pull-right btn-xs" data-toggle="modal" href="#advancedsettings">Paramètres avancés</button>
        <h4>Urgence</h4>
        <div class="form-group">
            <div class="list-group">
                
                <div class="list-group-item"><label>Date de retour étude inférieur à J+</label><input type="number" class="form-control" id="filter-dre" value="<?php echo $config_default->filterdre ?>"></input><div class="pull-right"><input type='text' class="color-picker" id="filter-color-urgent" value="<?php echo $config_default->filtercolorurgent ?>"></div></div>
                <div class="list-group-item"><div class="panel panel-default">
      <div class="panel-heading"> 
            <h3 class="panel-title">
              <a href="#item1" data-toggle="collapse"> Sous-justification liste </a> 
            </h3>
      </div>
      <div id="item1" class="panel-collapse collapse">
      <!-- start panel -->
      <?php foreach($listeUi as $ui){
       ?>
         <div class="panel-body">
             <div class="panel-heading"> 
                <h3 class="panel-title">
                    <a href="#item-<?php echo $ui->ft_zone; ?>" data-toggle="collapse"><?php echo $ui->libelle; ?></a>  <span class="pull-right"><input type="text" class="filter-add-sj" maxlength="2" id="filter-add-sj-txt-<?php echo $ui->ft_zone;?>" ui="<?php echo $ui->ft_zone; ?>"> <button class="btn btn-success btn-xs filter-add-sj-btn" ui="<?php echo $ui->ft_zone; ?>"><span class="glyphicon glyphicon-plus-sign"></span> Ajouter</button></span>
                </h3>
             </div>
              <div id="item-<?php echo $ui->ft_zone; ?>" class="panel-collapse collapse">
                <div class="panel-body" id="filter-sj-list-<?php echo $ui->ft_zone; ?>">
                 </div>
             </div>
         </div><hr/>
      <?php } ?>
    <!-- end panel -->
         
      </div>
      
    </div></div>
    
  
            </div>
            
        </div>
            
     </div>
     <h4>POI</h4>
        <div class="form-group">
            <div class="list-group">
                <div class="list-group-item"><label>Couleur POI Client</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-client" value="<?php echo $config_default->filtercolorclient ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Immobilier</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-immo" value="<?php echo $config_default->filtercolorimmo ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Dissimulation</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-dissi" value="<?php echo $config_default->filtercolordissi ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI FO & CU</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-focu" value="<?php echo $config_default->filtercolorfocu ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Coordination</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-coord" value="<?php echo $config_default->filtercolorcoord ?>"></div></div>
            </div>
        </div>
        <button class="btn btn-primary pull-right" id="filter-save">Enregister</button>
     </div>

    </body>
</html>
<script src="plugins/colorpicker/color.js"></script>
<script>   
        $(".slide-close").click(function(){
        $("#side_bar").animate({left:'-500px'},500);
        $("#glyph").animate({left:'0px'},500);
        $(".glyph_div").removeClass("active");
    });
</script>

<script>
    $("#filter-save").click(function(){
        var list_sj = [];
        $(".filter-sj-badge").each(function(sj){
            list_sj.push($(this).attr("id"))
        })
        var param = {
         filterdre : $("#filter-dre").val(),
         filtercolorurgent : $("#filter-color-urgent").val(),
         filtercolorclient : $("#filter-color-client").val(),
         filtercolorimmo : $("#filter-color-immo").val(),
         filtercolordissi : $("#filter-color-dissi").val(),
         filtercolorfocu : $("#filter-color-focu").val(),
         filtercolorcoord : $("#filter-color-coord").val(),
         filtersj : list_sj
        };

        var json_param = JSON.stringify(param);
        $.post("API/addConfigById.php",{id: 1, config: json_param}, function(){
            location.reload();
        });
    });
</script>
<script>
$('.filter-add-sj').on('input',function(e){
   $(this).val($(this).val().toUpperCase());
});
</script>
<script>
 $(".filter-add-sj-btn").click(function(){
    var ui = $(this).attr("ui");
    var sj = $("#filter-add-sj-txt-"+ui).val();
    if(sj.length != 2){
        var $el = $("#filter-add-sj-txt-"+ui),
                x = 2000,
                originalColor = $el.css("background");

            $el.css("background", "pink");
            setTimeout(function(){
            $el.css("background", originalColor);
            }, x);
    }else
    {
        
        if (($("#label-"+sj+"-"+ui).length == 0)){
            var $el = $("#filter-add-sj-txt-"+ui),
                x = 500,
                originalColor = $el.css("background");

            $el.css("background", "lightgreen");
            setTimeout(function(){
            $el.css("background", originalColor);
            }, x);
            $("#filter-sj-list-"+ui).append('<label class="filter-sj-badge" id="label-'+sj+'-'+ui+'">'+sj+' <span class="glyphicon glyphicon-cog gly-spin" ui="'+ui+'" sj="'+sj+'"></span></label>');
}
else
{
    var $el = $("#filter-add-sj-txt-"+ui),
                x = 2000,
                originalColor = $el.css("background");

            $el.css("background", "pink");
            setTimeout(function(){
            $el.css("background", originalColor);
            }, x);
}
    }
 });
</script>
<script>
//console.log("test")
    $.post("API/getConfigById.php",{utilisateur_id : $("#user_id").val()},function(data){
        data = JSON.parse(data);
        var json_code = data;
    var obj = $.parseJSON(json_code);
    var sj_list = obj.filtersj;
    sj_list.forEach(function(sj){
        ui = sj.split("-")[2];
        sj = sj.split("-")[1];
        //console.log(ui + " " + sj)
        $("#filter-sj-list-"+ui).append('<label class="filter-sj-badge" id="label-'+sj+'-'+ui+'">'+sj+' <span class="glyphicon glyphicon-remove filter-remove-sj" ui="'+ui+'" sj="'+sj+'"></span></label>');
    })
    $(".filter-remove-sj").click(function(){
     var labeltoremove = "label-"+$(this).attr('sj')+"-"+$(this).attr('ui');
     $("#"+labeltoremove).remove();
})
    })
   
</script>
<script>
$(".color-picker").spectrum({
    preferredFormat: "hex",
    change: function(color) {
        var filter_id = $(this).attr('id');
        $("#"+filter_id).val(color)
    }
});
</script>
<script>
    var coef_non_react = $("#coef_non_react").text();
              var coef_react = $("#coef_react").text();
              var rayon_km = $("#rayon_km").text();
              var coef_rayon = $("#coef_rayon").text();
              var max_rayon = $("#max_rayon").text();
              var max_day = $("#max_day").text();
              var max_week = $("#max_week").text();
              var jours_avant_conges = $("#max_avant_conges").text();
              var jours_conges = $("#max_conges").text();

    $(".config_modify").click(function(){
     var ui = $(this).attr('ui');
     console.log(ui);
               coef_non_react = $("#coef_non_react_"+ui).text();
               coef_react = $("#coef_react_"+ui).text();
               rayon_km = $("#rayon_km_"+ui).text();
               coef_rayon = $("#coef_rayon_"+ui).text();
               max_rayon = $("#max_rayon_"+ui).text();
               max_day = $("#max_day_"+ui).text();
               max_week = $("#max_week_"+ui).text();
               jours_avant_conges = $("#max_avant_conges_"+ui).text();
               jours_conges = $("#max_conges_"+ui).text();
              $(".coef_non_react_"+ui).html("<input class='input_modif coef_non_react_change_"+ui+"' type='number' value='"+ coef_non_react +"'>");
              $("#rayon_km_"+ui).html("<input type='number' class='input_modif' value='"+ rayon_km +"'>");
              $(".coef_react_"+ui).html("<input type='number' class='input_modif coef_react_change_"+ui+"' value='"+ coef_react +"'>");
              $("#coef_rayon_"+ui).html("<input type='number' class='input_modif' value='"+ coef_rayon +"'>");
              $("#max_rayon_"+ui).html("<input type='number' class='input_modif' value='"+ max_rayon +"'>");
              $("#max_day_"+ui).html("<input type='number' class='input_modif' value='"+ max_day +"'>");
              $("#max_week_"+ui).html("<input type='number' class='input_modif' value='"+ max_week +"'>");
              $("#max_avant_conges_"+ui).html("<input type='number' class='input_modif' value='"+ jours_avant_conges +"'>");
              $("#max_conges_"+ui).html("<input type='number' class='input_modif' value='"+ jours_conges +"'>");
              $(".config_modify[ui='"+ui+"']").addClass("hide");
              $(".config_valid[ui='"+ui+"']").removeClass("hide");
              $(".config_cancel[ui='"+ui+"']").removeClass("hide");
                $(".coef_non_react_change_"+ui).on('change paste keyup',function(){
                $(".coef_non_react_change_"+ui).val($(this).val())
                });
                $(".coef_react_change_"+ui).on('change paste keyup',function(){
                $(".coef_react_change_"+ui).val($(this).val())
                });

            });

            $(".config_valid").click(function(){
                var ui = $(this).attr('ui');
     console.log(ui);
               var coef_non_react_new =  $("#coef_non_react_"+ui).children().val();
               var coef_react_new =  $("#coef_react_"+ui).children().val();
               var rayon_km_new = $("#rayon_km_"+ui).children().val();
               var coef_rayon_new = $("#coef_rayon_"+ui).children().val();
               var max_rayon_new = $("#max_rayon_"+ui).children().val();
               var max_day_new = $("#max_day_"+ui).children().val();
               var max_week_new = $("#max_week_"+ui).children().val();
               var jours_avant_conges_new = $("#max_avant_conges_"+ui).children().val();
               var jours_conges_new = $("#max_conges_"+ui).children().val();
                $(".coef_non_react_"+ui).html(coef_non_react_new);
              $("#rayon_km_"+ui).html(rayon_km_new);
              $(".coef_react_"+ui).html(coef_react_new);
              $("#coef_rayon_"+ui).html(coef_rayon_new);
              $("#max_rayon_"+ui).html(max_rayon_new);
              $("#max_week_"+ui).html(max_week_new);
              $("#max_day_"+ui).html(max_day_new);
              $("#max_avant_conges_"+ui).html(jours_avant_conges_new);
              $("#max_conges_"+ui).html(jours_conges_new);
              $(".config_modify[ui='"+ui+"']").removeClass("hide");
              $(".config_valid[ui='"+ui+"']").addClass("hide");
              $(".config_cancel[ui='"+ui+"']").addClass("hide");
              var modif_json = {
                  coef_non_react: coef_non_react_new,
                  coef_react: coef_react_new,
                  rayon_km_new: rayon_km_new,
                  coef_rayon_new: coef_rayon_new,
                  max_rayon_new: max_rayon_new,
                  max_day: max_day_new,
                  max_week: max_week_new,
                  jours_avant_conges: jours_avant_conges_new,
                  jours_conges: jours_conges_new
              };
              var modif_json_string = JSON.stringify(modif_json);
              console.log(modif_json_string);
              $.post("API/changeAdvancedConfig.php",{config:modif_json_string, ui:ui});
              

            });

            $(".config_cancel").click(function(){
                var ui = $(this).attr('ui');
     console.log(ui);
            $(".coef_non_react_"+ui).html(coef_non_react);
            $(".coef_react_"+ui).html(coef_react);
              $("#rayon_km_"+ui).html(rayon_km);
              $("#coef_rayon_"+ui).html(coef_rayon);
              $("#max_rayon_"+ui).html(max_rayon);
              $("#max_day_"+ui).html(max_day);
              $("#max_week_"+ui).html(max_week);
              $("#max_avant_conges_"+ui).html(jours_avant_conges);
              $("#max_conges_"+ui).html(jours_conges);
              $(".config_modify[ui='"+ui+"']").removeClass("hide");
              $(".config_valid[ui='"+ui+"']").addClass("hide");
              $(".config_cancel[ui='"+ui+"']").addClass("hide");
            });
</script>

