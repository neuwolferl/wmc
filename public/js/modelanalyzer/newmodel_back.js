/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

$(document).ready(
        function() {
            if ($("#formnewmodel").length !== 1) {
                console.log("Error: can't find form");
                return;
            }
            var baseurl = document.URL;
            baseurl = baseurl.split("modelanalyzer");
            baseurl = baseurl[0];
            baseurl += "dbanalyzer/";
            console.log(baseurl);
            var gotostep1 = function() {
                $("#navigator").css("overflow", "visible");
                var fs = $("#formnewmodel");
                fs.empty();
                fs.append($("<br>")).append($("<label for=\"maindb\">DB principale</label>"));//.attr("for", "maindb").text("DB principale"));
                fs.append($("<br>"));
                fs.append($("<select required>").attr("required", "required")
                        .attr("name", "maindb")
                        .attr("id", "maindb")
                        .append($("<option>").val(-1).text("Scegli")));
                fs.append($("<br>")).append($("<br>"));
                fs.append($("<label>").attr("for", "maintable").text("Tabella principale"));
                fs.append($("<select>").attr("required", "required")
                        .attr("name", "maintable")
                        .attr("id", "maintable"));
                fs.append($("<br>")).append($("<br>"));
                fs.append($("<button>").attr("type", "button").attr("id", "btngotostep2").text("Avanti"));
                fs.removeAttr("fieldsetprincipali");
                $("[for=maintable]").hide();
                $("#maintable").empty().hide();
                $("#btngotostep2").hide();
                var dbs = JSON.parse(getdbs(baseurl));
                if ($("#maindb").length !== 1) {
                    console.log("Error: can't find maindb select");
                    return;
                }
                if ($("#maintable").length !== 1) {
                    console.log("Error: can't find maintable select");
                    return;
                }

                if (typeof (dbs.count) === "undefined") {
                    console.log("Error in dbs infos");
                    return;
                }
                for (var i = 0; i < dbs.count; i++) {
                    if (typeof (dbs[i]) === "undefined") {
                        console.log("Error in db " + i);
                        return;
                    }
                    $("<option>").val(i).text(dbs[i]).appendTo($("#maindb"));
                }
                $("#maindb").change(function() {

                    $("[for=maintable]").hide();
                    $("#maintable").hide();
                    $("#maintable").unbind("change");
                    var dbn = $(this).find(":selected").val();
                    if (dbn === -1) {
                        $("#maintable").empty().hide();
                    }
                    else {
                        $("<option>").val(-1).text("Scegli").appendTo($("#maintable"));
                        var tabs = JSON.parse(gettabs(baseurl, dbn));
                        if (typeof (tabs.count) === "undefined") {
                            alert("No tabs infos for this db");
                            return;
                        }
                        for (var i = 0; i < tabs.count; i++) {
                            if (typeof (tabs[i]) === "undefined") {
                                alert("Error in tab " + i);
                                return;
                            }

                            $("<option>").val(i).text(tabs[i]).appendTo($("#maintable"));
                        }
                        $("[for=maintable]").show();
                        $("#maintable").show();
                        $("#maintable").bind("change", function() {
                            var tabn = $(this).find(":selected").val();
                            var tab = $(this).find(":selected").text();
                            if (tabn === -1) {
                                $("#btngotostep2").hide().unbind("click");
                            }
                            else {
                                $("#btngotostep2").show().bind("click", function() {
                                    gotostep2();
                                });
                            }
                        });
                    }
                });
            };
//          

            var addnewtabletonav = function(dbn, dbname, table, options) {

                var prog = $("#composer").attr("prog");
                $("#composer").attr("prog", Number(prog) + 1);
                if ((typeof (options.fromtable) === "undefined") || (typeof (options.fromfield) === "undefined") || (typeof (options.tofield) === "undefined")) {
                    alert("Something wrong in arguments for table. Reload page and retry!");
                    return;
                }
                var nav = $("#navigator");
                if (nav.length !== 1) {
                    alert("Error: can't find navigator");
                    return;
                }
                var numfield = $("#numberoffields");
                if (numfield.length !== 1) {
                    alert("Error: corrupted form, reload page and retry");
                    return;
                }
                var numberoffields = numfield.val();
                if (($("#maindb").length !== 1) || ($("#maindbn").length !== 1) || ($("#maintable").length !== 1) || ($("#maintablen").length !== 1)) {
                    alert("Error: corrupted form, reload page and retry");
                    return;
                }
                var maindb = $("#maindb").val();
                var maindbn = $("#maindbn").val();
                var maintable = $("#maintable").val();
                var maintablen = $("#maintablen").val();
                var fields = JSON.parse(getfields(baseurl, dbn, table));
                if (typeof (fields.count) === "undefined") {
                    alert("No fields infos for this table");
                    return;
                }
                var tableid = "tablediv_" + table + "_" + prog;
                var tablediv = $("<div>").attr({
                    class: "tablediv",
                    id: tableid,
                    dbname: dbname,
                    dbn: dbn,
                    tablename: table,
                    fromtable: options.fromtable,
                    fromfield: options.fromfield,
                    tofield: options.tofield,
                    mult: options.mult
                });
                var tabletitletext = dbname + "." + table;
                if (options.fromtable !== "main") {
                    var prog2 = $("#composer").attr("prog");
                    $("#composer").attr("prog", Number(prog2) + 1);
                    var fromtableel = $("#" + options.fromtable);
                    var fromfieldel = fromtableel.find(".fielddiv[fieldname='" + options.fromfield + "']");
                    var text = fromfieldel.text();
                    text += " *" + prog2;
                    fromfieldel.text(text);
                    tabletitletext += " *" + prog2;
                }
                var tabletitle = $("<span>")
                        .attr({
                            open: "open",
                            id: "tabletitle_" + table + "_" + prog,
                            class: "tabletitle",
                        })
                        .text(tabletitletext)
                        .click(function() {
                            var open = $(this).attr("open");
                            var tablediv = $(this).parent();
                            if (typeof (open) === "undefined") {
                                $(this).attr("open", "true");
                                tablediv.children().show();
                                adjustnav();
                            }
                            if (open === "open") {
                                $(this).removeAttr("open");
                                tablediv.children(":not([id^='tabletitle'])").hide();
                                adjustnav();
                            }

                        });
                tablediv.append(tabletitle);
                for (var i = 0; i < fields.count; i++) {
                    var fielddes = "";
                    var fieldname = fields[i].name;
                    if (fields[i].details.key === "PRI")
                        fielddes += "PK ";
                    fielddes += fields[i].name;
                    var fielddiv = $("<div>")
                            .attr({
                                class: "fielddiv",
                                id: "fielddiv_" + table + "." + fieldname + "_" + prog,
                                fieldname: fieldname,
                                belongstotableid: "tablediv_" + table + "_" + prog,
                                tablename: table,
                                dbname: dbname,
                                dbn: dbn
                            })
                            .text(fielddes)
                            .dblclick(function() {
                                console.log($(this));
                                var dbn = $(this).attr("dbn");
                                var dbname = $(this).attr("dbname");
                                var table = $(this).attr("tablename");
                                var fieldname = $(this).attr("fieldname");
                                addnewfieldtocomp(dbn, dbname, table, fieldname, {
                                    belongstotableid: tableid
                                });
                            })
                            .draggable({
                                containment: "window",
//                                cursor: "crosshair",
                                scroll: false,
                                helper: "clone"
                            })
                            ;
                    tablediv.append(fielddiv);
                }
                nav.append(tablediv);
                adjustnav();
                return tableid;
            };
            var addnewfieldtocomp = function(dbn, db, table, field, options) {
                if (typeof (options.belongstotableid) === "undefined") {
                    alert("Something wrong in arguments for field. Reload page and retry!");
                    return;
                }
                var prog = $("#composer").attr("prog");
                $("#composer").attr("prog", Number(prog) + 1);
                var comp = $("#composer");
                var completefieldname = db + "." + table + "." + field;
                var fieldcolor = "#ccc";
                if (typeof (options.id) !== "undefined" && options.id === true) {
                    var fieldcolor = "#ff0000";
                }
                var left = 0;
                var top = 0;
                if (typeof (options.positionX) !== "undefined") {
                    left = options.positionX;
                }
                if (typeof (options.positionY) !== "undefined") {
                    top = options.positionY;
                }
                var fieldbottomlabel = "from: " + completefieldname;
                var fielddiv = $("<div>")
                        .attr(
                                {
                                    class: "fieldincomp",
                                    id: "fieldincomp_" + completefieldname + "_" + prog,
                                    fieldname: field,
                                    tablename: table,
                                    dbname: db,
                                    dbn: dbn,
                                    draggable: "true",
                                    belongstotableid: options.belongstotableid
                                }
                        )
                        .css(
                                {
                                    "background-color": fieldcolor,
                                    left: left,
                                    top: top
                                }
                        );
                var fieldopen = $("<span>")
                        .attr({
                            open: "open",
                            id: "fieldopen_" + completefieldname + "_" + prog,
                            class: "fieldopen"
                        })
                        .text("-")
                        .click(function() {
                            var open = $(this).attr("open");
                            var fielddiv = $(this).parent();
                            if (typeof (open) === "undefined") {
                                $(this).attr("open", "true")
                                        .text("-");
                                if ((typeof (fielddiv.attr("role")) === "undefined") || (fielddiv.attr("role") !== "RE")) {
                                    fielddiv.children("[class!='fieldref'][class!='fieldalias']").show();
                                    fielddiv.css("height", "150px");
                                }
                                else {
                                    fielddiv.children("[class!='fieldalias']").show();
                                    fielddiv.css("height", "150px");
                                }



                            }
                            if (open === "open") {
                                $(this).removeAttr("open").text("+");
                                fielddiv.children("[class!='fieldopen'][class!='fieldtitle']").hide();
                                fielddiv.css("height", "20px");
                            }
                        });
                var fieldremove = $("<span>")
                        .attr({
                            id: "fieldremove_" + completefieldname + "_" + prog,
                            class: "fieldremove"
                        })
                        .append("<img width=\"15px\" height=\"15px\" src=\"../../img/remove.png\">")
                        .click(function() {

                            var fielddiv = $(this).parent();
                            fielddiv.empty().remove();
                        });
                var fieldalias = $("<input>")
                        .attr({
                            id: "fieldalias_" + completefieldname + "_" + prog,
                            class: "fieldalias"
                        })
                        .dblclick(function() {
                            var newvalue = $(this).val();
                            if (newvalue === "") {
                                newvalue = $(this).parent().attr("fieldname");
                                newvalue = findFirstAvailableFieldname(newvalue, $(this).parent().attr("id"));
                            }
                            $(this).parent().find(".fieldtitle").text(newvalue).show();
                            $(this).hide();
                        });
                var fieldtitle = $("<span>")
                        .attr({
                            id: "fieldtitle_" + completefieldname + "_" + prog,
                            class: "fieldtitle"
                        })
                        .text(findFirstAvailableFieldname(field))
                        .dblclick(function() {
                            $(this).parent().find(".fieldalias").show();
                            $(this).hide();
                        });
                var fieldref = $("<select>")
                        .attr({
                            id: "fieldref_" + completefieldname + "_" + prog,
                            class: "fieldref"
                        });
                var fieldfilter = $("<button>")
                        .attr({
                            id: "fieldfilter_" + completefieldname + "_" + prog,
                            class: "fieldfilter"
                        })
                        .html("nessun filtro")
                        .click(function() {
                            openfieldfilter(completefieldname + "_" + prog)
                        });
                var fieldbottom = $("<span>")
                        .attr({
                            class: "fieldbottom"
                        })
                        .text(fieldbottomlabel);
                var roleselector = $("<div>")
                        .attr({
                            id: completefieldname + "_" + prog + "_roleselector",
                            class: "roleselectorcontainer"
                        });
                comp.append(fielddiv.append(fieldopen).append(fieldtitle).append(fieldbottom).append(fieldalias).append(fieldref).append(fieldfilter));
                var roles = {
                    ID: 0,
                    PR: 0,
                    AT: 0,
                    LI: 0,
                    RE: 0
                }; //new Array(0, 0, 0, 0, 0);
                if (typeof (options.id) !== "undefined" && options.id === true) {
                    roles.ID = 1;
                }
                else {
                    fielddiv.append(fieldremove);
                    roles.PR = 1;
                    roles.AT = 1;
                    roles.LI = 1;
                    var links = JSON.parse(getlinks(baseurl, dbn, table, field));
                    if (typeof (links.count) === "undefined" || links.count === 0) {
                        roles.RE = 0;
                    }
                    else {
                        roles.RE = 1;
                    }
                }
                fielddiv.append(makeroleselector(completefieldname, roles));
                $(".roleselector")
                        .unbind("dblclick")
                        .dblclick(function() {
                            if ($(this).attr("disabled") !== "disabled") {
                                var divfield = $(this).parents("div.fieldincomp");
                                divfield.attr({
                                    role: $(this).html()
                                })
                                        .css({
                                            background: $(this).css("background")
                                        });
                                divfield.css("height", "150px");
                                var sel = divfield.find(".fieldref");
                            }
                            else {
                                return;
                            }
                            if ($(this).html() === "RE") {
                                console.log("click su RE");
                                divfield.css("height", "150px");
                                divfield.find(".fieldfilter").hide();
                                var links = JSON.parse(getlinks(baseurl, dbn, table, field));
                                if (typeof (links.count) === "undefined" || links.count === 0) {
                                    return;
                                }
                                else {
                                    if (sel.length !== 1) {
                                        return;
                                    }
                                    sel.append(
                                            $("<option>").val("-1").text("Scegli")
                                            );
                                    for (var i = 0; i < links.count; i++) {
                                        var presenttables = $(".tablediv")
                                                .filter("[dbname='" + links[i].db + "']")
                                                .filter("[tablename='" + links[i].table + "']")
                                                .filter("[tofield='" + links[i].fieldname + "']");
                                        if (presenttables.length === 0) {
                                            sel.append(
                                                    $("<option>")
                                                    .val(i)
                                                    .text(links[i].mult + " " + links[i].dbtable + " via " + links[i].fieldname)
                                                    .attr({
                                                        fieldname: links[i].fieldname,
                                                        tablename: links[i].table,
                                                        db: links[i].db,
                                                        dbn: links[i].dbn,
                                                        mult: links[i].mult
                                                    }));
                                        }
                                    }
                                    if (sel.children().length === 1) {
                                        sel.children().text("Nessuna opzione disponibile");
                                    }
                                    else {
                                        sel.after(
                                                $("<button>")
                                                .attr({
                                                    id: "btn" + sel.attr("id"),
                                                    class: "btn" + sel.attr("class")
                                                })
                                                .text("Ok")
                                                .click(function() {
                                                    sel = $(this).siblings(".fieldref");
                                                    var selected = sel.find(":selected");
                                                    var val = selected.val();
                                                    var divfield = $(this).parent();
                                                    if (val === -1) {
                                                        return;
                                                    }
                                                    else {
                                                        var duplicate = $("#navigator").find(".tablediv")
                                                                .filter("[table='" + selected.attr("tablename") + "']")
                                                                .filter("[fromtable='" + divfield.attr("belongstotableid") + "']");
                                                        console.log(duplicate);
                                                        if (duplicate.length !== 0) {
                                                            return;
                                                        }
                                                        var options = {
                                                            fromtable: divfield.attr("belongstotableid"),
                                                            fromfield: divfield.attr("fieldname"),
                                                            tofield: selected.attr("fieldname"),
                                                            mult: selected.attr("mult")
                                                        };
                                                        addnewtabletonav(selected.attr("dbn"), selected.attr("db"), selected.attr("tablename"), options);
                                                        $(this).remove();
                                                        var selid = sel.attr("id");
                                                        sel.remove();
                                                        var seltext = "<p>" + divfield.attr("dbname") + "." + divfield.attr("tablename") + "." + divfield.attr("fieldname") + "</p>";
                                                        seltext += "<p>V V V</p>";
                                                        seltext += "<p>" + selected.attr("db") + "." + selected.attr("tablename") + "." + selected.attr("fieldname") + "</p>";
                                                        sel = $("<span>")
                                                                .attr({
                                                                    id: selid,
                                                                    class: "fieldrefchoosen"
                                                                })
                                                                .append(seltext)
                                                        divfield.append(sel);
                                                        divfield.find(".fieldremove").remove();
                                                        divfield.find(".roleselectorcontainer").remove();
                                                    }
                                                }));
                                    }
                                    sel.show();
                                }
                            }
                            else {
                                divfield.find(".fieldfilter").show();
                                divfield.find(".btnfieldref").remove();
                                sel.empty().hide();
                            }
                        });
                fielddiv
                        .draggable({
                            grid: [5, 5],
                            containment: "parent",
                            zIndex: 1000
                        });
            };
            var gotostep2 = function() {
                var fs = $("#maindb").parent();
                var maindb = $("#maindb").find(":selected").text();
                var maindbn = $("#maindb").find(":selected").val();
                var maintable = $("#maintable").find(":selected").text();
                var maintablen = $("#maintable").find(":selected").val();
                //controllo che la tabella scelta vada bene
                var fields = JSON.parse(getfields(baseurl, maindbn, maintable));
                if (typeof (fields.count) === "undefined") {
                    alert("No fields infos for this table");
                    return;
                }
                var flag = false;
                var pri = "";
                for (var i = 0; i < fields.count; i++) {
                    if (fields[i].details.key == "PRI") {
                        flag = true;
                        pri = fields[i].name;
                        break;
                    }
                }
                if (!flag) {
                    $("#notvalidtablerror").remove();
                    fs.append($("<span>")
                            .attr("id", "notvalidtablerror")
                            .css("color", "#ff0000")
                            .text("La tabella scelta non contiene una chiave primaria")
                            );
                    return;
                }


                $("[for=maindb]").remove();
                $("[for=maintable]").remove();
                $("#maindb").remove();
                $("#maintable").remove();
                $("#btngotostep2").remove();
                $("#notvalidtablerror").remove();
                fs.append($("<input>").attr({
                    type: "hidden",
                    id: "maindbn",
                    value: maindbn
                }).hide())
                        .append($("<input>").attr({
                            type: "hidden",
                            id: "maindb",
                            value: maindb
                        }).attr("disabled", "disabled"))
                        .append($("<input>").attr({
                            type: "hidden",
                            id: "maintablen",
                            value: maintablen
                        }).hide())
                        .append($("<br>"))
                        .append($("<input>").attr({
                            type: "hidden",
                            id: "maintable",
                            value: maintable
                        }).attr("disabled", "disabled"));
                fs.append($("<input>").attr({
                    type: "hidden",
                    id: "numberoffields"
                }).val(0))
                        .appendTo($("#formnewmodel"));
                /////
                var nav = $("#navigator");
                if (nav.length != 1) {
                    console.log("Can't find navigator");
                    return;
                }
                $("#first-form").hide();
                nav.css({
                    "overflow-y": "scroll",
                    "overflow-x": "hidden"
                });
                var tableid = addnewtabletonav(maindbn, maindb, maintable, {
                    fromtable: "main",
                    fromfield: "",
                    tofield: ""
                });
                addnewfieldtocomp(maindbn, maindb, maintable, pri, {
                    id: true,
                    belongstotableid: tableid
                });
                $("#btnSave").show();
                $("#btnPreview").show().click(function() {
                    var prev = $("#preview");
                    prev.empty();
                    var model = {}
                    preview(model);
                    prev.html(JSON.stringify(model));
                });
            };
            gotostep1();
            $("#navigator").on('scroll', function() {
                adjustnav();
            });
            $("#composer")
                    .attr("prog", 0)
                    .droppable({
                        accept: ".fielddiv",
                        tollerance: "touch",
                        drop: function(event, ui) {
                            var obj = ui.draggable;
                            var options = {
                                positionX: event.offsetX,
                                positionY: event.offsetY,
                                belongstotableid: ui.draggable.attr("belongstotableid")
                            };
                            addnewfieldtocomp(obj.attr("dbn"), obj.attr("dbname"), obj.attr("tablename"), obj.attr("fieldname"), options);
                        }});
            $('.tablediv').sortable();
        });