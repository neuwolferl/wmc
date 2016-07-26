var app = angular.module("testapi", ['DATAMODULE', 'THEMASK', 'SMARTBUTTON', 'COMMON', 'MULTIPAGE', 'SINOTTICO', 'restangular'],
        ['$interpolateProvider',
            function ($interpolateProvider) {
                $interpolateProvider.startSymbol("--__");
                $interpolateProvider.endSymbol("__--");
            }]);
app.config(['RestangularProvider', function (RestangularProvider) {
        RestangularProvider.setBaseUrl('https://ec2-54-149-19-183.us-west-2.compute.amazonaws.com/tsnwtest/public/publicapi/json');
        RestangularProvider.setParentless(true);
        RestangularProvider.setRequestSuffix(".ws");
        RestangularProvider.setRestangularFields({
            id: '__id'
        });
//        RestangularProvider.addRequestInterceptor(function (elem, operation, what, url) {
//            console.log("interceptor",elem, operation, what, url);
//            if (operation === 'put') {
//                elem._id = undefined;
//                return elem;
//            }
//            return elem;
//        });
        RestangularProvider.addFullRequestInterceptor(function (element, operation, what, url, headers, params, httpConfig) {

            //some logic here...
            console.log(element, operation, what, url, headers, params, httpConfig);
            //originally, the return below was not there... It is mandatory, if nothing is returned in that function, any POST or PUT request won't have any payload data.
            return {
                element: element,
                headers: headers,
                params: params,
                httpConfig: httpConfig
            };
        });
    }]);
app.controller("ApiController", ['$http', 'DataService', '$scope', '$rootScope', '$timeout', 'TheMask', '$interval', 'SmartButton', 'mPage', 'Sin', 'Common', 'Restangular',
    function ($http, DataService, $scope, $rootScope, $timeout, TheMask, $interval, SmartButton, mPage, Sin, Common, Restangular) {
        "use strict";
        var that = this;

        $scope.method = 1;
        $scope.setMethod = function (m) {
            $scope.method = parseInt(m);
        };
        $scope.showResults = {
            get: false,
            post: false,
            put: false,
            delete: false
        };
        $scope.results = {
            get: {},
            post: {},
            put: {},
            delete: {}
        }
        $scope.operators = [
            "=",
            "!=",
            "<",
            "<=",
            ">",
            ">=",
            "contiene",
            "non contiene"
        ];
        $scope.resourceTree = [
            "",
            "com_potential",
            "crm_cd_account",
            "crm_cd_contact",
            "sales_comapp",
            "tools_users_gusers"
        ];
        $scope.getRequest = {
            resourceName: "tools_users_gusers",
            resourceId: "",
            pars: [
//                {id: 0, key: "ragione_sociale", op: "contiene", value: "salumificio"}
            ],
            limit: {offset: 1, amount: 10}
        };
        $scope.postRequest = {
            resourceName: "",
            resourceId: "",
            pars: [
//                {id: 0, key: "ragione_sociale", op: "contiene", value: "salumificio"}
            ]
        };

        $scope.getrequestNewPar = function () {
            var id = -1;
            for (var i in $scope.getRequest.pars) {
                if ($scope.getRequest.pars[i].id > id)
                    id = $scope.getRequest.pars[i].id;
            }
            id++;
            $scope.getRequest.pars.push({id: id, key: "", op: "", value: ""});
        };
        $scope.getrequestRemPar = function (id) {
            for (var i in $scope.getRequest.pars) {
                if ($scope.getRequest.pars[i].id === id) {
                    $scope.getRequest.pars.splice(i, 1);
                    return;
                }
            }
        };
        $scope.postrequestNewPar = function () {
            var id = -1;
            for (var i in $scope.postRequest.pars) {
                if ($scope.postRequest.pars[i].id > id)
                    id = $scope.postRequest.pars[i].id;
            }
            id++;
            $scope.postRequest.pars.push({id: id, key: "", op: "=", value: ""});
        };
        $scope.postrequestRemPar = function (id) {
            for (var i in $scope.postRequest.pars) {
                if ($scope.postRequest.pars[i].id === id) {
                    $scope.postRequest.pars.splice(i, 1);
                    return;
                }
            }
        };

        $scope.canMakeGet = function () {
            var can = true;
            can = can && (!$scope.getrequestform.resourceName.$invalid);
            can = can && (!$scope.getrequestparsform.$invalid);
            can = can && ($scope.getRequest.limit.amount || $scope.getRequest.pars.length || $scope.getRequest.resourceId.length);
            return can;
        }
        $scope.makeGet = function () {
            var res = Restangular.all($scope.getRequest.resourceName);
            if ($scope.getRequest.resourceId) {
                res = Restangular.one($scope.getRequest.resourceName, $scope.getRequest.resourceId).get().then(
                        function (a, b, c) {
                            console.log("success", a, b, c);
                            $scope.results.get = a;
                        });
            }
            else {
                var params = {};
                for (var i in $scope.getRequest.pars) {
                    if (typeof (params[$scope.getRequest.pars[i].key]) === "undefined")
                        params[$scope.getRequest.pars[i].key] = {};
                    var reqop = "";
                    switch ($scope.getRequest.pars[i].op) {
                        case "=":
                            reqop = "eq";
                            break;
                        case "!=":
                            reqop = "ne";
                            break;
                        case "<":
                            reqop = "lt";
                            break;
                        case "<=":
                            reqop = "le";
                            break;
                        case ">":
                            reqop = "gt";
                            break;
                        case ">=":
                            reqop = "ge";
                            break;
                        case "contiene":
                            reqop = "cc";
                            break;
                        case "non contiene":
                            reqop = "nc";
                            break;
                        default:
                            return;
                    }
                    params[$scope.getRequest.pars[i].key][reqop] = $scope.getRequest.pars[i].value
//                    params.push($scope.getRequest.pars[i].key + reqop + $scope.getRequest.pars[i].value);
                }
                for (var i in params) {
                    if (params.hasOwnProperty(i)) {
                        params[i] = JSON.stringify(params[i]);
                    }
                }
                params["___limit"] = {offset: $scope.getRequest.limit.offset, amount: $scope.getRequest.limit.amount};
                params["___sort"] = {"ragione_sociale": "asc", "id_cliente": "desc"};
//                var p = {"like":"salumificio"}
                res = Restangular.all($scope.getRequest.resourceName).getList(params)
                        .then(
                                function (a, b, c) {
                                    console.log("success", a, b, c);
                                    $scope.results.get = a;
                                    window.results = a;
                                });
//                res = Restangular.one($scope.getRequest.resourceName, params.join("/")).get()
//                        .then(
//                        function (a, b, c) {
//                            console.log("success", a, b, c);
//                            $scope.results.get = a;
//                        });
            }
            console.log(res);
            $scope.showResults.get = true;
        }
        $scope.$watch("postRequest.resourceName", function (newvalue, oldvalue) {
            if (newvalue !== oldvalue) {
                $scope.getVMForPost();
            }
        });
        $scope.getVMForPost = function () {
            var resource = $scope.postRequest.resourceName;

            if (resource && resource !== "") {
                Restangular.one(resource, "__DESCRIBE__").get().then(function (vm, b, c) {
                    vm = vm[0];
                    if (typeof (vm["__id"]) === "undefined" || vm["__id"] === "") {
                        alert("Questa risorsa non ha id, non è possibile creare un elemento con questo sistema");
                        return;
                    }
                    else
                        $scope.postRequest.__id = vm["__id"];

                    var id = 0;
                    var pars = [];
                    for (var i in vm) {
                        if (vm.hasOwnProperty(i) && i !== "__id" && i !== vm["__id"]) {
                            pars.push({id: id++, key: i, op: "=", value: "", placeholder: vm[i]});
                        }
                    }
                    $scope.postRequest.pars = pars;


                });
            }
        }

        $scope.canMakePost = function () {
            var can = true;
            can = can && (!$scope.postrequestform.postResourceName.$invalid);
            can = can && (!$scope.postrequestparsform.$invalid);
            can = can && ($scope.postRequest.pars.length);
            return can;
        }
        $scope.makePost = function () {
            var vm = {};
            for (var i in $scope.postRequest.pars) {
                var par = $scope.postRequest.pars[i];
                vm[par.key] = par.value;
            }
            if ($scope.postRequest.resourceId !== "")
                vm[$scope.postRequest.__id] = $scope.postRequest.resourceId;
            console.log("vm", vm);
            console.log(Restangular.all($scope.getRequest.resourceName));
            var res = Restangular.all($scope.getRequest.resourceName).post(vm).then(function (a, b, c) {
                console.log(a, b, c);
            })
        }



        this.hasError = function (check) {
            if (check)
                return "has-error";
            else
                return "";
        };
        this.parHasError = function (index) {
            if (!$scope.getrequestform["par_" + index + "_0"] ||
                    !$scope.getrequestform["par_" + index + "_1"] ||
                    !$scope.getrequestform["par_" + index + "_0"])
                return "";
            if (($scope.getrequestform["par_" + index + "_0"].$dirty && $scope.getrequestform["par_" + index + "_0"].$invalid)
                    ||
                    ($scope.getrequestform["par_" + index + "_1"].$dirty && $scope.getrequestform["par_" + index + "_1"].$invalid)
                    ||
                    ($scope.getrequestform["par_" + index + "_2"].$dirty && $scope.getrequestform["par_" + index + "_2"].$invalid)
                    ) {
                console.log(index, "error");
                return "has-error";
            }
            else
                return "";
        };
        this.postParIsIndex = function (index) {
            var par = $scope.postRequest.pars[index];
            if ($scope.postRequest.__id && $scope.postRequest.__id === par.key)
                return "{background-color: #a1ecfe;}";
            return "";
        };
        this.parControlName = function (index, role) {
            return "par_" + index + "_" + role;
        };

        this.getRequestFormClass = function () {
            if ($scope.method === 1) {
                if ($scope.showResults.get) {
                    return "col-sm-4";
                }
                else {
                    return "col-sm-9";
                }
            }
            else {
                return "col-sm-9";
            }
        }










        //MAIL

        $scope.tmks = [
            "Giovanni Bianchi",
            "Giulia Castro",
            "Ida Ponti",
            "Mario Rossi"
        ];
        $scope.vends = [
            "Alberto Brudaglio",
            "Centro Stampa"
        ];
        $scope.mailRequest = {
            azienda: "",
            location: "",
            tmk: "",
            venditore: ""
        }
        $scope.mailRequestStatus = "";
        $scope.mailRequestResponse = "";
        $scope.sendMail = function () {
            $http.post('https://ec2-54-149-19-183.us-west-2.compute.amazonaws.com/tsnwtest/public/mkt/sendComMail.ws',
                    $scope.mailRequest).
                    success(function (data, status, headers, config) {
                        $scope.mailRequestStatus = status;
                        $scope.mailRequestResponse = data;
                        // this callback will be called asynchronously
                        // when the response is available
                    }).
                    error(function (data, status, headers, config) {
                        $scope.mailRequestStatus = status;
                        $scope.mailRequestResponse = data;
                        // called asynchronously if an error occurs
                        // or server returns response with an error status.
                    });
        }

    }]);


app.directive("dynamicName", ['$compile', function ($compile) {
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