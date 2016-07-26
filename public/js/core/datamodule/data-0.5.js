
var DATAMODULE = angular.module("DATAMODULE", [], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);
DATAMODULE.provider("DataService", function () {
    this.version = "0.5";
    this.conf = {};
    this.conf.loaded = false;
    this.conf.config = [];
    this.hash = [];
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
        this.registeredServices = [];
        this.ping = [];
        this.pingRef = null;
        return [opIndex, this.hash[opIndex]];
    };
    this.$get = ['$rootScope', '$http', '$interval', '$timeout', function giovanni($rootScope, $http, $interval, $timeout) {
            var argumentsForGiovanni = [$rootScope, $http, $interval, $timeout];
            this.getProviderVersion = function () {
                return that.version;
            };
            this.countRequest = function (ticketHash) {
                var request = jQuery.extend(true, {}, that.hash[ticketHash].request);
                request.headers["X-DeferredResponse-Count"] = true;
                $http(request)
                        .success(function (data, status, headers, config) {
                            if (typeof (data.length) !== "undefined" && !(isNaN(data[0].count))) {
                                that.hash[ticketHash].count = Number(data[0].count);
                                that.hash[ticketHash].downloaded = 0;
                                $rootScope.$emit("countPerformed " + ticketHash, {});
                            }
                            else {
                                that.hash[ticketHash].status = "internal_error: count is not a number";
                                that.hash[ticketHash].result = false;
                            }
                        })
                        .error(function (data, status, headers, config) {
                            that.hash[ticketHash].status = status;
                            that.hash[ticketHash].result = false;
                        });
            };
            this.partialRequest = function (ticketHash, limit) { //limit = [0,10]
                var request = jQuery.extend(true, {}, that.hash[ticketHash].request);
                request.headers["X-DeferredResponse-Limit"] = limit[0] + "|" + limit[1];
                $http(request)
                        .success(function (data, status, headers, config) {
                            that.hash[ticketHash].lastResTimestamp = new Date().getTime();
                            if (typeof (that.hash[ticketHash].response.data) === "undefined")
                                that.hash[ticketHash].response.data = [];
                            that.hash[ticketHash].response.data = that.hash[ticketHash].response.data.concat(data);
                            that.hash[ticketHash].status = status;
                            that.hash[ticketHash].downloaded += data.length;
//                        console.log(that.hash[ticketHash].response.data);
                            $rootScope.$emit("partialRequestPerformed " + ticketHash, {limit: limit});
                        })
                        .error(function (data, status, headers, config) {
                            that.hash[ticketHash].status = status;
                            that.hash[ticketHash].result = false;
                        });
            };
            return {
                find: function (ticketHash) {
                    if (typeof (that.hash[ticketHash]) !== "undefined")
                        return [ticketHash, that.hash[ticketHash]];
                    else
                        return null;
                },
                makePing: function () {
                    var URL = document.URL;
                    URL = URL.split("/public/");
                    URL = URL[0] + "/public/ping";
                    var reqDate = new Date();
                    var responsePromise = $http.get(URL)
                            .success(function (data, status, headers, config) {
                                var resDate = new Date();
                                var diff = (resDate.getTime() - reqDate.getTime());
                            })
                            .error(function (data, status, headers, config) {
                            });
                },
                initialize: function (url, successCallback, errorCallback) {
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
                                    if (typeof (successCallback) === "function")
                                        return successCallback();
                                })
                                .error(function (data, status, headers, config) {
                                    $rootScope.$emit('confFailure', []);
                                    if (typeof (errorCallback) === "function")
                                        return errorCallback();
                                });
                        return true;
                    }
                },
                initializeOnCurrentPage: function (successCallback, errorCallback) {
                    var baseurl = document.URL;
                    baseurl = baseurl.split("public/");
                    var controlleraction = baseurl[1].split("/");
                    
                    baseurl = baseurl[0] + "public/"+controlleraction[0]+"/"+controlleraction[1]+".ws";
                    return giovanni($rootScope, $http, $interval, $timeout).initialize(baseurl, successCallback, errorCallback);
                },
                getStaticResource: function (op, queryPars, message, messagePars, successCallback, errorCallback) {
                    if (that.conf.loaded) {
                        var url = that.conf.config.services[op];
                        if (!url || typeof (url) !== "string") {
                            console.log("Errore - servizio " + op + " non disponibile");
                            return;
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
                                        ticket[1].message = {message: message, messagePars: messagePars};
                                        if (typeof (message) !== "undefined") {
                                            if (typeof (messagePars) === "undefined" || !messagePars) {
                                                messagePars = {ticket: ticket[0]};
                                            }
                                            else {
                                                messagePars.ticket = ticket[0];
                                            }
                                            $rootScope.$emit(message, messagePars);
                                        }
                                        if (typeof (successCallback) === "function")
                                            successCallback(ticket);
                                    })
                                    .error(function (data, status, headers, config) {

                                        ticket[1].lastResTimestamp = new Date().getTime();
                                        ticket[1].response = data;
                                        ticket[1].status = status;
                                        ticket[1].result = false;
                                        if (typeof (message) !== "undefined") {
                                            if (messagePars) {
                                                messagePars = {result: "failure"};
                                            }
                                            $rootScope.$emit(message, messagePars);
                                        }
                                        if (typeof (errorCallback) === "function")
                                            errorCallback(ticket);
                                    });
                            return responsePromise;
                        }
                    }
                    else {
                        console.log("Missing configuration!");
                        return false;
                    }

                },
                getData: function (op, queryPars, message, messagePars, successCallback, errorCallback) {
//                result.result = "pending";
                    if (that.conf.loaded) {
                        var url = that.conf.config.services[op];
                        if (!url || typeof (url) !== "string") {
                            console.log("Errore - servizio " + op + " non disponibile");
                            return;
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
                                        ticket[1].message = {message: message, messagePars: messagePars};
                                        if (typeof (message) !== "undefined") {
                                            if (typeof (messagePars) === "undefined" || !messagePars) {
                                                messagePars = {ticket: ticket[0]};
                                            }
                                            else {
                                                messagePars.ticket = ticket[0];
                                            }
                                            $rootScope.$emit(message, messagePars);
                                        }
                                        if (typeof (successCallback) === "function")
                                            successCallback(ticket);
                                    })
                                    .error(function (data, status, headers, config) {

                                        ticket[1].lastResTimestamp = new Date().getTime();
                                        ticket[1].response = data;
                                        ticket[1].status = status;
                                        ticket[1].result = false;
                                        if (typeof (message) !== "undefined") {
                                            if (messagePars) {
                                                messagePars = {result: "failure"};
                                            }
                                            $rootScope.$emit(message, messagePars);
                                        }
                                        if (typeof (errorCallback) === "function")
                                            errorCallback(ticket);
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
                            console.log("Errore - servizio " + op + " non disponibile");
                            return;
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
                            ticket[1].message = {message: message, messagePars: messagePars};
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
                                that.partialRequest(ticket[0], [ticket[1].chunks.shift(), ticket[1].chunckLength]);
                                that.partialRequest(ticket[0], [ticket[1].chunks.shift(), ticket[1].chunckLength]);
                                that.partialRequest(ticket[0], [ticket[1].chunks.shift(), ticket[1].chunckLength]);
                            });
                            $rootScope.$on("partialRequestPerformed " + ticket[0], function (event, mass) {
                                var limit = mass.limit;
                                if (ticket[1].downloaded < ticket[1].count) {
                                    if (ticket[1].chunks.length)
                                        that.partialRequest(ticket[0], [ticket[1].chunks.shift(), ticket[1].chunckLength]);
                                }
                                else if (ticket[1].downloaded >= ticket[1].count) {
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
                            console.log("Errore - servizio " + op + " non disponibile");
                            return;
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
                postData: function (op, dataObj, message, messagePars, successCallback, errorCallback) {

//                result.result = "pending";
                    if (that.conf.loaded) {
                        var url = that.conf.config.services[op];
                        if (!url || typeof (url) !== "string") {
                            console.log("Errore - servizio " + op + " non disponibile");
                            return;
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
                            ticket[1].message = {message: message, messagePars: messagePars};
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
                                        if (typeof (successCallback) === "function")
                                            successCallback(ticket);
                                    })
                                    .error(function (data, status, headers, config) {
                                        ticket[1].lastResTimestamp = new Date().getTime();
                                        ticket[1].response = false;
                                        ticket[1].status = status;
                                        ticket[1].result = false;
                                        ticket[1].errorData = data;
                                        if (typeof (message) !== "undefined") {
                                            if (messagePars) {
                                                messagePars = {result: "failure"};
                                            }
                                            $rootScope.$emit(message, messagePars);
                                        }
                                        if (typeof (errorCallback) === "function")
                                            errorCallback(ticket);
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
        }];
});
