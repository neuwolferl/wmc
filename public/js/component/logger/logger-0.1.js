var version = "0.1";
var Logger = angular.module("LOGGER", [], ['$interpolateProvider',function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
}]);
Logger.directive('logGeneral', ['Logger', function (Logger) {
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/logger/logGeneral-" + version + ".html";
            },
            restrict: 'E',
            scope: {
                logId: '=logid',
                logDescriptor: '@logdescriptor'
            },
            controller: ['$scope', '$element', '$attrs', '$rootScope', '$timeout', '$interpolate', '$filter',
                function ($scope, $element, $attrs, $rootScope, $timeout, $interpolate, $filter) {
                var date = new Date();
                $scope.dateObj = {
                    date: date,
                    dateEngString: date.getFullYear() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + (date.getDate() < 10 ? "0" : "") + date.getDate(),
                    dateItaString: (date.getDate() < 10 ? "0" : "") + date.getDate() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + date.getFullYear()
                };
                $scope.logged = [];
                $scope.formatLogged = [];
                $scope.openLogRow = function (index) {
                    if ($scope.formatLogged[index]) {
                        if (!$scope.formatLogged[index].comments && !$scope.formatLogged[index].children.length) {
                            return;
                        }

                        $scope.formatLogged[index].open = true;
                    }
                }
                $scope.closeLogRow = function (index) {
                    if ($scope.formatLogged[index])
                        $scope.formatLogged[index].open = false;
                }
                $scope.format = function () {
                    $scope.formatLogged = [];
                    var format = Logger.getLogger($scope.logId).getLogDescriptor($scope.logDescriptor);
                    var users = Logger.getLogger($scope.logId).getLogDescriptor("users");
                    for (var i in $scope.logged) {
                        var riga = $scope.logged[i];
                        if (!format[$scope.logged[i].code_main]
                                || !format[$scope.logged[i].code_main][$scope.logged[i].code_detail]
                                || !format[$scope.logged[i].code_main][$scope.logged[i].code_detail][$scope.logged[i].code_type]
                                || !format[$scope.logged[i].code_main][$scope.logged[i].code_detail][$scope.logged[i].code_type][$scope.logged[i].what_element]) {
                            continue;
                        }
                        if (users) {
                            if (users[riga.who]) {
                                riga.who = users[riga.who].NOME + " " + users[riga.who].COGNOME;
                            }
                        }
                        var f = format[$scope.logged[i].code_main][$scope.logged[i].code_detail][$scope.logged[i].code_type][$scope.logged[i].what_element].row;
                        if (!f)
                            continue;
                        riga.row = $interpolate(f);
                        riga.row = riga.row(($scope.logged[i]));
                        if (riga.parent != null) {
                            for (var kk in $scope.formatLogged) {
                                if ($scope.formatLogged[kk].log_id == riga.parent) {
                                    riga.parent = kk;
                                    riga.level = [];
                                    for (var x in $scope.formatLogged[kk].level) {
                                        riga.level.push("x");
                                    }
                                    riga.level.push("x");
                                    break;
                                }
                            }
                        }
                        else {
                            riga.level = [];
                        }
                        if (riga.comment) {
                            riga.children.push("c" + riga.log_id);
                        }
                        $scope.formatLogged.push(riga);

                        if (riga.comment) {
                            var comment = {
                                log_id: "c" + riga.log_id,
                                parent: $scope.formatLogged.length - 1,
                                children: [],
                                level: [],
                                row: "NOTE: " + riga.comment
                            };
                            for (var x in riga.level) {
                                comment.level.push("x");
                            }
                            comment.level.push("x");
                            $scope.formatLogged.push(comment);
                        }
                    }
                };
                Logger.getLogger($scope.logId).addOnChange(function (arg) {
                    $scope.logged = arg.adjustedData;
                    $scope.format();
                    var modal = $element.parents(".modal");
                    if (modal.length) {
                        modal = modal.eq(0);
                        if (modal.trigger)
                            modal.trigger("adjust");

                        $timeout(function () {
                            $scope.adjustForMask();
                        }, 100);
                    }
                });
                $scope.adjustForMask = function () {

                    $element.parents(".dimensionatore").children().each(function () {
                        $(this).height($element.parents(".dimensionatore").height());
                    });

                    $element.height($element.parents(".dimensionatore").height());
                };
            }]
        }
        ;
    }]);
Logger.provider("Logger", function () {
    var pippo = this;
    this.randomString = function (le)
    {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        for (var i = 0; i < le; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    };
    this.adjustData = function (data, logDescriptorName) {
        var adjustedData = [];
        for (var i in data) {
            var date = new Date(data[i]["timestamp"]);
            var now = new Date();
            var nowDB = new Date(data[i].now);
            var dataStr = "";
            if (date.getDate() === now.getDate() && date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear()) {
                dataStr = "oggi";
            }
            else if (date.getDate() === (now.getDate() - 1) && date.getMonth() === now.getMonth() && date.getFullYear() === now.getFullYear()) {
                dataStr = "ieri";
            }
            else {
                dataStr = "il " + (date.getDate() < 10 ? "0" : "") + date.getDate() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + date.getFullYear();
            }
            var diff = (now.getHours() - nowDB.getHours());
            var oraStr = ((date.getHours() + diff) < 10 ? "0" : "") + (date.getHours() + diff) + ":" + (date.getMinutes() < 10 ? "0" : "") + date.getMinutes() + ":" + (date.getSeconds() < 9 ? "0" : "") + date.getSeconds();
            var tmp = {
                log_id: data[i].log_id,
                comment: data[i].comment,
                data: dataStr,
                ora: oraStr,
                code_detail: data[i].code_detail,
                what_detail: data[i].what_detail,
                what_element: data[i].what_element,
                code_main: data[i].code_main,
                what_main: data[i].what_main,
                code_type: data[i].code_type,
                who: data[i].who_vtiger,
                parameters: data[i].parameters,
                parent: data[i].parent,
                diff: diff,
                children: []
            };
            if (tmp.parent === "0") {
                tmp.parent = null;
            }
            if (typeof (tmp.parameters) === "string" && tmp.parameters.length > 0) {
                tmp.parameters = JSON.parse(tmp.parameters);
            }
            adjustedData.push(tmp);
            if (tmp.parent && tmp.parent != "0") {
                for (var kk in adjustedData) {
                    if (adjustedData[kk].log_id == tmp.parent) {
                        adjustedData[kk].children.push(tmp.log_id);
                    }
                }
            }
        }
        return adjustedData;
    }
    this.setTicket = function () {
        var flag = true;
        while (flag) {
            var logIndex = this.randomString(10);
            if (typeof (this.hash[logIndex]) === "undefined") {
                flag = false;
            }
        }
        this.hash[logIndex] = {
            piva: "",
            adjustedData: null,
            data: null,
            previousData: null,
            pars: {},
            getLog: function () {

            },
            putLog: function () {

            },
            onChange: [],
            changePromise: null,
            refreshPromise: null,
            logDescriptors: {}
        };

        return [logIndex, this.hash[logIndex]];
    }
    this.hash = [];
    this.$get = ['$filter','$rootScope','$interval',function ($filter, $rootScope, $interval) {
        var that = this;
        return {
            getLogger: function (logIndex) {
                return {
                    setData: function (data) {
                        if (!that.hash[logIndex].data || data.length !== that.hash[logIndex].data.length) {
                            that.hash[logIndex].data = data;
                            that.hash[logIndex].adjustedData = that.adjustData(data);
                        }
                    },
                    setPiva: function (piva) {

                    },
                    setPars: function (args) {

                    },
                    setLogGetter: function (callback) {
                        that.hash[logIndex].getLog = function (args) {
                            return callback(args);
                        }
                    },
                    setLogPutter: function (callback) {
                        that.hash[logIndex].putLog = function (args) {
                            return callback(args);
                        }
                    },
                    getArgs: function(){
                        return that.hash[logIndex].args;
                    },
                    startLogRefresher: function (secs, args) {
                        if (that.hash[logIndex].refreshPromise !== null) {
                            $interval.cancel(that.hash[logIndex].refreshPromise);
                        }
                        that.hash[logIndex].getLog(args);
                        that.hash[logIndex].args = args;
                        that.hash[logIndex].refreshPromise = $interval(function () {
                            that.hash[logIndex].getLog(args);
                        }, secs);
                    },
                    stopLogRefresher: function () {
                        if (that.hash[logIndex].refreshPromise !== null) {
                            $interval.cancel(that.hash[logIndex].refreshPromise);
                            that.hash[logIndex].refreshPromise = null;
                        }

                    },
                    addOnChange: function (callback) {
                        that.hash[logIndex].onChange.push(function () {
                            var arg = {
                                data: that.hash[logIndex].data,
                                adjustedData: that.hash[logIndex].adjustedData
                            };
                            return callback(arg);
                        }
                        );
                    },
                    startChangeMirroring: function () {
                        if (that.hash[logIndex].changePromise !== null) {
                            $interval.cancel(that.hash[logIndex].changePromise);
                        }
                        that.hash[logIndex].changePromise = $interval(function () {
                            var isChanged = false;
                            if (that.hash[logIndex].previousData === null && that.hash[logIndex].data !== null) {
                                isChanged = true;
                                that.hash[logIndex].previousData = that.hash[logIndex].data;
                            }
                            else if (that.hash[logIndex].previousData !== null && that.hash[logIndex].previousData.length !== that.hash[logIndex].data.length) {
                                isChanged = true;
                                that.hash[logIndex].previousData = that.hash[logIndex].data;
                            }
                            if (isChanged) {
                                for (var i in that.hash[logIndex].onChange) {
                                    that.hash[logIndex].onChange[i]();
                                }
                            }
                        }, 2000);
                    },
                    stopChangeMirroring: function () {
                        $interval.cancel(that.hash[logIndex].changePromise);
                        that.hash[logIndex].changePromise = null;
                    },
                    setLogDescriptor: function (name, desc, users) {
                        var array = {};
                        for (var i in desc) {
                            if (typeof (array[desc[i].what_main]) === "undefined") {
                                array[desc[i].what_main] = {};
                            }
                            if (typeof (array[desc[i].what_main][desc[i].what_detail]) === "undefined") {
                                array[desc[i].what_main][desc[i].what_detail] = {};
                            }
                            if (typeof (array[desc[i].what_main][desc[i].what_detail][desc[i].what_type]) === "undefined") {
                                array[desc[i].what_main][desc[i].what_detail][desc[i].what_type] = {};
                            }
                            array[desc[i].what_main][desc[i].what_detail][desc[i].what_type][desc[i].what_element] = {
                                row: desc[i].logGeneralTemplate,
                                translation: desc[i].what_translation
                            };
                        }
                        that.hash[logIndex].logDescriptors[name] = array;
                        if (typeof (users) !== "undefined") {
                            var usersObj = {};
                            for (var i in users) {
                                if (users[i].USERID) {
                                    usersObj[users[i].USERID] = users[i];
                                }
                            }

                            that.hash[logIndex].logDescriptors["users"] = usersObj;
                        }
                        else {
                            that.hash[logIndex].logDescriptors["users"] = null;
                        }
                    },
                    getLogDescriptor: function (name) {
                        return that.hash[logIndex].logDescriptors[name];
                    }
                }
            },
            getAll: function () {
                return that.hash;
            },
            isDefined: function (calIndex) {
                return (typeof (that.hash[calIndex]) !== "undefined");
            },
            newLogger: function () {
                return that.setTicket();
            }
        };
    }]
}
);