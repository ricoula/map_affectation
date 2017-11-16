<?php
  include("API/fonctions.php");
  switch($_GET["liaison_name"])
  {
    case 'titulaire': $listePoi = json_decode(getListePoiByCaffByTitulaire($_GET["caff_id"], $_GET["titulaire"]));
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
?>
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
</div>