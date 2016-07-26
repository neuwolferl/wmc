
var SANG = angular.module("SANGINTHERAIN", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});



SANG.directive("customCircle", function () {
    return {
        // the following two configuration options are 
        // required for SVG custom elements.
//            templateNamespace: 'svg',
        replace: true,
        // NOTE: ng-attr- style binding is used to prevent SVG validation
        // error messages.
        template: '<circle ng-attr-cx="25" ng-attr-cy="25" ng-attr-r="25"/>',
//        template: '<circle ng-attr-cx="--__radius__--" ng-attr-cy="--__radius__--" ng-attr-r="--__radius__--"/>',
        // everything else as normal
        scope: {
            radius: '@',
        }

    };
});
