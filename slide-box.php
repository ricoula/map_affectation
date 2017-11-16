<html>
    <body>
    <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span></br>        
    <h1 id="home-poi" class="well">Liste des POI</h1>
    <div class="card well">
    <div class="card-block">
        <span class="label label-danger">Pas de référence GPS</span><span class="label label-warning pull-right">Non affectée</span>
      <h4 class="card-title">BOU704656<span class="label label-primary pull-right">Client</span></h4>
      <h6 class="card-subtitle mb-2 text-muted">DRE: <span>18/12/2017</span></h6>
      <h6 class="card-subtitle mb-2 text-muted">Commune: <span>Grenoble</span></h6>
    </div>
  </div>
    </body>
</html>
<script>   
        $("#slide-close").click(function(){
        $("#side_bar").animate({left:'-500px'},500);
        $("#glyph").animate({left:'0px'},500);
        $(".glyph_div").removeClass("active");
        $("#side_bar").html("");
    });
</script>