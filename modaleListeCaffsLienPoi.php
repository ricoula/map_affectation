<?php
    include("API/fonctions.php");
    $poi = json_decode(getPoiById($_GET["poi"]));
    $liensClient = json_decode(getCaffsEnLienAvecPoiByTitulaire($poi->ft_titulaire_client, $poi->atr_ui));
    $liensVoie = json_decode(getCaffsEnLienAvecPoiByVoie($poi->ft_numero_de_voie, $poi->ft_libelle_commune, $poi->atr_ui));
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
                    <a href="#" class="list-group-item"><?php echo $caff->name ?> <span class="badge"><?php echo $caff->nb_poi ?></span></a>
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
    if($poi->ft_numero_de_voie != null && $poi->ft_numero_de_voie != '')
    {
        ?>
        <div class="list-group">
            <h3>Liens avec <?php echo $poi->ft_numero_de_voie ?></h3>
            <?php
            if(sizeof($liensVoie) > 0)
            {
                foreach($liensVoie as $caff)
                {
                    ?>
                    <a href="#" class="list-group-item"><?php echo $caff->name ?> <span class="badge"><?php echo $caff->nb_poi ?></span></a>
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
                    <a href="#" class="list-group-item"><?php echo $caff->name ?> <span class="badge"><?php echo $caff->nb_poi ?></span></a>
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