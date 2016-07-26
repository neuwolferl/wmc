/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

$(document).ready(
        function() {
            localStorage.clear();
            if ($("#formnewmodel").length !== 1) {
                console.log("Error: can't find form");
                return;
            }

            var baseurl = document.URL;
            baseurl = baseurl.split("modelanalyzer");
            baseurl = baseurl[0];
            baseurl += "dbanalyzer/";
//            console.log(baseurl);
            var gotostep1 = function() {
                console.clear();
                $("#navigator").css("overflow", "visible");
                var fs = $("#formnewmodel");
                fs.empty();
                fs.append($("<br>")).append($("<label for=\"maindb\">DB principale</label>")); //.attr("for", "maindb").text("DB principale"));
                fs.append($("<br>"));
                fs.append($("<select required name=\"maindb\" id=\"maindb\">")
                        .append($("<option value=\"-1\">Scegli</option>")));
                fs.append($("<br>")).append($("<br>"));
                fs.append($("<label for=\"maintable\">Tabella principale</label>"));
                fs.append($("<select required name=\"maintable\" id=\"maintable\">"));
                fs.append($("<br>")).append($("<br>"));
                fs.append($("<button type=\"button\" id=\"btngotostep2\">Avanti</button>"));
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
            //controllo retrieve;
            var url = document.URL;
            url = url.split("/newmodel/");
            url = url[1].split("/")
                if (url.length > 1 && url[1] !== "" ){
                    getSavedModel();
                }
            
            };
//          

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
                var tabella = new DBTable({
                    DBNum: maindbn,
                    DBName: maindb,
                    DBTableName: maintable
                }
                );
                var modtabella = new MODTable({
                    DBTable: tabella.DBName + "." + tabella.DBTableName,
                    parentMODTableId: "",
                    linkDBFieldName: "",
                    parentLinkDBFieldName: "",
                    pathInModel: "structure",
                    linkMultiplicity: ""
                });
                
                renderMODTable(modtabella);
                
                adjustMODTable(modtabella.MODTableId);
                
                localStorage.setItem("maintable", modtabella.MODTableId);
                //cerco id:
                var fields = tabella.DBFields;
                var modfields = modtabella.MODFields;
                for (var i in fields) {
                    if (fields[i].isKey === true) {
                        var keyFieldName = fields[i].DBFieldName;
                        break;
                    }
                }
                for (var i in modfields) {
                    if (modfields[i].DBFieldName === keyFieldName) {
                        var keyMODFieldId = modfields[i].MODFieldId;
                        break;
                    }
                }
                var Element = new MODElement({
                    MODFieldId: keyMODFieldId,
                    MODTableId: modtabella.MODTableId,
                    isKey: true
                });
                renderMODElement(Element);
                $("#btnSave").show();
                $("#btnPreview").show().click(function(){ preview();});
            };
            gotostep1();
            $("#composer")
                    .droppable({
                        accept: ".MODField",
                        tollerance: "touch",
                        drop: function(event, ui) {
                            var DOMMODField = ui.draggable;
                            var DOMMODTable = DOMMODField.parents(".MODTable");
                            var MODFieldId = DOMMODField.attr("id");
                            var MODTableId = DOMMODTable.attr("id");
                            var position = {
                                X: event.offsetX,
                                Y: event.offsetY
                            };
                            var Element = new MODElement({
                                MODFieldId: MODFieldId,
                                MODTableId: MODTableId
                            });
                            renderMODElement(Element, position);
                            return;
                        }});
        });