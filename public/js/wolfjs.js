/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */


function wolf_cens() {
    var tabs = $("table[orderingtable]");
    tabs.each(function() {
//        console.log($(this).attr('id'));
//                   var headtr = $("#"+$(this).attr('id')+" tr:eq(0)");
        var tableid = $(this).attr('id');
        if (typeof (tableid) === "undefined") {
            var rdnid = random_char_string_for_id(10, "wolfot_");
            $(this).prop('id', rdnid);
            tableid = rdnid;
        }
        //now i have a table that can be analyzed and has an id = tableid;
        //call initializer
        wolf_ot_initialize(tableid);

    });
}

function wolf_ot_initialize(tableid) {
    var table = $("#" + tableid);
    if (!table.length)
        return;
    var ths = $("#" + tableid + " th");
    var thscont = $("#" + tableid + " th").map(function() {
        return $(this).html();
    });
    ths.each(function(i) {
        var dt = $(this).attr('otdatatype');
//        if (typeof (dt) === 'undefined')
//            dt = "text";


        if (typeof (dt) != "undefined") {
//            var matchenum = dt.match(/^(?:enum:)(.*)$/i);
//            if (matchenum != null){
//                matchenum = matchenum[1].split("|");
//                
//            }
            var html = "<div>";
            html += "<img style=\"display:none; width:10px; height:15px;\" class=\"wolfot_colup\" id=\"" + tableid + "_colup_" + i + "\" src=\"./public/img/arrowup.png\">";
            html += "<a href=\"javascript:wolf_ot_ordertable('" + tableid + "', new Array('" + i + "') , new Array(true),new Array('" + dt + "'));\" onclick=\"wolfot_changeOrder(this)\">" + thscont[i] + "</a>";
            html += "<img style=\"display:none; width:10px; height:15px;\" class=\"wolfot_coldown\" id=\"" + tableid + "_coldown_" + i + "\" src=\"./public/img/arrowdown.png\"></div>";
            $(this).html(html);
        }
    });
    var trs = table.find("tr:not(:eq(0))");
    trs.each(function(i) {
        $(this).attr("wolfot_row", i);
    });
//    table.before("<img id=\"" + tableid + "_gear\"width=\"20px\" src=\"./public/img/gear.png\">");
    var gear = $("<img>").attr("id", tableid + "_gear")
            .attr("src", "./public/img/gear.png").attr("width", "20px").bind("click", function(event) {
        $("#" + tableid + "_menu_container").css("display", "block");
        $("#" + tableid + "_menu_inner").css("display", "block");
    });
    ;
    console.log(gear);
    table.before(gear);
    var search = $("<input>").attr("type","search").attr("size","20").attr("id",tableid+"_search");
    search.keyup(function () {
        wolfot_search(tableid,$( this ).val(),"highlight");
    });
//    var searchmode1 = $("<input>").attr("type","radio").attr("name",tableid+"_searchmode").attr("id",tableid+"_highlight")
//            .attr("value","highlight");
//    searchmode1 = searchmode1.append("Evidenzia");
//    var searchmode2 = $("<input>").attr("type","radio").attr("name",tableid+"_searchmode").attr("id",tableid+"_showhide")
//            .attr("value","showhide");
//    searchmode2 = searchmode2.append("Filtra");
//    table.before("<input>");
//    table.before(searchmode2);
    table.before(search);
    table.before($("<p>").attr("id","prova"));
    var divcloser = $("<div>").attr("class", "wolfot_menu_container")
            .attr("id", tableid + "_menu_container").css("display", "none").bind("click", function(event) {
        this.style.display = 'none';
        $("#" + tableid + "_menu_inner").css("display", "none");
    });

    var divinner = $("<div>").attr("class", "wolfot_menu_inner")
            .attr("id", tableid + "_menu_inner").css("display", "none");

    var divmenu = $("<div>").attr("class", "wolfot_menu")
            .attr("id", tableid + "_menu");
    divmenu.appendTo(divinner);
    divmenu.append($("<img>").attr("src", "./public/img/ordina.png")
            .attr("width", "200px").css("float", "left")
            .bind("click", function(event) {
                $("#" + tableid + "_menu_container").css("display", "none");
                $("#" + tableid + "_menu_inner").css("display", "none");
                $("#" + tableid + "_gear").attr("src", "./public/img/done.png")
                        .unbind("click")
                        .bind("click", function(event) {
                            wolfot_deactivatearrows(tableid);
                            $("#" + tableid + "_gear").attr("src", "./public/img/gear.png")
                                    .unbind("click")
                                    .bind("click", function(event) {
                                        $("#" + tableid + "_menu_container").css("display", "block");
                                        $("#" + tableid + "_menu_inner").css("display", "block");
                                    });
                        });
                wolfot_activatearrows(tableid);
            }));
    divmenu.append($("<img>").attr("src", "./public/img/filtra.png")
            .attr("width", "200px").css("float", "right")
            .bind("click", function(event) {
                $("#" + tableid + "_menu_container").css("display", "none");
                $("#" + tableid + "_menu_inner").css("display", "none");

            }));
    table.before(divcloser);
    table.before(divinner);

//    table.before("<div class=\"wolfot_menu_container\" id=\"" + tableid + "_menu_container\" style=\"display: none;\" onclick=\"this.style.display='none';\"><div class=\"wolfot_menu_inner\" id=\"" + tableid + "_menu_inner\"><div class=\"wolfot_menu\" id=\"" + tableid + "_menu\"><img src=\"./public/img/ordina.png\" width=\"200px\"><img src=\"./public/img/filtra.png\" width=\"200px\"></div></div></div>");



    $("#" + tableid + "_gear").bind("click", function(event) {
        $("#" + tableid + "_menu_container").css("display", "solid");
    });
}


function wolf_ot_ordertable(tableid, cols, ords, flags) {
    if ((typeof (cols.length) == "undefined") || (typeof (ords.length) == "undefined") || (typeof (flags.length) == "undefined")) {
        return; // cols, ords & flags must be array
    }
    if ((cols.length != ords.length) || (cols.length != flags.length)) {
        return;
    }
    console.log(cols);
    console.log(ords);
    console.log(flags);
//    if (cols.length == 1) {
//        if (ords[0] == true) {
//            $("#tableid_colup_" + cols[0]).css("display", "none");
//            $("#tableid_coldown_" + cols[0]).css("display", "solid");
//            var th = $("#" + tableid + " th").eq(cols[0]);
//            th.html(th.html().replace("new Array('" + cols[0] + "') , new Array(true)", "new Array('" + cols[0] + "') , new Array(false)"));
//        }
//        else {
//            $("#tableid_colup_" + cols[0]).css("display", "solid");
//            $("#tableid_coldown_" + cols[0]).css("display", "none");
//            var th = $("#" + tableid + " th").eq(cols[0]);
//            th.html(th.html().replace("new Array('" + cols[0] + "') , new Array(false)", "new Array('" + cols[0] + "') , new Array(true)"));
//        }
//    }
    for (var i = 0; i < cols.length; i++) {
        cols[i] = Number(cols[i]);
        ords[i] = (ords[i] == true);
    }
    console.log(cols);
    console.log(ords);
    console.log(flags);
    var indexindex = Number(Math.max.apply(Math, cols) + 1);
    var data = new Array();
    var format = new Array();
    var table = $("#" + tableid);
    var hrow = table.find("tr:eq(0)");
    var intestazione = "";
    hrow.find("th").each(function() {
        intestazione += $(this).html() + " || ";
    })
    rows = table.find("tr:gt(0)");
    rows.each(function(i) {
        data[i] = new Array();
        for (var j = 0; j < cols.length; j++) {
            data[i][cols[j]] = $(this).find("td:eq(" + cols[j] + ")").html();
        }
        data[i][Number(indexindex)] = i;
    });
    var msort = function(a, b) {
        for (var i = 0; i < cols.length; i++) {
            var k = cols[i];
            if (ords[i]) { //ASC
                switch (flags[i]) {
                    case '':
                    case 'text':
                        var match1 = a[k].match(/<a.*>(.*)<\/a>/);
                        var match2 = b[k].match(/<a.*>(.*)<\/a>/);
                        if (match1 != null) {
                            var s1 = match1[1];
                        }
                        else {
                            var s1 = a[k];
                        }
                        if (match2 != null) {
                            var s2 = match2[1];
                        }
                        else {
                            var s2 = b[k];
                        }
                        var comp = strcomp(s1, s2);
                        if (comp == 1)
                            return 1;
                        else
                            continue;
                        break;
                    case 'number':
                        if (Number(a[k]) > Number(b[k])) {
                            return 1;
                        }
                        break;
                    case 'date':
                        if (strdatecomp(a[k], b[k]) == 1) {
                            return 1;
                        }
                        break;
                    default:
                        if (a[k] > b[k]) {
                            return 1;
                        }
                }

            }
            else {
                switch (flags[i]) {
                    case '':
                    case 'text':
                        var match1 = a[k].match(/<a.*>(.*)<\/a>/);
                        var match2 = b[k].match(/<a.*>(.*)<\/a>/);
                        if (match1 != null) {
                            var s1 = match1[1];
                        }
                        else {
                            var s1 = a[k];
                        }
                        if (match2 != null) {
                            var s2 = match2[1];
                        }
                        else {
                            var s2 = b[k];
                        }
                        var comp = strcomp(s1, s2);
                        if (comp == -1)
                            return 1;
                        else
                            continue;
                        break;
                    case 'number':
                        if (Number(a[k]) < Number(b[k])) {
                            return 1;
                        }
                        break;
                    case 'date':
                        if (strdatecomp(a[k], b[k]) == -1) {
                            return 1;
                        }
                        break;
                    default:
                        if (a[k] < b[k]) {
                            return 1;
                        }
                }

            }
        }
        return -1;
    }

    data.sort(msort);


    for (var i = data.length - 1; i >= 0; i--) {
        table.prepend(rows.eq(data[i][indexindex]));
    }
    table.prepend(hrow);
    console.log("ordering done");
    return;
}

function swaprow(tableid) {
    var table = $("#" + tableid);
    console.log(table);
}








function wolfot_generateTable(data) {

}


function wolfot_activatearrows(tableid) {
    var colups = $("#" + tableid).find(".wolfot_colup");
    console.log(colups);
    var coldowns = $("#" + tableid).find(".wolfot_coldown");
    console.log(coldowns);
    colups.each(function(i) {
        var id = $(this).attr("id").replace(tableid + "_colup_", "");
        var coldown = $("#" + tableid + "_coldown_" + id);
        $(this).bind("click", function(event) {
            $(this).attr("sel", "true");
            $(this).css("opacity", "1");
            coldown.css("opacity", "0.5");
            coldown.removeAttr("sel");
        });
    });
    coldowns.each(function(i) {
        var id = $(this).attr("id").replace(tableid + "_coldown_", "");
        var colup = $("#" + tableid + "_colup_" + id);
        $(this).bind("click", function(event) {
            $(this).attr("sel", "true");
            $(this).css("opacity", "1");
            colup.css("opacity", "0.5");
            colup.removeAttr("sel");
        });
    });
    colups.removeAttr("sel").css("opacity", "1").show();
    coldowns.removeAttr("sel").css("opacity", "1").show();
    return;
}

function wolfot_deactivatearrows(tableid) {
    var colups = $("#" + tableid).find(".wolfot_colup");
//    console.log(colups);
    var coldowns = $("#" + tableid).find(".wolfot_coldown");
//    console.log(coldowns);
    var ths = $("#" + tableid).find("th");
    console.log(ths);

    var cols = new Array();
    var flags = new Array();
    var ords = new Array();
    var j = 0;
    for (var i = 0; i < colups.length; i++) {
        var dt = ths.eq(i).attr("otdatatype");
        if (typeof (colups.eq(i).attr("sel")) != "undefined" && colups.eq(i).attr("sel") == "true") {
            cols[j] = colups.eq(i).attr("id").replace(tableid+"_colup_","");
            ords[j] = true;
            if (typeof (dt) != "undefined")
                flags[j] = dt;
            else
                flags[j] = "text";
            j++;
        }
        else if (typeof (coldowns.eq(i).attr("sel")) != "undefined" && coldowns.eq(i).attr("sel") == "true") {
            cols[j] = coldowns.eq(i).attr("id").replace(tableid+"_coldown_","");;
            ords[j] = false;
            if (typeof (dt) != "undefined")
                flags[j] = dt;
            else
                flags[j] = "text";
            j++;
        }
        //altrimenti non è stato scelto niente per questa colonna e vado avanti
        
    }
    wolf_ot_ordertable(tableid, cols, ords, flags);
    colups.each(function(i) {
        $("#" + $(this).attr("id")).css("opacity", "1")
                .removeAttr("sel")
                .hide();
    });
    coldowns.each(function(i) {
        $("#" + $(this).attr("id")).css("opacity", "1")
                .removeAttr("sel")
                .hide();
    });
}


function wolfot_filterTable(tableid, filters) {
    if (typeof (filters) === "undefined" || typeof (filters.length) === "undefined") {
        return;
    }
    var table = $("#" + tableid);
    var rows = table.find("tr:gt(0)");


}





function wolfot_search(tableid, key, mode, casesens) {
    //identifico celle-righe
    
//    var search = $("#"+tableid+"_search");
//    if (search.length > 0){
//        key = search.val();
//    }
    $("#prova").html(key);
    key = key.toLowerCase();
    var table = $("#" + tableid);
    if (mode == "reset" || key.trim() == "") {
        table.find("td").css("background-color", "");
        table.find("tr").show();
        return;
    }
    var trtoshow = 0;
    var trs = table.find("tr:gt(0)");
    for (var i = 0; i < trs.length; i++) {
        trtoshow = 0;
        tds = trs.eq(i).find("td");
        tds.each(function(j) {
            var cont = $(this).html();
            console.log(cont);
            if (cont.toLowerCase().search(key) >= 0) {
                if (mode == "highlight") {
                    $(this).css("background-color", "#ff0000");
                }
                else if (mode == "showhide") {
                    trtoshow = 1;
                }
            }
            else {
                $(this).css("background-color", "");
            }
        });
        //coloro oppure nascondo
        if (mode == "showhide") {
            if (trtoshow) {
                trs.eq(i).show();
            }
            else {
                trs.eq(i).hide();
            }
        }
    }

    
}