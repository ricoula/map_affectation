<?php
    include("kpi/API/fonctions.php");
    if(!isset($_SESSION["user_id"]))
    {
        $_SESSION["user_id"] = 1;
    }
    $idUser = $_SESSION["user_id"];
    $gridster = null;
    $gridster = json_decode(getGridsterByUserId($idUser));
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- Bootstrap Core CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="plugins/gridster/gridster.css" />
    <link rel="stylesheet" href="kpi/css/kpi.css" />
  </head>
  <body oncontextmenu="return false;" >
    <input type="hidden" name="user_id" id="user_id" value="<?php echo $idUser ?>" />

    <div>
        <button id="ajouterCase" class="btn btn-default">Ajouter</button>
        <button id="sauvegarderEmplacement" class="btn btn-success">Sauvegarder</button>
        <button id="annulerModif" class="btn btn-danger" >Annuler</button>
        <button id="modifierEmplacement" class="btn btn-primary">Modifier</button>
    </div>

    <div class="gridster">
        <ul>
            <?php
            if($gridster == null)
            {
                ?>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="2" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="3" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>

                <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="2" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="2" data-col="2" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>

                <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="4" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="2" data-col="4" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="3" data-col="4" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>

                <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="5" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="3" data-col="5" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>

                <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="6" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="2" data-col="6" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                <?php
            }
            else{
                foreach($gridster as $wdg)
                {
                    ?>
                    <li class="widgetPasModif" lien="<?php echo $wdg->lien ?>" taille="<?php echo $wdg->taille ?>" data-row="<?php echo $wdg->row ?>" data-col="<?php echo $wdg->col ?>" data-sizex="<?php echo $wdg->size_x ?>" data-sizey="<?php echo $wdg->size_y ?>"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>
                    <?php
                }
            }
            ?>
            
        </ul>
    </div>

    <div class="modal fade" id="modaleWidgetFullScreen">
        <div class="modal-dialog">  
          <div class="modal-content" id="contenuModaleWidgetFullScreen">
          </div>  
        </div> 
      </div>
    
    <footer>
        <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="plugins/gridster/gridster.js"></script>
        <script src="kpi/js/kpi.js"></script>
    </footer>
  </body>
</html>
