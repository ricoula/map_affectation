<?php
  include("API/fonctions.php");
  switch($_GET["liaison_name"])
  {
    case 'titulaire': $listePoi = json_decode(getListePoiByCaffByTitulaire($_GET["caff_id"], $_GET["titulaire"], $_GET["ui"]));
    $nameLiaison = $_GET["titulaire"];
    break;
    case 'voie' : $listePoi = json_decode(getListePoiByCaffByVoie($_GET["caff_id"], $_GET["voie"], $_GET["commune"]));
    $nameLiaison = $_GET["voie"];
    break;
    case 'commune' : $listePoi = json_decode(getListePoiByCaffByCommune($_GET["caff_id"], $_GET["commune"]));
    $nameLiaison = $_GET["commune"];
    break;
    default : $listePoi = array();
  }
  $caff = json_decode(getCaffById($_GET["caff_id"]));
  $poi = json_decode(getPoiById($_GET["poi_id"]));
?>

<input type="hidden" name="idCaffModaleListePoiCaff" id="idCaffModaleListePoiCaff" value="<?php echo $caff->id ?>" />
<input type="hidden" name="nameCaffModaleListePoiCaff" id="nameCaffModaleListePoiCaff" value="<?php echo $caff->name_related ?>" />
<input type="hidden" name="idPoiModaleListePoiCaff" id="idPoiModaleListePoiCaff" value="<?php echo $poi->id ?>" />
<input type="hidden" name="domainePoiModaleListePoiCaff" id="domainePoiModaleListePoiCaff" value="<?php echo $poi->domaine ?>" />
<input type="hidden" name="numPoiModaleListePoiCaff" id="numPoiModaleListePoiCaff" value="<?php echo $poi->ft_numero_oeie ?>" />

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h1>Liste des Poi de <?php echo $_GET["caff_name"] ?> liées à <?php echo $nameLiaison ?></h1>
</div>
<div class="modal-body">
  <?php
  if(sizeof($listePoi) > 0)
  {
    ?>
    <table class="table table-triped table-hover table-condensed table-striped">
      <tr>
        <th>Numéro Poi</th>
        <th>DRE</th>
        <th>PG</th>
        <th>SJ</th>
        <th>Commentaire</th>
        <th>Etat</th>
      </tr>
      <?php
      foreach($listePoi as $poi)
      {
        ?>
        <tr>
          <td><?php echo $poi->ft_numero_oeie ?></td>
          <td><?php echo $poi->ft_oeie_dre ?></td>
          <td><?php echo $poi->ft_pg ?></td>
          <td><?php echo $poi->ft_sous_justification_oeie ?></td>
          <td><?php echo $poi->ft_commentaire_creation_oeie ?></td>
          <td><?php echo $poi->ft_etat ?></td>
        </tr>
        <?php
      }
      ?>
    </table>
    <?php
  }
  else{
    ?>
    <label class="label-info">Aucune POI</label>
    <?php
  }
  ?>
  <div class='affect_btn'>
    <button id="btnAffectationCaffPoiLien" class='affectationListePoiNA'><span>Affecter </span></button>
  </div>
</div>

<script>
  $(function(){
    $("#btnAffectationCaffPoiLien").click(function(){
      $.post("API/addPoiAffect.php", {poi_id: $("#idPoiModaleListePoiCaff").val(), poi_num: $("#numPoiModaleListePoiCaff").val(), poi_domaine: $("#domainePoiModaleListePoiCaff").val(), caff_id: $("#idCaffModaleListePoiCaff").val(), caff_name: $("#idCaffModaleListePoiCaff").val()}, function(data2){
          window.location.reload();
      });
    });
  });
</script>