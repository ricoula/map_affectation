$(function(){ //DOM Ready

    var gridster = $(".gridster ul").gridster({widget_margins: [3, 3], widget_base_dimensions: [140, 160]}).data('gridster');
    
    $("#ajouterCase").click(function(){
        gridster.add_widget('<li class="new"></li>', 1, 1);
    });

    
});