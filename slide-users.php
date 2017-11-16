<html>
    <body>
        <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span>        
        <div class="jumbotron">
        <h1>USERS</h1>
        <button class="btn btn-primary">test</button>
        <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Sunt, commodi eos? Porro explicabo dolorum enim autem facilis dolores aperiam asperiores cumque quam vero! Assumenda sunt quidem repudiandae sapiente fugit consectetur!</p>
        </div>
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