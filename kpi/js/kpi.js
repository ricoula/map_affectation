$(function(){ //DOM Ready

    var gridster = $(".gridster ul").gridster({widget_margins: [3, 3], widget_base_dimensions: [140, 160]}).data('gridster');
    
    $("#ajouterCase").click(function(){
        gridster.add_widget('<li class="new"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div></li>', 1, 1);
    });

    
});