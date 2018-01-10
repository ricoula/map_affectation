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
  <body>
    <div>
        <button id="ajouterCase" class="btn btn-default">Ajouter</button>
        <button id="sauvegarderEmplacement" class="btn btn-success">Sauvegarder</button>
        <button id="modifierEmplacement" class="btn btn-primary">Modifier</button>
    </div>

    <div class="gridster" oncontextmenu="return false;">
        <ul>
            <li data-row="1" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
            <li data-row="2" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
            <li data-row="3" data-col="1" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>

            <li data-row="1" data-col="2" data-sizex="2" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
            <li data-row="2" data-col="2" data-sizex="2" data-sizey="2"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>

            <li data-row="1" data-col="4" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
            <li data-row="2" data-col="4" data-sizex="2" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
            <li data-row="3" data-col="4" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>

            <li data-row="1" data-col="5" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
            <li data-row="3" data-col="5" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>

            <li data-row="1" data-col="6" data-sizex="1" data-sizey="1"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
            <li data-row="2" data-col="6" data-sizex="1" data-sizey="2"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>
        </ul>
    </div>


    <div class="modal" id="modaleAjoutCase">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">x</button>
            <h4 class="modal-title">Nouvelle case</h4>
        </div>
        <div class="modal-body">
            
        </div>
        <div class="modal-footer">
            <button class="btn btn-info" data-dismiss="modal">Fermer</button>
        </div>
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
