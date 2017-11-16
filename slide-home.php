<html>
    <body>
    <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span></br> 
    <h1 id="home-poi" class="well">BOU400000<span class="badge badge-default pull-right" id="home-domaine">Client</span></h1>
    <h4>Information POI</h4>
    <form>
    <div class="form-group">
        <div class="list-group">
            <div class="list-group-item"><label>AS</label><label class="pull-right home-result " id="home-as">12561235</label></div>
           <div class="list-group-item"><label>Date de création</label><label class="pull-right home-result " id="home-creation">30/05/2016</label></div>
           <div class="list-group-item"><label>Date de retour d'étude</label><label class="pull-right home-result label label-danger" id="home-dre">18/11/2017</label></div>
           <div class="list-group-item"><label>Date limite de réalisation</label><label class="pull-right home-result " id="home-dlr">10/01/2018</label></div>
           <div class="list-group-item"><label>Projet générique</label><label class="pull-right home-result label label-warning" id="home-pg">67</label></div>
           <div class="list-group-item"><label>Sous justification</label><label class="pull-right home-result label label-warning" id="home-sj">F6</label></div>
           <div class="list-group-item"><label>Commentaire</label><p class="list-group-item" id="home-commentaire">	20/05/2011 - Etude en ligne, ref.  25912Q - Dérivation d'amorce, transfert de paires, mutations, renvoie de paires et construction de ligne. - La Conduite d'Activités négocie le rendez-vous.</p></div>    
        </div>
        <h4>Information Client</h4>
        <div class="list-group">
            <div class="list-group-item"><label>Titulaire</label><label class="pull-right home-result" id="home-titulaire">SFR WHOLESALE</label></div>
            <div class="list-group-item"><label>Commune</label><label class="pull-right home-result" id="home-commune">Grenoble</label></div>
            <div class="list-group-item"><label>Voie</label><label class="pull-right home-result" id="home-voie">Rue de la république</label></div>
        </div>
        <h4>Information d'affectation</h4>
        <div class="list-group">
            <div class="list-group-item"><label>Site le plus proche</label><label class="pull-right home-result" id="home-closest">Vigny</label></div>
            <div class="list-group-item"><label>Distance</label><label class="pull-right home-result" id="home-distance">12.7 km</label></div>
            <div class="list-group-item"><label>Temps</label><label class="pull-right home-result" id="home-temps">17min</label></div>
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