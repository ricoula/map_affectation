$(function(){
    $("#div-slide-box").load('slide-box.php');
    $("#div-slide-users").load('slide-users.php');
    $("#div-slide-filter").load('slide-filter.php?user_id=' + $("#user_id").val());

    $("#slide-box").click(function(){
        $("#side_bar").animate({left:'0px'},500);
        $("#glyph").animate({left:'500px'},500);
        $(".slide").hide();
        $("#div-slide-box").show();
        $(".glyph_div").removeClass("active");
        $("#slide-box").addClass("active");
    });
    $("#slide-filter").click(function(){
        $("#side_bar").animate({left:'0px'},500);
        $("#glyph").animate({left:'500px'},500);
        $(".slide").hide();
        $("#div-slide-filter").show();
        $(".glyph_div").removeClass("active");
        $("#slide-filter").addClass("active");
    });
    $("#slide-home").click(function(){
        var poi = getUrlParameter('poi');
        $("#side_bar").animate({left:'0px'},500);
        $("#glyph").animate({left:'500px'},500);
        $("#div-slide-home").load('slide-home.php');
        $(".slide").hide();
        $("#div-slide-home").show();
        $(".glyph_div").removeClass("active");
        $("#slide-home").addClass("active");
        if(poi != undefined){
            $("#div-slide-home").load('slide-home.php?poi_id=' + poi);
        }
    });
    $("#slide-users").click(function(){
        $("#side_bar").animate({left:'0px'},500);
        $("#glyph").animate({left:'500px'},500);
        $(".slide").hide();
        $("#div-slide-users").show();
        $(".glyph_div").removeClass("active");
        $("#slide-users").addClass("active");
    });
    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

});