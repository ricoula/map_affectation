<?php 
$_POST['size'] = 'lg';
include("../API/fonctions.php");
include("../../API/fonctions.php");
$listaffect = json_decode(getNbAffectation());
$listeui = json_decode(getUi());

// echo sizeof($listaffect);
if(!isset($_POST['size']) || $_POST['size'] == "sm"){
?>
<style>
    #testtaille{
        width: 140px;
        height: 140px;
        background-color:white;
        
     
    }
    .top{
        padding:5px;
        text-align:center;
        font-weight:bold;
        background-color:#ff730054;
        font-size:11px;
        font-family:calibri;
    }
    #result{
        font-family:calibri;
        text-align:center;
        font-weight:bold;
        font-size:90px;
        height:94px;
        line-height:94px
    }
    
</style>
<input type="hidden" id="testx" value="1">
<input type="hidden" id="testy" value="1">
<div id="testtaille">
<div class="top"><span>Nombre POI affectée(s)</span></div>
<div id="result"><?php echo sizeof($listaffect); ?></div>
<div class="top"><span>Toutes UI - S<?php echo date("W"); ?></span></div>
</div>
<?php
}else{
?>
<style>
    #testtaille{
        width: 283px;
        height: 302px;
        background-color:white;
        border: solid 1px black;
     
    }
    .top{
        padding:5px;
        text-align:center;
        font-weight:bold;
        background-color:#ff730054;
        font-size:11px;
        font-family:calibri;
    }
    #result{
        font-family:calibri;
        text-align:center;
        font-weight:bold;
        font-size:10px;
        height:258px;
        background-color:#ffd1ab78;
    }
    #table_kpi {
        font-family: Arial, Helvetica, sans-serif;

        width: 100%;
        height: 100%;
        text-align: left;
       
        border-collapse: collapse;
        
      }
      #table_kpi tr{
        border-bottom:solid 2px #f79f56;
      }
      #table_kpi td{
     padding-left:10px;
      }
      
      .kpi_table_center{
          text-align:center;
      }
</style>
<input type="hidden" id="testx" value="1">
<input type="hidden" id="testy" value="1">
<div id="testtaille">
<div class="top"><span>Nombre POI affectée(s)</span></div>
<div id="result"><table id="table_kpi">
<tbody>
<tr>
<?php foreach($listeui as $ui){
    echo "<tr>
    <td>".$ui->libelle."</td>
    <td  class='kpi_table_center' id='ui-".$ui->ft_zone."'>0</td>
    
    </tr>";
} ?>
</tr>
</tbody>
</table></div>
<div class="top"><span>Par UI - S<?php echo date("W"); ?></span></div>
</div>
<?php
}
?>
<script>
$.post("../API/getNbAffectationByUi.php",function(data){
    console.log(data)
})
</script>
