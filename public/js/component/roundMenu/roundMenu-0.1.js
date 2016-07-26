var ROUNDMENU = angular.module("ROUNDMENULIB", ['ngAnimate'], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);

ROUNDMENU.directive('roundMenu', ['$timeout', function ($timeout) {
//        
        var version = "0.1";
        return {
            restrict: "E",
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/roundMenu/roundMenu-" + version + ".html";
            },
            replace: true,
            scope: {
                roundMenuData: '=data'
            },
            link: function (scope, element, attrs) {
                if (attrs.working)
                    return;
                scope.openMenu = function(e){
                    console.log(element.find(".cn-wrapper").toggleClass("opened-nav"));
                }
                attrs.working = true;
                scope.randomString = function (le)
                {
                    var text = "";
                    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
                    for (var i = 0; i < le; i++)
                        text += possible.charAt(Math.floor(Math.random() * possible.length));
                    return text;
                };

                scope.menu = [];
                for (var i in scope.roundMenuData) {
                    scope.menu.push(
                            {
                                label: scope.roundMenuData[i].label,
                                link: scope.roundMenuData[i].value,
                            }
                    );
                }



            }
        }

    }
]);