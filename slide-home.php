<html>
    <body>

    <?php
    if(isset($_GET["poi_id"]) && $_GET["poi_id"] != null)
    {
    include("API/fonctions.php");
    $poi = json_decode(getPoiById($_GET["poi_id"]));
    $closestSite = json_decode(getClosestSite($poi->id));
    ?>

    <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span></br> 
    <h1 id="home-poi" class="well"><?php echo $poi->ft_numero_oeie ?><span class="badge badge-default pull-right" id="home-domaine"><?php echo $poi->domaine ?></span></h1>
    <h4>Information POI</h4>
    <form>
    <div class="form-group">
        <div class="list-group">
            <div class="list-group-item"><label>AS</label><label class="pull-right home-result " id="home-as"><?php echo $poi->ft_numero_as ?></label></div>
           <div class="list-group-item"><label>Date de création</label><label class="pull-right home-result " id="home-creation"><?php echo $poi->ft_date_creation_oeie ?></label></div>
           <div class="list-group-item"><label>Date de retour d'étude</label><label class="pull-right home-result label label-danger" id="home-dre"><?php echo $poi->ft_oeie_dre ?></label></div>
           <div class="list-group-item"><label>Date limite de réalisation</label><label class="pull-right home-result " id="home-dlr"><?php echo $poi->ft_date_limite_realisation ?></label></div>
           <div class="list-group-item"><label>Projet générique</label><label class="pull-right home-result label label-warning" id="home-pg"><?php echo $poi->ft_pg ?></label></div>
           <div class="list-group-item"><label>Sous justification</label><label class="pull-right home-result label label-warning" id="home-sj"><?php echo $poi->ft_sous_justification_oeie ?></label></div>
           <div class="list-group-item"><label>Commentaire</label><p class="list-group-item" id="home-commentaire"><?php echo $poi->ft_commentaire_creation_oeie ?></p></div>    
        </div>
        <h4>Information Client</h4>
        <div class="list-group">
            <div class="list-group-item"><label>Titulaire</label><label class="pull-right home-result" id="home-titulaire"><?php echo $poi->ft_titulaire_client ?></label></div>
            <div class="list-group-item"><label>Commune</label><label class="pull-right home-result" id="home-commune"><?php echo $poi->ft_libelle_commune ?></label></div>
            <div class="list-group-item"><label>Voie</label><label class="pull-right home-result" id="home-voie"><?php echo $poi->ft_libelle_de_voie ?></label></div>
        </div>
        <h4>Information d'affectation</h4>
        <div class="list-group">
            <div class="list-group-item"><label>Site le plus proche</label><label class="pull-right home-result" id="home-closest"><?php echo $closestSite->libelle ?></label></div>
            <div class="list-group-item"><label>Distance</label><label class="pull-right home-result" id="home-distance"><?php echo $closestSite->distance ?></label></div>
            <div class="list-group-item"><label>Temps</label><label class="pull-right home-result" id="home-temps"><?php echo $closestSite->duree ?></label></div>
            <div class="list-group-item"><label>Caff conseillé</label><label class="pull-right home-result label label-success" id="home-caff">DELABAERE Simon</label></div>
        </div>
        
      <!-- <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
      <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
    </div>
    <div class="form-group">
      <label for="exampleInputPassword1">Password</label>
      <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
    </div>
    <div class="form-check">
      <label class="form-check-label">
        <input type="checkbox" class="form-check-input">
        Check me out
      </label>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button> -->
  </form>

    </div>
    <?php
    }else{
      ?>
      <label class="label label-primary" >Aucune POI sélectionnée</label>
      <?php
    }
    ?>
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