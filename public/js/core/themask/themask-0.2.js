var version = "0.2";
var app = angular.module("THEMASK", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
app.directive('maschera', ['TheMask', function (TheMask) {
        function link(scope, element, attrs) {
        }
        ;
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/core/themask/themask-" + version + ".html";
            },
            restrict: 'E',
            transclude: true,
            replace: true,
            scope: {
                title: '@titlep',
                size: '@sizep',
                maskId: '=maskid',
                buttons: '@buttons'
            },
            link: link,
            controller: function ($scope, $element, $attrs, $rootScope, $timeout, TheMask) {
//                    console.log(TheMask.getAll());
//                    console.log($scope.maskId);
                console.log($scope.buttons);
                $scope.btns = $scope.buttons.split("|");
                TheMask.getMask($scope.maskId).setOpener(function () {
                    $element.show(500);
                });
                TheMask.getMask($scope.maskId).setCloser(function () {
                    $element.hide(500);
                });
                $scope.modalClass = function () {
                    if (typeof ($scope.size) !== "undefined") {
                        switch ($scope.size) {
                            case "lg":
                                return "modal-dialog modal-lg";
                                break;
                            case "sm":
                                return "modal-dialog modal-sm";
                                break;
                            default:
                                return "modal-dialog";
                                break;
                        }
                    }
                    else {
                        return "modal-dialog";
                    }

                };
                $element.on("DOMNodeInserted", function () {
                    var dimensionatore = $element.find(".dimensionatore")
                    if (!dimensionatore.length)
                        return;
                    var h = dimensionatore.height();
                    var top = dimensionatore.offset().top;
                    var winh = $(window).height();
                    var newh = winh - 2 * top;
                    if (h > newh)
                        dimensionatore.height(newh);
                    else
                        newh = h;
                    var head = dimensionatore.find(".dimensionatore-head");
                    if (!head.length) {
                        return;
                    }
                    var tail = $element.find(".dimensionatore").find(".dimensionatore-tail");
                    if (!tail.length) {
                        return;
                    }
                    head.height("auto");
                    tail.height(newh - head.height());


                });

                $scope.btnClick = function (btn_no) {
                    $rootScope.$emit("TheMask said something", {btn: btn_no, maskid: $scope.maskId});
                }
//                    $scope.items = ['item1', 'item2', 'item3'];
//                    $scope.selected = {
//                        item: $scope.items[0]
//                    };



            }
        };
    }]);
app.provider("TheMask", function () {
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
            var tableIndex = this.randomString(10);
            if (typeof (this.hash[tableIndex]) === "undefined") {
                flag = false;
            }
        }
        this.hash[tableIndex] = {
            open: function () {
            },
            close: function () {
            }
        };
        return [tableIndex, this.hash[tableIndex]];
    }
    this.lock = false;
    this.hash = [];
    this.$get = function ($filter, $rootScope) {
        var that = this;
        return {
            getMask: function (maskIndex) {
                return {
                    setOpener: function (callback) {

                        that.hash[maskIndex].open = function () {
                            callback();
                        };
                    },
                    setCloser: function (callback) {
                        that.hash[maskIndex].close = function () {
                            callback();
                        };
                    },
                    show: function () {
                        that.hash[maskIndex].open();
                    },
                    hide: function () {
                        that.hash[maskIndex].close();
                    },
                    canShow: function () {

                    }
                }
            },
            getAll: function () {
                return that.hash;
            },
            isDefined: function (maskIndex) {
                return (typeof (that.hash[maskIndex]) !== "undefined");
            },
            newMask: function () {
                return that.setTicket();
            }
        };
    }
});

