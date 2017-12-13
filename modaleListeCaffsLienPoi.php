<?php
    include("API/fonctions.php");
    $poi = json_decode(getPoiById($_GET["poi"]));
    $liensClient = json_decode(getCaffsEnLienAvecPoiByTitulaire($poi->ft_titulaire_client, $poi->atr_ui));
    $liensVoie = json_decode(getCaffsEnLienAvecPoiByVoie($poi->ft_libelle_de_voie, $poi->ft_libelle_commune, $poi->atr_ui));
    $liensCommune = json_decode(getCaffsEnLienAvecPoiByCommune($poi->ft_libelle_commune, $poi->atr_ui));
?>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h1>Caffs en lien avec la POI: <?php echo $poi->ft_numero_oeie ?></h1>
</div>
<div class="modal-body">
    <div class="form-group">
    <?php
    if($poi->ft_titulaire_client != null && $poi->ft_titulaire_client != '')
    {
        ?>
        <div class="list-group">
            <h3>Liens avec <?php echo $poi->ft_titulaire_client ?></h3>
            <?php
            if(sizeof($liensClient) > 0)
            {
                foreach($liensClient as $caff)
                {
                    ?>
                    <a href="#" data-target="#modaleListePoiLienByCaff" class="list-group-item lienTitulaire idcaff_<?php echo $caff->id ?> namecaff_<?php echo $caff->name ?>">
                        <?php echo $caff->name ?> <?php if($caff->entraide){ ?><label class="label label-primary">Entraide</label><?php } ?> <span class="badge"><?php echo $caff->nb_poi ?></span>
                    </a>
                    <?php
                }
            }
            else{
                ?>
                <label class="label label-info">Aucun caff en lien avec ce client</label>
                <?php
            }
            ?>
        </div>
        <?php
    }
    else{
        ?>
        <label class="label label-info">Client non renseigné</label>
        <?php
    }
    ?>
    </div>
    <div class="form-group">
    <?php
    if($poi->ft_libelle_de_voie != null && $poi->ft_libelle_de_voie != '')
    {
        ?>
        <div class="list-group">
            <h3>Liens avec <?php echo $poi->ft_libelle_de_voie ?></h3>
            <?php
            if(sizeof($liensVoie) > 0)
            {
                foreach($liensVoie as $caff)
                {
                    ?>
                    <a href="#" class="list-group-item lienVoie idcaff_<?php echo $caff->id ?> namecaff_<?php echo $caff->name ?>">
                        <?php echo $caff->name ?> <?php if($caff->entraide){ ?><label class="label label-primary">Entraide</label><?php } ?> <span class="badge"><?php echo $caff->nb_poi ?></span>
                    </a>
                    <?php
                }
            }
            else{
                ?>
                <label class="label label-info">Aucun caff en lien avec cette voie</label>
                <?php
            }
            ?>
        </div>
        <?php
    }
    else{
        ?>
        <label class="label label-info">Voie non renseignée</label>
        <?php
    }
    ?>
    </div>
    <div class="form-group">
    <?php
    if($poi->ft_libelle_commune != null && $poi->ft_libelle_commune != '')
    {
        ?>
        <div class="list-group">
            <h3>Liens avec <?php echo $poi->ft_libelle_commune ?></h3>
            <?php
            if(sizeof($liensCommune) > 0)
            {
                foreach($liensCommune as $caff)
                {
                    ?>
                    <a href="#" class="list-group-item lienCommune  idcaff_<?php echo $caff->id ?> namecaff_<?php echo $caff->name ?>">
                        <?php echo $caff->name ?> <?php if($caff->entraide){ ?><label class="label label-primary">Entraide</label><?php } ?> <span class="badge"><?php echo $caff->nb_poi ?></span>
                    </a>
                    <?php
                }
            }
            else{
                ?>
                <label class="label label-info">Aucun caff en lien avec cette commune</label>
                <?php
            }
            ?>
        </div>
        <?php
    }
    else{
        ?>
        <label class="label label-info">Commune non renseignée</label>
        <?php
    }
    ?>
    </div>
</div>

<input type="hidden" name="titulaire" id="titulaire" value="<?php echo urlencode($poi->ft_titulaire_client) ?>" />
<input type="hidden" name="voie" id="voie" value="<?php echo urlencode($poi->ft_libelle_de_voie) ?>" />
<input type="hidden" name="commune" id="commune" value="<?php echo urlencode($poi->ft_libelle_commune) ?>" />
<input type="hidden" name="ui" id="ui" value="<?php echo urlencode($poi->atr_ui) ?>" />

<script>
    var titulaire = $("#titulaire").val();
    var voie = $("#voie").val();
    var commune = $("#commune").val();
    var ui = $("#ui").val();

    $(".lienTitulaire").click(function(){
        var liaisonName = "titulaire";
        
        var listeClasse = this.classList;
        var idCaff = 0;
        var nameCaff = "";
        listeClasse.forEach(function(classe){
            if(classe.split("_").length > 1)
            {
                if(classe.split("_")[0] == "idcaff")
                {
                    idCaff = classe.split("_")[1];
                }
                if(classe.split("_")[0] == "namecaff")
                {
                    nameCaff = classe.split("_")[1];
                }
            }
        });
        var url = "modaleListePoiCaff.php?liaison_name=" + liaisonName + "&caff_name=" + nameCaff + "&caff_id=" + idCaff + "&titulaire=" + titulaire + "&voie=" + voie + "&commune=" + commune + "&ui=" + ui;
        $("#divListePoiLienByCaff").load(url, function(){
            $('#modaleListePoiLienByCaff').modal('show');
        });
    });

    $(".lienVoie").click(function(){
        var liaisonName = "voie";
        var listeClasse = this.classList;
        var idCaff = 0;
        var nameCaff = "";
        listeClasse.forEach(function(classe){
            if(classe.split("_").length > 1)
            {
                if(classe.split("_")[0] == "idcaff")
                {
                    idCaff = classe.split("_")[1];
                }
                if(classe.split("_")[0] == "namecaff")
                {
                    nameCaff = classe.split("_")[1];
                }
            }
        });
        var url = "modaleListePoiCaff.php?liaison_name=" + liaisonName + "&caff_name=" + nameCaff + "&caff_id=" + idCaff + "&titulaire=" + titulaire + "&voie=" + voie + "&commune=" + commune + "&ui=" + ui;
        $("#divListePoiLienByCaff").load(url, function(){
            $('#modaleListePoiLienByCaff').modal('show');
        });
    });

    $(".lienCommune").click(function(){
        var liaisonName = "commune";
        var listeClasse = this.classList;
        var idCaff = 0;
        var nameCaff = "";
        listeClasse.forEach(function(classe){
            if(classe.split("_").length > 1)
            {
                if(classe.split("_")[0] == "idcaff")
                {
                    idCaff = classe.split("_")[1];
                }
                if(classe.split("_")[0] == "namecaff")
                {
                    nameCaff = classe.split("_")[1];
                }
            }
        });
        var url = "modaleListePoiCaff.php?liaison_name=" + liaisonName + "&caff_name=" + nameCaff + "&caff_id=" + idCaff + "&titulaire=" + titulaire + "&voie=" + voie + "&commune=" + commune + "&ui=" + ui;
        $("#divListePoiLienByCaff").load(url, function(){
            $('#modaleListePoiLienByCaff').modal('show');
        });
    });
</script>

