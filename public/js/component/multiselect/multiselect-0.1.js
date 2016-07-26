

var MULTISELECT = angular.module("MULTISELECT", [], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);
MULTISELECT.directive('multiSelect', [function () {
        var version = "0.1";
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/multiselect/multiselect-" + version + ".html";
            },
            scope: {
                valueName: "@valuename",
                opts: "=opts",
                outerModel: "=model",
                model: "=innermodel"
            },
            restrict: 'E',
            link: function (s, e, a) {
            },
            controller: ['$scope', '$element', '$attrs', '$rootScope', '$timeout', '$interval',
                function ($scope, $element, $attrs, $rootScope, $timeout, $interval) {
                    var pippo = this;
                    $scope.dirty = false;
                    $scope.loaded = false;
                    $scope.colors = [
                        "#7FFFD4", "#E52B50", "#884DA7", "#ED7465", "#FFA500", "#87A96B", "#5D8AA8", "#ABCDEF", "#7FFF00", "#DF73FF",
                        "#EBB55F", "#FFFF66", "#BDB76B", "#F984E5", "#CD853F", "#FF2400", "#C0007F"
                    ];
                    $element.find(".multiselectcontainer, .multiselectul").css("height", $element.attr("height"));
                    if (!$scope.valueName) {
                        if ($attrs.valueName) {
                            $scope.valueName = $attrs.valuename;
                        }
                        else {
                            $scope.valueName = "value";
                        }
                    }

                    $scope.int = null;
                    $scope.initialize = function () {
                        $scope.loaded = false;
                        if (!$scope.model) {
                            $scope.model = [];
                        }

                        $scope.model = [];

                        for (var i in $scope.opts) {
                            var opt = $scope.opts[i];
                            var newopt = {
                                value: opt[$scope.valueName],
                                selected: $scope.outerModel.indexOf(opt[$scope.valueName]) > -1 ? true : false,
                                color: $scope.colors[i % $scope.colors.length]
                            }
                            $scope.model.push(newopt);
                        }
                        $scope.loaded = true;
                    }
                    $scope.$watch("model", function (newvalue, oldvalue) {
                        if (!$scope.loaded) {
                            return;
                        }
                        if (!newvalue || (!newvalue.length && (!$scope.opts || $scope.opts.length)))
                            return;
                        var out = [];
                        for (var i in newvalue) {
                            if (newvalue[i].selected) {
                                out.push(newvalue[i].value);
                            }
                        }

                        $scope.outerModel = out;
                        console.log("change", "outer", JSON.stringify($scope.outerModel));
                    }, true);

                    $scope.$watch("outerModel", function (newvalue, oldvalue) {
                        var flag = false;
                        for (var i in newvalue) {
                            var s = _.find($scope.model, {value: newvalue[i]});
                            if (!s || !s.selected) {
                                flag = true;
                                break;
                            }
                        }
                        if (flag) {
                            $scope.loaded = false;
                            $scope.initialize();
                        }
                    })

                    $scope.$watch("opts", function () {
                        $scope.loaded = false;
                        $scope.initialize();
                    }, true);

                    $scope.toggleSelected = function (index) {
                        if (typeof ($scope.model[index]) === "object") {
                            $scope.model[index].selected = !$scope.model[index].selected;
                        }
                        $scope.dirty = true;
                    }

                    $scope.initialize();
                    $scope.colorer = function (opt) {
                        if (opt.selected) {
                            return {'background-color': opt.color};
                        }
                        else
                            return {};
                    }
                }]
        }
        ;
    }]);