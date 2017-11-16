<html>
    <body>
    <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span>        
        <h1>BOX</h1>
        <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Fugiat nobis autem animi exercitationem dolorem vero voluptas repellat. Sint non qui voluptas? Praesentium ullam amet sunt quaerat, veniam sed odio asperiores.</p>
    </body>
</html>
<script>   
        $("#slide-close").click(function(){
        $("#side_bar").animate({left:'-500px'},500);
        $("#glyph").animate({left:'0px'},500);
        $(".glyph_div").removeClass("active");
        $("#side_bar").html("");
    });
</script>