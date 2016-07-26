
var app = angular.module("THEMASK", ['SMARTBUTTON'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
app.directive('maschera', ['TheMask', function (TheMask, SmartButton) {
        var version = "0.3";
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
            link: function (scope, element, attrs) {
                element.attr("id", "myModal" + scope.title);
                var adjust = function () {
                    console.log("AGGIUSTO!");
                    var dimensionatore = element.find(".dimensionatore")
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
                    var tail = element.find(".dimensionatore").find(".dimensionatore-tail");
                    if (!tail.length) {
                        return;
                    }
                    head.height("auto");
                    tail.height(newh - head.height());
                };
                element.on("DOMSubtreeModified", adjust());
                element.on("DOMNodeInserted", adjust());
                console.log("MASK LINK " + scope.title);
                if (scope.title === "Inventario") {
                    console.log(element);
                }
            },
            controller: function ($scope, $element, $attrs, $rootScope, $timeout, TheMask, SmartButton, $interval) {
//                    console.log(TheMask.getAll());
//                    console.log($scope.maskId);
                var pippo = this;
                $scope.maskObj = TheMask.getMask($scope.maskId).getObj();
                $scope.btnClick = function (btn_no) {
                    //cerca button:
                    for (var i in $scope.btns) {
                        if ($scope.btns[i].btn[0] === btn_no) {
                            btn_no = i;
                            break;
                        }
                    }
                    $rootScope.$emit("TheMask said something", {btn: btn_no, maskid: $scope.maskId});
                    if (typeof ($scope.maskObj.btnCallBacks[btn_no]) !== "undefined") {
                        ($scope.maskObj.btnCallBacks[btn_no])();
                    }
                    return true;
                }
                $scope.btns = $scope.buttons.split("|");
                for (var i in $scope.btns) {
                    $scope.btns[i] = {label: $scope.btns[i], btn: SmartButton.newBtn()};
                }
                for (var i in $scope.btns) { //link per click
                    SmartButton.getBtn($scope.btns[i].btn[0]).setClickFcn($scope.btnClick);
                }
                TheMask.getMask($scope.maskId).setOpener(function () {
                    $element.show(500);
                    $scope.btnRefresher = $interval(function () {
                        if (typeof ($scope.maskObj.btnEnableConditions) !== "undefined") {
                            for (var i in $scope.btns) {
                                if (typeof ($scope.maskObj.btnEnableConditions[i]) === "function") {
                                    if (($scope.maskObj.btnEnableConditions[i])()) {
                                        $scope.btns[i].btn[1].enable();
                                    }
                                    else {
                                        $scope.btns[i].btn[1].disable();
                                    }
                                }
                                else {
                                    $scope.btns[i].btn[1].enable();
                                }
                            }
                        }
                    }, 500);
                });
                TheMask.getMask($scope.maskId).setCloser(function () {
                    $element.hide(500);
                    if ($scope.btnRefresher)
                        $interval.cancel($scope.btnRefresher);
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
                this.adjust = function () {
                    console.log("AGGIUSTO!");
                    var dimensionatore = $element.find(".dimensionatore")
                    if (!dimensionatore.length)
                        return;
                    var h = dimensionatore.height();
                    var top = dimensionatore.offset().top;
                    var winh = $(window).height();
                    var newh = winh - 2 * top;
                    console.log(newh);
//                    if (h > newh)
                        dimensionatore.height(newh);
//                    else
//                        newh = h;
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
                };
                $element.on("adjust", function(){
                    pippo.adjust()   
                });
                $element.find(".dimensionatore").on("DOMSubtreeModified", pippo.adjust());
//                $interval(function () {
//                    if ($scope.title === "Inventario") {
//                        console.log($element);
//                    }
//                }, 1000);

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
            var maskIndex = this.randomString(10);
            if (typeof (this.hash[maskIndex]) === "undefined") {
                flag = false;
            }
        }
        this.hash[maskIndex] = {
            open: function () {
            },
            close: function () {
            },
            btnCallBacks: [],
            btnEnableConditions: []
        };
        return [maskIndex, this.hash[maskIndex]];
    }
    this.lock = false;
    this.hash = [];
    this.$get = function ($filter, $rootScope, SmartButton) {
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
                    setBtnCallback: function (btn_no, callback) {
                        if (!that.hash[maskIndex].btnCallBacks)
                            that.hash[maskIndex].btnCallBacks = [];
                        that.hash[maskIndex].btnCallBacks[btn_no] = function () {
                            callback();
                        };
                    },
                    setBtnEnableCondition: function (btn_no, callback) {
                        if (!that.hash[maskIndex].btnCallBacks)
                            that.hash[maskIndex].btnEnableConditions = [];
                        that.hash[maskIndex].btnEnableConditions[btn_no] = function () {
                            return callback();
                        };
                    },
                    show: function () {
                        that.hash[maskIndex].open();
                    },
                    hide: function () {
                        that.hash[maskIndex].close();
                    },
                    canShow: function () {

                    },
                    getObj: function () {
                        return that.hash[maskIndex];
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

