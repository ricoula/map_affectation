$(function(){

    $("#slide-box").click(function(){
        
        $("#side_bar").animate({left:'0px'},500);
        $("#glyph").animate({left:'500px'},500);
        $("#side_bar").load('slide-box.php');
        $(".glyph_div").removeClass("active");
        $("#slide-box").addClass("active");
    });
    $("#slide-filter").click(function(){
        $("#side_bar").animate({left:'0px'},500);
        $("#glyph").animate({left:'500px'},500);
        $("#side_bar").load('slide-filter.php');
        $(".glyph_div").removeClass("active");
        $("#slide-filter").addClass("active");
    });
    $("#slide-home").click(function(){
        $("#side_bar").animate({left:'0px'},500);
        $("#glyph").animate({left:'500px'},500);
        $("#side_bar").load('slide-home.php');
        $(".glyph_div").removeClass("active");
        $("#slide-home").addClass("active");
    });
    $("#slide-users").click(function(){
        $("#side_bar").animate({left:'0px'},500);
        $("#glyph").animate({left:'500px'},500);
        $("#side_bar").load('slide-users.php');
        $(".glyph_div").removeClass("active");
        $("#slide-users").addClass("active");
    });

});