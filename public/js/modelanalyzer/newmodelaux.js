/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

function hashing(len) {
    var hash = localStorage.getItem("hash");
    var danger = new Array("is");
    if (typeof (hash) === "undefined" || hash === null) {
        hash = "{}";
    }
//    console.log(hash);
    hash = JSON.parse(hash);

    var lettere = new Array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
    while (true) {
        var stringa = "";
        for (var i = 0; i < len; i++) {
            stringa += lettere[Math.floor(Math.random() * lettere.length)];
        }
        if (danger.indexOf(stringa) > -1) continue;
        if (typeof (hash[stringa]) === "undefined" || hash[stringa] === null) {
            hash[stringa] = stringa;
            break;
        }
    }
    localStorage.setItem("hash", JSON.stringify(hash));
    return stringa;
}


function check_object(obj, template) {
    for (var i in template) {
        if (typeof (obj[i]) === "undefined")
            return false;
    }
    return true;
}

function updatestorage(elm, what) {
    console.log("bind!");
    var jelm = $(elm);
    if (typeof (jelm.attr("id")) === "undefined") // deve avere un id
        return;
    var id = jelm.attr("id");
    switch (what) {
        case "value":
            localStorage.setItem(id, jelm.val());
            break;
        case "text":
            localStorage.setItem(id, jelm.text());
            break;
        case "html":
            localStorage.setItem(id, jelm.html());
            break;
    }
}

function storageSetTable(table) {
    if (typeof (table.dbn) === "undefined" || typeof (table.dbname) === "undefined" || typeof (table.tablename) === "undefined")
        return -1;
    if (typeof (table.tablefrom) === "undefined" || typeof (table.fieldfrom) === "undefined" || typeof (table.fieldto) === "undefined")
        return -1;
    if (typeof (table.fields) === "undefined" || typeof (table.mult) === "undefined")
        return -1;
    console.log("bind!");
    var jelm = $(elm);
    if (typeof (jelm.attr("id")) === "undefined") // deve avere un id
        return;
    var id = jelm.attr("id");
    switch (what) {
        case "value":
            localStorage.setItem(id, jelm.val());
            break;
        case "text":
            localStorage.setItem(id, jelm.text());
            break;
        case "html":
            localStorage.setItem(id, jelm.html());
            break;
    }
}

function MODElementForStorage(MODElement) {
    var MODTable = MODTableIdToMODTable(MODElement.MODTableId);
    var MODFields = MODTable.MODFields;
    for (var i in MODFields) {
        if (MODFields[i].MODFieldId === MODElement.MODFieldId) {
            var MODField = MODFields[i];
            break;
        }
    }
    var storage = {
        fieldname: MODField.DBFieldName,
        alias: MODElement.MODElementName,
        table: MODTable.MODTableId,
        role: MODElement.MODElementRole,
        filters: MODElement.MODElementFilter
    };
    return storage;
}

function MODTableForStorage(MODTable, type) {
    if (type === "tables") {
        var DBTable = JSON.parse(localStorage.getItem("DBTable_" + MODTable.DBTable));
        var fields = new Array();
        var modElements = MODTable.MODElements;
        for (var i in modElements) {
            fields.push("field_" + modElements[i]);
        }
        if (typeof (MODTable.MODRelationFromId) !== "undefined") {
            var DOMMODRelationFrom = $("#" + MODTable.MODRelationFromId);
            var MODRelationPMcheck = DOMMODRelationFrom.find(".MODRelationPMcheck");
            var MODRelationPMGV = DOMMODRelationFrom.find(".MODRelationPMGV");
            var pm = (MODRelationPMcheck.prop("checked") === true);
            var gv = MODRelationPMGV.val();
        }
        else {
            var pm = true;
            var gv = "ND";
        }
        var storage = {
            dbname: DBTable.DBName,
            dbnum: DBTable.DBNum,
            tablename: DBTable.DBTableName,
            hasid: "",
            id: "",
            tablefrom: "table_" + MODTable.parentMODTableId,
            fieldrom: MODTable.parentLinkDBFieldName,
            fieldto: MODTable.linkDBFieldName,
            linkmult: MODTable.linkMultiplicity,
            partialmatching: pm,
            genericvalueformissingmatch: gv,
            fields: fields.join("|")
        };
    }
    else if (type === "structure") {
        var storage = {
            onetoone: "",
            onetomany: "",
            manytoone: ""
        }
    }
    return storage;
}


function getSavedModel_DBTable(key, DBTable, data) { //DBTable è stringa
    if (localStorage.getItem("DBTable_" + key) === null) {
        localStorage.setItem("DBTable_" + key, DBTable);
    }
}
function getSavedModel_MODTable(key, MODTable, data) { // MODTable è stringa
    if (localStorage.getItem("MODTable_" + key) === null) {
        localStorage.setItem("MODTable_" + key, MODTable);
    }
    MODTable = JSON.parse(MODTable);
    getSavedModel_DBTable(MODTable.DBTable, data["DBTable_" + MODTable.DBTable], data);
    renderMODTable(MODTable);
    adjustMODTable(MODTable.MODTableId);
    adjustMODTables();
    var MODElements = MODTable.MODElements;
    for (var k in MODElements) {
        var MODElement = data["MODElement_" + MODElements[k]];
        localStorage.setItem("MODElement_" + MODElements[k], MODElement);
        MODElement = JSON.parse(MODElement);
//        console.log(MODElement);
        var DOMMODElement = renderMODElement(MODElement);
        MODElementApplyRole(MODElement.MODElementRole, MODElement.MODElementId);
        applyFilterToElement(MODElement.MODElementId,MODElement.MODElementFilter);
        openCloseElement(DOMMODElement);
        openCloseElement(DOMMODElement);
    }
    var MODRelations = MODTable.MODRelations;
    for (var k in MODRelations) {
        var MODRelation = data["MODRelation_" + MODRelations[k]];
        localStorage.setItem("MODRelation_" + MODRelations[k], MODRelation);
        MODRelation = JSON.parse(MODRelation);
//        console.log(MODRelation);
        if (MODRelation.MODTableToId !== null) {
            var MODTableTo = data["MODTable_" + MODRelation.MODTableToId];
            getSavedModel_MODTable(MODRelation.MODTableToId, MODTableTo, data);
        }
        var DOMMODRelation = renderMODRelation(MODRelation);
        openCloseRelation(DOMMODRelation);
        openCloseRelation(DOMMODRelation);
    }
}

function getSavedModel() {
    var baseurl = document.URL;
    var modelname = baseurl.split("/newmodel/");
    modelname = modelname[1].split("/");
    modelname = modelname[1];
    baseurl = baseurl.replace("newmodel", "retrievemodel");
    baseurl += ".ws";

    var aj = $.ajax({
        type: "GET",
        url: baseurl,
        success: function(data) {

            if (data === null || typeof (data["DBTables"]) === "undefined") {
                $("#modelname").val(modelname);
                return;
            }
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
            $("#btnSave").show();
            $("#btnPreview").show().click(function() {
                preview();
            });
            var firstform = data["first-form"];
            $("#first-form").html(firstform).hide();
            var maintable = data["maintable"];
            var MODTable = data["MODTable_" + maintable];
//            console.log(MODTable);
//            console.log(JSON.parse(MODTable));
            getSavedModel_MODTable(maintable, MODTable, data);
            localStorage.setItem("DBTables", data["DBTables"]);
            localStorage.setItem("first-form", data["first-form"]);
            localStorage.setItem("hash", data["hash"]);
            localStorage.setItem("maintable", data["maintable"]);
            localStorage.setItem("model", data["model"]);
            $("#modelname").val(modelname);
            return;

        },
        dataType: "json"
    });

}

function getdbs(baseurl) {
    var baseurl = document.URL;
    baseurl = baseurl.split("modelanalyzer");
    baseurl = baseurl[0];
    baseurl += "dbanalyzer/";
    var aj = $.ajax({
        url: baseurl + "getdbs" + ".ws",
        headers: {
            "Accept": "application/json, text/html;q=0.5",
            "Content-Type": "application/json"
        },
        async: false

    })
            .done(function(data) {
//                            return JSON.parse(data);
                return data;
            });
    return aj.responseText;
}
function gettabs(baseurl, dbn) {
    var baseurl = document.URL;
    baseurl = baseurl.split("modelanalyzer");
    baseurl = baseurl[0];
    baseurl += "dbanalyzer/";
    var aj = $.ajax({
        url: baseurl + "gettabs/" + dbn + ".ws",
        headers: {
            "Accept": "application/json, text/html;q=0.5",
            "Content-Type": "application/json"
        },
        async: false

    })
            .done(function(data) {
//                            return JSON.parse(data);
                return data;
            });
    return aj.responseText;
}
function getfields(baseurl, dbn, tab) {
    var baseurl = document.URL;
    baseurl = baseurl.split("modelanalyzer");
    baseurl = baseurl[0];
    baseurl += "dbanalyzer/";
    var aj = $.ajax({
        url: baseurl + "getfields/" + dbn + "/" + tab + ".ws",
        headers: {
            "Accept": "application/json, text/html;q=0.5",
            "Content-Type": "application/json"
        },
        async: false

    })
            .done(function(data) {
//                            return JSON.parse(data);
                return data;
            });
    return aj.responseText;
}
function getlinks(baseurl, dbn, tab, field) {
    var baseurl = document.URL;
    baseurl = baseurl.split("modelanalyzer");
    baseurl = baseurl[0];
    baseurl += "dbanalyzer/";
    var aj = $.ajax({
        url: baseurl + "getlinks/" + dbn + "/" + tab + "/" + field + ".ws",
        headers: {
            "Accept": "application/json, text/html;q=0.5",
            "Content-Type": "application/json"
        },
        async: false

    })
            .done(function(data) {
//                            return JSON.parse(data);
                return data;
            });
    return aj.responseText;
}

function addToModel(key, what, path) { //path è il percorso al nodo padre
    var model = localStorage.getItem("model");
    if (model === null || typeof (model) === "undefined") {
        var model = {
            maintable: "",
            tables: {},
            fields: {},
            structure: {}
        };
    }
    else {
        model = JSON.parse(model);
    }
    var pathexp = path.split("->");
    var node = model;
    if (pathexp[0] !== "") {
        for (var i in pathexp) {
            if (typeof (node[pathexp[i]]) === "undefined" || node[pathexp[i]] === "") {
                node[pathexp[i]] = {};
            }
            node = node[pathexp[i]];
        }
    }
    node[key] = what;
    localStorage.setItem("model", JSON.stringify(model));
}

function getFromModel(key, path) { //path è il percorso al nodo padre
    var model = localStorage.getItem("model");
    if (typeof (model) === "undefined" || model === null) {
        return null;
    }
    else {
        model = JSON.parse(model);
    }
    var pathexp = path.split("->");
    var node = model;
    for (var i in pathexp) {
        if (typeof (node[pathexp[i]]) === "undefined") {
            node[pathexp[i]] = {};
        }
        node = node[pathexp[i]];
    }
    if (key === "") {
        return node;
    }
    if (typeof (node[key]) !== undefined) {
        return node[key];
    }
    else {
        return null;
    }
}

function MODTableToModel(MODTable) {
    addToModel("table_" + MODTable.MODTableId, MODTableForStorage(MODTable, "tables"), "tables");
    addToModel("table_" + MODTable.MODTableId, MODTableForStorage(MODTable, "structure"), MODTable.pathInModel);
    var MODElements = MODTable.MODElements;
    var fields = new Array();
    console.log(MODElements);
    for (var i in MODElements) {
        var MODElement = MODElementIdToMODElement(MODElements[i]);
        addToModel("field_" + MODElement.MODElementId, MODElementForStorage(MODElement), "fields");
    }
    var MODRelations = MODTable.MODRelations;
    for (var i in MODRelations) {
        var MODRelation = MODRelationIdToMODRelation(MODRelations[i]);
        MODTableToModel(MODTableIdToMODTable(MODRelation.MODTableToId));
    }
}

function preview() {
    console.log("-------------------------------------------preview");
    var modelname = $("#modelname").val();
    if (modelname === "") {
//        $("#modelname").css("background", "red");
//        return;
        var modelname = "model";
    }
    if (localStorage.getItem("model") === null) {
        localStorage.setItem("model", "{}");
    }
    var maintableId = localStorage.getItem("maintable");
    if (maintableId === null) {
        console.log("Error: can't detect maintable.");
        return;
    }
    var maintableMODTable = MODTableIdToMODTable(maintableId);
    console.log(maintableMODTable);
    addToModel("maintable", "table_" + maintableMODTable.MODTableId, "");
    MODTableToModel(maintableMODTable);
    makeSelectAll();
    console.log(localStorage.getItem("model"));
    var model = JSON.parse(localStorage.getItem("model"));
    $("#preview").html((model.selectAll));
    return;
}

function save() {
    var modelname = $("#modelname").val();
    if (modelname === "") {
//        $("#modelname").css("background", "red");
//        return;
        var modelname = "model";
    }

    localStorage.setItem("modelname", modelname);
    console.log(JSON.stringify(localStorage));
    localStorage.setItem("first-form", $("#first-form").html());
    var baseurl = document.URL;
    baseurl = baseurl.replace("newmodel", "savenewmodel");
    baseurl += ".ws";
    var aj = $.ajax({
        type: "POST",
        url: baseurl,
        data: localStorage, //JSON.stringify(localStorage),
        success: function(data) {
//            console.log(data);
            $("#btnSave").css("background", "green");
            var baseurl = document.URL;
            console.log(baseurl);
            baseurl = baseurl.split("/newmodel/");
            console.log(baseurl);
            var endbaseurl = baseurl[1].split("/");
            console.log(endbaseurl);
            baseurl = baseurl[0] + "/newmodel/" + endbaseurl[0] + "/" + modelname;
            console.log(baseurl);
            window.location.href = baseurl;
        },
        dataType: "json"
    });
}
