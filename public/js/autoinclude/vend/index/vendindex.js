(function () {
    var app = angular.module("VendIndex", [ 'DATAMODULE'], ['$interpolateProvider', function ($interpolateProvider) {
            $interpolateProvider.startSymbol("--__");
            $interpolateProvider.endSymbol("__--");
        }]);
    app.controller("VendIndexController", ['DataService', '$scope', '$rootScope', '$timeout',
        function (DataService, $scope, $rootScope, $timeout) {
            
            $scope.roundmenudata = [
                {label: "menu1", link: "#ciao"},
                {label: "menu2", link: "#ehi"},
                {label: "menu3", link: "#we"}
            ]
        }]);
})();



