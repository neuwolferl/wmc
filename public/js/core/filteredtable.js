(function () {
    var app = angular.module("FILTEREDTABLE", ['ui.sortable'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });

    app.filter("FilteredTableStaticPickList", function () {
        return function (input, filt) {
            if (typeof (filt) === "string" || typeof (filt.length) === "undefined") {
                return input;
            }
            if (typeof (filt[input]) !== "undefined") {
                return filt[input];
            }
            else if (typeof (filt[input + ""]) !== "undefined") {
                return filt[input + ""];
            }
            else {
                return input;
            }
        }
    });

    app.filter("FilteredTableGeneralFilter", function () {
        return function (input, filt) {
            if (typeof (filt) === "undefined") {
                filt = "";
            }
            filt = filt.toLowerCase();
            var ret = [];
            for (var i in input) {
                var flag = false;
                for (var j in input[i]) {

                    if (input[i].hasOwnProperty(j)) {

                        if (input[i][j] && typeof (input[i][j]) === "string" && input[i][j].toLowerCase().indexOf(filt) > -1) {
                            flag = true;
                        }
                    }
                }
                if (flag) {
                    ret.push(input[i]);
                }
            }
            return ret;
        }
    });
    app.filter("FilteredTableSelectedColFilter", function () {
        return function (input, filt) {
            if (typeof (filt) === "undefined") { //caso vuoto
                return input;
            }
            var flag = true;
            for (var i in filt) { //caso vuoto
                if (filt[i] && filt[i] !== "")
                    flag = false;
            }
            if (flag)
                return input;
            var ret = [];
            for (var i in input) {
                var flag = false;
                for (var j in input[i]) {

                    if (input[i].hasOwnProperty(j)) {
                        if (filt[j] === "")
                            continue;
                        else if (input[i][j] && filt[j] && typeof (input[i][j]) === "string") {
                            if (input[i][j].toLowerCase().indexOf(filt[j].toLowerCase()) === -1) { //ho trovato uno che non corrisponde
                                flag = true;
                                break;
                            }
                        }
                    }
                }
                if (!flag) {
                    ret.push(input[i]);
                }
            }
            return ret;
        }
    });

    app.directive('filteredtable', ['FilteredTable', function (FilteredTable) {
            function link(scope, element, attrs) {
                scope.FilteredTable = FilteredTable;
            }
            return {
                templateUrl: function () {
                    var baseurl = document.URL;
                    baseurl = baseurl.split("public");
                    return baseurl[0] + "public/js/core/filteredtable.html";
                },
                restrict: 'E',
                scope: {
                    tableId: '=tableid'
                },
                link: link,
                controller: function ($scope, $element, $attrs, $rootScope, $timeout) {
                    this.table = $element.find(".tableFiltered");
                    var that = this;
                    $rootScope.$on("setDataPerformed", function () {
                        var table = that.table;
                        var ths = table.find("th");

                        var sum = 0;
                        for (var i in FilteredTable.getTableStructure($scope.tableId).colsWidth) {
                            sum += Number(FilteredTable.getTableStructure($scope.tableId).colsWidth[i]);
                        }
                        table.css("width", sum + "px");
                        ths.each(function (i, elm) {
                            if (i === 0)
                                return;
                            $(this).css({width: FilteredTable.getTableStructure($scope.tableId).colsWidth[FilteredTable.getTableStructure($scope.tableId).labels[i - 1]] + "px"});
                        });
                        var offset = $element.offset();
                        var parenth = Number($('body').css("height").replace("px", ""));
                        $element.css("height", (95 / 100 * (parenth - offset.top)) + "px");
//                        $element.parents(".container").css("height",(95/100*(parenth-offset.top))+"px");
                        $element.find(".tableFiltered").css("height", (95 / 100 * (parenth - offset.top)) + "px");
                        $(document).keyup(function (e) {
                            if (e.ctrlKey && e.keyCode == 77) { // 77 = m
                                $rootScope.$emit("ToggleLeftBar", []);
                                $rootScope.$emit("ToggleRightBar", []);
                            }
                        });

                    });

                }
            };
        }]);
    app.directive('filteredtableoperation', ['FilteredTable', function (FilteredTable) {

            return {
                templateUrl: function () {
                    var baseurl = document.URL;
                    baseurl = baseurl.split("public");
                    return baseurl[0] + "public/js/core/filteredtableoperation.html";
                },
                restrict: 'E',
                scope: {
                    tableId: '=tableid'
                },
                link: function (scope, element, attrs) {
                    scope.FilteredTable = FilteredTable;
                    scope.isAvalaible = function (availability) {
                        var rowSelectionLength = FilteredTable.getTableDynamics(scope.tableId).rowSelection.length;
                        var selectedData = [];
                        var tracking = FilteredTable.getTableStructure(scope.tableId).tracking;
                        var data = FilteredTable.getTableData(scope.tableId);
                        for (var i in FilteredTable.getTableDynamics(scope.tableId).rowSelection) {
                            for (var j in data) {
                                if (data[j][tracking] === FilteredTable.getTableDynamics(scope.tableId).rowSelection[i]) {
                                    selectedData.push(data[j]);
                                }
                            }
                        }
                        var exp = "";
                        var ret = true;
                        if (typeof (availability) === "undefined" || availability.length === 0)
                            return true;
                        for (var i in availability) {
                            var av = availability[i];
                            if (av.length === 0) {
                                continue;
                            }
                            if (av.length === 1) {
                                ret = eval(av[0]);
                                continue;
                            }
                            exp = av[0];
                            for (var j in av) {
                                if (j === 0)
                                    continue;
                                switch (av[j]) {
                                    case 'rowSelectionLength':
                                        exp = exp.replace(/rowSelectionLength/g, rowSelectionLength);
                                        break;
                                    case 'selectedRows':
                                        var check = [];
                                        var index = exp.indexOf("selectedRows") + 12;
                                        if (exp[index] === ".") {
                                            var index2 = exp.indexOf(" ", index);
                                            var label = exp.substring(index + 1, index2);
                                            if (FilteredTable.getTableStructure(scope.tableId).labels.indexOf(label) > -1) {
                                                for (var kk in selectedData) {
                                                    check.push(exp.replace("selectedRows." + label, "'" + selectedData[kk][label] + "'"));
                                                }
                                            }
                                            exp = check.join(" && ");
                                        }
                                        else {
                                            exp = false;
                                        }


                                        break;
                                }
                            }
                            ret = eval(exp);
                        }
                        return ret;
                    }
                    scope.xOp = function (callback, arguments) {

                        //traduco argomenti
                        var argumentList = [];
                        for (var i in arguments) {
                            switch (arguments[i]) {
                                case '{selectedRow}':
                                    var arg = FilteredTable.getTableDynamics(scope.tableId).rowSelection;
                                    if (arg.length === 1) {
                                        arg = arg[0];
                                        var tracking = FilteredTable.getTableStructure(scope.tableId).tracking;
                                        var data = FilteredTable.getTableData(scope.tableId);
                                        for (var i in data) {

                                            if (data[i][tracking] === arg) {
                                                arg = data[i];
                                                break;
                                            }
                                        }
                                        argumentList.push(arg);
                                    }
                                    break;
                                case '{selectedRows}':
                                    var arg = FilteredTable.getTableDynamics(scope.tableId).rowSelection;
                                    var argument = [];
                                    var tracking = FilteredTable.getTableStructure(scope.tableId).tracking;
                                    var data = FilteredTable.getTableData(scope.tableId);
                                    for (var j in arg) {
                                        for (var i in data) {
                                            if (data[i][tracking] === arg[j]) {
                                                argument.push(data[i]);
                                                break;
                                            }
                                        }
                                    }
                                    argumentList.push(argument);
                                    break;
                                default:
                                    argumentList.push(arguments[i]);
                            }
                        }
                        callback.apply(this, argumentList);
                    }
                }
            };
        }]);
    app.directive('filteredtableselector', ['FilteredTable', function (FilteredTable) {

            return {
                templateUrl: function () {
                    var baseurl = document.URL;
                    baseurl = baseurl.split("public");
                    return baseurl[0] + "public/js/core/filteredtableselector.html";
                },
                restrict: 'E',
                scope: {
                    tableId: '=tableid'
                },
                link: function (scope, element, attrs) {
                    scope.FilteredTable = FilteredTable;
                }
            };
        }]);
    app.directive('filteredtableorderer', ['FilteredTable', function (FilteredTable) {

            return {
                templateUrl: function () {
                    var baseurl = document.URL;
                    baseurl = baseurl.split("public");
                    return baseurl[0] + "public/js/core/filteredtableorderer.html";
                },
                restrict: 'E',
                scope: {
                    tableId: '=tableid',
                },
                link: function (scope, element, attrs) {
                    scope.FilteredTable = FilteredTable;
                },
                controller: function () {
                    return {
                    }
                }
            };
        }]);
    app.directive('filteredtableinspector', ['FilteredTable', function (FilteredTable) {

            return {
                templateUrl: function () {
                    var baseurl = document.URL;
                    baseurl = baseurl.split("public");
                    return baseurl[0] + "public/js/core/filteredtableinspector.html";
                },
                restrict: 'E',
                scope: {
                    tableId: '=tableid',
                },
                link: function (scope, element, attrs) {
                    scope.FilteredTable = FilteredTable;
                },
                controller: function () {
                    return {
                    }
                }
            };
        }]);
    app.provider("FilteredTable", function () {

        this.tplTableStructure = {
            fields: [],
            labels: [],
            types: [],
            aligns: [],
            flags: [],
            colsWidth: {},
            tracking: ""
        };
        this.tplTableData = {
        };
        this.tplTableDynamics = {
            generalFilter: "",
            filterings: {},
            orderings: [],
            colSelection: [],
            rowSelection: [],
            ops: {}
        };
        this.randomString = function (le)
        {
            var text = "";
            var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
            for (var i = 0; i < le; i++)
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            return text;
        };
        this.setTicket = function () {
            var flag = true;
            while (flag) {
                var tableIndex = this.randomString(10);
                if (typeof (this.hash[tableIndex]) === "undefined") {
                    flag = false;
                }
            }
            this.hash[tableIndex] = {
                tableStructure: jQuery.extend(true, {}, this.tplTableStructure),
                tableDynamics: jQuery.extend(true, {}, this.tplTableDynamics),
                tableData: []

            };
            return [tableIndex, this.hash[tableIndex]];
        }
        this.hash = [];
        this.sortData = function (tableIndex) {

            var data = this.hash[tableIndex].tableData;
            console.log(data);
            var orderings = this.hash[tableIndex].tableDynamics.orderings;
            var labels = this.hash[tableIndex].tableStructure.labels;
            var types = this.hash[tableIndex].tableStructure.types;
            var ords = [];
            for (var i in orderings) {
                var field = orderings[i].field;
                var direction = orderings[i].direction;
                var index = labels.indexOf(field);
                if (index === -1) {
                    console.log("Errore nell'ordinamento");
                    return;
                }
                var type = types[index];
                ords.push({index: index, field: field, type: type, dir: direction});

            }
            this.hash[tableIndex].tableData = this.hash[tableIndex].tableData.sort(function (a, b) {

                for (var j in ords) {

                    switch (ords[j].type) {
                        case 'integer':
                        case 'currency':
                            if (!a[ords[j].field] || a[ords[j].field] === "") {
                                if (j < ords.length - 1)
                                    return 1;
                                else
                                    return 1;
                            }
                            if (!b[ords[j].field] || b[ords[j].field] === "") {
                                if (j < ords.length - 1)
                                    return -1;
                                else
                                    return -1;
                            }
                            if (Number(a[ords[j].field]) === Number(b[ords[j].field])) {
                                continue;
                            }
                            else {
                                return Number(ords[j].dir) * (Number(a[ords[j].field]) - Number(b[ords[j].field]));
                            }
                            break;
                        case 'string':
                        case 'longtext':
                            if (!a[ords[j].field] || a[ords[j].field] === "") {
                                if (j < ords.length - 1)
                                    continue;
                                else
                                    return 1;
                            }
                            if (!b[ords[j].field] || b[ords[j].field] === "") {
                                if (j < ords.length - 1)
                                    continue;
                                else
                                    return -1;
                            }
                            if (a[ords[j].field] === b[ords[j].field]) {
                                continue;
                            }
                            else {
                                return Number(ords[j].dir) * (a[ords[j].field].localeCompare(b[ords[j].field]));
                            }
                            break;
                        case 'date':
                            if ((!a[ords[j].field] || a[ords[j].field] === "0000-00-00") && (!b[ords[j].field] || b[ords[j].field] === "0000-00-00")){
                                continue;
                            }
                            if (!a[ords[j].field] || a[ords[j].field] === "0000-00-00") {
                                    return 1;
                            }
                            if (!b[ords[j].field] || b[ords[j].field] === "0000-00-00") {
                                    return -1;
                            }
                            if (a[ords[j].field] === b[ords[j].field]) {
                                continue;
                            }
                            else {
                                var date1 = new Date(a[ords[j].field]);
                                var date2 = new Date(b[ords[j].field]);
                                return Number(ords[j].dir) * ((date1 < date2) ? -1 : 1);
                            }
                            break;

                    }
                }
                return 0;
            });


        }
        this.$get = function ($filter, $rootScope) {
            var that = this;
            return {
                newTable: function () {
                    return that.setTicket();
                },
                isDefined: function (tableIndex) {
                    return (typeof (that.hash[tableIndex]) !== "undefined");
                },
                getTableStructure: function (tableIndex) {
                    return that.hash[tableIndex].tableStructure;
                },
                getTableData: function (tableIndex) {
                    return that.hash[tableIndex].tableData;
                },
                getTableDynamics: function (tableIndex) {
                    return that.hash[tableIndex].tableDynamics;
                },
                setOps: function (tableIndex, ops) {
                    that.hash[tableIndex].tableDynamics.ops = ops;
                },
                configureTable: function (tableIndex, tableStructure) {
                    if (typeof (tableStructure.fields) === "undefined") {
                        throw new Error({Error: "Missing fields, can't configure table " + tableIndex});
                    }
                    if (typeof (tableStructure.labels) === "undefined") {
                        tableStructure.labels = [];
                    }
                    if (typeof (tableStructure.types) === "undefined") {
                        tableStructure.types = [];
                    }
                    if (typeof (tableStructure.aligns) === "undefined") {
                        tableStructure.aligns = [];
                    }
                    if (typeof (tableStructure.flags) === "undefined") {
                        tableStructure.flags = [];
                    }

                    for (var i in tableStructure.fields) {
                        if (typeof (tableStructure.labels[i]) === "undefined") {
                            tableStructure.labels[i] = tableStructure.fields[i];
                        }
                        if (typeof (tableStructure.types[i]) === "undefined") {
                            tableStructure.types[i] = "string";
                        }
                        if (typeof (tableStructure.aligns[i]) === "undefined") {
                            tableStructure.aligns[i] = "left";
                        }
                        if (tableStructure.flags[i].indexOf("|") > -1) {
                            tableStructure.flags[i] = tableStructure.flags[i].split("|");
                        }
                    }
                    that.hash[tableIndex].tableStructure = tableStructure;
//                    console.log(that.hash[tableIndex].tableStructure);
                },
                setData: function (tableIndex, data) {
                    that.hash[tableIndex].tableData = data;
                    that.hash[tableIndex].tableStructure.colsWidth = {};
                    for (var j in that.hash[tableIndex].tableStructure.labels) {
                        that.hash[tableIndex].tableStructure.colsWidth[that.hash[tableIndex].tableStructure.labels[j]] = that.hash[tableIndex].tableStructure.labels[j].length;
                    }
//                    console.log(data[0]);
                    for (var i in data) {
                        for (var j in data[i]) {
                            if (typeof (that.hash[tableIndex].tableStructure.colsWidth[j]) !== "undefined") {
                                if (data[i][j] && data[i][j].length && data[i][j].length > that.hash[tableIndex].tableStructure.colsWidth[j])
                                    that.hash[tableIndex].tableStructure.colsWidth[j] = data[i][j].length;

                            }
                        }
                    }
                    for (var i in that.hash[tableIndex].tableStructure.colsWidth) {
                        that.hash[tableIndex].tableStructure.colsWidth[i] = (that.hash[tableIndex].tableStructure.colsWidth[i] * 11);
                    }
                    $rootScope.$emit("setDataPerformed", []);
                },
                setLabels: function (tableIndex, labels) {
                    for (var i in labels) {
                        if (typeof (that.hash[tableIndex].tableStructure.labels[i]) !== "undefined") {
                            that.hash[tableIndex].tableStructure.labels[i] = labels[i];
                        }
                    }
                },
                setOrder: function (tableIndex, field) {
                    var orderings = that.hash[tableIndex].tableDynamics.orderings;
                    var found = false;

                    for (var i in orderings) {
                        if (orderings[i].field === field) {
                            if (orderings[i].direction === 1)
                                orderings[i].direction = -1;
                            else if (orderings[i].direction === -1)
                                orderings[i].direction = 1;
                            found = true;
                        }
                    }
                    if (!found) {
                        orderings.push({field: field, direction: 1});
                    }
                    if (orderings.length > 4) {
                        orderings.splice(0, 1);
                    }
                    //eseguo:
                    that.sortData(tableIndex);

                },
                unsetOrder: function (tableIndex, field) {
                    var orderings = that.hash[tableIndex].tableDynamics.orderings;
                    var found = false;

                    for (var i in orderings) {
                        if (orderings[i].field === field) {
                            orderings.splice(i, 1);
                            return;
                        }
                    }
                    that.sortData(tableIndex);
                },
                selToOrder: function (tableIndex) {
                    var colSelection = that.hash[tableIndex].tableDynamics.colSelection;
                    var orderings = that.hash[tableIndex].tableDynamics.orderings;
                    that.hash[tableIndex].tableDynamics.orderings.splice(0, orderings.length);
                    for (var i in colSelection) {
                        orderings.push({field: that.hash[tableIndex].tableStructure.labels[colSelection[i]], direction: 1});
                    }
                    that.sortData(tableIndex);

                },
                getOrder: function (tableIndex, field) {
                    var orderings = that.hash[tableIndex].tableDynamics.orderings;
                    var found = false;
                    for (var i in orderings) {
                        if (orderings[i].field === field) {
                            return orderings[i].direction;
                            found = true;
                        }
                    }
                    if (!found) {
                        return 0;
                    }
                },
                showFiltered: function (tableId, label, value) {
                    var types = that.hash[tableId].tableStructure.types;
//                    console.log(label + "   " + value);
                    var labels = that.hash[tableId].tableStructure.labels;
                    var flags = that.hash[tableId].tableStructure.flags;
                    var index = labels.indexOf(label);
                    if (index > -1 && typeof (types[index]) !== "undefined") {
//                        console.log(types[index]);
                        switch (types[index]) {
                            case 'integer':
                                return value;
                                break;
                            case 'string':
                                return value;
                                break;
                            case 'currency':
                                return $filter('currency')(value, '€ ', 2);
                                break;
                            case 'pl':
                                return $filter('FilteredTableStaticPickList')(value, flags[index]);
                                break;
                            default:
                                return value;
                                break;

                        }
                    }
                    else {
                        return value;
                    }
                },
                selectCol: function (tableId, field) {
                    console.log(field);
                    var fields = that.hash[tableId].tableStructure.fields;
                    var colSelection = that.hash[tableId].tableDynamics.colSelection;
                    var index = fields.indexOf(field);
                    if (index > -1) {
                        var index2 = colSelection.indexOf(index);
                        if (index2 > -1) {
                            colSelection.splice(index2, 1);
                        }
                        else {
                            colSelection.push(index);
                        }
                    }
                    console.log(colSelection);
                },
                selectRow: function (tableId, rowIndex) {
                    console.log(rowIndex);
                    var rowSelection = that.hash[tableId].tableDynamics.rowSelection;
                    var index = rowSelection.indexOf(rowIndex);
//                    console.log("index "+index);
                    if (index > -1) {
                        that.hash[tableId].tableDynamics.rowSelection.splice(index, 1);
                    }
                    else {
                        that.hash[tableId].tableDynamics.rowSelection.push(rowIndex);
                    }
                    if (typeof ($rootScope.total) === "undefined") {
                        $rootScope.total = {};
                    }
                    var data = that.hash[tableId].tableData;
                    var tracking = that.hash[tableId].tableStructure.tracking;
                    for (var j in that.hash[tableId].tableStructure.types) {
                        if (that.hash[tableId].tableStructure.types[j] === "currency") {
                            $rootScope.total[that.hash[tableId].tableStructure.labels[j]] = 0;
                            for (var i in rowSelection) {
                                for (var k in data) {
                                    if (data[k][tracking] === rowSelection[i]) {
                                        $rootScope.total[that.hash[tableId].tableStructure.labels[j]] += Number(data[k][that.hash[tableId].tableStructure.labels[j]]);
                                        break;
                                    }
                                }

                            }
                        }

                    }
                    $rootScope.$digest();
                },
                getClass: function (tableId, label, row) {
                    var cl = [];
                    var labels = that.hash[tableId].tableStructure.labels;
                    var aligns = that.hash[tableId].tableStructure.aligns;
                    var colSelection = that.hash[tableId].tableDynamics.colSelection;
                    var rowSelection = that.hash[tableId].tableDynamics.rowSelection;
                    var filterings = that.hash[tableId].tableDynamics.filterings;
                    var orderings = that.hash[tableId].tableDynamics.orderings;
                    var index = labels.indexOf(label);
                    if (index > -1 && typeof (aligns[index]) !== "undefined") {
                        cl.push("filteredTable" + aligns[index].charAt(0).toUpperCase() + aligns[index].slice(1) + "AlignedCell");
                    }
                    var index2 = colSelection.indexOf(index);
                    if (index2 > -1) {
                        cl.push("colSelected");
                    }
                    var index4 = rowSelection.indexOf(row);
                    if (index4 > -1) {
                        cl.push("rowSelected");
                    }
                    var index3 = -1;
                    for (var i in orderings) {
                        if (orderings[i].field === label) {
                            index3 = i;
                            break;
                        }
                    }
                    if (index3 > -1) {
                        if (orderings[index3].direction === 1)
                            cl.push("filteredTableAscCol");
                        else
                            cl.push("filteredTableDescCol");
                    }
                    if (typeof (filterings[label]) !== "undefined" && filterings[label].length) {
                        cl.push("colFiltered");
                    }
                    return cl.join(" ");
                }

            };
        }
    });
})();

