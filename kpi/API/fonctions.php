<?php
    function getSizeWidget($lien, $taille)
    {
        $obj = null;
        if($lien == "test")
        {
            $obj = (object) array();
            $obj->sm = (object) array();
            $obj->sm->x = 1;
            $obj->sm->y = 1;
            $obj->lg = (object) array();
            $obj->lg->x = 2;
            $obj->lg->y = 2;
            $obj->full = true;
        }

        return json_encode($obj);
    }
?>