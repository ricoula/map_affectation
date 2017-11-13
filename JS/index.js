$(function(){
    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
    }
  
    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
    }

    $("#closeSlidebar").click(function(){
        closeNav();
    });

    $(".open").click(function(){
        openNav();
    });
});