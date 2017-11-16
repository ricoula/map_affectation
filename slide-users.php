<html>
    <body>
        <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span><br/>
        <h1 id="home-poi" class="well">Information Caff</h1>
        <div class="btn-group" role="group" aria-label="Basic example" id="users-group">
            <button type="button" class="btn btn-default">BFC</button>
            <button type="button" class="btn btn-default">ALP</button>
            <button type="button" class="btn btn-default">RD</button>
            <button type="button" class="btn btn-default">ALS</button>
            <button type="button" class="btn btn-default">PCA</button>
            <button type="button" class="btn btn-default">MPY</button>
            <button type="button" class="btn btn-default">LYO</button>
            <button type="button" class="btn btn-default">AUV</button>
        </div>
        <div class="input-group users-card-caff">
        <label class="input-group-addon" id="basic-addon1"></label>
        <div class="card-block users-card-info" id="">
            <h4 class="users-name">PERRIN GREGORY<span class="label label-warning pull-right users-state">Congé</span></h4>
            <h6 class="users-site">Nice</h6>
            <h6 class="users-charge">Charge: <span class="label label-danger users-charge-count">123</span><button class="btn btn-info btn-xs pull-right">Afficher POI</button></h6>
        </div>
      </div>
      <div class="input-group users-card-caff">
      <label class="input-group-addon" id="basic-addon2"></label>
      <div class="card-block users-card-info" id="">
          <h4 class="users-name">HUBERT PHILIPPE<span class="label label-success pull-right users-state">Actif</span></h4>
          <h6 class="users-site">Ambérieu</h6>
          <h6 class="users-charge">Charge: <span class="label label-default users-charge-count">60</span><button class="btn btn-info btn-xs pull-right">Afficher POI</button></h6>
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