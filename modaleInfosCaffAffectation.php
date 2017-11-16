<?php
    $caff = json_decode(urldecode($_GET["caff"]));
?>

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
</div>