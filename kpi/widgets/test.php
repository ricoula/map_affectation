<?php 
if(!isset($_POST['size']) || $_POST['size'] == "sm"){
?>
<style>
    #testtaille{
        width: 140px;
        height: 140px;
        background-color:red;
    }
</style>
<input type="hidden" id="testx" value="1">
<input type="hidden" id="testy" value="1">
<div id="testtaille"></div>
<?php
}elseif($_POST['size'] == "lg"){
?>
<style>
    #testtaille{
        width: 283px;
        height: 302px;
        background-color:blue;
    }
</style>
<input type="hidden" id="testx" value="2">
<input type="hidden" id="testy" value="2">
<div id="testtaille"></div>
<?php
}
else{
    ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h1>TEST</h1>
    </div>
    <div class="modal-body">
        TEST
    </div>
    <div class="modal-footer">
        <button class="btn btn-info" data-dismiss="modal">Fermer</button>
    </div>
    <?php
}
?>
