<html>
    <body>
    <span id="slide-close" class="glyphicon glyphicon-remove pull-right"></span>        
        <h1>FILTER</h1>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Perferendis accusamus neque, quisquam repudiandae ipsum quis officiis laudantium exercitationem odio temporibus sed quaerat soluta recusandae error reprehenderit voluptates provident mollitia eius.</p>
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