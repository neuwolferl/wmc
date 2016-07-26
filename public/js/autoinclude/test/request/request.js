//var myres = angular.module('myresource', ['ngResource']);
//
//myres.factory('Resource', ['$resource', function ($resource) {
//        return function (url, params, methods) {
//            var defaults = {
//                update: {method: 'put', isArray: false},
//                create: {method: 'post'}
//            };
//
//            methods = angular.extend(defaults, methods);
//
//            var resource = $resource(url, params, methods);
//
//            resource.prototype.$save = function () {
//                if (!this.id) {
//                    return this.$create();
//                }
//                else {
//                    return this.$update();
//                } 
//            };
//
//            return resource;
//        };
//    }]);


var app = angular.module("RequestMod", ['TSNWCLIENT'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
app.controller("MainController", function (ClientTSNW, $rootScope) {
    this.payload = {giovanni: 27, chiave: "ciao", altrachiave: 11};
    this.response = {};
    var pippo = this;
    $rootScope.$on("getPerformed", function (event,mass) {
//        console.log(mass);
        console.log(ClientTSNW.show(mass[0]));
    });
    this.sendRequest = function () {
        console.log(ClientTSNW.get("/accounts/4321/contacts",
                null,
                {giovanni: 27, chiave: "ciao", altrachiave: 11}));
    }
//        switch (pippo.method) {
//            case 'RETRIEVE':
//                var prova = Risorsa.get(pippo.payload, function (successResult) {
//                    pippo.response = successResult;
//                }, function (errorResult) {
//                    pippo.response = errorResult;
//                });
//                break;treetree
//            case 'CREATE':
//                var prova = Risorsa.save(pippo.payload,pippo.payload, function (successResult) {
//                    pippo.response = successResult;
//                }, function (errorResult) {
//                    pippo.response = errorResult;
//                });
//                break;
//            case 'UPDATE':
//                var prova = Risorsa.update(pippo.payload,pippo.payload, function (successResult) {
//                    pippo.response = successResult;
//                }, function (errorResult) {
//                    pippo.response = errorResult;
//                });
//                break;
//            case 'DELETE':
//                var prova = Risorsa.delete(pippo.payload, function (successResult) {
//                    pippo.response = successResult;
//                }, function (errorResult) {
//                    pippo.response = errorResult;
//                });
//                break;
//        }
//    };
});

