var app = angular.module("TSNWCLIENT", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
})
        .run(function ($rootScope) {
            $rootScope.tsnwdata = [];
        });
app.provider("ClientTSNW", function () {

    this.randomString = function (le)
    {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < le; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    };

    this.analyzeResponse = function (requestIndex, data, status, headers, config) {
        var successCodes = ["200", "201", "202", "203", "204", "205", "206", "207",
            200, 201, 202, 203, 204, 205, 206, 207];
        var result = {};
        var meta = {};
        if (successCodes.indexOf(status) > -1) {
            if (typeof (data) === "object") {
                if (typeof (data.result) === "object") {
                    result = data.result;
                }
                if (typeof (data.meta) === "object") {
                    meta = data.meta;
                }
                this.hash[requestIndex] = {result: result, meta: meta, status: status};
            }
        }
        else {
            console.log({data: data, status: status, headers: headers, config: config});
            this.hash[requestIndex] = {result: "error", status: status};
        }
//        this.hash[requestIndex] = {data: data, status: status, headers: headers, config: config};
    };


    this.baseUrl = "https://ec2-54-213-213-176.us-west-2.compute.amazonaws.com/tsnwtest/public/tsnwapi/json";
    this.hash = [];
    this.setTicket = function () {
        var flag = true;
        while (flag) {
            var requestIndex = this.randomString(10);
            if (typeof (this.hash[requestIndex]) === "undefined") {
                flag = false;
            }
        }
        this.hash[requestIndex] = requestIndex;
        return requestIndex;
    }
    this.$get = function ($http, $rootScope) {
        var that = this;
        return {
            show: function (requestIndex) {
                return that.hash[requestIndex];
            },
            get: function (resource, id, query) {
                var requestIndex = that.setTicket();
                var url = that.baseUrl;
                url += resource;
                if (typeof (id) !== "undefined" && id) {
                    url += "/" + id + ".ws";
                }
                else if (typeof (query) === "object" && query) {
                    var qs = $.param(query);
                    url += ".ws?" + qs;
                }
                else {
                    url += ".ws";
                }

                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.analyzeResponse(requestIndex, data, status, headers, config);
                            $rootScope.$emit("getPerformed", [requestIndex]);
                        })
                        .error(function (data, status, headers, config) {
                            that.analyzeResponse(requestIndex, data, status, headers, config);
                            $rootScope.$emit("getPerformed", [requestIndex]);
                        });
                return requestIndex;
            },
            post: function (resource, vm) {
                var requestIndex = that.setTicket();
                var url = that.baseUrl;
                url += resource + ".ws";

                var responsePromise = $http({
                    method: 'POST',
                    url: url,
                    data: 'vm=' + JSON.stringify(vm),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }
                )
                        .success(function (data, status, headers, config) {
                            that.hash[requestIndex] = that.analyzeResponse(data, status, headers, config);
                            $rootScope.$emit("postPerformed", [requestIndex]);
                        })
                        .error(function (data, status, headers, config) {
                            that.hash[requestIndex] = that.analyzeResponse(data, status, headers, config);
                            $rootScope.$emit("postPerformed", [requestIndex]);
                        });
                return requestIndex;
            },
            put: function (resource, vm) {
                var requestIndex = that.setTicket();
                var url = that.baseUrl;
                url += resource + ".ws";

                var responsePromise = $http({
                    method: 'PUT',
                    url: url,
                    data: 'vm=' + JSON.stringify(vm),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                }
                )
                        .success(function (data, status, headers, config) {
                            that.hash[requestIndex] = that.analyzeResponse(data, status, headers, config);
                            $rootScope.$emit("putPerformed", [requestIndex]);
                        })
                        .error(function (data, status, headers, config) {
                            that.hash[requestIndex] = that.analyzeResponse(data, status, headers, config);
                            $rootScope.$emit("putPerformed", [requestIndex]);
                        });
                return requestIndex;
            }
        }
    };
});


