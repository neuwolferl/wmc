function adjustMODTables() {
    var MODTables = $(".MODTable");
    var top = 0;
    MODTables.each(function() {
        var close = $(this).find(".MODTableOpen").attr("close");
        if (close === "close") {
            height = 70 + Number($(".MODTableAlias").css("height").replace("px", ""))
                    + Number($(".MODTableName").css("height").replace("px", ""))
                    + Number($(".MODTableDependency").css("height").replace("px", ""))
                    + Number($(".MODTableSep").css("height").replace("px", ""));
        }
        else {
            height = 70 + Number($(".MODTableAlias").css("height").replace("px", ""))
                    + Number($(".MODTableName").css("height").replace("px", ""))
                    + Number($(".MODTableDependency").css("height").replace("px", ""))
                    + Number($(".MODTableSep").css("height").replace("px", ""))
                    + ($(this).find(".MODField").length * (10 + Number($(".MODField").css("height").replace("px", ""))));
        }
        $(this).attr("h", Math.floor(height));


        $(this).css({
            height: $(this).attr("h") + "px",
            top: top
        });
        top += Math.floor(height) + 40;
    });
}

function adjustMODTable(MODTableId) {
    var MODTable = $("#" + MODTableId);
    console.log(MODTable);
    var height = MODTable.attr("h");
    if (typeof (height) === "undefined") {
        height = 70 + Number($(".MODTableAlias").css("height").replace("px", ""))
                + Number($(".MODTableName").css("height").replace("px", ""))
                + Number($(".MODTableDependency").css("height").replace("px", ""))
                + Number($(".MODTableSep").css("height").replace("px", ""))
                + (MODTable.find(".MODField").length * (10 + Number($(".MODField").css("height").replace("px", ""))));
        MODTable.attr("h", Math.floor(height));
    }
    MODTable.css("height", MODTable.attr("h") + "px");
    var MODFields = MODTable.find(".MODField");
    MODFields.each(function() {
        var offset = $(this).position();
        var id = $(this).attr("id");
        if (typeof (id) === "undefined")
            return;
        var det = $("#MODFieldDetails_" + id.replace("MODField_", ""));
        if (det.length === 1) {
            det.css("top", Math.floor(offset.top)).hide();
        }
    });
}

function showMODFieldDetails(MODFieldId) {
    var MODField = $("#" + MODFieldId);
    if (MODField.length !== 1)
        return;
    var details = MODField.attr("showdet");
    if (typeof (details) !== "undefined") {
        var MODFieldDetails = $("#MODFieldDetails_" + MODFieldId.replace("MODField_", ""));
        MODFieldDetails.show(50);
    }
    MODField.removeAttr("showdet");
}

$(document).delegate("#modelname", "change", function() {
    localStorage.setItem("modelname", $(this).val());
});

$(document).delegate("#btnSave", "click", function() {
    save();
});
//attiva visualizzazione dettagli campo
$(document).delegate(".MODField", "mouseover", function() {
    var id = $(this).attr("id");
    $(this).attr("showdet", "showdet");
    setTimeout(function() {
        showMODFieldDetails(id);
    }, 500);
});
//disattiva visualizzazione dettagli campo
$(document).delegate(".MODField", "mouseout", function() {
    $(this).removeAttr("showdet");
    var id = $(this).attr("id");
    if (typeof (id) === "undefined")
        return;
    var MODFieldDetails = $("#MODFieldDetails_" + id.replace("MODField_", ""));
    if ((MODFieldDetails).length === 1)
        MODFieldDetails.hide();
});


//effetto pulsante su bottone per aprire tabella
$(document).delegate(".MODTableOpen", "mousedown", function() {
    $(this).css({
        "box-shadow": "0px 0px"
    });

});
//apertura e chiusura div tabelle
$(document).delegate(".MODTableOpen", "mouseup", function() {
    $(this).css({
        "box-shadow": "-1px -1px #74777c"
    });
    var MODTableOpen = $(this);
    var MODTable = $(this).parent();
    if (MODTableOpen.length !== 1)
        return;
    var close = MODTableOpen.attr("close");
    if (close === "close") {
        MODTable.children().show(500);
        MODTableOpen.removeAttr("close").attr("open", "open").html("-");
        MODTable.css("height", MODTable.attr("h") + "px");
        adjustMODTable(MODTable.MODTableId);
        adjustMODTables();
    }
    else {
        MODTable.children().not(".MODTableOpen").not(".MODTableAlias").not(".MODTableName")
                .not(".MODTableDependency").hide(500);
        MODTableOpen.removeAttr("open").attr("close", "close").html("+");
        MODTable.css("height", "55px");
        adjustMODTables();
    }
});
//cambio nome element
$(document).delegate(".MODElementTitle", "dblclick", function() {
    if ($(this).prop("tagName").toLowerCase() === "div") {
        $(this).replaceWith(
                $("<input>")
                .attr({
                    class: $(this).attr("class"),
                    id: $(this).attr("id"),
                    value: $(this).html()
                })
                .css({
                    background: $(this).css("background")
                })
                );
    }
    else if ($(this).prop("tagName").toLowerCase() === "input") {
        var newTitle = $(this).val();
        $(this).replaceWith($("<div>")
                .attr({
                    class: $(this).attr("class"),
                    id: $(this).attr("id")
                })
                .css({
                    background: $(this).css("background")
                })
                .html(newTitle)
                );
        var MODElementId = $(this).attr("id").replace("MODElementTitle_", "");
        var MODElement = MODElementIdToMODElement(MODElementId);
        MODElement.MODElementName = newTitle;
        localStorage.setItem("MODElement_" + MODElementId, JSON.stringify(MODElement));
    }
});
//cambio nome relation
$(document).delegate(".MODRelationTitle", "dblclick", function() {
    if ($(this).prop("tagName").toLowerCase() === "div") {
        $(this).replaceWith(
                $("<input>")
                .attr({
                    class: $(this).attr("class"),
                    id: $(this).attr("id"),
                    value: $(this).html()
                })
                .css({
                    background: $(this).css("background")
                })
                );
    }
    else if ($(this).prop("tagName").toLowerCase() === "input") {
        var newTitle = $(this).val();
        $(this).replaceWith($("<div>")
                .attr({
                    class: $(this).attr("class"),
                    id: $(this).attr("id")
                })
                .css({
                    background: $(this).css("background")
                })
                .html(newTitle)
                );
        var MODRelationId = $(this).attr("id").replace("MODRelationTitle_", "");
        var MODRelation = MODRelationIdToMODRelation(MODRelationId);
        MODRelation.MODRelationName = newTitle;
        localStorage.setItem("MODRelation_" + MODRelationId, JSON.stringify(MODRelation));
    }
});
//apertura e chiusura div element
function openCloseElement(DOMMODElement){
    
    var MODElement = DOMMODToMODElement(DOMMODElement);
    
    var open = MODElement.isOpen;
    if (typeof (open) === "undefined" || open === false) {
        DOMMODElement.children().show();
        MODElement.isOpen = true;
        DOMMODElement.find(".MODElementOpen").css("background-position-y", "0px");
    }
    else if (open === true) {
        MODElement.isOpen = false;
        DOMMODElement.children().not(".MODElementOpen").not(".MODElementDeleteDisabled")
                .not(".MODElementDelete").not(".MODElementTitle").hide();
        DOMMODElement.find(".MODElementOpen").css("background-position-y", "-15px");
    }
    localStorage.setItem("MODElement_"+MODElement.MODElementId, JSON.stringify(MODElement));
}
$(document).delegate(".MODElementOpen", "click", function() {
    var DOMMODElement = $(this).parent(".MODElement");
    openCloseElement(DOMMODElement);
});
//apertura e chiusura div relation
function openCloseRelation(DOMMODRelation){
    var MODRelation = DOMMODToMODRelation(DOMMODRelation);
    console.log(MODRelation);
    var open = MODRelation.isOpen;
    if (typeof (open) === "undefined" || open === false) {
        DOMMODRelation.children().show();
        MODRelation.isOpen = true;
        DOMMODRelation.find(".MODRelationOpen").css("background-position-y", "0px");
    }
    else if (open === true) {
        DOMMODRelation.children().not(".MODRelationOpen")
                .not(".MODRelationDelete").not(".MODRelationTitle").hide();
        MODRelation.isOpen = false;
        DOMMODRelation.find(".MODRelationOpen").css("background-position-y", "-15px");
    }
    localStorage.setItem("MODRelation_"+MODRelation.MODRelationId, JSON.stringify(MODRelation));
}
$(document).delegate(".MODRelationOpen", "click", function() {
    var DOMMODRelation = $(this).parent(".MODRelation");
    openCloseRelation(DOMMODRelation);
});
//elimina element
$(document).delegate(".MODElementDelete", "click", function() {
    var DOMMODElement = $(this).parent(".MODElement");
    var MODElementId = DOMMODElement.attr("id");
    var MODElement = JSON.parse(localStorage.getItem("MODElement_" + MODElementId));
    var MODTableId = MODElement.MODTableId;
    var MODTable = JSON.parse(localStorage.getItem("MODTable_" + MODTableId));
    var MODElements = MODTable.MODElements;
    for (var i in MODElements) {
        if (MODElements[i] === MODElementId) {
            MODElements.splice(i, 1);
        }
    }
    localStorage.setItem("MODTable_" + MODTableId, JSON.stringify(MODTable));
    localStorage.removeItem("MODElement_" + MODElementId);
    DOMMODElement.empty().remove();
});
//elimina relation
$(document).delegate(".MODRelationDelete", "click", function() {
    alert("Funzione non ancora implementata.");
    return;
});
//evidenzia tabella da element
$(document).delegate(".MODElementBottom", "mouseover", function() {
    var DOMMODElement = $(this).parents(".MODElement");
    var MODElementId = DOMMODElement.attr("id");
    var MODElement = JSON.parse(localStorage.getItem("MODElement_" + MODElementId));
    var MODTableId = MODElement.MODTableId;
    var DOMMODTable = $("#" + MODTableId);
    DOMMODTable.css({background: "#f9e29d"});
});
$(document).delegate(".MODElementBottom", "mouseout", function() {
    var DOMMODElement = $(this).parents(".MODElement");
    var MODElementId = DOMMODElement.attr("id");
    var MODElement = JSON.parse(localStorage.getItem("MODElement_" + MODElementId));
    var MODTableId = MODElement.MODTableId;
    var DOMMODTable = $("#" + MODTableId);
    DOMMODTable.css({background: "#cdd0d7"});
});
//evidenzia element da tabella
$(document).delegate(".MODTable","mouseover",function(){
    var MODTable = MODTableIdToMODTable($(this).attr("id"));
    var MODElements = MODTable.MODElements;
    for (var i in MODElements){
        $("#"+MODElements[i]).find(".MODElementTitle").css("border","2px black solid");
    }
});

$(document).delegate(".MODTable","mouseout",function(){
    $(".MODElementTitle").css("border","1px black dotted");
    
});


//evidenzia tabelle da relation
$(document).delegate(".MODRelationBottom", "mouseover", function() {
    var DOMMODRelation = $(this).parents(".MODRelation");
    var MODRelationId = DOMMODRelation.attr("id");
    var MODRelation = JSON.parse(localStorage.getItem("MODRelation_" + MODRelationId));
    var MODTableFromId = MODRelation.MODTableFromId;
    var DOMMODTableFrom = $("#" + MODTableFromId);
    DOMMODTableFrom.css({background: "#f9e29d"});
    var MODTableToId = MODRelation.MODTableToId;
    var DOMMODTableTo = $("#" + MODTableToId);
    DOMMODTableTo.css({background: "#f9e29d"});
});
$(document).delegate(".MODRelationBottom", "mouseout", function() {
    var DOMMODRelation = $(this).parents(".MODRelation");
    var MODRelationId = DOMMODRelation.attr("id");
    var MODRelation = JSON.parse(localStorage.getItem("MODRelation_" + MODRelationId));
    var MODTableFromId = MODRelation.MODTableFromId;
    var DOMMODTableFrom = $("#" + MODTableFromId);
    DOMMODTableFrom.css({background: "#cdd0d7"});
    var MODTableToId = MODRelation.MODTableToId;
    var DOMMODTableTo = $("#" + MODTableToId);
    DOMMODTableTo.css({background: "#cdd0d7"});
});

//applica relazione
$(document).delegate(".MODRelationChooseLinkButton", "click", function() {
    var select = $(this).siblings(".MODRelationChooseLinkSelect");
    if (select.length !== 1 || typeof (select.val()) === "undefined" || select.val() === "-1") {
        return;
    }
    var DOMMODRelation = $(this).parents(".MODRelation");
    var MODRelation = DOMMODToMODRelation(DOMMODRelation);
    var MODTableFrom = MODTableIdToMODTable(MODRelation.MODTableFromId);
    var MODFieldsFrom = MODTableFrom.MODFields;
    for (var i in MODFieldsFrom) {
        if (MODFieldsFrom[i].MODFieldId === MODRelation.MODFieldFromId) {
            var MODFieldFrom = MODFieldsFrom[i];
        }
    }
    var link = MODRelation.links[Number(select.val())];
    var chooseLink = select.parents(".MODRelationChooseLink");
    var linkDiv = $("<div class=\"MODRelationLink\"></div>")
            .append("To: <span class=\"MODRelationLinkTo\">" + link.dbtable + "</span><br>")
            .append("Via: <span class=\"MODRelationLinkVia\">" + link.fieldname + "</span><br>")
            .append("Mult: <span class=\"MODRelationLinkMult\">" + link.mult + "</span>");
    chooseLink.empty().replaceWith(linkDiv);
    //controllo presenza DBTable
    var dbTable = localStorage.getItem("DBTable_" + link.dbtable);
    if (dbTable === null) {
        dbTable = new DBTable({
            DBNum: link.dbn,
            DBName: link.db,
            DBTableName: link.table
        });
    }
    switch (link.mult) {
        case "!":
            var multExtended = "onetoone";
            break;
        case "+":
            var multExtended = "onetomany";
            break;
        case "*":
            var multExtended = "manytoone";
            break;
    }
    var modTableTo = new MODTable({
        DBTable: dbTable.DBName + "." + dbTable.DBTableName,
        parentMODTableId: MODRelation.MODTableFromId,
        linkDBFieldName: link.fieldname,
        parentLinkDBFieldName: MODFieldFrom.DBFieldName,
        pathInModel: MODTableFrom.pathInModel + "->table_" + MODTableFrom.MODTableId + "->" + multExtended,
        linkMultiplicity: link.mult,
        MODRelationFromId: MODRelation.MODRelationId
    });
    MODRelation.MODTableToId = modTableTo.MODTableId;
    var MODFieldsTo = modTableTo.MODFields;
    for (var i in MODFieldsTo) {
        if (MODFieldsTo[i].DBFieldName === link.fieldname) {
            var MODFieldTo = MODFieldsTo[i];
        }
    }
    MODRelation.MODFieldToId = MODFieldTo.MODFieldId;
    MODRelation.MODRelationMult = link.mult;
    localStorage.setItem("MODRelation_" + MODRelation.MODRelationId, JSON.stringify(MODRelation));
    renderMODTable(modTableTo);
    adjustMODTable(modTableTo.MODTableId);
    adjustMODTables();
});

/*
 * RUOLI
 */
// apre e chiude scelta ruoli
$(document).delegate(".MODElementRole", "click", function() {
    var rolebutton = $(this);
    var DOMMODElement = rolebutton.parents(".MODElement");
    var MODElementId = DOMMODElement.attr("id");
    var MODElement = JSON.parse(localStorage.getItem("MODElement_" + MODElementId));
    if (typeof (MODElement.isKey) !== "undefined" && MODElement.isKey === true) {
        console.log("id");
        return;
    }
    else {
        var roleselector = $("#MODElementRoleSelector_" + MODElementId);
        if (roleselector.length >= 1) {
            roleselector.empty().remove();
        }
        else {
            var position = DOMMODElement.position();
            position.top = position.top + 40;
            position.left = position.left + 220;
            var roleselector = $("<div>")
                    .attr({
                        class: "MODElementRoleSelector",
                        id: "MODElementRoleSelector_" + MODElementId
                    })
                    .css({
                        top: position.top,
                        left: position.left
                    });
            if (typeof (MODElement.links) !== "undefined" && MODElement.links.count > 0) {
                var roles = ["PR", "AT", "LI", "RE"];
            }
            else {
                var roles = ["PR", "AT", "LI"];
            }

            for (var i in roles) {
                var rolesquare = $("<div>")
                        .attr({
                            class: "MODElementRoleSquare",
                            id: "MODElementRoleSquare_" + roles[i] + "_" + MODElementId
                        })
                        .css({
                            "background-position-x": "-" + Number((Number(i) + 1) * 40) + "px"
                        });
                roleselector.append(rolesquare);
            }
            $("#composer").append(roleselector);
            return;
        }
    }
});
//scelta del ruolo
$(document).delegate(".MODElementRoleSquare", "click", function() {
    var squareid = $(this).attr("id");
    var squareid2 = squareid.split("_");
    var MODElementId = squareid2[squareid2.length - 1];
    var role = squareid2[squareid2.length - 2];
    MODElementApplyRole(role, MODElementId)
    $(this).parents(".MODElementRoleSelector").empty().remove();
}
);
function MODElementApplyRole(role, MODElementId) {
    console.log("applyrole");
    console.log(role);
    console.log(MODElementId);
    if (role === "ID") { return; }
    var colors = {"ID": "#ff4a4a", "PR": "#4fff4f", "AT": "#ffe16d", "LI": "#ff9953", "RE": "#94fffb"};
    var extroles = {"ID": "Identificativo", "PR": "Principale", "AT": "Attributo", "LI": "Lista-dettaglio", "RE": "Riferimento"};
    if (role !== "RE") {
        var DOMMODElement = $("#" + MODElementId);
        console.log(DOMMODElement);
        var MODElement = MODElementIdToMODElement(MODElementId);
        console.log(MODElement);
        DOMMODElement.find(".MODElementTitle").css("background", colors[role]);
        console.log(DOMMODElement.find(".MODElementTitle").css("background"));
            DOMMODElement.find(".MODElementRole").html("Ruolo: " + extroles[role] + " >>");
        MODElement.MODElementRole = role;
        localStorage.setItem("MODElement_" + MODElementId, JSON.stringify(MODElement));
    }
    else {
        console.log("RE"); //trasformazione! TBI!
        var DOMMODElement = $("#" + MODElementId);
        var DOMMODRelation = DOMMODElementToDOMMODRelation(DOMMODElement);
    }
}
//apre e chiude scelta filtri
$(document).delegate(".MODElementFilter", "click", function() {
    var filterbutton = $(this);
    var DOMMODElement = filterbutton.parents(".MODElement");
    var MODElement = DOMMODToMODElement(DOMMODElement);
    var DBField = DOMMODElementToDBField(DOMMODElement);
    showloader();
    $(".backfilter").show();
    initializeFilter({
        completeFieldName: DBField.DBTable + "." + DBField.DBFieldName,
        fieldName: MODElement.MODElementName,
        MODElementId: MODElement.MODElementId
    });
    $(".MODElementFilterPanel").show("fold", 500);
    hideloader();
});



/*
 * Filtri
 */
//fa scrollare la barra laterale insieme alla pagina
$(window).delegate($(window), "scroll", function() {
    $("ul.MODElementFilterTokens").css({top: $(window).scrollTop()});
});

$(document).delegate(".MODElementFilterDelete", "click", function() {
    var filter = $(this).parents(".MODElementFilterPanelFilter");
    var newfilter2 = filter.siblings(".MODElementFilterPanelNewFilter:eq(1)");
    var group = $(this).parents(".MODElementFilterPanelGroup");
    filter.empty().remove();
    if (group.find(".MODElementFilterPanelFilter").length === 0) {
        newfilter2.hide(500);
    }
});
$(document).delegate(".MODElementFilterGroupDelete", "click", function() {
    var group = $(this).parents(".MODElementFilterPanelGroup");
    var groupsib = group.siblings(".MODElementFilterPanelGroup");
    if (groupsib.length === 0) {
        group.siblings(".MODElementFilterPanelNewGroup").eq(1).hide();
    }
    group.empty().remove();
});
$(document).delegate(".MODElementFilterButtonCancel", "click", function() {
    showloader();
    var filterpanel = $(".MODElementFilterPanel");
    var groups = filterpanel.find(".MODElementFilterPanelGroup");

    filterpanel.hide("fold", 1000);
    $(".backfilter").hide();
    groups.remove();
    hideloader();
});
$(document).delegate(".MODElementFilterOperator", "change", function() {
    $(this).css("background", "#DDD");
});
$(document).delegate(".MODElementFilterButtonOk", "click", function() {
    showloader();
    var filterpanel = $(this).parents(".MODElementFilterPanel");
    var MODElementId = filterpanel.find(".MODElementFilterPanelMODElementId").html();
    var groups = filterpanel.find(".MODElementFilterPanelGroup");
    var filtersdata = new Array();
    var error = false;
    groups.each(function() {
        var group = {};
        var filters = $(this).find(".MODElementFilterPanelFilter");
        var filtersslice = new Array();
        filters.each(function() {
            var filt = {};
            filt.first = "{FIELD}";
            if ($(this).find(".MODElementFilterOperator").val() === '-1') {
                $(this).find(".MODElementFilterOperator").css("background", "red");
                error = true;
                return;
            }
            filt.op = $(this).find(".MODElementFilterOperator").val();
            filt.second = "'" + $(this).find(".MODElementFilterField2Name").val() + "'";

            var cong = $(this).find(".MODElementFilterPanelJoin");
            if (cong.length === 0) {
                var congiunzione = "AND";
            }
            else if (cong.html() === "AND") {
                var congiunzione = "AND";
            }
            else {
                var congiunzione = "OR";
            }
            filt.con = congiunzione;
            filtersslice.push(filt);
        });
        if (error) {
            hideloader();
            return;
        }
        group.filters = filtersslice;
        var cong = $(this).find(".MODElementFilterGroupJoin");
        if (cong.length === 0) {
            var congiunzione = "AND";
        }
        else if (cong.html() === "AND") {
            var congiunzione = "AND";
        }
        else {
            var congiunzione = "OR";
        }
        group.con = congiunzione;
        filtersdata.push(group);
    });
    if (error)
        return;
    applyFilterToElement(MODElementId, filtersdata);
    groups.remove();
});

function initializeFilter(options) {
    var filterpanel = $(".MODElementFilterPanel");
    filterpanel.find("#MODElementFilterPanelTitle").html(options['completeFieldName'] + " AS " + options['fieldName']);
    filterpanel.find(".MODElementFilterField1Name").html(options['fieldName']);
    filterpanel.find(".MODElementFilterPanelMODElementId").html(options['MODElementId']);
    var MODElement = MODElementIdToMODElement(options['MODElementId']);
    var filtersdata = MODElement.MODElementFilter;
    console.log("INIZIALIZZO FILTRI");
    console.log(filtersdata);
    if (typeof (filtersdata.length) === "undefined" || filtersdata.length === 0)
        return;
    var newGroup2 = filterpanel.find(".MODElementFilterPanelNewGroup:eq(1)").show();
    for (var g in filtersdata) {
        var group = putNewGroup(newGroup2, "before");
        var newFilter2 = group.find(".MODElementFilterPanelNewFilter:eq(1)").show();
        var conPlaceholder = group.find(".MODElementFilterPanelNewGroupJoin");
        putNewGroupCon(conPlaceholder, filtersdata[g].con.toLowerCase());
        var filtersslice = filtersdata[g].filters;
        for (var f in filtersslice) {
            var filter = putNewStaticFilter(newFilter2, "before");
            if (filtersslice[f].first === "{FIELD}")
                filtersslice[f].first = options['fieldName'];
            filter.find(".MODElementFilterField1Name").html(filtersslice[f].first);
            filter.find(".MODElementFilterOperator").val(filtersslice[f].op);
            filtersslice[f].second = filtersslice[f].second.substr(1, filtersslice[f].second.length - 2);
            filter.find(".MODElementFilterField2Name").val(filtersslice[f].second);
            var conPlaceholder = filter.find(".MODElementFilterPanelNewFilterJoin");
            putNewFilterCon(conPlaceholder, filtersslice[f].con.toLowerCase());
        }
    }
}

function applyFilterToElement(MODElementId, filtersdata) {
    var filterpanel = $(".MODElementFilterPanel");

    console.log("APPLICO FILTRI");
    console.log(filtersdata);
    var groupNum = filtersdata.length;
    var filterNum = 0;
    for (var i in filtersdata) {
        if (filtersdata[i].filters.length === 0) {
            filtersdata.splice(i, 1);
        }
        else {
            var filtersslice = filtersdata[i].filters;
            filterNum += filtersslice.length;
        }
    }
    if (groupNum * filterNum === 0)
        filtersdata = {};

    var DOMMODElement = $("#" + MODElementId);
    var MODElement = MODElementIdToMODElement(MODElementId);
    MODElement.MODElementFilter = filtersdata;
    if (groupNum * filterNum === 0) {
        DOMMODElement.find(".MODElementFilter").html("Filtri: -nessuno- >>");
    }
    else {
        DOMMODElement.find(".MODElementFilter").html("Filtri: " + filterNum + " filtri in " + groupNum + " gruppi >>");
    }
    localStorage.setItem("MODElement_" + MODElementId, JSON.stringify(MODElement));
    $(".MODElementFilterPanel").hide("fold", 500);
    $(".backfilter").hide();
    hideloader();

}
function putNewGroupCon(conPlaceholder, con) {
    var newcon = $("<div>")
            .attr("class", "MODElementFilterPanelGroupJoin");
    if (con === "and") {
        conPlaceholder.siblings(".MODElementFilterPanelGroupJoin").remove();
        conPlaceholder.before(newcon.html("AND"));
    }
    if (con === "or") {
        conPlaceholder.siblings(".MODElementFilterPanelGroupJoin").remove();
        conPlaceholder.before(newcon.html("OR"));
    }
    newcon.show("explode", 500);
    return newcon;
}
function putNewGroup(groupPlaceholder, aftBef) {

    var newgroup = $(".MODElementFilterPanelContainer")
            .find(".MODElementFilterPanelGroupTemplate")
            .clone()
            .removeAttr("class")
            .attr("class", "MODElementFilterPanelGroup")
    switch (aftBef) {
        case "after":
            groupPlaceholder.after(newgroup);
            break;
        case "before":
            groupPlaceholder.before(newgroup);
            break;
        default:
            return;
    }
    newgroup.sortable({
        items: ".MODElementFilterPanelFilter",
        placeholder: "MODElementFilterPanelNewFilter"
    });
    newgroup.show("explode", 500);
    newgroup.find(".MODElementFilterPanelNewGroupJoin")
            .droppable({//drop su nuova congiunzione gruppo
                accept: "#MODElementFilterTokens_and, #MODElementFilterTokens_or",
                over: function(event, ui) {
                    $(this).css("opacity", "1");
                },
                out: function(event, ui) {
                    $(this).css("opacity", "0.5");
                },
                drop: function(event, ui) {
                    $(this).css("opacity", "0.5");
                    putNewGroupCon($(this), ui.draggable.attr("id").replace("MODElementFilterTokens_", ""))
                }
            });
    newgroup.find(".MODElementFilterPanelNewFilter:eq(0)")
            .droppable({//drop su nuovo filtro 1
                accept: "#MODElementFilterTokens_newstatic",
                greedy: true,
                tolerance: "touch",
                over: function(event, ui) {
                    $(this).css("opacity", "1");
                },
                out: function(event, ui) {
                    $(this).css("opacity", "0.5");
                },
                drop: function(event, ui) {
                    $(this).css("opacity", "0.5");
                    if (ui.draggable.attr("id") === "MODElementFilterTokens_newstatic") {
                        if (ui.draggable.attr("id") === "MODElementFilterTokens_newstatic") {
                            putNewStaticFilter($(this), "after");
                            $(this).siblings("[class^='" + $(this).attr("class") + "']").show(500);
                        }
                    }
                }
            });
    newgroup.find(".MODElementFilterPanelNewFilter:eq(1)")
            .droppable({//drop su nuovo filtro 2
                accept: "#MODElementFilterTokens_newstatic",
                greedy: true,
                tolerance: "touch",
                over: function(event, ui) {
                    $(this).css("opacity", "1");
                },
                out: function(event, ui) {
                    $(this).css("opacity", "0.5");
                },
                drop: function(event, ui) {
                    $(this).css("opacity", "0.5");
                    if (ui.draggable.attr("id") === "MODElementFilterTokens_newstatic") {
                        if (ui.draggable.attr("id") === "MODElementFilterTokens_newstatic") {
                            putNewStaticFilter($(this), "before");
                            $(this).siblings("[class^='" + $(this).attr("class") + "']").show(500);
                        }
                    }
                }
            });
    return newgroup;
}
function putNewStaticFilter(filterPlaceholder, aftBef) {
    var newfilter = $(".MODElementFilterPanelContainer")
            .find(".MODElementFilterPanelFilterTemplate")
            .clone()
            .removeAttr("class")
            .attr("class", "MODElementFilterPanelFilter");
    switch (aftBef) {
        case "after":
            filterPlaceholder.after(newfilter);
            break;
        case "before":
            filterPlaceholder.before(newfilter);
            break;
        default:
            return;
    }
    newfilter.show("explode", 500);
    newfilter.find(".MODElementFilterPanelNewFilterJoin")
            .droppable({
                accept: "#MODElementFilterTokens_and, #MODElementFilterTokens_or",
                over: function(event, ui) {
                    $(this).css("opacity", "1");
                },
                out: function(event, ui) {
                    $(this).css("opacity", "0.5");
                },
                drop: function(event, ui) {
                    $(this).css("opacity", "0.5");
                    putNewFilterCon($(this), ui.draggable.attr("id").replace("MODElementFilterTokens_", ""))
                }
            });
    return newfilter;
}

function putNewFilterCon(conPlaceholder, con) {
    var newcon = $("<div>")
            .attr("class", "MODElementFilterPanelFilterJoin");
    if (con === "and") {
        conPlaceholder.siblings(".MODElementFilterPanelFilterJoin").remove();
        conPlaceholder.before(newcon.html("AND"));
    }
    if (con === "or") {
        conPlaceholder.siblings(".MODElementFilterPanelFilterJoin").remove();
        conPlaceholder.before(newcon.html("OR"));
    }
    newcon.show("explode", 500);
    return newcon;
}

// drag & drop filtri
$(function() {
    $("li.MODElementFilterTokens").draggable({
        helper: "clone",
//        function(){
//                return $("<div></div>").attr("class","MODElementFilterPanelGroup")
//                        .html("Nuovo gruppo");
//            },
        cursor: "default",
        zIndex: 1000,
        start: function(ui, event) {
        },
        stop: function(ui, event) {
        }
//        connectToSortable: ".MODElementFilterPanelContainer"

    });

    $("ul.MODElementFilterTokens").disableSelection();
    $(".MODElementFilterPanelContainer").sortable({
        items: ".MODElementFilterPanelGroup",
        placeholder: "MODElementFilterPanelNewGroup"
    });
    $(".MODElementFilterPanelNewGroup:eq(0)").droppable({//drop su nuovo gruppo 1
        accept: "#MODElementFilterTokens_newgroup",
        greedy: true,
        tolerance: "touch",
        over: function(event, ui) {
            $(this).css("opacity", "1");
        },
        out: function(event, ui) {
            $(this).css("opacity", "0.5");
        },
        drop: function(event, ui) {
            if (ui.draggable.attr("id") === "MODElementFilterTokens_newgroup") {
                putNewGroup($(this), "after");
                $(this).siblings("[class^='" + $(this).attr("class") + "']").show(500);
            }
        }
    });
    $(".MODElementFilterPanelNewGroup:eq(1)").droppable({//drop su nuovo gruppo 2
        accept: "#MODElementFilterTokens_newgroup",
        greedy: true,
        tolerance: "touch",
        over: function(event, ui) {
            $(this).css("opacity", "1");
        },
        out: function(event, ui) {
            $(this).css("opacity", "0.5");
        },
        drop: function(event, ui) {
            if (ui.draggable.attr("id") === "MODElementFilterTokens_newgroup") {
                putNewGroup($(this), "before");
                $(this).siblings("[class^='" + $(this).attr("class") + "']").show(500);
            }
        }
    });
});


/*
 * Gestione Livelli Visualizzazione
 */

$(document).delegate("#MODElementFilterPanel", "DOMSubtreeModified", function(event) {
    var baselevel = Number($(this).css("z-index"));
    var elements = $(this).find("*");
    elements.each(function() {
        var z = $(this).css("z-index");
        if (z + "" === "auto") {
            $(this).css("z-index", baselevel);
        }
        else {
            $(this).css("z-index", baselevel + (Number(z) % 1000));
        }
    });
});

$(document).delegate("#navigator", "DOMSubtreeModified", function(event) {
    var baselevel = Number($(this).css("z-index"));
    var elements = $(this).find("*");
    elements.each(function() {
        var z = $(this).css("z-index");
        if (z + "" === "auto") {
            $(this).css("z-index", baselevel);
        }
        else {
            $(this).css("z-index", baselevel + (Number(z) % 1000));
        }
    });
});

$(document).delegate("#composer", "DOMSubtreeModified", function(event) {
    var baselevel = Number($(this).css("z-index"));
    var elements = $(this).find("*");
    elements.each(function() {
        var z = $(this).css("z-index");
        if (z + "" === "auto") {
            $(this).css("z-index", baselevel);
        }
        else {
            $(this).css("z-index", baselevel + (Number(z) % 1000));
        }
    });
});

function showloader() {
    $(".loader").show("fade", 500);
}
function hideloader() {
    $(".loader").hide("fade", 500);
}