<?php
    include("kpi/API/fonctions.php");
    if(!isset($_SESSION["user_id"]))
    {
        $_SESSION["user_id"] = 1;
    }
    $idUser = $_SESSION["user_id"];
    $gridster = null;
    $gridster = json_decode(getGridsterByUserId($idUser));

    $listeWidgets = json_decode(getListeWidgetsByUserId($idUser));
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

    <div id="total" class="container-fluid">
        <div id="menuAjoutWidget" class="col-md-1">
            <h4>Ajouter KPI</h4>
            <div>
                <?php
                foreach($listeWidgets as $widget)
                {
                    ?>
                    <div>
                        <span class="<?php if($widget->disponible){ echo "widgetListe widgetDispo"; }else{ echo "widgetListe"; } ?>" lien="<?php echo $widget->lien ?>" ><?php echo $widget->libelle ?></span> <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" data-placement="bottom" title="<?php echo $widget->description ?>"></span>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>

        <!-- <div id="partieGridster" class="partieGridsterEnModif"> -->
        <div id="partieGridster" class="col-md-10" >
            <div>
                <!-- <button id="ajouterCase" class="btn btn-default">Ajouter</button> -->
                <button id="sauvegarderEmplacement" class="btn btn-success">Sauvegarder</button>
                <button id="annulerModif" class="btn btn-danger" >Annuler</button>
                <button id="modifierEmplacement" class="btn btn-primary">Modifier</button>
            </div>

            <div class="gridster">
                <ul>
                <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
                        <li class="widgetPasModif" lien="test" taille="sm" data-row="2" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>
                        <li class="widgetPasModif" lien="test" taille="sm" data-row="3" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>

                        <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="2" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>
                        <li class="widgetPasModif" lien="test" taille="sm" data-row="2" data-col="2" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>

                        <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="4" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>
                        <li class="widgetPasModif" lien="test" taille="sm" data-row="2" data-col="4" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>
                        <li class="widgetPasModif" lien="test" taille="sm" data-row="3" data-col="4" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>

                        <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="5" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>
                        <li class="widgetPasModif" lien="test" taille="sm" data-row="3" data-col="5" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>

                        <li class="widgetPasModif" lien="test" taille="sm" data-row="1" data-col="6" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>
                        <li class="widgetPasModif" lien="test" taille="sm" data-row="2" data-col="6" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modaleWidgetFullScreen">
        <div class="modal-dialog modal-lg">  
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
