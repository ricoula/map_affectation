<?php 

include("../API/fonctions.php");
include("../../API/fonctions.php");
$listchargeui = json_decode(getChargeByUi());
$config = json_decode(getAdvancedConfig(null));
$coef_react = $config->coef_react;
$coef_non_react= $config->coef_non_react;

// echo sizeof($listaffect);
if(!isset($_POST['size']) || $_POST['size'] == "sm"){
?>
<style>

    #testtaille{
        width: 140px;
        height: 140px;
    }
    .top{
        padding:5px;
        text-align:left;

        font-weight:bold;
        font-size:11px;
        font-family:calibri;
        color:white;

    }
    .bottom{
        padding:5px;
        text-align:left;
        font-weight:bold;
        font-size:11px;
        font-family:calibri;
        color:white;

    }
    #result_kpi2{
        font-family:calibri;
        text-align:center;
        font-weight:bold;
        font-size:45px;
        height:94px;
        line-height:94px;
        color:#b5b500;
    }
    
</style>
<input type="hidden" id="testx" value="1">
<input type="hidden" id="testy" value="1">
<div id="testtaille">
<div class="top"><span>Charge Globale AT&R</span></div>
<div class="bottom"><span>Toutes UI - S<?php echo date("W"); ?></span></div>
<?php 
$charge_globale = 0;
$charge_react = 0;
$charge_non_react = 0;
$nb_caff = 0;
    foreach($listchargeui as $chargeui){
        $charge_react += $chargeui->charge_reactive * $coef_react;
        $charge_non_react += $chargeui->charge_non_reactive * $coef_non_react;
        $nb_caff += $chargeui->nb_caff;
    }
    $charge_globale = round(($charge_react + $charge_non_react) / $nb_caff,1);
?>
<div id="result_kpi2"><?php echo $charge_globale; ?></div>

</div>
<?php
}else{
?>
<style>
    #testtaillelg{
        width: 303px;
        height: 302px;
    }
    .toplg{
        padding:5px;
        text-align:left;
        font-weight:bold;
        font-size:16px;
        font-family:calibri;
        color:white;
    }
    #resultlg{
        font-family:calibri;
        text-align:center;
        font-weight:bold;
        font-size:10px;
        height:244px;

    }
    #table_kpi {
        font-family: Arial, Helvetica, sans-serif;

        width: 100%;
        height: 100%;
        text-align: left;
        color:#c5c5c5;
        border-collapse: collapse;
        
      }
      #table_kpi tr{
      }
      #table_kpi td{
     padding-left:10px;
      }
      
      .kpi_table_center{
          text-align:center;
          color:#b5b500;
          font-weight:bold;
      }
</style>
<input type="hidden" id="testx" value="1">
<input type="hidden" id="testy" value="1">

<div id="testtaillelg">
<div class="toplg"><span>Charge</span></div>
<div class="toplg"><span>Par UI - S<?php echo date("W"); ?></span></div>
<div id="resultlg"><table id="table_kpi">

<tbody>
<tr>
<?php 
foreach($listchargeui as $chargeui){
     $charge_react = $chargeui->charge_reactive * $coef_react;
     $charge_non_react = $chargeui->charge_non_reactive * $coef_non_react;
     $nb_caff = $chargeui->nb_caff;
     $charge_globale = round(($charge_react + $charge_non_react) / $nb_caff,1);
    echo "<tr>
    <td>".$chargeui->ui."</td>
    <td  class='kpi_table_center'>".$charge_globale."</td>
    </tr>";
} ?>
</tr>
</tbody>
</table></div>

</div>
<?php
}
?>



