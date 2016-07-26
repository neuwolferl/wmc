
var version = "0.1";
var common = angular.module("COMMON", ['COMMONINCLUDE', 'ngAnimate'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});


common.directive('validatepiva', ['Common', function (Common) {
        return {
            require: 'ngModel',
            link: function (scope, elm, attrs, ctrl) {
                ctrl.$validators.piva = function (modelValue, viewValue) {
                    return Common.checkPiva(modelValue);
                };
            }
        };
    }]);
common.directive('validatecf', ['Common', function (Common) {
        return {
            require: 'ngModel',
            link: function (scope, elm, attrs, ctrl) {
                ctrl.$validators.piva = function (modelValue, viewValue) {
                    return Common.checkCf(modelValue);
                };
            }
        };
    }]);
common.directive('validateemptyoption', ['Common', function (Common) {
        return {
            require: 'ngModel',
            link: function (scope, elm, attrs, ctrl) {

                ctrl.$validators.emptyoption = function (modelValue, viewValue) {
                    if (ctrl.$isEmpty(modelValue)) {
                        return false;
                    }
                    return (modelValue.length > 0);
                };
            }
        };
    }]);
common.directive('validatecap', ['Common', function (Common) {
        return {
            require: 'ngModel',
            link: function (scope, elm, attrs, ctrl) {
                var INTEGER_REGEXP = /^\d{5}$/;
                ctrl.$validators.cap = function (modelValue, viewValue) {

                    if (ctrl.$isEmpty(modelValue)) {
                        return false;
                    }
                    if (!INTEGER_REGEXP.test(modelValue))
                        return false;
                    if (attrs && attrs.validatecap && attrs.validatecap.length) {
                        var capT = attrs.validatecap.replace(/x/g, "");
                        if (modelValue.indexOf(capT) !== 0)
                            return false;
                    }
                    return true;
                };
            }
        };
    }]);


common.directive("alertSeed", ['$compile', '$timeout', 'Common', function ($compile, $timeout, Common) {
        var version = '0.1'
        return {
            restrict: 'E',
            terminal: true,
            scope: {
                driver: "="
            },
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/core/common/alertSeed-" + version + ".html";
            },
//            controller: function($scope,$element,$attrs){
//                this.deleteAlert = function(hash){
//                    $scope.deleteAlert(hash);
//                }
//            },
            link: function (scope, element, attrs) {
                scope.alerts = [];
                if (!scope.driver || !angular.isObject(scope.driver)) {
                    console.error(scope.driver);
                    throw Error("alertSeed link fcn: driver is not valid");
                    return void(0);
                }
                var innerHTML = function (hash, html) {
                    var elm = element.find("#alert-seed-" + hash).find(".deletebtn");
                    if (elm && html && typeof html === "string" && html.length) {
                        elm.after($compile(html)(scope));
                    }
                }
                var newAlert = function (vm) {
                    var hash;
                    while (true) {
                        hash = Common.randomString(10);
                        if (!_.find(scope.alerts, {hash: hash})) {
                            break;
                        }
                    }
                    if (vm && angular.isObject(vm)) {
                        scope.alerts.push(jQuery.extend({}, vm, {hash: hash}));
                    }
                    scope.alerts.push({hash: hash});
                    return hash;
                };
                var getAlert = function (hash) {
                    var alert = _.find(scope.alerts, {hash: hash});
                    if (!alert) {
                        throw Error("alertSeed, getAlert fcn: can't find alert with hash " + hash);
                        return void(0);
                    }
                    return alert;
                };
                scope.deleteAlert = function (hash) {
                    var alertIndex = _.findIndex(scope.alerts, {hash: hash});
                    if (alertIndex === -1) {
                        throw Error("alertSeed, deleteAlert fcn: can't find alert with hash " + hash);
                        return void(0);
                    }
                    scope.alerts.splice(alertIndex, 1);
                };
                scope.driver.run = function (hash) {
                    var alert = getAlert(hash);
                    if (alert.template) {
                        innerHTML(hash, alert.template);
                    }
                    if (alert.timeout && !isNaN(alert.timeout)) {
                        $timeout(function () {
                            scope.deleteAlert(hash);
                        }, parseInt(alert.timeout));
                    }
                };
                scope.driver.newAlert = function (conf) {
                    if (!conf || !angular.isObject(conf)) {
                        var hash = newAlert();
                        return hash;
                    }
                    var vm = {};
                    if (conf.template) {
                        vm.template = conf.template;
                    }
                    if (conf.timeout) {
                        vm.timeout = conf.timeout;
                    }
                    if (conf.data) {
                        vm.data = conf.data;
                    }
                    switch (conf.type) {
                        case 0:
                        case 'info':
                            vm.type = 'info';
                            break;
                        case 1:
                        case 'success':
                            vm.type = 'success';
                            break;
                        case 2:
                        case 'warning':
                            vm.type = 'warning';
                            break;
                        case 3:
                        case 'danger':
                            vm.type = 'danger';
                            break;
                    }
                    var hash = newAlert(vm);
                    return hash;
                };
                scope.driver.setType = function (hash, type) {
                    var alert = getAlert(hash);
                    switch (type) {
                        case 0:
                        case 'info':
                            alert.type = 'info';
                            break;
                        case 1:
                        case 'success':
                            alert.type = 'success';
                            break;
                        case 2:
                        case 'warning':
                            alert.type = 'warning';
                            break;
                        case 3:
                        case 'danger':
                            alert.type = 'danger';
                            break;
                    }
                }
                scope.driver.setTemplate = function (hash, tpl) {
                    var alert = getAlert(hash);
                    alert.template = tpl;
                };
                scope.driver.setData = function (hash, data) {
                    var alert = getAlert(hash);
                    alert.data = data;
                };
                scope.driver.setTimeout = function (hash, timeout) {
                    var alert = getAlert(hash);
                    if (isNaN(timeout)) {
                        throw Error("alertSeed, setTimeout fcn: invalid timeout " + timeout);
                        return void(0);
                    }
                    alert.timeout = parseInt(timeout);
                };
                scope.driver.getAllHashes = function () {
                    var hashes = [];
                    for (var i in scope.alerts) {
                        hashes.push(scope.alerts[i].hash);
                    }
                    return hashes;
                }
            }
        }
    }]);




common.directive("dynamicName", ['$compile', function ($compile) { // permette di inserire dinamicamente l'attributo name
        return {
            restrict: "A",
            terminal: true,
            priority: 1000,
            link: function (scope, element, attrs) {
                element.attr("name", scope.$eval(attrs.dynamicName));
                element.removeAttr("dynamic-name");
                $compile(element)(scope);
            }
        }
    }]);

common.directive('errorMask', [function () {
        function link(scope, element, attrs) {
        }
        ;
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/core/common/errorMask.html";
            },
            restrict: 'E',
            scope: {
            },
            controller: function ($scope, $element, $attrs, $rootScope, $timeout, $interval) {
            }
        };
    }]);
common.provider("lD", function () {
    this.$get = function ($window) {
        return $window._;
    }
});
common.provider("Common", ['resourceDefinitions', function (resourceDefinitions) {
        var that = this;
        if (typeof (_) !== "function") {
            console.log("Errore, non trovo lodash!");
            return;
        }
        this.linkCtrl = null;
        this.customResources = {};
        this.fits = function (a, b, onlyStruct, strict) {
            if (typeof (a) != typeof (b) || typeof (a.length) != typeof (b.length)) {
                return false;
            }
            if (typeof (a) === "string" || typeof (a) === "number" || typeof (a) === "boolean") {
                return (strict ? ((onlyStruct && typeof (a) === typeof (b)) || a === b) : (onlyStruct || a == b));
            }
            if (typeof (a) === "object") { //vettore o oggetto
                for (var i in a) {
                    if (a.hasOwnProperty(i)) {
                        if (typeof (b[i]) === "undefined")
                            return false;
                        if (!that.fits(a[i], b[i], onlyStruct, strict))
                            return false;
                    }
                }
                return true;
            }
        };
        this.checkDateStr = function (str) {
            var dateRE = /^((\d{4})-(\d{2})-(\d{2})|(\d{4})\/(\d{2})\/(\d{2})|(\d{2})-(\d{2})-(\d{4})|(\d{2})\/(\d{2})\/(\d{4}))$/;
            var match = str.match(dateRE);
            return match ? match : false;
        };
        this.checkTimeStr = function (str) {
            var timeRE = /^((\d{2}):(\d{2})(:(\d{2}))?\s{0,1}([ap]m)?)$/;
            var match = str.match(timeRE);
            return match ? match : false;
        };
        this.checkDateTimeStr = function (str) {
            var dateRE = /^((\d{4})-(\d{1,2})-(\d{1,2})|(\d{4})\/(\d{1,2})\/(\d{1,2})|(\d{1,2})-(\d{1,2})-(\d{4})|(\d{1,2})\/(\d{1,2})\/(\d{4}))((\s{0,1}|\s{0,1}@\s{0,1})((\d{2}):(\d{2})(:\d{2})?\s{0,1}([ap]m)?))?$/;
            match = str.match(dateRE);
            return match ? match : false;
        };
        this.strToDate = function (str) {
            var ore, minuti, secondi, giorno, mese, anno;
            var date = new Date();
            date.setDate(1);
            var match = that.checkDateTimeStr(_.trim(str));
//            console.log(match); 
            if (match) {
                anno = match[2];
                anno = anno ? anno : match[5];
                anno = anno ? anno : match[10];
                anno = anno ? anno : match[13];
                mese = match[3];
                mese = mese ? mese : match[6];
                mese = mese ? mese : match[9];
                mese = mese ? mese : match[12];
                giorno = match[4];
                giorno = giorno ? giorno : match[7];
                giorno = giorno ? giorno : match[8];
                giorno = giorno ? giorno : match[11];
                ore = match[17];
                ore = ore ? ore : "00";
                minuti = match[18];
                minuti = minuti ? minuti : "00";
                secondi = match[19];
                secondi = secondi ? secondi : "00";
                secondi = secondi ? secondi.replace(":", "") : "00";
                ore = (match[20] === "am" && ore === "12" ? "00" : ore);
                ore = (match[20] === "pm" ? (Number(ore) + 12) + "" : ore);
                ore = (ore === "24" ? "12" : ore);
                date.setFullYear(Number(anno));
                date.setMonth(Number(mese) - 1);
                date.setDate(Number(giorno));
                date.setHours(Number(ore));
                date.setMinutes(Number(minuti));
                date.setSeconds(Number(secondi));
//                console.log(anno, mese, giorno, ore, minuti, secondi);
//                console.log(date);

                return date;
            }
            return str;
        };
        this.dateMultiRep = function (date) {
            return {
                date: date,
                dateEngString: date.getFullYear() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + (date.getDate() < 10 ? "0" : "") + date.getDate(),
                dateItaString: (date.getDate() < 10 ? "0" : "") + date.getDate() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + date.getFullYear(),
                dateFullEngString: date.getFullYear() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + (date.getDate() < 10 ? "0" : "") + date.getDate() + " " + (date.getHours() < 10 ? "0" : "") + date.getHours() + ":" + (date.getMinutes() < 10 ? "0" : "") + date.getMinutes() + ":" + (date.getSeconds() < 10 ? "0" : "") + date.getSeconds(),
                dateFullItaString: (date.getDate() < 10 ? "0" : "") + date.getDate() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + date.getFullYear() + " " + (date.getHours() < 10 ? "0" : "") + date.getHours() + ":" + (date.getMinutes() < 10 ? "0" : "") + date.getMinutes() + ":" + (date.getSeconds() < 10 ? "0" : "") + date.getSeconds()
            }
        };
        this.$get = [function giovanni() {
                var pippo = this;
                return {
                    pageOfficer: {
                        linkCtrl: function (ctrl) {
                            that.linkCtrl = ctrl;
                            that.linkCtrl.errorMask = false;
                        },
                        destroyPage: function () {
                            if (!that.linkCtrl)
                                return;
                            that.linkCtrl.errorMask = true;
                            $(".container").find(".row").children().not("error-mask").each(function () {
                                $(this).hide();
                            });
                        },
                        attachToScope: function (sc) {
                            if (sc.pageOfficer) {
                                sc.pageOfficer = null;
                                delete sc.pageOfficer;
                            }
                            sc.pageOfficer = {
                                destroyPage: function () {
                                    if (!that.linkCtrl)
                                        return;
                                    that.linkCtrl.errorMask = true;
                                    $(".container").find(".row").children().not("error-mask").each(function () {
                                        $(this).hide();
                                    });
                                }
                            };
                        }
                    },
                    /**
                     * 
                     * @param {string} delimiter
                     * @returns {Function} Function that takes a string "delimiter" and returns a function that takes a string "string" and splits it
                     * by delimiter avoiding repetitions
                     */
                    splitNoRep: function (delimiter) {

                        return function (string) {
                            if (string) {
                                var array = string.split(delimiter);
                            }
                            else {
                                array = [];
                            }
                            var out = [];
                            for (var i in array) {
                                if (out.indexOf(array[i]) === -1) {
                                    out.push(array[i]);
                                }
                            }
                            return out;
                        }
                    },
                    /**
                     * 
                     * @param {Function} map
                     * @param {Object} outVm
                     * @returns {Function} Takes a map and returns a function that takes an array of inVm objects and returns an array of outVm
                     * object holdng input values.
                     */
                    expandArrayItems: function (map) {

                        return function (array) {

                        }
                    },
                    /**
                     * @param {string} resourceName
                     * @returns {Function} Returns a function which search searchObj-pattern in array and returns first-occurrence index or -1 if not found
                     * or -2 if searchObj doesnt fit resource definition
                     */
                    indexOfFor: function (resourceName) {
                        var def = null;
                        if (that.customResources[resourceName]) {
                            def = that.customResources[resourceName];
                        }
                        else if (resourceDefinitions[resourceName]) {
                            def = resourceDefinitions[resourceName];
                        }

                        if (def) {
                            var fcn = function (array, searchObj, strict) {
                                if (typeof (strict) === "undefined")
                                    strict = false;
                                console.log(searchObj, def);
                                if (Object.getOwnPropertyNames(def).length > 0 && !that.fits(searchObj, def, true, false)) //<<risorsa vuota va sempre bene
                                    return -2;
//                                console.log(def);
                                for (var i in array) {
                                    if (that.fits(searchObj, array[i], false, strict))
                                        return i;
                                }
                                return -1;
                            }
                            return fcn;
                        }
                        else
                            return function (array, searchObj) {
                                for (var i in array) {
                                    if (that.fits(searchObj, array[i]))
                                        return i;
                                }
                                return -1;
                            }
                    },
                    adaptToRes: function (resourceName, being) {
                        var def = null;
                        if (that.customResources[resourceName]) {
                            def = that.customResources[resourceName];
                        }
                        else if (resourceDefinitions[resourceName]) {
                            def = resourceDefinitions[resourceName];
                        }
                        if (def) {
                            var fcn = function (obj) {
                                var newObj = {};
                                var match = {};
                                var conversion = {};
                                _.forOwn(def, function (v, k, o) {
                                    var dataType = null;
                                    if (_.isObject(v)) {
                                        if (_.isDate(v)) {
                                            dataType = "date";
                                        }
                                        else {
                                            dataType = "object"
                                        }
                                    }
                                    else {
                                        dataType = typeof (v);
                                    }
                                    _.forOwn(obj, function (vv, kk, oo) {
                                        if (_.findKey(match, kk)) {
                                            return;
                                        }
                                        if (kk === k) {
                                            match[k] = kk;
                                            switch (dataType) {
                                                case "number":
                                                    switch (typeof (vv)) {
                                                        case "number":
                                                            conversion[k] = false;
                                                            newObj[k] = vv;
                                                            break;
                                                        case "string":
                                                            //controllo date o orari:
                                                            var date = false;
                                                            date = that.strToDate(vv);
                                                            if (_.isDate(date)) {
                                                                conversion[k] = "dateStr to timestamp";
                                                                newObj[k] = date.getTime();
                                                            }
                                                            else {
                                                                conversion[k] = "str to num";
                                                                if (isNaN(vv))
                                                                    conversion[k] += " (loosing)";
                                                                newObj[k] = Number(vv);
                                                            }
                                                            break;
                                                        case "boolean":
                                                            conversion[k] = "bool to int";
                                                            newObj[k] = (vv ? 1 : 0);
                                                        case "date":
                                                            conversion[k] = "date to timestamp";
                                                            newObj[k] = vv.getTime();
                                                            break;
                                                        case "object":
                                                            conversion[k] = "obj to num";
                                                            if (isNaN(vv))
                                                                conversion[k] += " (loosing)";
                                                            var partial = _.find(vv, _.identity);
                                                            newObj[k] = partial ? Number(partial) : 0;
                                                            break;
                                                    }
                                                    break;
                                                case "string":
                                                    switch (typeof (vv)) {
                                                        case "number":
                                                            conversion[k] = "number to string";
                                                            newObj[k] = vv + "";
                                                            break;
                                                        case "string":
                                                            conversion[k] = false
                                                            newObj[k] = vv;
                                                            break;
                                                        case "boolean":
                                                            conversion[k] = "bool to string";
                                                            newObj[k] = (vv ? "true" : "false");
                                                        case "date":
                                                            conversion[k] = "date to string";
                                                            newObj[k] = vv.toString();
                                                            break;
                                                        case "object":
                                                            conversion[k] = "obj to string";
                                                            newObj[k] = JSON.stringify(vv);
                                                            break;
                                                    }
                                                    break;
                                                case "date":
                                                    switch (typeof (vv)) {
                                                        case "number":
                                                            conversion[k] = "number to date";
                                                            var date = new Date();
                                                            date.setTime(vv);
                                                            newObj[k] = date;
                                                            break;
                                                        case "string":
                                                            conversion[k] = "string to date"
                                                            var date = that.strToDate(vv);
                                                            if (_.isDate(date)) {
                                                                newObj[k] = date;
                                                            }
                                                            else {
                                                                date = new Date("0000-00-00 00:00:00");
                                                                newObj[k] = date;
                                                            }
                                                            break;
                                                        case "boolean":
                                                            conversion[k] = "bool to date (nonsense)";
                                                            newObj[k] = new Date("0000-00-00 00:00:00");
                                                        case "date":
                                                            conversion[k] = false;
                                                            newObj[k] = vv;
                                                            break;
                                                        case "object":
                                                            conversion[k] = "obj to date (nonsense)";
                                                            newObj[k] = new Date("0000-00-00 00:00:00");
                                                            break;
                                                    }
                                                    break;
                                                case "object":
                                                    switch (typeof (vv)) {
                                                        case "number":
                                                            conversion[k] = "number to obj";
                                                            newObj[k] = {"0": vv};
                                                            break;
                                                        case "string":
                                                            conversion[k] = "string to obj (trying JSON)"
                                                            try {
                                                                var js = JSON.parse(vv);
                                                            } catch (e) {
                                                                var js = {"0": vv};
                                                            }
                                                            newObj[k] = js;
                                                            break;
                                                        case "boolean":
                                                            conversion[k] = "bool to obj (nonsense)";
                                                            newObj[k] = {"0": vv};
                                                        case "date":
                                                            conversion[k] = "date to obj (identity)";
                                                            newObj[k] = vv;
                                                            break;
                                                        case "object":
                                                            conversion[k] = false;
                                                            newObj[k] = vv;
                                                            break;
                                                    }
                                                    break;
                                            }
                                        }
                                    });
                                    console.log(k, v, dataType ? dataType : typeof (v), " :::: ", match[k] ? match[k] : "nomatch", conversion[k] ? conversion[k] : "", match[k] ? obj[match[k]] : "", newObj[k] ? newObj[k] : "ERROR");
                                });
                            }
                            return fcn;
                        }

                    },
                    strToDate: function (str) {
                        return that.strToDate(str);
                    },
                    dateMultiRep: function (date) {
                        if (typeof (date) === "string")
                            date = giovanni().strToDate(date);
                        return that.dateMultiRep(date);
                    },
                    rDotString: function (str, length) {
                        var out = str.substr(0, length);
                        return (out + "...");
                    },
                    lDotString: function (str, length) {
                        var out = str.substr(Math.max(0, str.length - 1 - length), str.length - 1);
                        return ("..." + out);
                    },
                    cDotString: function (str, length) {
                        if (length >= str.length)
                            return str;
                        var l = parseInt(length / 2);
                        var out = str.substr(0, l) + "..." + str.substr(str.length - 1 - l, str.length - 1);
                        return out;
                    },
                    showOnly: function (array, mask) {
                        var out = [];
                        for (var i in array) {
                            var item = {};
                            for (var k in mask) {
                                if (mask.hasOwnProperty(k) && typeof (array[i][k]) !== "undefined") {
                                    item[k] = array[i][k];
                                }
                            }
                            out.push(item);
                        }
                    },
                    verbItaDate: function (date, spec) {
                        var d;
                        var mesi = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
                        if (!date)
                            return "ND";
                        if (!date.getTime) {
                            d = new Date(date);
                        }
                        else {
                            d = date;
                        }
                        if (isNaN(date.getTime())) {
                            return "ND";
                        }
                        var out = (date.getDate() + ' ' + mesi[date.getMonth()] + ' ' + date.getFullYear());
                        if (spec) {
                            var today = new Date();
                            today.setHours(0);
                            today.setMinutes(0);
                            today.setSeconds(0);
                            today.setMilliseconds(0);
                            var dd = new Date();
                            dd.setTime(date.getTime());
//                            dd.setHours(0);
//                            dd.setMinutes(0);
//                            dd.setSeconds(1);
                            var diff = parseInt((dd.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
                            if (diff === -1) {
                                out = [out, "Ieri"];
                            }
                            else if (diff === 0) {
                                out = [out, "Oggi"];
                            }
                            else if (diff === 1) {
                                out = [out, "Domani"];
                            }
                            else {
                                out = [out, ""];
                            }

                        }
                        return out;
                    },
                    verbItaDateNoYear: function (date, spec) {
                        var d;
                        var mesi = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];
                        if (!date)
                            return "ND";
                        if (!date.getTime) {
                            d = new Date(date);
                        }
                        else {
                            d = date;
                        }
                        if (isNaN(date.getTime())) {
                            return "ND";
                        }
                        var out = (date.getDate() + ' ' + mesi[date.getMonth()]);
                        if (spec) {
                            var today = new Date();
                            today.setHours(0);
                            today.setMinutes(0);
                            today.setSeconds(0);
                            today.setMilliseconds(0);
                            var dd = new Date();
                            dd.setTime(date.getTime());
//                            dd.setHours(0);
//                            dd.setMinutes(0);
//                            dd.setSeconds(1);
                            var diff = parseInt((dd.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));
                            if (diff === -1) {
                                out = [out, "Ieri"];
                            }
                            else if (diff === 0) {
                                out = [out, "Oggi"];
                            }
                            else if (diff === 1) {
                                out = [out, "Domani"];
                            }
                            else {
                                out = [out, ""];
                            }

                        }
                        return out;
                    },
                    isValidDate: function (d) {
                        if (!d.getTime || isNaN(d.getTime())) {
                            return false;
                        }
                        return true;
                    },
                    /**
                     * Calcola quanti giorni passano da date2 a date1
                     * @param date/string date1
                     * @param date/string date2
                     * @returns int
                     */
                    diffInDays: function (date1, date2) {
                        var d1, d2;
                        if (!giovanni().isValidDate(date1)) {
                            d1 = new Date(date1);
                        }
                        else {
                            d1 = new Date();
                            d1.setTime(date1.getTime());
                        }
                        if (!giovanni().isValidDate(d1)) {
                            console.error("Fatal Error - Common.diffInDays: invalid date1", date1);
                            throw Error("Fatal Error - Common.diffInDays: invalid date1");
                        }
                        if (!giovanni().isValidDate(date2)) {
                            d2 = new Date(date2);
                        }
                        else {
                            d2 = new Date();
                            d2.setTime(date2.getTime());
                        }
                        if (!giovanni().isValidDate(d2)) {
                            console.error("Fatal Error - Common.diffInDays: invalid date2", date2);
                            throw Error("Fatal Error - Common.diffInDays: invalid date2");
                        }
                        d1.setHours(0);
                        d1.setMinutes(0);
                        d1.setSeconds(0);
                        d1.setMilliseconds(0);
                        d2.setHours(0);
                        d2.setMinutes(0);
                        d2.setSeconds(0);
                        d2.setMilliseconds(0);
                        var diff = parseInt((d1.getTime() - d2.getTime()) / (1000 * 60 * 60 * 24));
                        return diff;
                    },
                    diffInDateComparedToStep: function (date1, date2, step) {
                        var d1, d2;
                        if (!giovanni().isValidDate(date1)) {
                            d1 = new Date(date1);
                        }
                        else {
                            d1 = new Date();
                            d1.setTime(date1.getTime());
                        }
                        if (!giovanni().isValidDate(d1)) {
                            console.error("Fatal Error - Common.diffInDays: invalid date1", date1);
                            throw Error("Fatal Error - Common.diffInDays: invalid date1");
                        }
                        if (!giovanni().isValidDate(date2)) {
                            d2 = new Date(date2);
                        }
                        else {
                            d2 = new Date();
                            d2.setTime(date2.getTime());
                        }
                        if (!giovanni().isValidDate(d2)) {
                            console.error("Fatal Error - Common.diffInDays: invalid date2", date2);
                            throw Error("Fatal Error - Common.diffInDays: invalid date2");
                        }
                        var d1Pos = d1.getTime() / step;
                        var d2Pos = d2.getTime() / step;
//                        console.log(d1, d2, d1Pos, d2Pos, step);
                        var diff = parseInt(d1Pos) - parseInt(d2Pos) - 1;
                        return diff;
                    },
                    /**
                     * 
                     * @param object scope Lo scope di partenza, di solito quello locale
                     * @param string what Nome della "cosa" da cercare
                     * @param string whatShouldBe Tipo della "cosa" da cercare - function/object
                     * @returns {undefined}
                     */
                    findInParentScope: function (scope, what, whatShouldBe) {
                        if (!what || !what.length) {
                            return;
                        }
                        var s = scope;
                        switch (whatShouldBe) {
                            case "function":
                                while (true) {
                                    if (angular.isFunction(s[what])) {
                                        return [s, s[what]];
                                        break;
                                    }
                                    else {
                                        s = s.$parent;
                                        if (!s) {
                                            break;
                                        }
                                    }
                                }
                                return null;
                                break;
                            case "object":
                                while (true) {
                                    if (angular.isObject(s[what])) {
                                        return [s, s[what]];
                                        break;
                                    }
                                    else {
                                        s = s.$parent;
                                        if (!s) {
                                            break;
                                        }
                                    }
                                }
                                return null;
                                break;
                            case "all":
                                while (true) {
                                    if (typeof (s[what]) !== "undefined") {
                                        return [s, s[what]];
                                        break;
                                    }
                                    else {
                                        s = s.$parent;
                                        if (!s) {
                                            break;
                                        }
                                    }
                                }
                                return null;
                                break;
                            default:
                                return null;
                        }
                    },
                    showTime: function (date) {
                        if (!giovanni().isValidDate(date)) {
                            return "--:--";
                        }
                        return (date.getHours() < 10 ? "0" : "") + date.getHours() + ":" + (date.getMinutes() < 10 ? "0" : "") + date.getMinutes();
                    },
                    linMap: function (x1, x2, y1, y2, forceInt) {
                        if (y1 === y2) {
                            if (x1 === x2) {
                                return function (x) {
                                    return (forceInt ? parseInt(x) : x);
                                };
                            }
                            return function (x) {
                                return (forceInt ? parseInt(x) : y1);
                            };
                        }
                        return function (x) {
                            var z = ((x - x1) * (y2 - y1) / (x2 - x1)) + y1;
                            return (forceInt ? parseInt(z) : z);
                        }
                    },
                    randomString: function (le)
                    {
                        var text = "";
                        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
                        for (var i = 0; i < le; i++)
                            text += possible.charAt(Math.floor(Math.random() * possible.length));
                        return text;
                    },
                    applyDefaultObject: function (defaultObject, object) {
                        if (!angular.isObject(defaultObject))
                            throw Error("Warning: in applyDefaultObject fcn --- invalid defaultObject", defaultObject);
                        if (!angular.isObject(object))
                            throw Error("Warning: in applyDefaultObject fcn --- invalid object", object);
                        for (var i in defaultObject) {
                            if (defaultObject.hasOwnProperty(i)) {
                                if (!object[i]) {
                                    if (angular.isObject(defaultObject[i])) {
                                        object[i] = angular.copy(defaultObject[i]);
                                    }
                                    else {
                                        object[i] = defaultObject[i];
                                        +" " + app.ora_visita
                                    }
                                }
                            }
                        }
                    },
                    extractFromObject: function (object, descriptor) {
                        if (!angular.isObject(object))
                            throw Error("Warning: in extractFromObject fcn --- invalid object", object);
                        switch (typeof descriptor) {
                            case 'string':
                                if (object.hasOwnProperty(descriptor)) {
                                    return object[descriptor];
                                }
                                else {
                                    return void(0);
                                }
                                break;
                            case 'function':
                                return descriptor(object);
                        }
                    },
                    rawSearchInObject: function (object, string) {
                        if (!string) {
                            return true;
                        }
                        if (!angular.isObject(object)) {
                            throw Error("rawSearchInObject Fcn: provided object is not valid", object, string);
                            return void(0);
                        }
                        var out = false;
                        for (var i in object) {
                            if (object.hasOwnProperty(i)) {
                                if (angular.isObject(object[i])) {
                                    out = out || giovanni().rawSearchInObject(object[i], string);
                                }
                                else if (angular.isArray(object[i])) {
                                    for (var j in object[i]) {
                                        out = out || giovanni().rawSearchInObject(object[i][j], string);
                                    }
                                }
                                else {
                                    var context = object[i].toString().toLowerCase();
                                    out = out || (context.indexOf(string) > -1);
                                }
                            }
                        }
                        return out;
                    },
                    countdownOnObject: function (intervalServ, object, timeField, count, step, callback) {
                        object[timeField] = count;
                        var int;
                        int = intervalServ(function () {
                            object[timeField] = Math.max(0, object[timeField] - step);
                            if (object[timeField] === 0) {
                                intervalServ.cancel(int);
                                if (callback && angular.isFunction(callback))
                                    callback();
                            }
                        }, step);
                    },
                    realTrim: function (string) {
                        return string.replace(/^\s+|\s+$/gm, '');
                    },
                    superRealTrim: function (string) {
                        return string.replace(/[^\x20-\x7E]+/g, '').replace(/^\s+|\s+$/gm, '');
                    },
                    pad10: function (padding) {
                        return (padding < 10 ? "0" : "") + padding;
                    },
                    listFilt: function (input) {
                        if (angular.isString(input)) {
                            return input;
                        }
                        if (angular.isArray(input)) {
                            return input.join(", ")
                        }
                        if (angular.isObject(input)) {
                            var out = "";
                            for (var i in input) {
                                out += input[i];
                            }
                            return out;
                        }
                    },
                    whereAmI: function (chiave) {
                        if (!pippo.reg || !angular.isArray(pippo.reg))
                            pippo.reg = [];
                        return {
                            register: function () {
                                while (true) {
                                    var chiave = giovanni().randomString(10);
                                    if (typeof (pippo.reg[chiave]) === "undefined") {
                                        pippo.reg[chiave] = {
                                            status: ""
                                        }
                                    }
                                    return chiave;
                                }
                            },
                            get: function () {
                                if (typeof pippo.reg[chiave] === "undefined" || typeof pippo.reg[chiave]["status"] === "undefined") {
                                    return null;
                                }
                                return pippo.reg[chiave]["status"];
                            },
                            put: function (newStatus) {
                                if (typeof pippo.reg[chiave] === "undefined" || typeof pippo.reg[chiave]["status"] === "undefined") {
                                    return null;
                                }
                                pippo.reg[chiave]["status"] = newStatus;
                                return pippo.reg[chiave]["status"];
                            }
                        }
                    },
                    wsDataAnalyze: function (chiave) {
                        if (!pippo.dataAnalyzer || !angular.isArray(pippo.dataAnalyzer))
                            pippo.dataAnalyzer = [];
                        return {
                            register: function () {
                                while (true) {
                                    var chiave = giovanni().randomString(10);
                                    if (typeof (pippo.dataAnalyzer[chiave]) === "undefined") {
                                        pippo.dataAnalyzer[chiave] = {
                                            checkers: []
                                        }
                                    }
                                    return chiave;
                                }
                            },
                            addChecker: function (checker) {
                                if (!checker || !angular.isFunction(checker)) {
                                    console.error("GIVEN ", checker);
                                    throw Error("Given checker is not a valid function");
                                }
                                if (typeof pippo.dataAnalyzer[chiave] === "undefined") {
                                    console.error("CHIAVE ", chiave);
                                    throw Error("Unknow data analyzer");
                                }
                                pippo.dataAnalyzer[chiave].checkers.push(checker);
//                                console.log(chiave);
                                return giovanni().wsDataAnalyze(chiave);
                            },
                            check: function (data, out) {
                                if (typeof pippo.dataAnalyzer[chiave] === "undefined") {
                                    console.error("CHIAVE ", chiave);
                                    throw Error("Unknow data analyzer");
                                }
                                if (!angular.isArray(data)) {
                                    console.error("GIVEN ", data);
                                    throw Error("Analyzing data must be an array, " + typeof (data) + " given");
                                }
                                out.errors = []
                                out.good = [];
                                out.evil = [];
                                for (var i in data) {
                                    var flag = true;
                                    for (var j in pippo.dataAnalyzer[chiave].checkers) {
                                        var checker = pippo.dataAnalyzer[chiave].checkers[j];
                                        flag = flag && checker(data[i], function (err) {
                                            out.errors.push(err);
                                        });
                                    }
                                    (flag) ? out.good.push(data[i]) : out.evil.push(data[i]);
                                }
                                return;
                            },
                            listCheckers: function () {
                                return pippo.dataAnalyzer[chiave].checkers;
                            }
                        }
                    },
                    checkPiva: function (piva) {
                        if (!piva || !piva.length || piva.length !== 11) {
                            return false;
                        }
                        var X = parseInt(piva[0]) + parseInt(piva[2]) + parseInt(piva[4]) + parseInt(piva[6]) + parseInt(piva[8]);
                        var Y = parseInt(2 * piva[1] - (2 * piva[1] > 9 ? 9 : 0)) + parseInt(2 * piva[3] - (2 * piva[3] > 9 ? 9 : 0)) + parseInt(2 * piva[5] - (2 * piva[5] > 9 ? 9 : 0)) + parseInt(2 * piva[7] - (2 * piva[7] > 9 ? 9 : 0)) + parseInt(2 * piva[9] - (2 * piva[9] > 9 ? 9 : 0));
                        var T = (X + Y) % 10;
                        var C = (10 - T) % 10;
                        return ((parseInt(piva[10])) === C);
                    },
                    checkCf: function (cf) {
                        function ControllaCF(cf)
                        {
                            var i, s, set1, set2, setpari, setdisp;
                            set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                            set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
                            setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                            setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
                            s = 0;
                            for (i = 1; i <= 13; i += 2)
                                s += setpari.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
                            for (i = 0; i <= 14; i += 2)
                                s += setdisp.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
                            if (s % 26 != cf.charCodeAt(15) - 'A'.charCodeAt(0))
                                return false;
                            return true;
                        }
                        if (!cf || !cf.length || cf.length !== 16) {
                            return false;
                        }
                        var CFexpr = /^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$/;
                        ;
                        if (!CFexpr.test(cf)) {
                            console.log("cf non passa test ereg");
                            return false;
                        }
                        return ControllaCF(cf.toUpperCase());
                    },
                    getRegioniItaliane: function () {
                        return ["Abruzzo", "Basilicata", "Calabria", "Campania", "Emilia-Romagna", "Friuli-Venezia Giulia", "Lazio",
                            "Liguria", "Lombardia", "Marche", "Molise", "Piemonte", "Puglia", "Sardegna", "Sicilia", "Toscana", "Trentino-Alto Adige",
                            "Umbria", "Valle d'Aosta", "Veneto"];
                    },
                    getProvinceItaliane: function (regione) {
                        var province = {
                            "Abruzzo": ["L'Aquila", "Teramo", "Chieti", "Pescara"],
                            "Basilicata": ["Matera", "Potenza"],
                            "Calabria": ["Cosenza", "Reggio Calabria", "Catanzaro", "Crotone", "Vibo Valentia"],
                            "Campania": ["Salerno", "Benevento", "Napoli", "Avellino", "Caserta"],
                            "Emilia-Romagna": ["Ravenna", "Modena", "Ferrara", "Rimini", "Piacenza", "Forli-Cesena", "Bologna", "Reggio Emilia", "Parma", "Forli"],
                            "Friuli-Venezia Giulia": ["Trieste", "Pordenone", "Udine", "Gorizia"],
                            "Lazio": ["Latina", "Roma", "Frosinone", "Rieti", "Viterbo"],
                            "Liguria": ["Imperia", "Savona", "Genova", "La Spezia"],
                            "Lombardia": ["Bergamo", "Monza e della Brianza", "Lodi", "Como", "Sondrio", "Milano", "Lecco", "Brescia", "Pavia", "Mantova", "Cremona", "Varese"],
                            "Marche": ["Pesaro Urbino", "Ascoli Piceno", "Macerata", "Ancona", "Fermo"],
                            "Molise": ["Isernia", "Campobasso"],
                            "Piemonte": ["Torino", "Biella", "Vercelli", "Novara", "Asti", "Verbano-Cusio-Ossola", "Cuneo", "Alessandria"],
                            "Puglia": ["Taranto", "Brindisi", "Lecce", "Barletta-Andria-Trani", "Foggia", "Bari"],
                            "Sardegna": ["Sassari", "Ogliastra", "Carbonia-Iglesias", "Oristano", "Nuoro", "Cagliari", "Olbia-Tempio", "Medio Campidano"],
                            "Sicilia": ["Siracusa", "Messina", "Caltanissetta", "Ragusa", "Enna", "Trapani", "Agrigento", "Palermo", "Catania"],
                            "Toscana": ["Pisa", "Livorno", "Arezzo", "Prato", "Massa-Carrara", "Grosseto", "Pistoia", "Lucca", "Firenze", "Siena"],
                            "Trentino-Alto Adige": ["Trento", "Bolzano"],
                            "Umbria": ["Terni", "Perugia"],
                            "Valle d'Aosta": ["Aosta"],
                            "Veneto": ["Venezia", "Padova", "Vicenza", "Treviso", "Belluno", "Verona", "Rovigo"]
                        };
                        return (province[regione] ? province[regione] : []);
                    },
                    usDateStringToIta: function (dateString) {
                        return dateString.replace(/(\d{4})-(\d{2})-(\d{2})/, "$3-$2-$1");
                    }
                }
            }]
    }]);






