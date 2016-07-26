/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */


function wolfmp_generate(numberofdiv, appendto) {
    if (typeof (numberofdiv) != "number") {
        return;
    }
    var mpid = random_char_string_for_id(10, "wolfmp_");
    var maindiv = $("<div>").attr("id", mpid + "_maindiv");

    maindiv.appendTo(appendto);
    var div = new Array();
    var previous = $("<div id='" + mpid + "_previous'><img width=\"50px\" id='" + mpid + "_previousimg' src='./public/img/arrowleft.png'></div>");
    var next = $("<div id='" + mpid + "_next'><img width=\"50px\" id='" + mpid + "_nextimg' src='./public/img/arrowright.png'></div>");

    for (var i = 0; i < numberofdiv; i++) {
        div[i] = $("<div>").attr("id", mpid + "_div_" + i)
                .css({"position": "absolute", "width": "80%", "height": "80%", "top": "10%", "left": "10%"}).html("Questo è il " + (i + 1) + "° div.")
                .hide();

        div[i].appendTo(maindiv);
    }
    maindiv.prepend(previous);
    maindiv.append(next);
    $("#" + mpid + "_maindiv").css({"background-color": "#ff0000", "width": "100%", "height": "100%"});

    $("#" + mpid + "_previous").css({"position": "absolute", "left": "0%", "width": "50px", "height": "100%"});
    $("#" + mpid + "_next").css({"position": "absolute", "top": "40%", "width": "50px", "left": "95%"});
    $("#" + mpid + "_previousimg").css({"position": "absolute", "top": "40%", "left": "0%", "width": "50px"});
    $("#" + mpid + "_nextimg").css({"float": "right", "position": "absolute", "top": "45%", "width": "50px", "left": "95%"});

    $("#" + mpid + "_previousimg")
    $("#" + mpid + "_previousimg").bind("click", function() {
        wolfmp_changediv(mpid, 0)
    });
    $("#" + mpid + "_nextimg").bind("click", function() {
        wolfmp_changediv(mpid, 1)
    });
//    div[0].show("slow");
    return mpid;
}


function wolfmp_changediv(mpid, div_number) {

    var div = $("[id^=" + mpid + "_div_]");
    var now = -1;
    $("#" + mpid + "_nextimg").show();
    $("#" + mpid + "_previousimg").show();

    if ($("#" + mpid + "_div_" + div_number).length == 1) {
        $("#" + mpid + "_div_" + div_number).show(2000);
    }
    else {
        return false;
    }
    div.not(":eq(" + div_number + ")").hide();
    $("#" + mpid + "_previousimg").unbind("click")
    if (div_number - 1 >= 0) {
        $("#" + mpid + "_previousimg").bind("click", function() {
            wolfmp_changediv(mpid, div_number - 1);
        });
    }
    else {
        $("#" + mpid + "_previousimg").hide();
    }
    if (div_number + 1 <= div.length - 1) {
        $("#" + mpid + "_nextimg").bind("click", function() {
            wolfmp_changediv(mpid, div_number + 1);
        });
    }
    else {
        $("#" + mpid + "_nextimg").hide();
    }
    return mpid;

}


function wolfmp_overview(mpid) {

    var overview = $("#overview");
    var div = $("[id^=" + mpid + "_div_]");
//    console.log(div);
    div.each(function(i) {
//        console.log("ciao");
        var divnumber = $(this).attr("id").replace(mpid + "_div_", "");
        console.log(divnumber);
        var tile = $("<div id='" + mpid + "_tile_" + divnumber + "'>").css({"float": "left", "width": "320px", "height": "440px", "background-color": random_color()});
        overview.append(tile);
    });

}

