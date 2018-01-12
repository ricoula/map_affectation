<?php 

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
    #result{
        font-family:calibri;
        text-align:center;
        font-weight:bold;
        font-size:90px;
        height:94px;
        line-height:94px;
        color:#b5b500;
    }
    
</style>
<input type="hidden" id="testx" value="1">
<input type="hidden" id="testy" value="1">
<div id="testtaille">
<div class="top"><span>Nombre POI affectée(s)</span></div>
<div class="bottom"><span>Toutes UI - S<?php echo date("W"); ?></span></div>
<div id="result"><?php echo sizeof($listaffect); ?></div>

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
<div class="toplg"><span>Nombre POI affectée(s)</span></div>
<div class="toplg"><span>Par UI - S<?php echo date("W"); ?></span></div>
<div id="resultlg"><table id="table_kpi">

<tbody>
<tr>
<?php foreach($listeui as $ui){
    echo "<tr>
    <td>".$ui->libelle."</td>
    <td  class='kpi_table_center' id='kpi_ui-".$ui->ft_zone."'>0</td>
    
    </tr>";
} ?>
</tr>
</tbody>
</table></div>

</div>
<?php
}
?>


<script>
    console.log("jfiozjizj");
$.post("kpi/API/getNbAffectationByUi.php",function(data){
    console.log(data);

    var listeaffect = JSON.parse(data);
    listeaffect.forEach(function(affect){
        var ui = affect.ui;
        var nb_poi = affect.nb_poi;
        $("#kpi_ui-"+ui).text(nb_poi);
    })
    
})
</script>
