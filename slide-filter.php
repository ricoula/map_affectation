<html>
<?php
    include("API/fonctions.php");
    $listeUi = json_decode(getUi());
    $config = json_decode(getConfigById(1));
    $config_default = json_decode($config);
?>
    <body>
    <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span></br>
        <h1 class="well">Filtre</h1>
        <h4>Urgence</h4>
        <div class="form-group">
            <div class="list-group">
                
                <div class="list-group-item"><label>Date de retour étude inférieur à J+</label><input type="number" class="form-control" id="filter-dre" value="<?php echo $config_default->{"filterdre"}; ?>"></input></div>
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
    
    <div class="list-group-item"><label>Couleur des urgences</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-urgent" value="<?php echo $config_default->{"filtercolorurgent"}; ?>"></div></div>
            </div>
            
        </div>
            
     </div>
     <h4>POI</h4>
        <div class="form-group">
            <div class="list-group">
                <div class="list-group-item"><label>Couleur POI Client</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-client" value="<?php echo $config_default->{"filtercolorclient"}; ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Immobilier</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-immo" value="<?php echo $config_default->{"filtercolorimmo"}; ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Dissimulation</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-dissi" value="<?php echo $config_default->{"filtercolordissi"}; ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI FO & CU</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-focu" value="<?php echo $config_default->{"filtercolorfocu"}; ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Coordination</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-coord" value="<?php echo $config_default->{"filtercolorcoord"}; ?>"></div></div>
            </div>
        </div>
        <button class="btn btn-primary pull-right" id="filter-save">Enregister</button>
     </div>
        
    </body>
</html>
<script src="plugins/colorpicker/color.js"></script>
<script>   
        $("#slide-close").click(function(){
        $("#side_bar").animate({left:'-500px'},500);
        $("#glyph").animate({left:'0px'},500);
        $(".glyph_div").removeClass("active");
        $("#side_bar").html("");
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
        $.post("API/addConfigById.php",{id : 1, config: json_param});
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
            $("#filter-sj-list-"+ui).append('<label class="filter-sj-badge" id="label-'+sj+'-'+ui+'">'+sj+' <span class="glyphicon glyphicon-remove filter-remove-sj" ui="'+ui+'" sj="'+sj+'"></span></label>');
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
    $.getJSON("API/getConfigById.php",function(data){
        var json_code = data;
    var obj = $.parseJSON(json_code);
    var sj_list = obj.filtersj;
    sj_list.forEach(function(sj){
        ui = sj.split("-")[2]
        sj = sj.split("-")[1]
        $("#filter-sj-list-"+ui).append('<label class="filter-sj-badge" id="label-'+sj+'-'+ui+'">'+sj+' <span class="glyphicon glyphicon-remove filter-remove-sj" ui="'+ui+'" sj="'+sj+'"></span></label>');
    })

    })
   
</script>
<script>
$(".color-picker").spectrum({
    change: function(color) {
        var filter_id = $(this).attr('id');
        $("#"+filter_id).val(color)
    }
});
</script>
