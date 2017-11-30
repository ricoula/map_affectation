$(function(){
    $("#div-slide-home").load('slide-home.php');
    $("#div-slide-box").load('slide-box.php');
    $("#div-slide-users").load('slide-users.php?coefCharge=' + $("#coefCharge").val());
    $("#div-slide-filter").load('slide-filter.php?utilisateur_id=' + $("#user_id").val());

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

    $("#selectUi").change(function(){
        $("#labelNbPoiNA").hide();
        $("#divListePoiNAUi").hide();
        $.post("API/getPoiNAByUi.php", {ui: $(this).val()}, function(data){
            var listePoi = JSON.parse(data);
            $("#nbPoiNA").text(listePoi.length);
            $("#labelNbPoiNA").show();
            $("#btnGenererPoiNA").click(function(){
                var el = document.getElementById('btnGenererPoiNA'),
                elClone = el.cloneNode(true);
                el.parentNode.replaceChild(elClone, el);
                $("#resultatsListePoiNA").html("").hide();
                $("#divLoadingPoiNA").show();
                $("#divListePoiNAUi").show();

                var html = "<table class='table table-striped table-hover table-condensed table-responsive'><thead><tr><th>POI</th><th>Domaine</th><th>DRE</th><th>SJ</th><th>Caff</th></tr></thead><tbody>";
                var i = 0;
                listePoi.forEach(function(poi){
                    $.post("API/getAffectationAuto.php", {poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_poi_client: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val()}, function(data2){
                        i++;
                        poi.affectationAuto = JSON.parse(data2);
                        var optionElt = "";
                        poi.affectationAuto.listeAutresCaffs.forEach(function(ceCaff){
                            
                            if(ceCaff.id == poi.affectationAuto.id)
                            {
                                optionElt += "<option id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info' selected>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + ceCaff.charge_totale + ")" + "</option>";
                            }
                            else{
                                optionElt += "<option id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + ceCaff.charge_totale + ")" + "</option>";
                            }
                        });
                        html += "<tr><td>" + poi.ft_numero_oeie + "</td><td>" + poi.domaine + "</td><td>" + poi.ft_oeie_dre + "</td><td>" + poi.ft_sous_justification_oeie + "</td><td><select>" + optionElt + "</select></td></tr>";
                        $("#btnCaffAffectAuto-" + poi.id).click()
                        if(i == listePoi.length)
                        {
                            html += "</tbody></table>";
                            $("#divLoadingPoiNA").hide();
                            document.getElementById("resultatsListePoiNA").innerHTML = html;
                            $("#resultatsListePoiNA").show();
                        }
                    });
                });
            });
        })
    });

});