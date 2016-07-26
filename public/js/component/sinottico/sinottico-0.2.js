

var Sinottico = angular.module("SINOTTICO", ['SMARTBUTTON', 'COMMON'], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);
Sinottico.directive('calendarioSinottico', ['Sin', 'Common', function (Sin, Common) {
        var templateVersion = "0.2";
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/sinottico/sinottico-" + templateVersion + ".html";
            },
            restrict: 'E',
            scope: {
                calId: '=calid',
                title: '@title',
                modal: '@'
            },
//            link: function (scope, element, attrs) {
//
//            },
            controller: ['$scope', '$element', '$attrs', '$rootScope', '$timeout', 'SmartButton',
                function ($scope, $element, $attrs, $rootScope, $timeout, SmartButton) {



                    function labelShow(label) {
                        label = label.split(" ");
                        label = label[1].toUpperCase();
                        return label;
                    }

                    $scope.getLabels = function () {
                        $scope.calObj.labelGetter($scope.calObj);
                    }

                    $scope.getData = function () {
                        $scope.calObj.data = [];
                        $scope.calObj.dataGetter($scope.calObj);
                    }


                    $scope.$watch("calObj.labels", function (newvalue, oldvalue) {
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
                        if (newvalue.length)
                            $scope.calObj.loaded = true;
                        $scope.getData();

                    });
                    $scope.$watch("calObj.data", function (newvalue, oldvalue) {
                        console.log("DATI CAMBIATI");
                        if (newvalue.length > 0) {
                            console.log($scope.calObj.data);
                            drawGantt();
                        }
                    });


                    Sin.getCal($scope.calId).setOpener(function () {
                        if (!$scope.calObj.loaded) {
                            console.log("get Labels");
                            $scope.getLabels();
                        }
                        $element.show(500);
                    });
                    Sin.getCal($scope.calId).setUpdater(function () {
                        $scope.getData();
                        $element.show(500);
                    });
                    console.log("Sin controller");
                    if ($scope.modal === true || $scope.modal === "true") {
                        $scope.modal = true;
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
                    }
                    else {
                        $scope.modal = false;
                        $scope.containerStyle = {
                            "text-align": "center",
                            "vertical-align": "middle",
                            border: "1px solid purple",
                            width: "100%",
                            heigth: "100%",
                            "background-color": "white",
                        };
                    }
                    console.log("Modal: ", $scope.modal);
                    $scope.dateObj = Common.dateMultiRep(new Date());
                    $scope.$watch("dateObj.date", function (newValue, oldValue) {
                        var oldDate = new Date(oldValue);
                        var date = new Date(newValue);
                        var diff = (date.getTime() - oldDate.getTime());
                        if (Math.abs(diff) < 1000 * 3600 * 12)
                            return;
                        console.log("data cambiata:", newValue, "aggiorno");
                        $scope.dateObj = Common.dateMultiRep(date);
                        console.log("nuova data: ", $scope.dateObj);
                        $scope.calObj.renderPars.startTime.setTime($scope.calObj.renderPars.startTime.getTime() + diff);
                        $scope.calObj.renderPars.endTime.setTime($scope.calObj.renderPars.endTime.getTime() + diff);
                    });
                    $scope.calObj = Sin.getCal($scope.calId).getObj();
                    var canvas = $element.find("canvas#calId");
                    canvas.attr("id", $scope.calId);
                    var h = canvas.parents(".canvas-container").parent().height();
                    console.log(h);
//                    canvas.height(h);//////////////////////////////////////////controllare altezza e larghezza
                    var w = canvas.parents(".canvas-container").parent().width();
                    console.log(w);
//                    canvas.width(w);
                    $element.find("input#calDate").attr("id", $scope.calId + "calDate"); //.val($scope.dateObj.date);
                    $element.find("div#calInfo").attr("id", $scope.calId + "calInfo"); //.val($scope.dateObj.date);
                    $scope.info = "";
                    $scope.$watch("info", function (newvalue, oldvalue) {
                        $element.find("#" + $scope.calId + "calInfo").html(newvalue);
                    });
//                    
                    $scope.$watch("dateObj.date", function (newvalue, oldvalue) {
                        $scope.getData();
                    });

                    function computeRenderPars() {
                        var canvas = $element.find("canvas#" + $scope.calId);
                        var labels = $scope.calObj.renderPars.ylabels;
                        var h = canvas.parents(".canvas-container").parent().height();
                        h = Math.max(h, 200 + labels.length * 20);
                        var w = canvas.parents(".canvas-container").parent().width();
                        var gutLeft = 0;
                        var gutRight = 0;
                        for (var i in labels) {
                            gutLeft = Math.max(gutLeft, labelShow(labels[i]).length);
                        }
                        gutLeft = parseInt(Math.min(gutLeft * 12, w * 0.25));
                        var showLegend = $scope.calObj.renderPars.showLegend;
                        if (showLegend && ((w - gutLeft) > 400)) {
                            gutRight = 200;
                        }
                        else {
                            showLegend = false;
                        }
                        return {
                            canvasH: h,
                            canvasW: w,
                            gutLeft: gutLeft,
                            gutRight: gutRight,
                            showLegend: showLegend
                        };
                    }

                    function drawLegend(colors) {
//                        return;
                        console.log("draw legend");
                        var canvas = document.getElementById($scope.calId);
                        var numberOfColors = 0;
                        for (var i in colors) {
                            if (colors.hasOwnProperty(i))
                                numberOfColors++;
                        }
                        var context = canvas.getContext('2d');
                        context.fillStyle = 'black';
                        context.font = 'Bold 18px Arial';
                        var titleWidth = context.measureText('Legenda').width;
                        var blankTitleWidth = parseInt((170 - titleWidth) / 2);
                        context.fillText('Legenda', canvas.width - titleWidth - blankTitleWidth - 10, 65);
                        var colorCounter = 1;
                        context.font = 'Bold 16px Arial';
                        for (var i in colors) {
                            if (colors.hasOwnProperty(i)) {
                                context.fillStyle = colors[i];
                                context.fillRect(canvas.width - 170, 65 + 22 + (colorCounter * 30), 20, 20);
                                context.fillText(i, canvas.width - 140, 65 + 22 + (colorCounter * 30) + 15);
                            }
                            colorCounter++;
                        }
                    }


                    function drawGantt() {
                        var data = [];
                        var canvas = $element.find("canvas#" + $scope.calId);
                        var computedRenderPars = computeRenderPars();
//                        console.log(computedRenderPars);
                        canvas.attr("height", computedRenderPars.canvasH);
                        canvas.attr("width", computedRenderPars.canvasW);
                        $scope.calObj.shapeIndexedData = [];
                        $scope.calObj.renderPars.startTime.setMinutes(0);
                        $scope.calObj.renderPars.startTime.setSeconds(0);
                        $scope.calObj.renderPars.endTime.setMinutes(0);
                        $scope.calObj.renderPars.endTime.setSeconds(0);
                        var startCal = $scope.calObj.renderPars.startTime.getTime();
                        var endCal = $scope.calObj.renderPars.endTime.getTime();
                        var ratio = ($scope.calObj.renderPars.xmax / (endCal - startCal + 3600000));
//                        console.log("$scope.calObj.renderPars.startTime", "$scope.calObj.renderPars.endTime", "startCal", "endCal");
                        console.log($scope.calObj.renderPars.startTime, $scope.calObj.renderPars.endTime, startCal, endCal);
                        for (var i in $scope.calObj.renderPars.ylabels) {
                            var lab = $scope.calObj.renderPars.ylabels[i];
                            var labShow = labelShow(lab);
                            var labdata = [];
                            for (var j in $scope.calObj.data) {
                                try {
                                    if ($scope.calObj.data[j].label === lab) {
//                                        console.log("---- ",$scope.calObj.data[j]);
                                        var start = $scope.calObj.data[j].start;
                                        if (!start.getTime) {
                                            console.log("!!!!!!!!!!!!!!!!!!!", start);
                                            start = new Date(start);
                                            console.log("!!!!!!!!!!!!!!!!!!!", start);
                                        }
                                        start = start.getTime();
//                                        console.log("start",start);
                                        var end = $scope.calObj.data[j].end;
                                        if (!end.getTime) {
                                            console.log("!!!!!!!!!!!!!!!!!!!", end);
                                            end = new Date(end);
                                            console.log("!!!!!!!!!!!!!!!!!!!", end);
                                        }
                                        end = end.getTime();
//                                        console.log("end",end);
                                        $scope.calObj.data[j].renderDuration = Math.floor((end - start) * ratio);
                                        $scope.calObj.data[j].renderStart = Math.floor((start - startCal) * ratio);
                                        var color = "grey";
                                        if (typeof $scope.calObj.renderPars.colorer === "function") {
                                            var colorer = $scope.calObj.renderPars.colorer($scope.calObj.data[j], $scope.calObj.data);
                                            if (typeof colorer !== "undefined" &&
                                                    typeof $scope.calObj.renderPars.colors[colorer] !== "undefined") {
                                                color = $scope.calObj.renderPars.colors[colorer];
                                            }
                                        }
                                        $scope.calObj.shapeIndexedData.push($scope.calObj.data[j]);
                                        labdata.push([$scope.calObj.data[j].renderStart, $scope.calObj.data[j].renderDuration, null, labShow, color]);
                                    }
                                }
                                catch (e) {
                                    console.log("error, ", e);
                                    console.log($scope.calObj.data[j]);
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
                            console.log("redraw");
                            var canvas = document.getElementById($scope.calId);
                            RGraph.Clear(canvas);
                            $scope.gantt.data = [];
                            $scope.gantt.data = data;
                            $scope.gantt.Set('title', title);
                            $scope.gantt.Draw();
                            if (computedRenderPars.showLegend) {

                                drawLegend($scope.calObj.renderPars.colors);
                            }
                        }
                        else {
                            console.log("draw");
                            $scope.gantt = new RGraph.Gantt($scope.calId, data)
                                    .Set('xmax', $scope.calObj.renderPars.xmax)
                                    .Set('gutter.right', computedRenderPars.gutRight)
                                    .Set('gutter.left', computedRenderPars.gutLeft)
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
//                            
                            if ($scope.gantt.reDraw) {
                                $scope.gantt.reDraw();
                                drawLegend($scope.calObj.renderPars.colors);
                            }
                            if (computedRenderPars.showLegend) {
                                drawLegend($scope.calObj.renderPars.colors);
                            }
                            $scope.calObj.drawn = true;
                            $scope.gantt.onmousemove = function (e, shape) {

                                var args = {e: e, shape: shape};
//                            $scope.calObj.onMouseMove(args);
                                var data = $scope.calObj.getShapeUnderlyingData(shape.index);
                                $scope.info = $scope.calObj.shapeInfoDisplayer(data);
                                $scope.calObj.shapeInfoPublisher({shapeIndex: shape.index, obj: data});
                                $scope.$digest();
                            };
                            $scope.gantt.onclick = function (e, shape) {
                                drawLegend($scope.calObj.renderPars.colors);
                                var data = $scope.calObj.getShapeUnderlyingData(shape.index);
                                var args = {e: e, shape: shape, data: data};
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
            openSet: false,
            close: function () {
            },
            closeSet: false,
            update: function () {

            },
            updateSet: false,
            labels: [],
            data: [],
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
            setShapeInfoPublisher: {}, //oggetto su cui viene scritto la shape info (per visualizzazioni esterne)
            getShapeUnderlyingData: function (shapeIndex) {
                return this.shapeIndexedData[shapeIndex];
            },
            renderPars: {
                xmax: 900,
                gutter: {right: 155, left: 120},
                defaultcolor: '#faa',
                background: {grid: false},
                text: {size: 10},
                vbars: [],
                chart: {borders: true},
                borders: false,
                title: {title: "", vpos: 0.6},
                labels: {labels: [], align: "bottom"},
                startTime: {},
                endTime: {},
                ylabels: [],
                colors: {},
                colorer: {},
                showLegend: false
            }
        };
        return [calIndex, this.hash[calIndex]];
    }
    this.lock = false;
    this.hash = [];
    this.$get = [function giovanni() {
            var that = this;
            return {
                getCal: function (calIndex) {
                    return {
                        setOpener: function (callback) {
                            that.hash[calIndex].open = function () {
                                return callback();
                            };
                            that.hash[calIndex].openSet = true;
                            return giovanni().getCal(calIndex); // for chaining
                        },
                        setCloser: function (callback) {
                            that.hash[calIndex].close = function () {
                                callback();
                            };
                            that.hash[calIndex].closeSet = true;
                            return giovanni().getCal(calIndex);
                        },
                        setUpdater: function (callback) {
                            that.hash[calIndex].update = function () {
                                callback();
                            };
                            that.hash[calIndex].updateSet = true;
                            return giovanni().getCal(calIndex);
                        },
                        show: function () {
                            that.hash[calIndex].open();
                            return giovanni().getCal(calIndex);
                        },
                        hide: function () {
                            that.hash[calIndex].close();
                            return giovanni().getCal(calIndex);
                        },
                        canShow: function () {

                        },
                        setLabelGetter: function (callback) {
                            that.hash[calIndex].labelGetter = function (args) {
                                return callback(args);
                            }
                            return giovanni().getCal(calIndex);
                        },
                        setLabelRetriever: function (callback) {
                            that.hash[calIndex].labelRetriever = function (args) {
                                return callback(args);
                            }
                            return giovanni().getCal(calIndex);
                        },
                        setDataGetter: function (callback) {
                            that.hash[calIndex].dataGetter = function (arg) {
                                return callback(arg);
                            }
                            return giovanni().getCal(calIndex);
                        },
                        setDataRetriever: function (callback) {
                            that.hash[calIndex].dataRetriever = function (args) {
                                return callback(args);
                            }
                            return giovanni().getCal(calIndex);
                        },
                        setOnClick: function (callback) {
                            that.hash[calIndex].onClick = function (args) {
                                return callback(args);
                            }
                            return giovanni().getCal(calIndex);
                        },
                        setOnMouseMove: function (callback) {
                            that.hash[calIndex].onMouseMove = function (args) {
                                return callback(args);
                            }
                            return giovanni().getCal(calIndex);
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
                            return giovanni().getCal(calIndex);
                        },
                        setShapeInfoPublisher: function (callback) {
                            that.hash[calIndex].shapeInfoPublisher = function (args) {
                                return callback(args);
                            }
                            return giovanni().getCal(calIndex);
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
        }]
}
);



