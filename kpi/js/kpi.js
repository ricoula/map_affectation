$(function(){ //DOM Ready
    var fonctionSerialize = function($w, wgd){
        var obj = { col: wgd.col, row: wgd.row, size_x: wgd.size_x, size_y: wgd.size_y } ;
        obj.lien = wgd.el.attr("lien");
        obj.taille = wgd.el.attr("taille");
        return obj;
    }


    var gridster = $(".gridster ul").gridster({widget_margins: [3, 3], widget_base_dimensions: [140, 160], serialize_params: fonctionSerialize, avoid_overlapped_widgets: false}).data('gridster').disable();
    
    $("#ajouterCase").click(function(){ 
        $.post("kpi/API/getSizeWidget.php",{lien: 'test', size: 'sm'},function(data){
            var size = JSON.parse(data);
            var thisWidget = gridster.add_widget('<li class="new"  lien="test.php" taille="sm" ><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>', size.sm.x, size.sm.y);
            var contenuWidget = thisWidget.children(".contenuCase");

            thisWidget.children(".menuCase").children(".glyphicon-resize-full").click(function(){
                var elt = $(this);
                console.log(elt);
                var widget = $(this).closest(".gs-w");
                gridster.resize_widget( widget, size.lg.x, size.lg.y);
                contenuWidget.load("kpi/widgets/test.php", {size: 'lg'});
            });
            thisWidget.children(".menuCase").children(".glyphicon-resize-small").click(function(){
                var elt = $(this);
                var widget = $(this).closest(".gs-w");
                gridster.resize_widget( $(this).closest(".gs-w"), size.sm.x, size.sm.y);
                contenuWidget.load("kpi/widgets/test.php", {size: 'sm'});
            });
            thisWidget.children(".menuCase").children(".glyphicon-fullscreen").click(function(){
                $("#contenuModaleWidgetFullScreen").load("kpi/widgets/test.php", {size: 'full'}, function(){
                    $("#modaleWidgetFullScreen").modal("show");
                });
            });
            thisWidget.children(".menuCase").children(".glyphicon-remove").click(function(){
                var widget = $(this).closest(".gs-w");
                gridster.remove_widget(widget);
            });

            contenuWidget.load("kpi/widgets/test.php", {size: 'sm'});
        });
        // var thisWidget = gridster.add_widget('<li class="new"><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div lien="test.php"></div></li>', 1, 1);
        // //console.log($("[lien='test.php']"));
        // console.log(thisWidget);
        // thisWidget.children(".menuCase .glyphicon-resize-small").click(function(){
        //     console.log("okkko");
        //     var widget = $(this).closest(".gs-w");
        //     gridster.resize_widget( widget, 1, 1, true, function(){
    
        //     } );
        // });
        // thisWidget.children(".menuCase .glyphicon-resize-small").click(function(){
        //     console.log("okkko");
        //     var widget = $(this).closest(".gs-w");
        //     gridster.resize_widget( widget, 2, 2, true, function(){
    
        //     } );
        // });
        // thisWidget.children(".menuCase .glyphicon-resize-small").click(function(){
        //     console.log("okkko");
        //     var widget = $(this).closest(".gs-w");
        // });
        // thisWidget.children(".menuCase .glyphicon-resize-small").click(function(){
        //     console.log("okkko");
        //     var widget = $(this).closest(".gs-w");
        // });
        // /*$("[lien='test.php']").load("kpi/widgets/test.php", {size: 'sm'}, function(data){
        //    var x = $("#testx").val();
        //    var y = $("#testy").val();
        //    console.log($(this).closest(".gs-w"));
        //    gridster.resize_widget( $(this).closest(".gs-w"), 1, 1, true, function(){

        // } );
        // });*/
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
        $("#contenuModaleWidgetFullScreen").load("kpi/widgets/test.php", {size: 'full'}, function(){
            $("#modaleWidgetFullScreen").modal("show");
        });
    });
    $(".menuCase .glyphicon-remove").click(function(){
        var widget = $(this).closest(".gs-w");
        gridster.remove_widget(widget);
    });
});