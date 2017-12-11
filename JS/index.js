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
        $("#loadingChoixUi").show();
        $("#labelNbPoiNA").hide();
        $("#divListePoiNAUi").hide();

        $.post("API/getPoiNAByUi.php", {ui: $(this).val()}, function(data){
            $("#loadingChoixUi").hide();
            var listePoi = JSON.parse(data);
            $("#nbPoiNA").text(listePoi.length);
            $("#labelNbPoiNA").show();

            $("#progress_bar_affect").css({"width":"0%"});
            $("#progress_bar_affect").html("0%");

            $("#btnGenererPoiNA").click(function(){
                var el = document.getElementById('btnGenererPoiNA'),
                elClone = el.cloneNode(true);
                el.parentNode.replaceChild(elClone, el);
                $("#resultatsListePoiNA").html("").hide();
               
                $("#divListePoiNAUi").show();
                var progress = 0;
                var html = "<table class='table table-striped table-hover table-condensed table-responsive'><thead><tr><th>POI</th><th>Domaine</th><th>DRE</th><th>SJ</th><th>Caff</th></tr></thead><tbody>";
                var i = 0;
                var y = 0;
                var listeCaffsSimulation = new Array();
                listePoi.forEach(function(poi){
                      y++;
                      
               //     console.log(progress);
               
                    //  progress = (i / listePoi.length) * 100;
                    // console.log(progress);
                    // $("#progress_bar_affect").css({"width":""+progress+"%"});
                    // $("#progress_bar_affect").html({progress+"%"});
                    //console.log(listeCaffsSimulation);
                    //listeCaffsSimulation = JSON.stringify(listeCaffsSimulation);
                    /*var seen = [];
                    
                    listeCaffsSimulation = JSON.stringify(listeCaffsSimulation, function(key, val) {
                       if (val != null && typeof val == "object") {
                            if (seen.indexOf(val) >= 0) {
                                return;
                            }
                            seen.push(val);
                        }
                        return val;
                    });
                    console.log(listeCaffsSimulation);
                    console.log("ENCODE");*/


                    console.log(y + "=" + poi.ft_numero_oeie);

                    /*$(document).ajaxStart(function() {
                        console.log('Méthode ajaxStart exécutée<br>');
                      });
                      $(document).ajaxSend(function(ev, req, options){
                        console.log('Méthode ajaxSend exécutée, ');
                        console.log('nom du fichier : ' + options.url + '<br>');
                      });
                      $(document).ajaxStop(function(){
                        console.log('Méthode ajaxStop exécutée<br>');
                      });
                      $(document).ajaxSuccess(function(ev, req, options){
                        console.log('Méthode ajaxSuccess exécutée<br>');
                      });
                      $(document).ajaxComplete(function(ev, req, options){
                        console.log('Méthode ajaxComplete exécutée<br>');
                      });
                      $(document).ajaxError(function(ev, req, options, erreur){
                        console.log('Méthode ajaxError exécutée, ');
                        console.log('erreur : ' + erreur + '<br>');
                      });*/
                    
                    //  console.log("Objet:");
                     // console.log({poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_poi_client: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val()});
                    

                    $.ajax({
                        type: 'POST',
                        url: "API/getAffectationAuto.php",
                        data: {poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_poi_client: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val()},// liste_caffs_simulation: listeCaffsSimulation},
                        success: function(data2){
                            i++;
                            console.log(i + "=" + poi.ft_numero_oeie);
                         
                            progress = Math.round(((i) / listePoi.length) * 100);  
                            $("#progress_bar_affect").attr("aria-valuenow", progress)
                            // $("#progress_bar_affect").css({"width":""+progress+"%"});
                            $("#progress_bar_affect").css("width", progress+"%");
                             $("#progress_bar_affect").html(progress+"%");
                          //  console.log("Data: " + data2);
                            poi.affectationAuto = JSON.parse(data2);
                            
                            //listeCaffsSimulation = JSON.parse(listeCaffsSimulation);
                            // console.log("DECODE" + i);
    
                            var optionElt = "";

                            listeCaffsSimulation.forEach(function(caffSimulation){
                                if(caffSimulation.id == poi.affectationAuto.id)
                                {
                                    poi.affectationAuto.listeAutresCaffs.forEach(function(caffSimu){
                                        if(caffSimu.id == caffSimulation.id)
                                        {
                                         //   console.log("SECONDAIRE");
                                            caffSimulation.listePoiSimulation.forEach(function(poiSimulation){
                                            if(poiSimulation.reactive)
                                            {
                                                caffSimu.charge_totale += 1;
                                            }
                                            else{
                                                caffSimu.charge_totale += parseFloat($("#coefCharge").val());
                                            }
                                        });
                                        }
                                    });
                                }
                            });

                            var compare = function(a, b){
                                if(a.charge_totale < b.charge_totale)
                                {
                                    return -1;
                                }
                                else if(a.charge_totale == b.charge_totale)
                                {
                                    return 0;
                                }
                                else{
                                    return 1;
                                }
                            };

                            poi.affectationAuto.listeAutresCaffs.sort(compare);

                            poi.affectationAuto.listeAutresCaffs.forEach(function(ceCaff){
                                
                               // if(ceCaff.id == poi.affectationAuto.id)
                                //{
                                    //optionElt += "<option id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info' selected>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + ceCaff.charge_totale + ")" + "</option>";
                                //}
                                //else{
                                    optionElt += "<option id='caffPoi" + poi.id + "-" + ceCaff.id + "' data-content=\"<span class='label label-info'>" + ceCaff.charge_totale + "</span>\">" + ceCaff.name_related + " (" + ceCaff.charge_totale + ")</option>";
                                //}
                            });
                            html += "<tr><td>" + poi.ft_numero_oeie + "</td><td>" + poi.domaine + "</td><td>" + poi.ft_oeie_dre + "</td><td>" + poi.ft_sous_justification_oeie + "</td><td><select>" + optionElt + "</select></td></tr>";
                            $("#btnCaffAffectAuto-" + poi.id).click()
                            if(i == listePoi.length)
                            {
                                html += "</tbody></table>";
                             
                                document.getElementById("resultatsListePoiNA").innerHTML = html;
                                $("#resultatsListePoiNA").show();
                            }
                            var caffSimulation = poi.affectationAuto;
                            var trouve = false;
                            if(listeCaffsSimulation.length > 0)
                            {
                                listeCaffsSimulation.forEach(function(caff){
                                    if(caff.id == caffSimulation.id)
                                    {
                                        trouve = true;
                                        caff.listePoiSimulation.push(poi);
                                    }
                                });
                            }
                            if(!trouve)
                            {
                                var caff = poi.affectationAuto;
                                caff.listePoiSimulation = new Array(poi);+
                                listeCaffsSimulation.push(caff);
                            }
                        },
                        async:true
                      });
                      
                    /*$.post("API/getAffectationAuto.php", {poi_id: poi.id, km: $("#kmRadius").val(), coef_poi_proxi: $("#coefPoiProxi").val(), coef_poi_client: $("#coefPoiClient").val(), coef_charge: $("#coefCharge").val(), liste_caffs_simulation: listeCaffsSimulation}, function(data2){
                        i++;
                        //console.log(data2);
                        poi.affectationAuto = JSON.parse(data2);
                        listeCaffsSimulation = JSON.parse(listeCaffsSimulation);
                        var caffSimulation = poi.affectationAuto;
                        var trouve = false;
                        if(listeCaffsSimulation.length > 0)
                        {
                            listeCaffsSimulation.forEach(function(caff){
                                if(caff.id == caffSimulation.id)
                                {
                                    caff.listePoiSimulation.push(poi);
                                }
                            });
                        }
                        if(!trouve)
                        {
                            var caff = poi.affectationAuto;
                            caff.listePoiSimulation = new Array(poi);
                            listeCaffsSimulation.push(caff);
                        }

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
                    });*/
                });
                
            });
        })
    });

});