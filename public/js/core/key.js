(function () {
    var app = angular.module("KEYS", [], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });

    app.provider("Keys", function () {

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
        this.$get = function ($filter, $rootScope) {
            var that = this;
            return {
                newTable: function () {
                    return that.setTicket();
                },
                isDefined: function (tableIndex) {
                    return (typeof (that.hash[tableIndex] !== "undefined"))
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
                },
                selToOrder: function (tableIndex) {
                    var colSelection = that.hash[tableIndex].tableDynamics.colSelection;
                    var orderings = that.hash[tableIndex].tableDynamics.orderings;
                    that.hash[tableIndex].tableDynamics.orderings.splice(0, orderings.length);
                    for (var i in colSelection) {
                        orderings.push({field: that.hash[tableIndex].tableStructure.labels[colSelection[i]], direction: 1});
                    }
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
                getClass: function (tableId, label) {
                    var cl = [];
                    var labels = that.hash[tableId].tableStructure.labels;
                    var aligns = that.hash[tableId].tableStructure.aligns;
                    var colSelection = that.hash[tableId].tableDynamics.colSelection;
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

