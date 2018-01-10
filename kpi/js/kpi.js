$(function(){ //DOM Ready
    var fonctionSerialize = function($w, wgd){
        var obj = { col: wgd.col, row: wgd.row, size_x: wgd.size_x, size_y: wgd.size_y } ;
        obj.lien = wgd.el.attr("lien");
        return obj;
    }

    var gridster = $(".gridster ul").gridster({widget_margins: [3, 3], widget_base_dimensions: [140, 160], serialize_params: fonctionSerialize}).data('gridster').disable();
    
    $("#ajouterCase").click(function(){
        gridster.add_widget('<li class="new"><div class="menuCase"><a href="#" class="glyphicon glyphicon-resize-small"></a> <a href="#" class="glyphicon glyphicon-resize-full"></a> <a href="#" class="glyphicon glyphicon-fullscreen"></a></div></li>', 1, 1);
    });

    $("#modifierEmplacement").click(function(){
        gridster.enable();
        $(this).hide();
        $("#ajouterCase").show();
        $("#sauvegarderEmplacement").show();
    });

    $("#sauvegarderEmplacement").click(function(){
        $("#ajouterCase").hide();
        $(this).hide();
        $("#modifierEmplacement").show();
        gridster.disable();
        var obj = gridster.serialize();
        console.log(obj);
    });
});