<?php 
if($_POST['size'] == "sm"){
?>
<style>
    #testtaille{
        width: 140px;
        height: 140px;
        background-color:red;
    }
</style>
<div id="testtaille"></div>
<?php 
$taille = array(1,1);
return json_encode($taille);
}else{
?>
<style>
    #testtaille{
        width: 320px;
        height: 300px;
        background-color:blue;
    }
</style>
<div id="testtaille"></div>
<?php
$taille = array(2,2);
return json_encode($taille);
}
?>
