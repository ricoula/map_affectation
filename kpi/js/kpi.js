$(function(){ //DOM Ready

    var gridster = $(".gridster ul").gridster({widget_margins: [3, 3], widget_base_dimensions: [140, 160]}).data('gridster');
    
    $("#ajouterCase").click(function(){
        gridster.add_widget('<li class="new"><div class="menuCase"><a href="#" class="glyphicon glyphicon-resize-small"></a> <a href="#" class="glyphicon glyphicon-resize-full"></a> <a href="#" class="glyphicon glyphicon-fullscreen"></a></div></li>', 1, 1);
    });

    
});