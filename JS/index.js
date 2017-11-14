$(function(){

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    $("#glyph-1").click(function(){
        $("#side_bar").animate({width:'600px'},500)
    });
    $("#glyph-2").click(function(){
        $("#side_bar").animate({width:'0px'},500)
    });
});