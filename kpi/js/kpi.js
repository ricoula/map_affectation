$(function(){
    $(".contenuCase").each(function(){
        var taille = $(this).parent().attr("taille");
        var elt = $(this);
        $(this).load("kpi/widgets/" + $(this).parent().attr("lien") + ".php", {size: $(this).parent().attr("taille")}, function(){
            if(taille == "sm")
            {
                elt.closest(".gs-w").children(".menuCase").children(".glyphicon-resize-small").hide();
            }
            else{
                elt.closest(".gs-w").children(".menuCase").children(".glyphicon-resize-full").hide();
            }
        });
    });

    var fonctionSerialize = function($w, wgd){
        var obj = { col: wgd.col, row: wgd.row, size_x: wgd.size_x, size_y: wgd.size_y } ;
        obj.lien = wgd.el.attr("lien");
        obj.taille = wgd.el.attr("taille");
        return obj;
    }


    var gridster = $(".gridster ul").gridster({widget_margins: [3, 3], widget_base_dimensions: [140, 160], serialize_params: fonctionSerialize, avoid_overlapped_widgets: false}).data('gridster').disable();
    
    $("#ajouterCase").click(function(){
        var lien = 'test';
        $.post("kpi/API/getSizeWidget.php",{lien: lien, size: 'sm'},function(data){
            var size = JSON.parse(data);
            var thisWidget = gridster.add_widget('<li class="new"  lien="' + lien + '" taille="sm" ><div class="menuCase"><span href="#" class="glyphicon glyphicon-resize-small"></span><span href="#" class="glyphicon glyphicon-resize-full"></span><span href="#" class="glyphicon glyphicon-fullscreen"></span><span href="#" class="glyphicon glyphicon-remove"></span></div><div class="contenuCase" ></div></li>', size.sm.x, size.sm.y);
            
            thisWidget.children(".menuCase").children(".glyphicon-resize-small").hide();
            if(!size.full)
            {
                thisWidget.children(".menuCase").children(".glyphicon-fullscreen").hide();
            }

            if(size.lg == null)
            {
                thisWidget.children(".menuCase").children(".glyphicon-resize-full").hide();
            }

            var contenuWidget = thisWidget.children(".contenuCase");

            thisWidget.children(".menuCase").children(".glyphicon-resize-full").click(function(){
                $(this).closest(".gs-w").attr("taille", "lg");
                var elt = $(this);
                var widget = $(this).closest(".gs-w");
                gridster.resize_widget( widget, size.lg.x, size.lg.y);
                contenuWidget.load("kpi/widgets/" + lien + ".php", {size: 'lg'}, function(){
                    elt.hide();
                    elt.closest(".menuCase").children(".glyphicon-resize-small").show();
                });
            });
            thisWidget.children(".menuCase").children(".glyphicon-resize-small").click(function(){
                $(this).closest(".gs-w").attr("taille", "sm");
                var elt = $(this);
                var widget = $(this).closest(".gs-w");
                gridster.resize_widget( $(this).closest(".gs-w"), size.sm.x, size.sm.y);
                contenuWidget.load("kpi/widgets/" + lien + ".php", {size: 'sm'}, function(){
                    elt.hide();
                    elt.closest(".menuCase").children(".glyphicon-resize-full").show();
                });
            });
            thisWidget.children(".menuCase").children(".glyphicon-fullscreen").click(function(){
                $("#contenuModaleWidgetFullScreen").load("kpi/widgets/" + lien + ".php", {size: 'full'}, function(){
                    $("#modaleWidgetFullScreen").modal("show");
                });
            });
            thisWidget.children(".menuCase").children(".glyphicon-remove").click(function(){
                var widget = $(this).closest(".gs-w");
                gridster.remove_widget(widget);
            });

            contenuWidget.load("kpi/widgets/" + lien + ".php", {size: 'sm'});
        });
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
        obj = JSON.stringify(obj);
        $.post("kpi/API/addGridster.php", {user_id: $("#user_id").val(), gridster_json: obj});
    });

    $(".menuCase .glyphicon-resize-small").click(function(){
        $(this).closest(".gs-w").attr("taille", "sm");
        var widget = $(this).closest(".gs-w");
        gridster.resize_widget( widget, 1, 1);
        var lien = $(this).closest(".gs-w").attr("lien");
        console.log(lien);
        var elt = $(this);
        $(this).closest(".gs-w").children(".contenuCase").load("kpi/widgets/" + lien + ".php", {size: 'sm'}, function(){
            elt.hide();
            elt.closest(".menuCase").children(".glyphicon-resize-full").show();
        });
    });
    $(".menuCase .glyphicon-resize-full").click(function(){
        $(this).closest(".gs-w").attr("taille", "lg");
        var widget = $(this).closest(".gs-w");
        gridster.resize_widget( widget, 2, 2);
        var elt = $(this);
        var lien = $(this).closest(".gs-w").attr("lien");
        $(this).closest(".gs-w").children(".contenuCase").load("kpi/widgets/" + lien + ".php", {size: 'lg'}, function(){
            elt.hide();
            elt.closest(".menuCase").children(".glyphicon-resize-small").show();
        });
    });
    $(".menuCase .glyphicon-fullscreen").click(function(){
        var lien = $(this).closest(".gs-w").attr("lien");
        if(lien != null)
        {
            $("#contenuModaleWidgetFullScreen").load("kpi/widgets/" + lien + ".php", {size: 'full'}, function(){
                $("#modaleWidgetFullScreen").modal("show");
            });
        }
    });
    $(".menuCase .glyphicon-remove").click(function(){
        var widget = $(this).closest(".gs-w");
        gridster.remove_widget(widget);
    });
});