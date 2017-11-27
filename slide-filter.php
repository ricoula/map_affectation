<html>
    <body>
    <?php
    require_once("API/fonctions.php");
    $filtresUser = json_decode(getFiltresByUtilisateurId($_GET["user_id"]));
    if($filtresUser != null)
    {
        $filtresUser = json_decode($filtresUser);
        var_dump($filtresUser);
    }
    ?>
    <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span></br>
        <h1 class="well">Filtre</h1>
        <h4>Urgence</h4>
        <div class="form-group">
            <div class="list-group">
                
                <div class="list-group-item"><label>Date de retour étude inférieur à J+</label><input type="number" class="form-control" id="filter-dre" value="<?php echo $filtresUser->filterdre ?>"></input></div>
                <div class="list-group-item"><div class="panel panel-default">
      <div class="panel-heading"> 
            <h3 class="panel-title">
              <a href="#item1" data-toggle="collapse"> Sous-justification liste </a> 
            </h3>
      </div>
      <div id="item1" class="panel-collapse collapse">
         <div class="panel-body">
             <div class="panel-heading"> 
                <h3 class="panel-title">
                    <a href="#item2" data-toggle="collapse">LYO</a> 
                </h3>
             </div>
              <div id="item2" class="panel-collapse collapse">
                <div class="panel-body">
                    <label for="cb"><input type="checkbox" name="cb" id="cb"> QP</label>
                    <label for="cb"><input type="checkbox" name="cb" id="cb"> QP</label>
                 </div>
             </div>
         </div>
      </div>
      
    </div></div>
    
    <div class="list-group-item"><label>Couleur des urgences</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-urgent" value="<?php echo $filtresUser->filtercolorurgent ?>"></div></div>
            </div>
            
        </div>
            
     </div>
     <h4>POI</h4>
        <div class="form-group">
            <div class="list-group">
                <div class="list-group-item"><label>Couleur POI Client</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-client" value="<?php echo $filtresUser->filtercolorclient ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Immobilier</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-immo" value="<?php echo $filtresUser->filtercolorimmo ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Dissimulation</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-dissi" value="<?php echo $filtresUser->filtercolordissi ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI FO & CU</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-focu" value="<?php echo $filtresUser->filtercolorfocu ?>"></div></div>
                <div class="list-group-item"><label>Couleur POI Coordination</label><div class="pull-right"><input type='text' class="color-picker" id="filter-color-coord" value="<?php echo $filtresUser->filtercolorcoord ?>"></div></div>
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
$(".color-picker").spectrum({
    change: function(color) {
        var filter_id = $(this).attr('id');
        $("#"+filter_id).val(color.toHexString())
        console.log(filter_id);
        console.log(color.toHexString());
    }
});
</script>
<script>
    $("#filter-save").click(function(){
        var param = {
         filterdre : $("#filter-dre").val(),
         filtercolorurgent : $("#filter-color-urgent").val(),
         filtercolorclient : $("#filter-color-client").val(),
         filtercolorimmo : $("#filter-color-immo").val(),
         filtercolordissi : $("#filter-color-dissi").val(),
         filtercolorfocu : $("#filter-color-focu").val(),
         filtercolorcoord : $("#filter-color-coord").val()
        };

        var json_param = JSON.stringify(param);
        console.log(param);
        
        $.post("API/updateFiltresUtilisateur.php", {utilisateur_id: $("#user_id").val(), filtres_json: json_param}, function(){

        });
    });
</script>