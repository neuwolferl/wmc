

var smartbutton = angular.module("SMARTBUTTON", [], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);
smartbutton.directive('smartButton', ['SmartButton', function (SmartButton) {
        var version = "0.1";
        function link(scope, element, attrs) {
        }
        ;
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/core/smartButton/smartButton-" + version + ".html";
            },
            restrict: 'E',
            scope: {
                btnId: '=btnid',
                label: '@label'
            },
            controller: ['$scope', '$element', '$attrs', '$rootScope', '$timeout', '$interval',
                function ($scope, $element, $attrs, $rootScope, $timeout, $interval) {
                    var pippo = this;
                    $timeout(function () {
                        if ($scope.btnObj.externalLabel != "") {
                            $scope.label = $scope.btnObj.externalLabel;
                            $scope.show.label = $scope.btnObj.externalLabel;
                            $scope.btnObj.externalLabel = "";
                        }
                    }, 2000);
                    $element.find("button").attr("id", $scope.btnId + "btn");
                    $element.find("button").attr("class", $element.attr("class"));
                    $element.attr("class", "");
                    $scope.show = {
                        label: $scope.label,
                        disabled: false,
                        labelRefresher: {}
                    };
                    $scope.btnObj = SmartButton.getBtn($scope.btnId).getObj();
                    this.elapsedFromLastClick = function () {
                        var lastClickTS = $scope.btnObj.clickTs[$scope.btnObj.clickTs.length - 1];
                        if (typeof (lastClickTS) === "undefined")
                            return "noclick";
                        var now = new Date();
                        var nowTS = now.getTime();
                        var elapsed = nowTS - Number(lastClickTS);
                        var hours = Math.floor(elapsed / (1000 * 60 * 60));
                        elapsed = elapsed - (hours * (1000 * 60 * 60));
                        var minutes = Math.floor(elapsed / (1000 * 60));
                        elapsed = elapsed - (minutes * (1000 * 60));
                        var seconds = Math.floor(elapsed / 1000);
                        return hours + ":" + minutes + ":" + seconds;
                    }


                    $scope.click = function () {
                        return ($scope.btnObj.click)($scope.btnId);
                    };

                    this.disable = function () {
                        $scope.show.disabled = true;
                        var whatToShow = $scope.btnObj.whatToShow.disabled;
                        if ($scope.show.labelRefresher.$$intervalId)
                            $interval.cancel($scope.show.labelRefresher);
                        $scope.show.labelRefresher = {};
                        if (!whatToShow || whatToShow === "") {
                            $scope.show.label = $scope.label;
                        }
                        else {
                            switch (whatToShow) {
                                case 'elapsedFromLastClick':
                                    pippo.setLabelRefresher(pippo.elapsedFromLastClick);
                                    break;
                                case 'wait':
                                    $scope.show.label = "Attendere..."
                                    break;
                                default:
                                    $scope.label;
                            }
                        }
                    }
                    this.enable = function () {

                        $scope.show.disabled = false;
                        var whatToShow = $scope.btnObj.whatToShow.enabled;
                        if ($scope.show.labelRefresher.$$intervalId)
                            $interval.cancel($scope.show.labelRefresher);
                        $scope.show.labelRefresher = {};
                        if (!whatToShow || whatToShow === "") {
                            $scope.show.label = $scope.label;
                        }
                        else {
                            switch (whatToShow) {
                                case 'elapsedFromLastClick':
                                    pippo.setLabelRefresher(pippo.elapsedFromLastClick());
                                    break;
                                case 'wait':
                                    $scope.show.label = "Attendere..."
                                    break;
                                default:
                                    $scope.label;
                            }
                        }
                    }

                    this.changeShowingLabel = function (fn) {
                        $scope.show.label = fn();
                    };
                    this.setLabelRefresher = function (fn) {
                        $scope.show.labelRefresher = $interval(function () {
                            pippo.changeShowingLabel(fn);
                        }, 500);
                    };
                    this.unsetLabelRefresher = function (fn) {
                        $interval.cancel($scope.show.labelRefresher);
                        $scope.show.labelRefresher = {};
                    };

                    this.getClass = function () {
                        console.log("ciao");
                        console.log($element.find("button"));
                        console.log($element.find("button").attr("class"));
                        return $element.find("button").attr("class");
                    };

                    this.remClass = function (cl) {
                        return $element.find("button").removeClass(cl);
                    };
                    this.setClass = function (cl) {
                        return $element.find("button").addClass(cl);
                    };


                    SmartButton.getBtn($scope.btnId).setDisableFcn(this.disable);
                    SmartButton.getBtn($scope.btnId).setEnableFcn(this.enable);
                    SmartButton.getBtn($scope.btnId).setClassGetter(this.getClass);
                    SmartButton.getBtn($scope.btnId).setClassRemover(this.remClass);
                    SmartButton.getBtn($scope.btnId).setClassSetter(this.setClass);
                }]
        }
        ;
    }]);
smartbutton.provider("SmartButton", function () {
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
            var btnIndex = this.randomString(10);
            if (typeof (this.hash[btnIndex]) === "undefined") {
                flag = false;
            }
        }
        this.hash[btnIndex] = {
            disable: function () {
            },
            enable: function () {
            },
            click: function () {

            },
            getClass: function () {

            },
            setClass: function () {

            },
            remClass: function () {

            },
            enabled: true,
            clickNo: 0,
            clickTs: [],
            whatToShow: {
                disabled: "",
                enabled: ""
            },
            externalLabel: ""

        };
        return [btnIndex, this.hash[btnIndex]];
    }
    this.lock = false;
    this.hash = [];
    this.$get = function () {
        var that = this;
        return {
            getBtn: function (btnIndex) {
                return {
                    setClickFcn: function (callback) {
                        that.hash[btnIndex].click = function (arg) {
                            that.hash[btnIndex].clickNo++;
                            that.hash[btnIndex].clickTs.push((new Date()).getTime());
                            return callback(arg);
                        };

                    },
                    setDisableFcn: function (callback) { //<-- questa viene chiamata dal controller della direttiva
                        that.hash[btnIndex].disable = function () { //<-- questa viene chiamata dall'esterno
                            that.hash[btnIndex].enabled = false;
                            return callback();
                        };
                    },
                    setEnableFcn: function (callback) { //<-- questa viene chiamata dal controller della direttiva
                        that.hash[btnIndex].enable = function () { //<-- questa viene chiamata dall'esterno
                            that.hash[btnIndex].enabled = true;
                            return callback();
                        };
                    },
                    setClassGetter: function (callback) { //<-- questa viene chiamata dal controller della direttiva
                        that.hash[btnIndex].getClass = function () { //<-- questa viene chiamata dall'esterno
                            return callback();
                        };
                    },
                    setClassSetter: function (callback) { //<-- questa viene chiamata dal controller della direttiva
                        that.hash[btnIndex].setClass = function (arg) { //<-- questa viene chiamata dall'esterno
                            return callback(arg);
                        };
                    },
                    setClassRemover: function (callback) { //<-- questa viene chiamata dal controller della direttiva
                        that.hash[btnIndex].remClass = function (arg) { //<-- questa viene chiamata dall'esterno
                            return callback(arg);
                        };
                    },
                    setWhatToShow: function (arg) {
                        that.hash[btnIndex].whatToShow = {
                            disabled: arg.disabled,
                            enabled: arg.enabled
                        };
                    },
                    hide: function () {
                        that.hash[calIndex].close();
                    },
                    canShow: function () {

                    },
                    setLabelGetter: function (callback) {
                        that.hash[calIndex].labelGetter = function (args) {
                            return callback(args);
                        }
                    },
                    setLabelRetriever: function (callback) {
                        that.hash[calIndex].labelRetriever = function (args) {
                            return callback(args);
                        }
                    },
                    setDataGetter: function (callback) {
                        that.hash[calIndex].dataGetter = function (arg) {
                            return callback(arg);
                        }
                    },
                    setDataRetriever: function (callback) {
                        that.hash[calIndex].dataRetriever = function (args) {
                            return callback(args);
                        }
                    },
                    setOnClick: function (callback) {
                        that.hash[calIndex].onClick = function (args) {
                            return callback(args);
                        }
                    },
                    setOnMouseMove: function (callback) {
                        that.hash[calIndex].onMouseMove = function (args) {
                            return callback(args);
                        }
                    },
                    setData: function (data) {

                    },
                    getObj: function () {
                        return that.hash[btnIndex];
                    },
                    setShapeInfoDisplayer: function (callback) {
                        that.hash[calIndex].shapeInfoDisplayer = function (args) {
                            return callback(args);
                        }
                    }
                }
            },
            getAll: function () {
                return that.hash;
            },
            getGroupEnableCallback: function (btnList) {
                if (typeof (btnList) === "object") {
                    return function (tf) {
                        for (var i in btnList) {
                            if (btnList.hasOwnProperty(i)) {
                                var btnIndex = btnList[i][0];
                                if (tf) {
                                    that.hash[btnIndex].enable();
                                }
                                else {
                                    that.hash[btnIndex].disable();
                                }
                            }
                        }
                    };

                }
                else {
                    return function (tf) {
                        for (var i in btnList) {
                            var btnIndex = btnList[i];
                            if (tf) {
                                that.hash[btnIndex].enable();
                            }
                            else {
                                that.hash[btnIndex].disable();
                            }
                        }
                    };
                }
            },
            isDefined: function (calIndex) {
                return (typeof (that.hash[calIndex]) !== "undefined");
            },
            newBtn: function () {
                return that.setTicket();
            }
        };
    }
}
);



