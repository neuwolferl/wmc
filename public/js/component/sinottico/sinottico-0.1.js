

var Sinottico = angular.module("SINOTTICO", ['SMARTBUTTON'], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);
Sinottico.directive('calendarioSinottico', ['Sin', function (Sin) {
        var version = "0.1";
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/sinottico/sinottico-" + version + ".html";
            },
            restrict: 'E',
            scope: {
                calId: '=calid',
                title: '@title'
            },
            controller: ['$scope', '$element', '$attrs', '$rootScope', '$timeout', 'SmartButton',
                function ($scope, $element, $attrs, $rootScope, $timeout, SmartButton) {
                    var pippo = this;
                    $scope.btn = SmartButton.newBtn();
                    SmartButton.getBtn($scope.btn[0]).setWhatToShow({
                        enabled: "",
                        disabled: "wait"
                    });
                    SmartButton.getBtn($scope.btn[0]).setClickFcn(function () {
                        $scope.btn[1].disable();
                        $scope.changeLabels();
                    });


                    var date = new Date();
                    $scope.dateObj = {
                        date: date,
                        dateEngString: date.getFullYear() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + (date.getDate() < 10 ? "0" : "") + date.getDate(),
                        dateItaString: (date.getDate() < 10 ? "0" : "") + date.getDate() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + date.getFullYear()
                    };
                    $scope.$watch("dateObj.dateEngString", function (newValue, oldValue) {
                        var oldDate = new Date(oldValue);
                        var date = new Date(newValue);

                        var diff = (date.getTime() - oldDate.getTime());
                        $scope.dateObj.date = date;
                        $scope.dateObj.dateItaString = (date.getDate() < 10 ? "0" : "") + date.getDate() + "-" + (date.getMonth() < 9 ? "0" : "") + (date.getMonth() + 1) + "-" + date.getFullYear();
                        $scope.calObj.renderPars.startTime.setTime($scope.calObj.renderPars.startTime.getTime() + diff);
                        $scope.calObj.renderPars.endTime.setTime($scope.calObj.renderPars.endTime.getTime() + diff);
                    });
                    $scope.calObj = Sin.getCal($scope.calId).getObj();
                    $element.find("canvas#calId").attr("id", $scope.calId);
                    $element.find("input#calDate").attr("id", $scope.calId + "calDate"); //.val($scope.dateObj.date);
                    $element.find("div#calInfo").attr("id", $scope.calId + "calInfo"); //.val($scope.dateObj.date);
                    $scope.info = "";
                    $scope.$watch("info", function (newvalue, oldvalue) {
                        $element.find("#" + $scope.calId + "calInfo").html(newvalue);
                    });
                    $scope.containerStyle = {
                        "text-align": "center",
                        "vertical-align": "middle",
                        position: "absolute",
                        border: "1px solid purple",
                        top: "10%",
                        left: "10%",
                        width: "80%",
                        heigth: "80%",
                        "background-color": "white",
                        "z-index": 2000
                    };
//                drawGantt();
                    $scope.labelTicket = {};
                    $scope.dataTicket = {};
                    $scope.labelSignal = "";
                    $scope.getLabels = function () {
                        $scope.labelSignal = Sin.getCal($scope.calId).getObj().labelGetter($scope.labelTicket);
                        $rootScope.$on($scope.labelSignal, function (event, mass) {
                            $scope.calObj.labels = Sin.getCal($scope.calId).getObj().labelRetriever(mass.ticket)
                            $scope.calObj.renderPars.ylabels = [];
                            for (var i in $scope.calObj.labels) {
                                $scope.calObj.labels[i] = {
                                    label: $scope.calObj.labels[i],
                                    active: true
                                }
                                $scope.calObj.renderPars.ylabels.push($scope.calObj.labels[i].label);
                            }
                            $scope.calObj.renderPars.ylabels.sort(function (a, b) {
                                return labelShow(a).localeCompare(labelShow(b));
                            });
                            $scope.calObj.loaded = true;
                        });
                    };
                    $scope.getData = function () {
                        var labels = $scope.calObj.renderPars.ylabels;
//                    for (var i in $scope.calObj.labels) {
//                        if ($scope.calObj.labels[i].active)
//                            labels.push($scope.calObj.labels[i].label);
//                    }

                        $scope.dataSignal = Sin.getCal($scope.calId).getObj().dataGetter({ticket: $scope.dataTicket, labels: labels, date: $scope.dateObj.dateEngString});
                        $rootScope.$on($scope.dataSignal, function (event, mass) {
                            $scope.calObj.data = Sin.getCal($scope.calId).getObj().dataRetriever(mass.ticket);
                            $scope.calObj.shapeIndexedData = [];
                            drawGantt();
                            $scope.btn[1].enable();
                        });
                    }
                    $scope.deactivateLabel = function (index) {
                        if ($scope.calObj.labels[index]) {
                            $scope.calObj.labels[index].active = false;
                        }
                    };
                    $scope.activateLabel = function (index) {
                        if ($scope.calObj.labels[index]) {
                            $scope.calObj.labels[index].active = true;
                        }
                    };
                    $scope.changeLabels = function () {
                        $scope.calObj.renderPars.ylabels = [];
                        for (var i in $scope.calObj.labels) {
                            if ($scope.calObj.labels[i].active) {
                                $scope.calObj.renderPars.ylabels.push($scope.calObj.labels[i].label);
                            }
                        }
                        $scope.calObj.renderPars.ylabels.sort(function (a, b) {
                            return labelShow(a).localeCompare(labelShow(b));
                        });
                        $scope.getData();
                    }
                    Sin.getCal($scope.calId).setOpener(function () {
                        if (!$scope.calObj.loaded) {
                            $scope.getLabels();
                        }
                        $element.show(500);
                    });
                    $scope.$watch("calObj.labels.length", function (newvalue, oldvalue) {
                        if (oldvalue === 0 && newvalue != 0) {
                            $scope.getData();
                        }
                    });
                    $scope.$watch("dateObj.date", function (newvalue, oldvalue) {
                        $scope.getData();
                    });
                    Sin.getCal($scope.calId).setCloser(function () {
                        $element.hide(500);
                    });
                    function labelShow(label) {
                        label = label.split(" ");
                        label = label[1].toUpperCase();
                        return label;
                    }


                    function drawGantt() {
                        var data = [];
                        $scope.calObj.shapeIndexedData = [];

                        $scope.calObj.renderPars.startTime.setMinutes(0);
                        $scope.calObj.renderPars.startTime.setSeconds(0);
                        $scope.calObj.renderPars.endTime.setMinutes(0);
                        $scope.calObj.renderPars.endTime.setSeconds(0);
                        var startCal = $scope.calObj.renderPars.startTime.getTime();
                        var endCal = $scope.calObj.renderPars.endTime.getTime();
                        var ratio = ($scope.calObj.renderPars.xmax / (endCal - startCal + 3600000));
                        for (var i in $scope.calObj.renderPars.ylabels) {
                            var lab = $scope.calObj.renderPars.ylabels[i];
                            var labShow = labelShow(lab);
                            var labdata = [];
                            for (var j in $scope.calObj.data) {
                                if ($scope.calObj.data[j].label === lab) {

                                    var start = new Date($scope.calObj.data[j].date_start + " " + $scope.calObj.data[j].time_start);
//                                console.log($scope.calObj.data[j]);
//                                console.log(startCal);
//                                console.log(start);
                                    start = start.getTime();
                                    var end = new Date($scope.calObj.data[j].due_date + " " + $scope.calObj.data[j].time_end);
//                                console.log(endCal);
//                                console.log(end);
                                    end = end.getTime();
                                    $scope.calObj.data[j].renderDuration = Math.floor((end - start) * ratio);
                                    $scope.calObj.data[j].renderStart = Math.floor((start - startCal) * ratio);
                                    var color;
                                    if ($scope.calObj.renderPars.colorer !== ""
                                            && typeof ($scope.calObj.data[j][$scope.calObj.renderPars.colorer]) !== "undefined"
                                            && typeof ($scope.calObj.renderPars.colors[$scope.calObj.data[j][$scope.calObj.renderPars.colorer]]) !== "undefined") {
                                        color = $scope.calObj.renderPars.colors[$scope.calObj.data[j][$scope.calObj.renderPars.colorer]];
                                    }
                                    else {
                                        color = "white";
                                    }
                                    $scope.calObj.shapeIndexedData.push($scope.calObj.data[j]);
                                    labdata.push([$scope.calObj.data[j].renderStart, $scope.calObj.data[j].renderDuration, null, labShow, color]);
                                }
                            }
                            if (!labdata.length) {
                                $scope.calObj.shapeIndexedData.push({});
                                labdata.push([, , null, labShow, 'red']);
                            }
                            data.push(labdata);
                        }
                        var numeroOre = $scope.calObj.renderPars.endTime.getTime() - $scope.calObj.renderPars.startTime.getTime();
                        numeroOre = 1 + Math.floor(numeroOre / (60 * 60 * 1000));
                        var lungOra = Math.floor($scope.calObj.renderPars.xmax / numeroOre);
                        var xlabels = [];
                        for (var i = 0; i < numeroOre; i++) {
                            var ora = $scope.calObj.renderPars.startTime.getHours() + i;
                            xlabels.push((ora < 10 ? "0" : "") + ora);
                        }
                        var vbars = [];
                        for (var i = 0; i < numeroOre; i++) {
                            vbars.push([i * lungOra, lungOra, 'rgba(192,255,192,0.5)']);
                            i++;
                        }
                        var title = "";
                        if (typeof ($scope.calObj.renderPars.title.title) === "function") {
                            title = $scope.calObj.renderPars.title.title($scope.calObj, $scope.dateObj);
                        }
                        else {
                            title = $scope.calObj.renderPars.title.title;
                        }
                        if ($scope.calObj.drawn) {
                            RGraph.Clear(document.getElementById($scope.calId));
                            $scope.gantt.data = [];
                            $scope.gantt.data = data;
                            $scope.gantt.Set('title', title);
                            $scope.gantt.Draw();
                        }
                        else {
                            $scope.gantt = new RGraph.Gantt($scope.calId, data)
                                    .Set('xmax', $scope.calObj.renderPars.xmax)
                                    .Set('gutter.right', $scope.calObj.renderPars.gutter.right)
                                    .Set('gutter.left', $scope.calObj.renderPars.gutter.left)
                                    .Set('labels', xlabels)
                                    .Set('title', title)
                                    .Set('defaultcolor', $scope.calObj.renderPars.defaultcolor)
                                    .Set('background.grid', $scope.calObj.renderPars.background.grid)
                                    .Set('text.size', $scope.calObj.renderPars.text.size)
                                    .Set('vbars', vbars)
                                    .Set('chart.borders', $scope.calObj.renderPars.chart.borders)
                                    .Set('borders', $scope.calObj.renderPars.borders)
                                    .Set('title.vpos', $scope.calObj.renderPars.title.vpos)
                                    .Set('labels.align', $scope.calObj.renderPars.labels.align)
                                    .Draw();
                            if ($scope.gantt.reDraw) {
                                $scope.gantt.reDraw();
                            }
                            $scope.calObj.drawn = true;
                            $scope.gantt.onmousemove = function (e, shape) {
                                var args = {e: e, shape: shape};
//                            $scope.calObj.onMouseMove(args);
                                $scope.info = $scope.calObj.shapeInfoDisplayer($scope.calObj.getShapeUnderlyingData(shape.index));
                                $scope.$digest();
                            };
                            $scope.gantt.onclick = function (e, shape) {
                                var args = {e: e, shape: shape};
                                $scope.calObj.onClick(args);
                            };
                        }
                        ;
                    }
                }]
        }
        ;
    }]);
Sinottico.provider("Sin", function () {
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
            var calIndex = this.randomString(10);
            if (typeof (this.hash[calIndex]) === "undefined") {
                flag = false;
            }
        }
        this.hash[calIndex] = {
            open: function () {
            },
            close: function () {
            },
            labels: [],
            data: {},
            shapeIndexedData: [],
            drawn: false,
            loaded: false,
            labelGetter: function () {
            },
            labelRetriever: function () {
            },
            dataGetter: function () {
            },
            dataRetriever: function () {
            },
            onMouseMove: function () {
            },
            onClick: function () {
            },
            shapeInfoDisplayer: function () {

            },
            getShapeUnderlyingData: function (shapeIndex) {
                return this.shapeIndexedData[shapeIndex];
            },
            renderPars: {
                xmax: 900,
                gutter: {right: 35, left: 120},
                defaultcolor: '#faa',
                background: {grid: false},
                text: {size: 12},
                vbars: [],
                chart: {borders: true},
                borders: false,
                title: {title: "", vpos: 0.6},
                labels: {labels: [], align: "bottom"},
                startTime: {},
                endTime: {},
                ylabels: [],
                colors: {},
                colorer: ""
            }
        };
        return [calIndex, this.hash[calIndex]];
    }
    this.lock = false;
    this.hash = [];
    this.$get = function () {
        var that = this;
        return {
            getCal: function (calIndex) {
                return {
                    setOpener: function (callback) {
                        that.hash[calIndex].open = function () {
                            callback();
                        };
                    },
                    setCloser: function (callback) {
                        that.hash[calIndex].close = function () {
                            callback();
                        };
                    },
                    show: function () {
                        that.hash[calIndex].open();
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
                        return that.hash[calIndex];
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
            isDefined: function (calIndex) {
                return (typeof (that.hash[calIndex]) !== "undefined");
            },
            newCal: function () {
                return that.setTicket();
            }
        };
    }
}
);



