$(function(){ //DOM Ready
    var fonctionSerialize = function($w, wgd){
        var obj = { col: wgd.col, row: wgd.row, size_x: wgd.size_x, size_y: wgd.size_y } ;
        obj.lien = wgd.el.attr("lien");
        return obj;
    }

    var gridster = $(".gridster ul").gridster({widget_margins: [3, 3], widget_base_dimensions: [140, 160], serialize_params: fonctionSerialize, avoid_overlapped_widgets: false}).data('gridster').disable();
    
    $("#ajouterCase").click(function(){
        gridster.add_widget('<li class="new"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>', 1, 1);
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

    $(".menuCase .glyphicon-resize-small").click(function(){
        var widget = $(this).closest(".gs-w");
        gridster.resize_widget( widget, 1, 1, true, function(){

        } );
    });
    $(".menuCase .glyphicon-resize-full").click(function(){
        var widget = $(this).closest(".gs-w");
        gridster.resize_widget( widget, 2, 2, true, function(){

        } );
    });
    $(".menuCase .glyphicon-fullscreen").click(function(){
        var widget = $(this).closest(".gs-w");
    });
    $(".menuCase .glyphicon-remove").click(function(){
        var widget = $(this).closest(".gs-w");
    });
});