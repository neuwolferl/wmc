(function () {
    var app = angular.module("SPLITSO", [], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.directive('barrafatture', [function () {
            function link(scope, element, attrs) {

            }
            return {
                templateUrl: function () {
                    var baseurl = document.URL;
                    baseurl = baseurl.split("public");
                    return baseurl[0] + "public/js/core/splitSo.html";
                },
                restrict: 'E',
                scope: {
                    data: '=data'
                },
                link: link,
                controller: function ($scope, $element, $attrs, $rootScope, $timeout) {
                    $scope.colors = {
                        "ALICEBLUE": "#A0CE00", "ANTIQUEWHITE": "#FAEBD7", "AQUA": "#00FFFF", "AQUAMARINE": "#7FFFD4",
                        "AZURE": "#F0FFFF", "BISQUE": "#FFE4C4",
                        "BLANCHEDALMOND": "#FFEBCD", "BLUE": "#0000FF", "BLUEVIOLET": "#8A2BE2", "BROWN": "#A52A2A",
                        "BURLYWOOD": "#DEB887", "CADETBLUE": "#5F9EA0", "CHARTREUSE": "#7FFF00", "CHOCOLATE": "#D2691E",
                        "CORAL": "#FF7F50", "CORNFLOWERBLUE": "#6495ED", "CORNSILK": "#FFF8DC", "CRIMSON": "#DC143C",
                        "CYAN": "#00FFFF", "DARKBLUE": "#00008B", "DARKCYAN": "##008B8B",
                        "DARKGOLDENROD": "#B8860B", "DARKGRAY": "#A9A9A9", "DEEPPINK": "#FF1493", "DEEPSKYBLUE": "#00BFFF",
                        "DIMGRAY": "##696969", "DODGERBLUE": "#1E90FF", "FIREBRICK": "#822222",
                        "FORESTGREEN": "#228B22", "FUCHSIA": "#FF00FF", "GAINSBORO": "#DCDCDC", "GHOSTWHITE": "#F8F8FF",
                        "GOLD": "#FFD700", "GOLDENROD": "#DAA520", "GRAY": "#808080", "GREEN": "#008800",
                        "GREENYELLOW": "#ADFF2F", "HONEYDEW": "#F0FFF0", "HOTPINK": "#FF69B4", "INDIANRED": "#CD5C5C",
                        "IVORY": "#FFFFF0", "LAVENDER": "#E6E6FA",
                        "LAVENDERBLUSH": "#FFF0F5", "LEMONCHIFFON": "#FFFACD", "LIGHTBLUE": "#ADD8E6", "LIGHTCORAL": "#F08080",
                        "LIGHTCYAN": "#E0FFFF", "LIGHTGOLDENRODYELLOW": "#FAFAD2", "LIGHTGREEN": "#90EE90", "LIGHTGRAY": "#D3D3D3",
                        "LIGHTPINK": "#FFB6C1", "LIGHTSALMON": "#FFA07A", "LIGHTSEAGREEN": "#20B2AA", "LIGHTSKYBLUE": "#87CEFA",
                        "LIGHTSLATEGRAY": "#778899", "LIGHTSTEELBLUE": "#B0C4DE", "LIGHTYELLOW": "#FFFFE0", "LIME": "#00FF00",
                        "LIMEGREEN": "#32CD32", "LINEN": "#FAF0E6", "MAGENTA": "#FF00FF",
                        "MEDIUMAQUAMARINE": "#66CDAA", "MEDIUMPURPLE": "#9370DB",
                        "MEDIUMSEAGREEN": "#3CB371", "MEDIUMSLATEBLUE": "#7B68EE", "MEDIUMSPRINGGREEN": "#00FA9A", "MEDIUMTORQUOISE": "#48D1CC",
                        "MEDIUMVIOLETRED": "#C71585", "MINTCREAM": "#F5FFFA", "MISTYROSE": "#FFE4E1",
                        "NAVAJOWHITE": "#FFDEAD", "NAVY": "#000080", "OLDLACE": "#FDF5E6", "OLIVE": "#808000",
                        "OLIVEDRAB": "#6B8E23", "ORANGE": "#FFA500", "ORANGERED": "#FF4500", "ORCHID": "#DA70D6",
                        "PALEGOLDENROD": "#EEE8AA", "PALEGREEN": "#98FB98", "PALETURQUOISE": "#AFEEEE", "PALEVIOLETRED": "#DB7093",
                        "PAPAYAWHIP": "#FFEFD5", "PEACHPUFF": "#FFDAB9", "PERU": "#CD853F", "PINK": "#FFC0CB",
                        "PLUM": "#DDA0DD", "POWDERBLUE": "#B0E0E6", "PURPLE": "#800080", "RED": "#FF0000",
                        "ROSYBROWN": "#BC8F8F", "ROYALBLUE": "#4169E1", "SADDLEBROWN": "#8B4513", "SALMON": "#FA8072",
                        "SANDYBROWN": "#F4A460", "SEAGREEN": "#2E8B57", "SIENNA": "#A0522D",
                        "SILVER": "#C0C0C0", "SKYBLUE": "#87CEEB", "SLATEBLUE": "#6A5ACD", "SLATEGRAY": "#708090",
                        "SNOW": "#FFFAFA", "SPRINGGREEN": "#00FF7F", "STEELBLUE": "#468284", "TAN": "#D2B48C",
                        "TEAL": "#008080", "THISTLE": "#D8BFD8", "TOMATO": "#FF6347", "TURQUOISE": "#40E0D0",
                        "VIOLET": "#EE82EE", "WHEAT": "#F5DEB3", "WHITESMOKE": "#F5F5F5",
                        "YELLOW": "#FFFF00", "YELLOWGREEN": "#9ACD32"

                    };
                    $scope.colorList = [];
                    for (var i in $scope.colors) {
                        $scope.colorList.push({name: i, code: $scope.colors[i]})
                    }
                    $scope.colorHash = [];
                    $scope.colorAssignedHash = {};
                    $scope.newColor = function () {
                        var cnt = 0;
                        while (true) {
                            var prova = Math.floor(Math.random() * $scope.colorList.length);
                            if (typeof ($scope.colorHash[prova]) === "undefined") {
                                $scope.colorHash[prova] = $scope.colorList[prova].name;
                                return $scope.colorHash[prova];
                                break;
                            }
                        }
                    }
                    $scope.assignColor = function (type, id) {
                        if (typeof ($scope.colorAssignedHash[type]) === "undefined") {
                            $scope.colorAssignedHash[type] = {};
                        }
                        if (typeof ($scope.colorAssignedHash[type][id]) === "undefined") {
                            $scope.colorAssignedHash[type][id] = $scope.newColor();

                        }
                        return $scope.colorAssignedHash[type][id];
                    }
                    $scope.confirmCollOnSo = function ($event, $data, soId) {
                        var collAmount = window.prompt("Stai attribuendo il pagamento " + $data.idCol + " alla SO " + soId + ". Specificare l'importo: ", $data.amountCol);
                        if (collAmount && !isNaN(collAmount)) {
                            $rootScope.$emit("PerformCol2So", {idCol: $data.idCol, idSo: soId, amount: collAmount});
                        }
                    };
                    $scope.splitDetails = {};
                    $scope.setDet = function (obj) {
                        $scope.splitDetails = obj;
                    };
                    $scope.unsetDet = function () {
                        $scope.splitDetails = {};
                    };
                    $rootScope.$on("col2SoPerformed", function (event, mass) {
                        console.log(mass);
                        var idSo = mass[0]["idSo"];
                        var idCol = mass[0]["idCol"];
                        var amount = mass[0]["amount"];
                        console.log(idSo);
                        console.log(idCol);
                        console.log(amount);
                        console.log($scope.data.draw.collFree);
                        var indexSo = -1;
                        for (var i in $scope.data.draw.so) {
                            if ($scope.data.draw.so[i].idSo === idSo) {
                                indexSo = i;
                                break;
                            }
                        }
                        var indexCollFree = -1;
                        for (var i in $scope.data.draw.collFree) {
                            if ($scope.data.draw.collFree[i].idCol === idCol) {
                                indexCollFree = i;
                                break;
                            }
                        }
                        console.log("indexCollFree " + indexCollFree);
                        if (Number(amount) === 0) {
                            return;
                        }
                        if (Number(amount) >= Number($scope.data.draw.collFree[indexCollFree].amountCol)) {
                            console.log("assegnazione totale");
                            if (typeof ($scope.data.draw.collSo) === "undefined") {
                                $scope.data.draw.collSo = [];
                            }
                            if (typeof ($scope.data.draw.collSo[indexSo]) === "undefined") {
                                $scope.data.draw.collSo[indexSo] = [];
                            }
                            $scope.data.draw.collSo[indexSo].push({
                                amountCol: $scope.data.draw.collFree[indexCollFree].amountCol,
                                col2So: idSo,
                                dateCol: $scope.data.draw.collFree[indexCollFree].dateCol,
                                idCol: $scope.data.draw.collFree[indexCollFree].idCol,
                                refCol: $scope.data.draw.collFree[indexCollFree].refCol,
                                statusCol: $scope.data.draw.collFree[indexCollFree].statusCol,
                                typeCol: $scope.data.draw.collFree[indexCollFree].typeCol,
                                ll: Number(100 * $scope.data.draw.collFree[indexCollFree].amountCol / $scope.data.draw.so[indexSo].amountSo)
                            });
                            $scope.data.draw.collFree.splice(indexCollFree, 1);
                        }
                        else {
                            console.log("assegnazione parziale");
                            if (typeof ($scope.data.draw.collSo) === "undefined") {
                                $scope.data.draw.collSo = [];
                            }
                            if (typeof ($scope.data.draw.collSo[indexSo]) === "undefined") {
                                $scope.data.draw.collSo[indexSo] = [];
                            }
                            $scope.data.draw.collSo[indexSo].push({
                                amountCol: amount,
                                col2So: idSo,
                                dateCol: $scope.data.draw.collFree[indexCollFree].dateCol,
                                idCol: $scope.data.draw.collFree[indexCollFree].idCol,
                                refCol: $scope.data.draw.collFree[indexCollFree].refCol,
                                statusCol: $scope.data.draw.collFree[indexCollFree].statusCol,
                                typeCol: $scope.data.draw.collFree[indexCollFree].typeCol,
                                ll: Number(100 * $scope.data.draw.collFree[indexCollFree].amountCol / $scope.data.draw.so[indexSo].amountSo)
                            });
                            $scope.data.draw.collFree[indexCollFree].amountCol -= amount;
                        }
                    });

                }
            };
        }]);
    app.provider("SplitSo", function () {

        this.$get = function ($filter, $rootScope) {
            var that = this;
            return {
            };
        }
    });
})();

