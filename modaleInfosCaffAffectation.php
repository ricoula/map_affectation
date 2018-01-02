<?php
    $caff = json_decode(urldecode($_GET["caff"]));
    $poi = json_decode(urldecode($_GET["poi"]));
?>
<input type="hidden" name="idCaffModaleInfosCaffAffectation" id="idCaffModaleInfosCaffAffectation" value="<?php echo $caff->id ?>" />
<input type="hidden" name="nameCaffModaleInfosCaffAffectation" id="nameCaffModaleInfosCaffAffectation" value="<?php echo $caff->name_related ?>" />
<input type="hidden" name="idPoiModaleInfosCaffAffectation" id="idPoiModaleInfosCaffAffectation" value="<?php echo $poi->id ?>" />
<input type="hidden" name="domainePoiModaleInfosCaffAffectation" id="domainePoiModaleInfosCaffAffectation" value="<?php echo $poi->domaine ?>" />
<input type="hidden" name="numPoiModaleInfosCaffAffectation" id="numPoiModaleInfosCaffAffectation" value="<?php echo $poi->ft_numero_oeie ?>" />

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal">&times;</button>
  <h1><?php echo $caff->name_related ?></h1>
</div>
<div class="modal-body">
  <table class="table">
    <tr>
        <th>Nom: </th>
        <td><?php echo $caff->name_related ?></td>
    </tr>
    <tr>
        <th>Téléphone: </th>
        <td><?php echo $caff->mobile_phone ?></td>
    </tr>
    <tr>
        <th>Mail: </th>
        <td><?php echo $caff->work_email ?></td>
    </tr>
    <tr>
        <th>Site: </th>
        <td><?php echo $caff->site ?></td>
    </tr>
    <tr>
        <th>Agence: </th>
        <td><?php echo $caff->agence ?></td>
    </tr>
    <tr>
        <th>Reactives: </th>
        <td><?php echo $caff->reactive ?></td>
    </tr>
    <tr>
        <th>Non Réactives: </th>
        <td><?php echo $caff->non_reactive ?></td>
    </tr>
  </table>
  <div class='affect_btn'>
      <button id="btnAffectationCaff" class='affectationListePoiNA'><span>Affecter </span></button>
    </div>
</div>

<script>
    $(function(){
        $("#btnAffectationCaff").click(function(){
            $.post("API/addPoiAffect.php", {poi_id: $("#idPoiModaleInfosCaffAffectation").val(), poi_num: $("#numPoiModaleInfosCaffAffectation").val(), poi_domaine: $("#domainePoiModaleInfosCaffAffectation").val(), caff_id: $("#idCaffModaleInfosCaffAffectation").val(), caff_name: $("#idCaffModaleInfosCaffAffectation").val()}, function(data2){
                window.location.reload();
            });
        });
    });
</script>