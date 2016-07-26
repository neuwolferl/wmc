
var DATAMODULE = angular.module("WORKFLOW", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
DATAMODULE.provider("WF", function () {
    this.conf = {};
    this.conf.loaded = false;
    this.conf.config = [];
    this.statuses = [];
    this.actualStatus = -1;
    var that = this;
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
            var opIndex = this.randomString(10);
            if (typeof (this.hash[opIndex]) === "undefined") {
                flag = false;
            }
        }
        this.hash[opIndex] = {
            status: "created",
            result: false,
            response: {},
            count: -1,
            downloaded: -1,
            request: {},
            isDeferred: false,
            cTimeStamp: new Date().getTime(),
            chunckLength: 100
        };
        return [opIndex, this.hash[opIndex]];
    };
    this.$get = function ($rootScope) {
        var that = this;
        return {
            find: function (ticketHash) {
                if (typeof (that.hash[ticketHash]) !== "undefined")
                    return [ticketHash, that.hash[ticketHash]];
                else
                    return null;
            },
            initialize: function (url) {
                if (typeof (url) === "undefined" && that.conf.loaded) {
                    return true;
                }
                else if (typeof (url) === "undefined" && !that.conf.loaded) {
                    return false;
                }
                else {
                    var responsePromise = $http.get(url)
                            .success(function (data, status, headers, config) {
                                that.conf.config = data;
                                that.conf.loaded = true;
                                $rootScope.$emit('confLoaded', []);
                            })
                            .error(function (data, status, headers, config) {
                                $rootScope.$emit('confFailure', []);
                            });
                    return true;
                }
            },
            getData: function (op, queryPars, message, messagePars) {
//                result.result = "pending";
                if (that.conf.loaded) {
                    var url = that.conf.config.services[op];
                    if (!url || typeof (url) !== "string") {
                        console.log("Errore");
                    }
                    else {
                        var ticket = that.setTicket();
                        ticket[1].request = {
                            method: "GET",
                            url: url,
                            params: queryPars,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        };
                        ticket[1].status = "pending";
                        ticket[1].reqTimestamp = new Date().getTime();
                        var responsePromise = $http(ticket[1].request)
                                .success(function (data, status, headers, config) {
                                    if (typeof (data.length) !== "undefined") {
                                        ticket[1].count = data.length;
                                    }
                                    ticket[1].lastResTimestamp = new Date().getTime();
                                    ticket[1].response.data = data;
                                    ticket[1].status = status;
                                    ticket[1].result = true;
                                    if (typeof (message) !== "undefined") {
                                        if (typeof (messagePars) === "undefined" || !messagePars) {
                                            messagePars = {ticket: ticket[0]};
                                        }
                                        else {
                                            messagePars.ticket = ticket[0];
                                        }
                                        $rootScope.$emit(message, messagePars);
                                    }
                                })
                                .error(function (data, status, headers, config) {
                                    ticket[1].lastResTimestamp = new Date().getTime();
                                    ticket[1].response = false;
                                    ticket[1].status = status;
                                    ticket[1].result = false;
                                    if (typeof (message) !== "undefined") {
                                        if (messagePars) {
                                            messagePars = {result: "failure"};
                                        }
                                        $rootScope.$emit(message, messagePars);
                                    }
                                });
                        return ticket;
                    }
                }
                else {
                    console.log("Missing configuration!");
                    return false;
                }

            },
            getDeferredData: function (op, queryPars, message, messagePars, asArray) {
                if (that.conf.loaded) {
                    var url = that.conf.config.services[op];
                    if (!url || typeof (url) !== "string") {
                        console.log("Errore");
                    }
                    else {
                        var ticket = that.setTicket();
                        ticket[1].request = {
                            method: "GET",
                            url: url,
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-DeferredResponseReady': true
                            }
                        };
                        ticket[1].isDeferred = true;
                        ticket[1].status = "pending";
                        ticket[1].reqTimestamp = new Date().getTime();
                        that.countRequest(ticket[0]);
                        $rootScope.$on("countPerformed " + ticket[0], function (event, mass) {
                            ticket[1].chunks = [];
                            var nChuncks = parseInt(ticket[1].count / ticket[1].chunckLength);
                            if (nChuncks * ticket[1].chunckLength < ticket[1].count) {
                                nChuncks++;
                            }
                            for (var i = 0; i < nChuncks; i++) {
                                ticket[1].chunks[i] = i * ticket[1].chunckLength;
                            }
                            console.log(nChuncks);
                            console.log(ticket[1].chunks);
                            that.partialRequest(ticket[0], [ticket[1].chunks.shift(), ticket[1].chunckLength]);
                            that.partialRequest(ticket[0], [ticket[1].chunks.shift(), ticket[1].chunckLength]);
                            that.partialRequest(ticket[0], [ticket[1].chunks.shift(), ticket[1].chunckLength]);
                        });
                        $rootScope.$on("partialRequestPerformed " + ticket[0], function (event, mass) {
                            var limit = mass.limit;
                            if (ticket[1].downloaded < ticket[1].count) {
                                console.log(ticket[1].downloaded + " / " + ticket[1].count);
                                if (ticket[1].chunks.length)
                                    that.partialRequest(ticket[0], [ticket[1].chunks.shift(), ticket[1].chunckLength]);
                            }
                            else if (ticket[1].downloaded >= ticket[1].count) {
                                console.log("END");
                                console.log("TIME: " + ((ticket[1].lastResTimestamp - ticket[1].cTimeStamp) / 1000) + " sec");
                            }

                        });
                        return ticket;
                    }
                }
                else {
                    console.log("Missing configuration!");
                    return false;
                }

            },
            getDeferredData2: function (op, queryPars, result, message, messagePars, asArray) {
                if (typeof (asArray) === "undefined") {
                    asArray = false;
                }
                result.result = "pending";
                if (that.conf.loaded) {
                    var url = that.conf.config.services[op];
                    if (!url || typeof (url) !== "string") {
                        console.log("Errore");
                    }
                    else {

                        var responsePromise = $http({
                            method: 'GET',
                            url: url,
//                            data: 'data=' + JSON.stringify(dataObject),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-DeferredResponseReady': true
                            }
                        })
                                .success(function (data, status, headers, config) {
                                    if (asArray) {
                                        var array = [];
                                        for (var i in data) {
                                            array.push({index: i, value: data[i]});
                                        }

                                        result.arrayData = array;
                                    }
                                    result.data = data;
                                    result.result = true;
                                    if (typeof (message) !== "undefined") {
                                        if (typeof (messagePars) === "undefined" || !messagePars) {
                                            messagePars = {ticket: ticket[0]};
                                        }
                                        else {
                                            messagePars.ticket = ticket[0];
                                        }
                                        $rootScope.$emit(message, messagePars);
                                    }
                                })
                                .error(function (data, status, headers, config) {
                                    if (typeof (message) !== "undefined") {
                                        if (messagePars) {
                                            messagePars = {result: "failure"};
                                        }
                                        $rootScope.$emit(message, messagePars);
                                    }
                                });
                    }
                }
                else {
                    console.log("Missing configuration!");
                    return false;
                }

            },
            postData: function (op, dataObj, message, messagePars) {

//                result.result = "pending";
                if (that.conf.loaded) {
                    var url = that.conf.config.services[op];
                    if (!url || typeof (url) !== "string") {
                        console.log("Errore");
                    }
                    else {
                        var ticket = that.setTicket();
                        ticket[1].request = {
                            method: "POST",
                            url: url,
                            data: $.param(dataObj),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            }
                        };
                        ticket[1].status = "pending";
                        ticket[1].reqTimestamp = new Date().getTime();
                        var responsePromise = $http(ticket[1].request)
                                .success(function (data, status, headers, config) {
                                    if (typeof (data.length) !== "undefined") {
                                        ticket[1].count = data.length;
                                    }
                                    ticket[1].lastResTimestamp = new Date().getTime();
                                    ticket[1].response.data = data;
                                    ticket[1].status = status;
                                    ticket[1].result = true;
                                    if (typeof (message) !== "undefined") {
                                        if (typeof (messagePars) === "undefined" || !messagePars) {
                                            messagePars = {ticket: ticket[0]};
                                        }
                                        else {
                                            messagePars.ticket = ticket[0];
                                        }
                                        $rootScope.$emit(message, messagePars);
                                    }
                                })
                                .error(function (data, status, headers, config) {
                                    ticket[1].lastResTimestamp = new Date().getTime();
                                    ticket[1].response = false;
                                    ticket[1].status = status;
                                    ticket[1].result = false;
                                    if (typeof (message) !== "undefined") {
                                        if (messagePars) {
                                            messagePars = {result: "failure"};
                                        }
                                        $rootScope.$emit(message, messagePars);
                                    }
                                });
                        return ticket;
                    }
                }
                else {
                    console.log("Missing configuration!");
                    return false;
                }

            }
        };
    };
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
 * var messageName = ""; //<<<<<<< must be a string
 * var messagePars = []; //<<<<<<< must be an array
 * SendDataService("operation_name", dataObject, loadedObject, messageName, messagePars);
 * due to response you have:
 * pippo.data = your data
 * loadedObject = {result: true, false, "pending"}
 * messageName is emitted on rootScope with mass messagePars
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



