<?php
 @session_start();
    function simu(){
        if($_SESSION['simu'] == true){
            $_SESSION['simu'] = false;
        }else
        {
            $_SESSION['simu'] = true;
        }
        return json_encode($_SESSION['simu']);
    }
    echo simu();
?>