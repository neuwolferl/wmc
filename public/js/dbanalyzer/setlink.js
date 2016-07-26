/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */


$(document).ready(
        function() {
            var fieldappoggio = $("#field").clone().empty().attr("id", "fieldappoggio").attr("name", "fieldappoggio").hide();
            $("#al").hide();
            $("#insertlink").hide();
            $("#field").after(fieldappoggio);
            var travaso = function(prefix) {
                $("#field").find("option[value!='-1']").appendTo($("#fieldappoggio"));
                $("#fieldappoggio").find("[value^='" + prefix + "']").appendTo($("#field"));
            };
            var estrazione = function(sh) {
                $("#al").hide().empty();
                $("#field").find(":not(:selected)").filter("[value!='-1']").clone().appendTo($("#al"));
                if (sh){
                    $("#al").show();
                }
            };

            var check = function() {
                var mult = $("#mult").val();
                var table = $("#table").val();
                var field = $("#field").val();
                var view = $("#view").val();
                var al = $("#al").val();
                if ((mult != "-1") && (table != "-1") && (field != "-1") && (view != "-1") && ((view == "0") || (view == "1") || ((view == "2") && (al != "-1")))) {
                    $("#insertlink").show();
                }
                else {
                    $("#insertlink").hide();
                }
            }

            travaso("-1");
            $("#mult").change(
                    function() {
                        check();
                        switch ($(this).val()) {
                            case 0:
                            case "0":
                                $(".mult1").html("corrisponde un solo record identificato");
                                $(".mult2").html("Ad ogni");
                                $(".mult3").html("o");
                                break;
                            case 1:
                            case "1":
                                $(".mult1").html("corrispondono pi&ugrave; record identificati");
                                $(".mult2").html("Ad ogni");
                                $(".mult3").html("o");
                                break;
                            case 2:
                            case "2":
                                $(".mult1").html("corrisponde un solo record identificato");
                                $(".mult2").html("A pi&ugrave;");
                                $(".mult3").html("i");
                                break;
                        }
                    }
            );


            $("#table").change(
                    function() {
                        check();
//                        setTimeout(function() {
                        travaso($(this).val() + ".");
                        $(".tabella2").html($(this).find(":selected").text());
//                        }, 100);
                    }
            );
            $("#field").change(
                    function() {
                        check();
                        estrazione(false);
                        $("#view").val("-1");
                        $(".campo2").html($(this).find(":selected").text());
                    }
            );
            $("#view").change(
                    function() {
                        check();
                        switch ($(this).val()) {
                            case 0:
                            case '0':
                                $("#al").hide().empty();
                                $(".view2").html("al solo campo collegato indicato");
                                break;
                            case 1:
                            case '1':
                                $("#al").hide().empty();
                                $(".view2").html("all'intero record");
                                break;
                            case 2:
                            case '2':
                                console.log("ciao");

                                estrazione(true);
                                $(".view2").html("al campo alias dello stesso record");
                                break;
                        }


                    });


        });