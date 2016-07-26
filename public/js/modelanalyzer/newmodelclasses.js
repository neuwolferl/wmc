function DBField(options) {
    this.DBTable = options["DBTable"];
    this.DBFieldName = options["DBFieldName"];
    this.isKey = options["isKey"];
    this.type = options["type"];
}

function DBTable(options) {
    this.DBNum = options["DBNum"];
    this.DBName = options["DBName"];
    this.DBTableName = options["DBTableName"];
    this.DBFields = new Array();
    this.MODTables = new Array();
    var baseurl = document.URL;
    baseurl = baseurl.split("modelanalyzer");
    baseurl = baseurl[0];
    baseurl += "dbanalyzer/";
    var fields = JSON.parse(getfields(baseurl, this.DBNum, this.DBTableName));
    if (typeof (fields.count) === "undefined") {
        alert("No fields infos for this table");
        return;
    }
    for (var i = 0; i < fields.count; i++) {
        this.DBFields.push(
                new DBField({
                    DBTable: this.DBName + "." + this.DBTableName,
                    DBFieldName: fields[i].name,
                    isKey: (fields[i].details.key === "PRI") ? true : false,
                    type: fields[i].details.type,
                })
                );
    }
    localStorage.setItem("DBTable_" + this.DBName + "." + this.DBTableName, JSON.stringify(this));
    var DBTables = localStorage.getItem("DBTables");
    if (DBTables === null) {
        DBTables = new Array();
    }
    else {
        DBTables = JSON.parse(DBTables);
    }
    DBTables.push(this.DBName + "." + this.DBTableName);
    localStorage.setItem("DBTables", JSON.stringify(DBTables));
}

function MODField(options) {
    this.MODFieldId = options["MODFieldId"];
    this.MODTableId = options["MODTableId"];
    this.DBFieldName = options["DBFieldName"];
}






function MODTable(options) {
    var hash = hashing(10);
    this.DBTable = options["DBTable"];
    this.MODTableId = hash;
    this.parentMODTableId = options["parentMODTableId"];
    this.linkDBFieldName = options["linkDBFieldName"];
    this.parentLinkDBFieldName = options["parentLinkDBFieldName"];
    this.pathInModel = options["pathInModel"];
    this.linkMultiplicity = options["linkMultiplicity"];
    this.MODFields = new Array();
    this.MODElements = new Array();
    this.MODRelations = new Array();
    var tabella = localStorage.getItem("DBTable_" + this.DBTable);
    tabella = JSON.parse(tabella);
    tabella.MODTables.push(this.MODTableId);
    var dbfields = tabella.DBFields;
    for (var i = 0; i < dbfields.length; i++) {
        this.MODFields.push(
                new MODField({
                    MODFieldId: hashing(5),
                    MODTableId: this.MODTableId,
                    DBFieldName: dbfields[i].DBFieldName
                })
                );
    }
    if (typeof (options["MODRelationFromId"]) !== "undefined") {
        this.MODRelationFromId = options["MODRelationFromId"];
    }

    localStorage.setItem("DBTable_" + this.DBTable, JSON.stringify(tabella));
    localStorage.setItem("MODTable_" + hash, JSON.stringify(this));
}

function MODElement(options) {
    if (typeof (options["hash"]) === "undefined")
        var hash = hashing(15);
    else
        var hash = options["hash"];
    this.MODElementId = hash;
    this.MODFieldId = options["MODFieldId"];
    this.MODTableId = options["MODTableId"];
    var MODTable = MODTableIdToMODTable(this.MODTableId);
    var MODFields = MODTable.MODFields;
    for (var i in MODFields) {
        if (MODFields[i].MODFieldId === this.MODFieldId) {
            var MODField = MODFields[i];
            break;
        }
    }

    this.MODElementName = MODField.DBFieldName;
    this.MODElementRole = "";
    this.MODElementFilter = new Array();
    this.MODElementOperation = "";
    this.isOpen = true;
    var DBTable = JSON.parse(localStorage.getItem("DBTable_" + MODTable.DBTable));
    var links = JSON.parse(getlinks("baseurl", DBTable.DBNum, DBTable.DBTableName, MODField.DBFieldName));
    if (typeof (links.count) !== "undefined" && links.count > 0) {
        this.links = links;
    }
    if (typeof (options["isKey"]) !== "undefined" && options["isKey"] === true) {
        this.isKey = true;
        this.MODElementRole = "ID";
    }


}

function MODRelation(options) {
    if (typeof (options["hash"]) === "undefined")
        var hash = hashing(15);
    else
        var hash = options["hash"];
    this.MODRelationId = hash;
    this.MODFieldFromId = options["MODFieldFromId"];
    this.MODTableFromId = options["MODTableFromId"];
    this.MODFieldToId = options["MODFieldToId"];
    this.MODTableToId = options["MODTableToId"];
    var MODTableFrom = MODTableIdToMODTable(this.MODTableFromId);
    var MODFields = MODTableFrom.MODFields;
    for (var i in MODFields) {
        if (MODFields[i].MODFieldId === this.MODFieldFromId) {
            var MODField = MODFields[i];
            break;
        }
    }
    this.MODRelationName = MODField.DBFieldName;
    this.position = options["position"];
    this.MODRelationPM = options["MODRelationPM"];
    this.MODRelationGenericValue = (options["MODRelationPM"] ? options["MODRelationGenericValue"] : "");
    this.links = options["links"];
    this.MODRelationMult = options["MODRelationMult"];
    this.isOpen = true;
}

function MODElementToMODRelation(MODElement) {
    var relation = new MODRelation({
        MODFieldFromId: MODElement.MODFieldId,
        MODTableFromId: MODElement.MODTableId,
        MODFieldToId: null,
        MODTableToId: null,
        MODRelationPM: null,
        MODRelationGenericValue: null,
        hash: MODElement.MODElementId,
        position: MODElement.position,
        links: MODElement.links,
        MODRelationPM: true,
                MODRelationGenericValue: "ND"
    });
    var MODTable = MODTableIdToMODTable(MODElement.MODTableId);
    var index = MODTable.MODElements.indexOf(MODElement.MODElementId);
    if (index >= 0) {
        MODTable.MODElements.splice(index, 1);
    }
    if (typeof (MODTable.MODRelations) === "undefined") {
        MODTable.MODRelations = new Array();
    }
    MODTable.MODRelations.push(relation.MODRelationId);
    localStorage.setItem("MODTable_" + relation.MODTableFromId, JSON.stringify(MODTable));
    localStorage.removeItem("MODElement_" + MODElement.MODElementId);
    localStorage.setItem("MODRelation_" + relation.MODRelationId, JSON.stringify(relation));
    return relation;
}

function DOMMODElementToDOMMODRelation(DOMMODElement) {
    var MODElement = DOMMODToMODElement(DOMMODElement);
    var MODRelation = MODElementToMODRelation(MODElement);
    var id = MODRelation.MODRelationId;
    var DOMMODRelation = $("<div>")
            .attr({
                class: "MODRelation",
                id: id
            })
            .append($("<div class=\"MODRelationBack\"></div>"))
            .append($("<div class=\"MODRelationOpen\" id=\"MODRelationOpen_" + id + "\"></div>"))
            .append($("<div class=\"MODRelationDelete\" id=\"MODRelationDelete_" + id + "\"></div>"))
            .append($("<div class=\"MODRelationVerticalLine\" id=\"MODRelationVerticalLine_" + id + "\"></div>"))
            .append($("<div class=\"MODRelationTitle\" id=\"MODRelationTitle_" + id + "\">" + MODRelation.MODRelationName + "</div>"))
            .append($("<div class=\"MODRelationBottom\" id=\"MODRelationBottom_" + id + "\">from: " + MODRelation.MODTableFromId + "</div>"))
            .append($("<div class=\"MODRelationChooseLink\" id=\"MODRelationChooseLink_" + id + "\"></div>")
                    .append($("<select class=\"MODRelationChooseLinkSelect\"></select>"))
                    .append($("<button type=\"button\" class=\"MODRelationChooseLinkButton\">Applica</button>"))
                    )
            .append($("<div class=\"MODRelationPM id=\"MODRelationPM_" + id + "\"></div>")
                    .append("PM<input type=\"checkbox\" checked = \"true\" class=\"MODRelationPMcheck\">&nbsp;&nbsp;GV:<input size=\"10\" value=\"ND\" class=\"MODRelationPMGV\" type=\"text\">"))
            .css({
                top: MODRelation.position.Y,
                left: MODRelation.position.X
            });
    var select = DOMMODRelation.find(".MODRelationChooseLinkSelect");
    select.append("<option value=\"-1\">Scegli</option>")
    for (var i = 0; i < MODRelation.links.count; i++) {
        var link = MODRelation.links[i];
        select.append("<option value=\"" + i + "\">" + link.mult + " " + link.dbtable + " via " + link.fieldname + " " + link.view + "</option>");
    }
    DOMMODElement.replaceWith(DOMMODRelation);
    $(".MODRelation").draggable({
        grid: [5, 5],
        containment: "parent",
        zIndex: 1000,
        handle: ".MODRelationTitle",
        cursor: "default",
        stop: function(event, ui) {
//            console.log(ui);
            var MODRelationId = ui.helper.attr("id");
            var MODRelation = JSON.parse(localStorage.getItem("MODRelation_" + MODRelationId));
            MODRelation.position.X = Math.floor(ui.position.left) + "px";
            MODRelation.position.Y = Math.floor(ui.position.top) + "px";
            localStorage.setItem("MODRelation_" + MODRelationId, JSON.stringify(MODRelation));
            $("#" + MODRelationId).css({
                top: MODRelation.position.Y,
                left: MODRelation.position.X
            });
        }
    });
    return DOMMODRelation;
}

function renderMODTable(MODTable) {
    var nav = $("#navigator");
    var DBTable = JSON.parse(localStorage.getItem("DBTable_" + MODTable.DBTable));
    var Table = $("<div>")
            .attr({
                id: MODTable.MODTableId,
                class: "MODTable"
            });
    var TableAlias = $("<div>")
            .attr({
                id: MODTable.MODTableId + "_Alias",
                class: "MODTableAlias"
            })
            .html(MODTable.MODTableId);
    var TableName = $("<div>")
            .attr({
                id: MODTable.MODTableId + "_TableName",
                class: "MODTableName"
            })
            .html(DBTable.DBName + "." + DBTable.DBTableName);
    var parentId = MODTable.parentMODTableId;
    var fieldfrom = MODTable.parentLinkDBFieldName;
    var fieldto = MODTable.linkDBFieldName;
    if (parentId === "") {
        var dependency = "main table - no dep";
    }
    else {
        var ParentMODTable = JSON.parse(localStorage.getItem("MODTable_" + parentId));
        var parentMODTableName = ParentMODTable.DBTable;
        var dependency = "from " + parentMODTableName + "." + fieldfrom + "<br>via " + fieldto;
    }
    var TableDependency = $("<div>")
            .attr({
                id: MODTable.MODTableId + "_Dependency",
                class: "MODTableDependency"
            })
            .html(dependency);
    var TableOpen = $("<div>")
            .attr({
                id: MODTable.MODTableId + "_Open",
                class: "MODTableOpen"
            })
            .html("-");
    var TableSep = $("<div>")
            .attr({
                id: MODTable.MODTableId + "_Sep",
                class: "MODTableSep"
            });
    var FieldList = $("<div>")
            .attr({
                id: MODTable.MODTableId + "_FieldList",
                class: "MODFieldList"
            });
    Table.append(TableAlias).append(TableName)
            .append(TableDependency).append(TableOpen)
            .append(TableSep).append(FieldList);
    var fields = MODTable.MODFields;
    var DBfields = DBTable.DBFields;
    for (var i in fields) {
        var Field = $("<div>")
                .attr({
                    id: fields[i].MODFieldId,
                    class: "MODField",
                    MODTableId: fields[i].MODTableId
                })
                .html(fields[i].DBFieldName);
        FieldList.append(Field);
        Field.draggable({
            containment: "window",
            cursor: "copy",
            scroll: false,
            helper: "clone"
        });
        var fielddetails = "";
        var DBFields = DBTable.DBFields;
        if (DBFields[i].isKey === true) {
            fielddetails += "PK<br>";
        }
        fielddetails += DBFields[i].type;
        var FieldDetails = $("<div>")
                .attr({
                    id: "MODFieldDetails_" + fields[i].MODFieldId,
                    class: "MODFieldDetails"
                })
                .html(fielddetails);
        FieldList.append(FieldDetails);
    }
    nav.append(Table);
    var MODTableForStorage1 = {
        dbname: DBTable.DBName,
        dbnum: DBTable.DBNum,
        tablename: DBTable.DBTableName,
        hasid: "",
        id: "",
        tablefrom: "table_" + MODTable.parentMODTableId,
        fieldrom: MODTable.parentLinkDBFieldName,
        fieldto: MODTable.linkDBFieldName,
        linkmult: MODTable.linkMultiplicity,
        partialmatching: ""
    };
    var MODTableForStorage2 = {
        onetoone: "",
        onetomany: "",
        manytoone: ""
    }
    addToModel("table_" + MODTable.MODTableId, MODTableForStorage1, "tables");
    addToModel("table_" + MODTable.MODTableId, MODTableForStorage2, MODTable.pathInModel);
}





function renderMODElement(MODElement, position) {
    var comp = $("#composer");
    var element = $("<div>")
            .attr({
                class: "MODElement",
                id: MODElement.MODElementId
            });
    var elementOpen = $("<div>")
            .attr({
                class: "MODElementOpen",
                id: "MODElementOpen_" + MODElement.MODElementId
            }); //.html("-");
    if (typeof (MODElement.isKey) === "undefined" || MODElement.isKey === false) {
        var elementDelete = $("<div>")
                .attr({
                    class: "MODElementDelete",
                    id: "MODElementDelete_" + MODElement.MODElementId
                });
    }
    else {
        var elementDelete = $("<div>")
                .attr({
                    class: "MODElementDeleteDisabled",
                    id: "MODElementDelete_" + MODElement.MODElementId
                });
    }
    var elementVerticalLine = $("<div>")
            .attr({
                class: "MODElementVerticalLine",
                id: "MODElementVerticalLine_" + MODElement.MODElementId
            });
    var elementTitle = $("<div>")
            .attr({
                class: "MODElementTitle",
                id: "MODElementTitle_" + MODElement.MODElementId
            }).html(MODElement.MODElementName);
    if (typeof (MODElement.isKey) !== "undefined" || MODElement.isKey === true) {
        elementTitle.css("background", "#ff4a4a");
    }
    var elementBottom = $("<div>")
            .attr({
                class: "MODElementBottom",
                id: "MODElementBottom_" + MODElement.MODElementId
            }).html("from: " + MODElement.MODTableId);
    if (typeof (MODElement.isKey) !== "undefined" || MODElement.isKey === true) {
        var elementRole = $("<div>")
                .attr({
                    class: "MODElementRole",
                    id: "MODElementRole_" + MODElement.MODElementId
                }).html("Ruolo: Identificativo");
    }
    else {
        var elementRole = $("<div>")
                .attr({
                    class: "MODElementRole",
                    id: "MODElementRole_" + MODElement.MODElementId
                }).html("Ruolo: assegna >>");
    }

    var elementFilter = $("<div>")
            .attr({
                class: "MODElementFilter",
                id: "MODElementFilter_" + MODElement.MODElementId
            }).html("Filtri: -nessuno- >>");
    var elementOperation = $("<div>")
            .attr({
                class: "MODElementOperation",
                id: "MODElementOperation_" + MODElement.MODElementId
            }).html("Operazioni: -nessuna- >>");
    element.append(elementOpen).append(elementDelete).append(elementVerticalLine)
            .append(elementTitle).append(elementBottom).append(elementRole)
            .append(elementFilter).append(elementOperation);
    comp.append(element);
    if (typeof (position) !== "undefined") {
        element.css({left: position.X, top: position.Y});
        MODElement.position = {X: position.X + "px", Y: position.Y + "px"};
    }
    else {
        if (typeof (MODElement.position) !== "undefined" && typeof (MODElement.position.X) !== "undefined" && typeof (MODElement.position.Y) !== "undefined") {
            element.css({left: MODElement.position.X, top: MODElement.position.Y});
        }
        else {
            element.css({left: "15px", top: "15px"});
            MODElement.position = {X: "15px", Y: "15px"};
        }
    }
//    console.log(MODElement);
    localStorage.setItem("MODElement_" + MODElement.MODElementId, JSON.stringify(MODElement));
    var MODTable = MODTableIdToMODTable(MODElement.MODTableId);
    var MODFields = MODTable.MODFields;
    for (var i in MODFields) {
        if (MODFields[i].MODFieldId === MODElement.MODFieldId) {
            var MODField = MODFields[i];
            break;
        }
    }
    if (MODTable.MODElements.indexOf(MODElement.MODElementId) === -1)
        MODTable.MODElements.push(MODElement.MODElementId);
    var DBTable = JSON.parse(localStorage.getItem("DBTable_" + MODTable.DBTable));
    localStorage.setItem("MODTable_" + MODElement.MODTableId, JSON.stringify(MODTable));
    $(".MODElement").draggable({
        grid: [5, 5],
        containment: "parent",
        zIndex: 1000,
        handle: ".MODElementTitle",
        cursor: "default",
        stop: function(event, ui) {
//            console.log(ui);
            var MODElementId = ui.helper.attr("id");
            var MODElement = JSON.parse(localStorage.getItem("MODElement_" + MODElementId));
            MODElement.position.X = Math.floor(ui.position.left) + "px";
            MODElement.position.Y = Math.floor(ui.position.top) + "px";
            localStorage.setItem("MODElement_" + MODElementId, JSON.stringify(MODElement));
            $("#" + MODElementId).css({
                top: MODElement.position.Y,
                left: MODElement.position.X
            });
        }
    });
    return element;
}

function renderMODRelation(MODRelation, position) {
    var comp = $("#composer");
    var relation = $("<div>")
            .attr({
                class: "MODRelation",
                id: MODRelation.MODRelationId
            });
    var relationOpen = $("<div>")
            .attr({
                class: "MODRelationOpen",
                id: "MODRelationOpen_" + MODRelation.MODRelationId
            }); //.html("-");
    var relationDelete = $("<div>")
            .attr({
                class: "MODRelationDelete",
                id: "MODRelationDelete_" + MODRelation.MODRelationId
            });
    var relationVerticalLine = $("<div>")
            .attr({
                class: "MODRelationVerticalLine",
                id: "MODRelationVerticalLine_" + MODRelation.MODRelationId
            });
    var relationTitle = $("<div>")
            .attr({
                class: "MODRelationTitle",
                id: "MODRelationTitle_" + MODRelation.MODRelationId
            }).html(MODRelation.MODRelationName);
    var relationBottom = $("<div>")
            .attr({
                class: "MODRelationBottom",
                id: "MODRelationBottom_" + MODRelation.MODRelationId
            }).html("from: " + MODRelation.MODTableId);
    relation.append(relationOpen).append(relationDelete).append(relationVerticalLine)
            .append(relationTitle).append(relationBottom);
    if (MODRelation.MODTableToId === null) {
        var relationChooseLink = $("<div>")
                .attr({
                    class: "MODRelationChooseLink",
                    id: "MODRelationChooseLink_" + MODRelation.MODRelationId
                });
        var relationChooseLinkSelect = $("<select>")
                .attr({
                    class: "MODRelationChooseLinkSelect",
                    id: "MODRelationChooseLinkSelect_" + MODRelation.MODRelationId
                });
        var relationChooseLinkButton = $("<button>")
                .attr({
                    class: "MODRelationChooseLinkButton",
                    id: "MODRelationChooseLinkButton_" + MODRelation.MODRelationId
                }).html("Applica");
        relation.append(relationChooseLink.append(relationChooseLinkSelect).append(relationChooseLinkButton));
    }
    else {
        var MODTableTo = MODTableIdToMODTable(MODRelation.MODTableToId);
        var MODFieldsTo = MODTableTo.MODFields;
        for (var i in MODFieldsTo) {
            if (MODRelation.MODFieldToId === MODFieldsTo[i].MODFieldId) {
                var MODFieldTo = MODFieldsTo[i];
                break;
            }
        }
        var relationLink = $("<div>")
                .attr({
                    class: "MODRelationLink",
                    id: "MODRelationLink_" + MODRelation.MODRelationId
                });
        var relationLinkTo = $("<span>")
                .attr({
                    class: "MODRelationLinkTo",
                    id: "MODRelationLinkTo_" + MODRelation.MODRelationId
                }).html(MODTableTo.DBTable);
        var relationLinkVia = $("<span>")
                .attr({
                    class: "MODRelationLinkVia",
                    id: "MODRelationLinkVia_" + MODRelation.MODRelationId
                }).html(MODFieldTo.DBFieldName);
        var relationLinkMult = $("<span>")
                .attr({
                    class: "MODRelationLinkVia",
                    id: "MODRelationLinkVia_" + MODRelation.MODRelationId
                }).html(MODRelation.MODRelationMult);
        relation.append(relationLink.append("To: ").append(relationLinkTo)
                .append("<br>Via: ").append(relationLinkVia)
                .append("<br>Mult: ").append(relationLinkMult));
    }
    var relationPM = $("<div>")
            .attr({
                class: "MODRelationPM",
                id: "MODRelationPM_" + MODRelation.MODRelationId
            });
    var relationPMcheck = $("<input>")
            .attr({
                class: "MODRelationPMcheck",
                id: "MODRelationPMcheck_" + MODRelation.MODRelationId,
                type: "checkbox"
            });
    if (MODRelation.MODRelationPM) {
        relationPMcheck.attr("checked", "true");
    }
    var relationPMGV = $("<input>")
            .attr({
                type: "text",
                class: "MODRelationPMGV",
                id: "MODRelationPMGV_" + MODRelation.MODRelationId
            }).val(MODRelation.MODRelationGenericValue);
    relation.append(relationPM.append("PM:").append(relationPMcheck)
            .append("&nbsp;&nbsp;GV:").append(relationPMGV));
    comp.append(relation);
    
    var select = relation.find(".MODRelationChooseLinkSelect");
    select.append("<option value=\"-1\">Scegli</option>")
    for (var i = 0; i < MODRelation.links.count; i++) {
        var link = MODRelation.links[i];
        select.append("<option value=\"" + i + "\">" + link.mult + " " + link.dbtable + " via " + link.fieldname + " " + link.view + "</option>");
    }
    
    if (typeof (position) !== "undefined") {
        relation.css({left: position.X, top: position.Y});
        MODRelation.position = {X: position.X + "px", Y: position.Y + "px"};
    }
    else {
        if (typeof (MODRelation.position) !== "undefined" && typeof (MODRelation.position.X) !== "undefined" && typeof (MODRelation.position.Y) !== "undefined") {
            relation.css({left: MODRelation.position.X, top: MODRelation.position.Y});
        }
        else {
            relation.css({left: "15px", top: "15px"});
            MODRelation.position = {X: "15px", Y: "15px"};
        }
    }
//    console.log(MODElement);
    localStorage.setItem("MODRelation_" + MODRelation.MODRelationId, JSON.stringify(MODRelation));
    var MODTableFrom = MODTableIdToMODTable(MODRelation.MODTableFromId);
    var MODFieldsFrom = MODTableFrom.MODFields;
    for (var i in MODFieldsFrom) {
        if (MODFieldsFrom[i].MODFieldId === MODRelation.MODFieldFromId) {
            var MODFieldFrom = MODFieldsFrom[i];
            break;
        }
    }
    if (MODTableFrom.MODRelations.indexOf(MODRelation.MODRelationId) === -1)
        MODTableFrom.MODRelations.push(MODRelation.MODRelationId);
    var DBTable = JSON.parse(localStorage.getItem("DBTable_" + MODTableFrom.DBTable));
    localStorage.setItem("MODTable_" + MODRelation.MODTableFromId, JSON.stringify(MODTableFrom));
    $(".MODRelation").draggable({
        grid: [5, 5],
        containment: "parent",
        zIndex: 1000,
        handle: ".MODRelationTitle",
        cursor: "default",
        stop: function(event, ui) {
//            console.log(ui);
            var MODRelationId = ui.helper.attr("id");
            var MODRelation = JSON.parse(localStorage.getItem("MODRelation_" + MODRelationId));
            MODRelation.position.X = Math.floor(ui.position.left) + "px";
            MODRelation.position.Y = Math.floor(ui.position.top) + "px";
            localStorage.setItem("MODRelation_" + MODRelationId, JSON.stringify(MODRelation));
            $("#" + MODRelationId).css({
                top: MODRelation.position.Y,
                left: MODRelation.position.X
            });
        }
    });
    return relation;
}

function applyFiltersToModElement(MODElementId, filtersdata) {
//si aspetta che filtersdata sia un vettore, ogni cella rappresenta un gruppo
//o una congiunzione tra gruppi.
//nel primo caso è un vettore che contiene filtri o congiunzioni tra filtri


}

//wrappers

function MODElementIdToMODElement(MODElementId) {
    var stor = localStorage.getItem("MODElement_" + MODElementId);
    if (stor === null) {
        console.log("MODElementIdToMODElement error : this is not a valid MODElement Id")
        return null;
    }
    return JSON.parse(stor);
}
function MODRelationIdToMODRelation(MODRelationId) {
    var stor = localStorage.getItem("MODRelation_" + MODRelationId);
    if (stor === null) {
        console.log("MODRelationIdToMODRelation error : this is not a valid MODRelation Id")
        return null;
    }
    return JSON.parse(stor);
}

function MODTableIdToMODTable(MODTableId) {
    var stor = localStorage.getItem("MODTable_" + MODTableId);
    if (stor === null) {
        console.log("MODTableIdToMODTable error : this is not a valid MODTable Id")
        return null;
    }
    return JSON.parse(stor);
}


function DOMMODToMODRelation(DOMMODRelation) {
    var checkclass = DOMMODRelation.attr("class");
    if (typeof (checkclass) === "undefined" || checkclass.search("MODRelation") !== 0) {
        console.log("DOMMODToMODRelation error : this is not a valid DOMMODRelation")
        return null;
    }
    return MODRelationIdToMODRelation(DOMMODRelation.attr("id"));
}
function DOMMODToMODElement(DOMMODElement) {
    var checkclass = DOMMODElement.attr("class");
    if (typeof (checkclass) === "undefined" || checkclass.search("MODElement") !== 0) {
        console.log("DOMMODToMODElement error : this is not a valid DOMMODElement")
        return null;
    }
    return MODElementIdToMODElement(DOMMODElement.attr("id"));
}

function DOMMODElementToMODTable(DOMMODElement) {
    var MODElement = DOMMODToMODElement(DOMMODElement);
    return MODTableIdToMODTable(MODElement.MODTableId);
}

function DOMMODElementToMODField(DOMMODElement) {
    var MODElement = DOMMODToMODElement(DOMMODElement);
    var MODTable = DOMMODElementToMODTable(DOMMODElement);
    var fields = MODTable.MODFields;
    for (var i in fields) {
        if (fields[i].MODFieldId === MODElement.MODFieldId) {
            return fields[i];
        }
    }
}

function DOMMODElementToDBTable(DOMMODElement) {
    var MODTable = DOMMODElementToMODTable(DOMMODElement);
    var DBTable = JSON.parse(localStorage.getItem("DBTable_" + MODTable.DBTable));
    return DBTable;
}

function DOMMODElementToDBField(DOMMODElement) {
    var DBTable = DOMMODElementToDBTable(DOMMODElement);
    var MODField = DOMMODElementToMODField(DOMMODElement);
    var fields = DBTable.DBFields;
    for (var i in fields) {
        if (fields[i].DBFieldName === MODField.DBFieldName) {
            return fields[i];
        }
    }
}
