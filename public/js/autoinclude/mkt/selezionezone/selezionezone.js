(function () {
    var app = angular.module("SelezioneZone", ['uiGmapgoogle-maps', 'ngDragDrop', 'DATAMODULE', 'COMMON', 'THEMASK'], ['$interpolateProvider', function ($interpolateProvider) {
            $interpolateProvider.startSymbol("--__");
            $interpolateProvider.endSymbol("__--");
        }])
            .config(
                    ['uiGmapGoogleMapApiProvider', function (GoogleMapApiProvider) {
                            GoogleMapApiProvider.configure({
                                key: 'AIzaSyAN6gZPMrAStrYRHahyoRFacAOF6uRo_SI',
                                v: '3.17',
                                libraries: 'weather,geometry,visualization'
                            });
                        }]
                    );

    app.controller("ZoneController", ['DataService', '$scope', '$rootScope', '$timeout', 'Common', function (DataService, $scope, $rootScope, $timeout, Common) {
            $rootScope.showNotShow = {geoDetails: false};
            var that = this;
            this.destroyPage = function () {
                this.errorMask = true;
                $("error-mask").siblings().each(function () {
                    $(this).hide();
                });
            };

            $scope.st = Common.whereAmI().register();
            $scope.st = Common.whereAmI($scope.st);
            $scope.st.put("mappa");

            this.goToMap = function () {
                $scope.st.put("mappa");
            };
            this.goToConsulenti = function () {
                $scope.st.put("consulenti");
            };
            this.calculateDistance = function (loc1, loc2) {
                var R = 6372.795477598;
                if (!loc1 || !loc2 || !loc1.LAT || !loc1.LNG || !loc2.LAT || !loc2.LNG)
                    return "";
                var rat = Math.PI / 180;
                var d = R * Math.acos(Math.sin(rat * loc1.LAT) * Math.sin(rat * loc2.LAT) + Math.cos(rat * loc1.LAT) * Math.cos(rat * loc2.LAT) * Math.cos(rat * loc1.LNG - rat * loc2.LNG))

                return parseInt(d.toFixed(0));
            };
            this.sortNearestLoc = function () {
                if (!that.hoverLoc)
                    return;
                for (var i in $scope.locList) {
                    $scope.locList[i].distance = that.calculateDistance(that.hoverLoc, $scope.locList[i]);
                }
                $scope.locList = _.sortBy($scope.locList, 'distance');
                $timeout(function () {
                    console.log($("#locList"), $("#locList").find("tr").find("td:contains(" + that.hoverLoc.loc + ")"));
                    var td = $("#locList").find("tr").find("td:contains(" + that.hoverLoc.loc + ")");
                    if (td && td.length)
                        td[0].scrollIntoView();
                }, 1000);
                return true;
            };
            this.sortLoc = function (by) {
                that.hoverLoc = null
                switch (by) {
                    case 0:
                        $scope.locList = _.sortBy($scope.locList, 'loc');
                        break;
                    case 1:
                        $scope.locList = _.sortBy($scope.locList, 'count');
                        break;
                    case 2:
                        $scope.locList = _.sortBy($scope.locList, 'callable');
                        break;
                }

                return true;
            };

            this.errorOnCampaign = function (sel) {
                if (sel === 1) {
                    alert("Attenzione: non è stata specificata una campagna, l'applicazione potrebbe presentare comportamenti non previsti e\n\
produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
                    return;
                }
                else {
                    alert("Attenzione: si é verificato un errore nel recupero della campagna, l'applicazione potrebbe presentare comportamenti non previsti e\n\
produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
                    return;
                }
            }
            DataService.initializeOnCurrentPage(function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("selezionezone/");
                if (baseurl.length < 2) {
                    that.errorOnCampaign(1);
                    return;
                }
                var campaign = parseInt(baseurl[1]);
                DataService.getData("getCampaign",
                        {camp: campaign},
                "getCampaignPerformed",
                        {camp: campaign},
                function (t) {
                    var data = t[1].response.data;
                    if (data.length !== 1) {
                        that.errorOnCampaign(1);
                        return;
                    }
                    var lots = data[0].lots;
                    if (!lots) {
                        that.errorOnCampaign(2);
                        return;
                    }
                    lots = lots.replace("[", "").replace("]", "");
                    $scope.camp = campaign;
                    $scope.lots = lots.split(",");
                    if (!$scope.currentLot || $scope.currentLot < 0)
                        $scope.currentLot = ($scope.lots.length ? $scope.lots[$scope.lots.length - 1] : -1);
                    that.reloadCommerciali();
                },
                        function (t) {
                            that.errorOnCampaign(2);
                            return;
                        });
            },
                    function () {
                        that.errorOnCampaign(1);

                        that.destroyPage();
                    }
            );
            this.reloadCommerciali = function () {
                DataService.getData("getCommerciali",
                        {},
                        "getCommercialiPerformed",
                        {},
                        function (t) {
                            that.loadCommerciali(t[1].response.data);
                            that.reloadPipe();
                        },
                        function (t) {
                            alert("Attenzione: si é verificato un errore nel recupero dei dati dei commerciali, l'applicazione potrebbe presentare comportamenti non previsti e\n\
produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
                            return;
                        });
            };
            this.reloadPipe = function () {
                DataService.getData("getPipeStatus",
                        {pipe: 1, lot: $scope.currentLot},
                "getPipeStatusPerformed",
                        {},
                        function (t) {
                            var data = t[1].response.data;
                            console.log(data);
                            for (var i in data) {
                                for (var j in $scope.consulenti) {
                                    if (data[i].vend == $scope.consulenti[j].userid) {
                                        $scope.consulenti[j].locs.push({
                                            name: data[i].Localita,
                                            prov: data[i].Provincia,
                                            count: data[i].count,
                                            loading: false
                                        });
                                        $scope.consulenti[j].totAssigned += Number(data[i].count);
                                        break;
                                    }
                                }
                            }
                        },
                        function (t) {
                            alert("Attenzione: si é verificato un errore nel recupero dei dati dello stato attuale marketing, l'applicazione potrebbe presentare comportamenti non previsti e\n\
produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
                            return;
                        });
            }
            $scope.Common = Common;
            $scope.selectZones = function (toggle) {
                if (toggle === 0) {
                    if (!$scope.zone) {
                        $scope.zone = {regione: null, provincia: null, locs: [], loc: ""};
                    }
                    $("#zoneChooser").show(500);
                }
                else if (toggle === 1) {
                    $("#zoneChooser").hide(500);
                    that.showRegionOnMap();
                }
                else if (toggle === 2) {
                    $("#zoneChooser").hide(500);
                }

            };

            $scope.consulenti = [];
            $scope.consDetails = false;
            $scope.lockChoosenCons = false;

            this.coloraVenditore = function (amount) {
                amount = Number(amount);
                if (amount >= 0 && amount <= 100) {
                    var r = 255;
                    var g = parseInt(Math.floor(255 / 100)) * amount;
                }
                else if (amount > 100 && amount <= 200) {
                    var r = parseInt((-1) * Math.floor(255 / 100) * amount + 510);
                    var g = 255;
                }
                else {
                    var r = 0;
                    var g = 255;
                }
                return {"background-color": "rgb(" + r + "," + g + ",0)"};
            };
            $scope.chosenCons = {name: "", totAssigned: "", locs: [], lastUpdate: 0};
            this.chooseCons = function (userid) {
                $scope.chosenCons = {name: "", totAssigned: "", locs: [], lastUpdate: 0};
                var index = -1;
                for (var i in $scope.consulenti) {
                    if ($scope.consulenti[i].userid === userid) {
                        index = i;
                        break;
                    }
                }
                if (index === -1) {
                    return;
                }
                $scope.chosenCons = {
                    userid: userid,
                    name: $scope.consulenti[i].name,
                    totAssigned: $scope.consulenti[i].totAssigned,
                    locs: $scope.consulenti[i].locs,
                    index: index,
                    lastUpdate: $scope.consulenti[i].lastUpdate,
                    loading: false
                }
                $scope.consDetails = true;
            };
            this.unchooseCons = function () {
                if ($scope.lockChoosenCons)
                    return;
                if (!$scope.chosenCons || !$scope.chosenCons.index || $scope.chosenCons.index < 0)
                    return;
                var sum = 0;
                for (var i in $scope.chosenCons.locs) {
                    if ($scope.chosenCons.locs[i].loading)
                        return;
                    sum += Number($scope.chosenCons.locs[i].count);
                }
                $scope.consulenti[$scope.chosenCons.index].locs = $scope.chosenCons.locs;
                $scope.consulenti[$scope.chosenCons.index].totAssigned = sum;
                $scope.chosenCons = {name: "", totAssigned: "", locs: [], lastUpdate: 0};
                $scope.consDetails = false;
            };

//            $rootScope.$on("changeLot", function (event, mass) { ////// aggiustare!!!!
//                that.lockChoosenCons = false;
//                that.unchooseCons();
//                that.consulenti = [];
//                that.chosenCons = {};
//                that.consDetails = false;
//                that.reloadPipe();
//            });

            this.loadCommerciali = function (data) {
                $scope.consulenti = [];
                for (var i in data) {
                    $scope.consulenti.push({
                        userid: data[i].USERID,
                        name: data[i].NOME + " " + data[i].COGNOME,
                        totAssigned: 0,
                        locs: []
                    });
                }
            };

            this.pipeOp = function (loc, callable, prov, reg, plusminus) {
                if ($scope.consDetails === false)
                    return;
                if ($scope.chosenCons.loading)
                    return;
                $scope.lockChosenCons = true;
                $scope.chosenCons.loading = true;
                if (plusminus === "plus") {
                    var amount = prompt("In questa località sono disponibili " + callable + " aziende. Quante ne vuoi assegnare?", callable);
                }
                else {
                    var amount = prompt("Questo venditore ha " + callable + " aziende nella località " + loc + ". Quante ne vuoi rimuovere?", callable);
                }
                if (isNaN(amount))
                    return;
                if (plusminus === "minus") { //aggiungo lead alla coda del venditore
//                    if (!all) {
                    var index = -1;
                    for (var i in $scope.chosenCons.locs) {
                        if ($scope.chosenCons.locs[i].name === loc && $scope.chosenCons.locs[i].prov === prov) {
                            index = i;
                            break;
                        }
                    }
                    if (index === -1) {
                        return;
                    }
                    else {
                        $scope.chosenCons.locs[index].count = Math.max(0, Number($scope.chosenCons.locs[index].count) - Number(amount));
                    }

                    DataService.postData("pipeCommand",
                            {loc: loc, lot: $scope.currentLot, amount: (-1) * Number(amount), vend: $scope.chosenCons.userid},
                    "remLocPerformed",
                            {},
                            function (t) {
                                var update = t;
                                $scope.chosenCons.lastUpdate = update[1].lastResTimestamp;
                                $scope.consulenti[$scope.chosenCons.index].lastUpdate = update[1].lastResTimestamp
                                $scope.chosenCons.locs = [];
                                var sum = 0;
                                for (var i in update[1].response.data[0]) {
                                    if (typeof (update[1].response.data[0][i].Localita) !== "undefined" && typeof (update[1].response.data[0][i].count) !== "undefined") {
                                        $scope.chosenCons.locs.push({
                                            name: update[1].response.data[0][i].Localita,
                                            prov: update[1].response.data[0][i].Provincia,
                                            count: parseInt(update[1].response.data[0][i].count),
                                            loading: false
                                        });
                                        sum += Number(parseInt(update[1].response.data[0][i].count));
                                    }
                                }
                                $scope.consulenti[$scope.chosenCons.index].totAssigned = sum;
                                $scope.chosenCons.totAssigned = sum;
                                $scope.lockChoosenCons = false;
                            },
                            function (t) {
                                $scope.lockChoosenCons = false;
                                alert("Qualcosa é andato storto! Riprovare.");
                            });
//                    }
//                    else {
//                        DataService.postData("pipeCommand",
//                                {loc: "_all_", lot: $scope.currentLot, amount: 999999, vend: $scope.chosenCons.userid},
//                        "emptyVendPerformed",
//                                {},
//                                function (t) {
//                                    var update = t;
//                                    $scope.chosenCons.lastUpdate = update[1].lastResTimestamp;
//                                    $scope.consulenti[$scope.chosenCons.index].lastUpdate = update[1].lastResTimestamp
//                                    $scope.chosenCons.locs = [];
//                                    var sum = 0;
//                                    console.log(update[1].response.data);
//                                    for (var i in update[1].response.data[0]) {
//                                        if (typeof (update[1].response.data[0][i].Localita) !== "undefined" && typeof (update[1].response.data[0][i].count) !== "undefined") {
//                                            $scope.chosenCons.locs.push({
//                                                name: update[1].response.data[0][i].Localita,
//                                                prov: update[1].response.data[0][i].Provincia,
//                                                count: parseInt(update[1].response.data[0][i].count),
//                                                loading: false
//                                            });
//                                            sum += Number(parseInt(update[1].response.data[0][i].count));
//                                        }
//                                    }
//                                    $scope.consulenti[$scope.chosenCons.index].totAssigned = sum;
//                                    $scope.chosenCons.totAssigned = sum;
//                                    $scope.lockChoosenCons = false;
//                                },
//                                function (t) {
//                                    $scope.lockChoosenCons = false;
//                                    alert("Qualcosa é andato storto! Riprovare.");
//                                });
//                    }
                    that.showRegionOnMap();
                    $scope.chosenCons.loading = false;
                    return;
                }
                var index = -1;
                for (var i in $scope.chosenCons.locs) {
                    if ($scope.chosenCons.locs[i].name === loc && $scope.chosenCons.locs[i].prov === prov) {
                        index = i;
                        break;
                    }
                }
                if (index === -1) {
                    $scope.chosenCons.locs.push({
                        name: loc,
                        prov: prov,
                        count: amount,
                        loading: true
                    });
                }
                else {
                    $scope.chosenCons.locs[index].count = Number($scope.chosenCons.locs[index].count) + Number(amount);
                }
                $scope.consulenti[$scope.chosenCons.index].totAssigned = Number($scope.consulenti[$scope.chosenCons.index].totAssigned) + Number(amount);
                $scope.chosenCons.totAssigned = Number($scope.chosenCons.totAssigned) + Number(amount);
                DataService.postData("pipeCommand",
                        {loc: loc, lot: $scope.currentLot, amount: amount, vend: $scope.chosenCons.userid},
                "addLocPerformed",
                        {},
                        function (t) {
                            var update = t;
                            $scope.chosenCons.lastUpdate = update[1].lastResTimestamp;
                            $scope.consulenti[$scope.chosenCons.index].lastUpdate = update[1].lastResTimestamp
                            $scope.chosenCons.locs = [];
                            var sum = 0;
                            console.log(update[1].response.data[0]);
                            for (var i in update[1].response.data[0]) {
                                if (typeof (update[1].response.data[0][i].Localita) !== "undefined" && typeof (update[1].response.data[0][i].count) !== "undefined") {
                                    $scope.chosenCons.locs.push({
                                        name: update[1].response.data[0][i].Localita,
                                        prov: update[1].response.data[0][i].Provincia,
                                        count: parseInt(update[1].response.data[0][i].count),
                                        loading: false
                                    });
                                    sum += Number(parseInt(update[1].response.data[0][i].count));
                                }
                            }
                            console.log($scope.chosenCons.locs);
                            $scope.consulenti[$scope.chosenCons.index].totAssigned = sum;
                            $scope.chosenCons.totAssigned = sum;
                            $scope.lockChoosenCons = false;
                        },
                        function (t) {
                            $scope.lockChoosenCons = false;
                            alert("Qualcosa é andato storto! Riprovare.");
                        });
                $scope.chosenCons.loading = false;
                that.showRegionOnMap();
                $rootScope.$digest();
            };
            this.removeLoc = function (loc, prov, count) {
                that.pipeOp(loc, count, prov, "", "minus");
            };
            this.newLot = function () {
                var newLotTicket = DataService.postData("newLot",
                        {camp: $scope.camp},
                "newLotPerformed",
                        {},
                        function (t) {
                            location.reload();
                        },
                        function (t) {
                            alert("Qualcosa é andato storto. Riprovare!");
                        });
            };
            this.emptyLot = function () {
                DataService.getData("emptyLot",
                        {lot: $scope.currentLot},
                "newLotPerformed",
                        {},
                        function (t) {
                            location.reload();
                        },
                        function (t) {
                            alert("Qualcosa é andato storto. Riprovare!");
                        });
            };
            this.changeLot = function () {
                $scope.lockChoosenCons = false;
                that.unchooseCons();
                $scope.consulenti = [];
                $scope.chosenCons = {};
                $scope.consDetails = false;
                that.reloadCommerciali();
            }

            $scope.geocoder = new google.maps.Geocoder();
            this.codeAddress = function (address, map, zoom) {
                $scope.geocoder.geocode({'address': address}, function (results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        map.setCenter(results[0].geometry.location);
                        zoom ? map.setZoom(zoom) : map.setZoom(10);
                    } else {
                        alert("Geocode was not successful for the following reason: " + status);
                    }
                });
            }
            this.showRegionOnMap = function () {
                if (!$scope.zone || !$scope.zone.provincia || !$scope.zone.provincia.length || !$scope.zone.regione || !$scope.zone.regione.length) {
                    return;
                }
                var address = $scope.zone.provincia;
                this.codeAddress(address, $scope.mapObj);

                DataService.getData("getLocalitaPerProvincia",
                        {provincia: $scope.zone.provincia},
                "getLocalitaPerProvinciaPerformed",
                        {provincia: $scope.zone.provincia},
                function (t) {
                    $scope.zone.locs = t[1].response.data[0];
                    console.log($scope.zone.locs);
                    for (var i in $scope.markers.markers) {
                        $scope.markers.markers[i].setMap(null);
                    }
                    $scope.markers = {markers: [], contentStrings: [], infowindows: []};
                    var addresses = [];
                    $scope.locList = [];
                    for (var i in $scope.zone.locs) {
                        $scope.markers.markers[i] = new google.maps.Marker({
                            position: new google.maps.LatLng(Number($scope.zone.locs[i].LAT), Number($scope.zone.locs[i].LNG)),
                            map: $scope.mapObj,
                            title: $scope.zone.locs[i].loc,
                            draggable: false
                        });

                        that.setMarkerDetails($scope.markers.markers[i], $scope.zone.locs[i].loc, $scope.zone.locs[i].count, $scope.zone.locs[i].callable, $scope.zone.locs[i].excluded, $scope.zone.regione, $scope.zone.provincia);
                        $scope.locList.push({
                            CAP: $scope.zone.locs[i].CAP,
                            LAT: $scope.zone.locs[i].LAT,
                            LNG: $scope.zone.locs[i].LNG,
                            PROV: $scope.zone.locs[i].PROV,
                            callable: parseInt($scope.zone.locs[i].callable),
                            count: parseInt($scope.zone.locs[i].count),
                            excluded: parseInt($scope.zone.locs[i].excluded),
                            loc: $scope.zone.locs[i].loc
                        });
                    }
                    console.log($scope.markers);
                    $scope.locList = _.sortBy($scope.locList, 'loc');
                    that.hoverLoc = null;

                },
                        function (t) {
                            alert("Spiacenti, non é stato possibile scaricare i dati di questa provincia. Riprovare.");
                        }
                );
            };
            this.clearSelection = function () {
                if ($scope.selectedShape) {
                    $scope.selectedShape = null;
                }
            }
            this.setSelection = function (shape) {
                clearSelection();
                $scope.selectedShape = shape;
                console.log(shape);
//                shape.setEditable(true);
//                selectColor(shape.get('fillColor') || shape.get('strokeColor'));
            }
            this.setMarkerDetails = function (marker, loc, count, callable, excluded, reg, prov) {
                var infowindow = new google.maps.InfoWindow({
                    content: '<div id="content" style="height: auto;"><div class=""><h3>' + loc + '</h3></div>' +
                            '<div class="">Aziende: ' + count + '</div>'
                            + '</div>',
                    maxWidth: 200
                });

                google.maps.event.addListener(marker, 'mouseover', function () {
                    $rootScope.showNotShow.geoDetails = true;
                    $scope.geoDetails = {
                        loc: loc,
                        count: count,
                        callable: callable,
                        excluded: excluded,
                        prov: prov,
                        reg: reg
                    };
                    $rootScope.$digest();
                });
                google.maps.event.addListener(marker, 'mouseout', function () {
                    $rootScope.showNotShow.geoDetails = false;
                });
                google.maps.event.addListener(marker, 'click', function () {
                    that.pipeOp(loc, callable, prov, reg, "plus");
                });
            }
            this.zoomOnLoc = function (loc) {
                console.log("click on ", loc);
                that.codeAddress(loc.LAT + ", " + loc.LNG, $scope.mapObj, 12);
                $scope.st.put("mappa");
                $timeout(function () {
                    var mC = $("#mapContainer");
                    if (mC && mC.length)
                        mC[0].scrollIntoView();
                }, 1000);
            }
            $scope.mapObj = {};
            $scope.mapControl = {};
            $scope.markers = {markers: [], contentStrings: [], infowindows: []};
            $timeout(function () {
                $scope.mapObj = $scope.mapControl.getGMap();
            }, 2000);
            $scope.map = {center: {latitude: 45.464407, longitude: 9.22683}, zoom: 16};
            $scope.options = {scrollwheel: false};
            $scope.drawingManagerOptions = {
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [
                        google.maps.drawing.OverlayType.POLYGON,
                    ]
                },
                polygonOptions: {
                    clickable: true,
                    draggable: true,
                    editable: true,
                    fillColor: '#ff0000',
                    fillOpacity: 0.5,
                    strokeWeight: 5,
                    zIndex: 1
                }
            };
            $scope.markersAndCircleFlag = true;
            $scope.drawingManagerControl = {};
            window.prova = $scope;
            $scope.$watch('markersAndCircleFlag', function () {
                if (!$scope.drawingManagerControl.getDrawingManager) {
                    console.log("!!");
                    return;
                }
                var controlOptions = angular.copy($scope.drawingManagerOptions);
                if (!$scope.markersAndCircleFlag) {
                    controlOptions.drawingControlOptions.drawingModes.shift();
                    controlOptions.drawingControlOptions.drawingModes.shift();
                }
                $scope.drawingManagerControl.getDrawingManager().setOptions(controlOptions);

            });
//            console.log($scope.drawingManagerControl);
//            console.log($scope.drawingManagerControl.getDrawingManager());
//            google.maps.event.addListener($scope.drawingManagerControl, 'overlaycomplete', function (e) {
//                console.log(e);
//                if (e.type != google.maps.drawing.OverlayType.MARKER) {
//                    // Switch back to non-drawing mode after drawing a shape.
//                    $scope.drawingManagerControl.setDrawingMode(null);
//
//                    // Add an event listener that selects the newly-drawn shape when the user
//                    // mouses down on it.
//                    var newShape = e.overlay;
//                    newShape.type = e.type;
//                    google.maps.event.addListener(newShape, 'click', function () {
//                        that.setSelection(newShape);
//                    });
//                    that.setSelection(newShape);
//                }
//            });

//            var baseurl = document.URL;
//            baseurl = baseurl.split("selezionezone");
//            baseurl = baseurl[0] + "selezionezone.ws";
//            DataService.initialize(baseurl);
//
//            $rootScope.$on("confFailure", function () {
//                alert("Self configuration failed. The page may not work properly. Please reload. If this is not the first time please contact an admin.");
//            });
        }]);

})();



