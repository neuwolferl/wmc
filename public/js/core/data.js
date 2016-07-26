
var DATAMODULE = angular.module("DATAMODULE", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
DATAMODULE.factory('ConfiguratorService', function ($http, $rootScope) {
    var conf = {};
    conf.loaded = false;
    conf.config = [];
    conf.get = function (url) {
        var responsePromise = $http.get(url)
                .success(function (data, status, headers, config) {
                    conf.config = data;
                    conf.loaded = true;
                    $rootScope.$emit('confLoaded', []);
                })
                .error(function (data, status, headers, config) {
//                    alert("CONF AJAX failed!");
                    $rootScope.$emit('confFailure', []);
//                    console.log(data);
//                    console.log(status);
//                    console.log(headers);
//                    console.log(config);
                });
    };
    conf.load = function () {
        if (!conf.loaded) {
            conf.get();
        }
        return conf.config;
    };
    return conf;
});

/*
 * usage: 
 * var dataObject = {}; //<<<<<<< must be an object or nothing will be returned
 * var loadedObject = {}; //<<<<<<< must be an object or nothing will be returned
 * GetDataService("operation_name", dataObject, loadedObject);
 * after a while you have:
 * pippo.data = your data
 * loadedObject = {result: true, false, "pending"}
 */
DATAMODULE.factory('SendDataService', function ($http, ConfiguratorService, $rootScope) {
    var loaded = false;
    return function (op, dataObject, resultObject, messageName, messagePars) {
        resultObject.result = "pending";
        resultObject.fail = false;
        resultObject.success = false;
        var url = ConfiguratorService.load();
        url = url.services[op];
        console.log(url);
//        var split = url.split("/");
//        var first = "";
//        for (var i in split){
//            if (split[i] !== ""){
//                first = split[i];
//                break;
//            }
//        }
//        var pre = document.URL.split("/"+first);
//        url = pre[0].replace("http","https")+url;
        var responsePromise = $http({
            method: 'POST',
            url: url,
            data: 'data=' + JSON.stringify(dataObject),
            headers: {'Content-Type': 'application/x-www-form-urlencoded'}
        })
                .success(function (data, status, headers, config) {
                    resultObject.result = data.result;
                    resultObject.status = status;
                    if (data.result === "failure") {
                        resultObject.outcome = data.error;
                        resultObject.fail = true;
                    }
                    else {
                        resultObject.outcome = "Eseguito correttamente";
                        resultObject.success = true;
                    }
                    if (typeof (messageName) !== "undefined" && typeof (messagePars) !== "undefined") {
                        if (typeof (messagePars[0]) === "undefined")
                            messagePars = [];
                        $rootScope.$emit(messageName, messagePars);
                    }

                })
                .error(function (data, status, headers, config) {
                    resultObject.result = "failed post";
                    resultObject.status = status;
                    resultObject.fail = true;
                    resultObject.error = "Failed post";
                });
    };
});
DATAMODULE.factory('GetDataService', function ($http, ConfiguratorService, $rootScope) {
    var loaded = false;
    return function (op, dataObject, loadedObject, messageName, messagePars) {
        if (typeof (dataObject) !== "object") {
            dataObject = {};
        }
        loadedObject.result = "pending";
        var url = ConfiguratorService.load();
        url = url.services[op];
        var responsePromise = $http.get(url)
                .success(function (data, status, headers, config) {
                    var array = [];
                    for (var i in data) {
                        array.push({index: i, value: data[i]});
                    }
                    dataObject.data = data;
                    dataObject.arrayData = array;
                    loadedObject.result = true;
                    if (typeof (messageName) !== "undefined" && typeof (messagePars) !== "undefined") {
                        if (typeof (messagePars[0]) === "undefined")
                            messagePars = [];
                        $rootScope.$emit(messageName, messagePars);
                    }
                })
                .error(function (data, status, headers, config) {
                    if (typeof (messageName) !== "undefined" && typeof (messagePars) !== "undefined") {
                        messagePars = {result: "failure"};
                        $rootScope.$emit(messageName, messagePars);
                    }
                });
    };
});
DATAMODULE.factory('GetResUriService', function ($http, ConfiguratorService) {
    var loaded = false;
    return function (resName, resObject, resLoadedObject) {
        if (typeof (resObject) !== "object") {
            resObject = {};
        }
        resLoadedObject.result = "pending";

        var uri = ConfiguratorService.load();

        var uri = uri.resuri;

        var splitName = resName.split(".");
        var obj = resObject;
        var source = uri;
        for (var i = 0; i < splitName.length; i++) {
            var s = splitName[i];
            if (typeof (obj[s]) === "undefined") {
                if (i < splitName.length - 1) {
                    obj[s] = {};
                    obj = obj[s];
                }
                else
                    obj[s] = "";

            }
            if (typeof (source[s]) === "undefined") {
                return;
            }
            else {
                source = source[s];
            }
        }
        obj[s] = source;
    };
});

