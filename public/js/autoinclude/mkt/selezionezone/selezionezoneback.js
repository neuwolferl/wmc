(function () {
    return;
    var app = angular.module("SelezioneZone", ['uiGmapgoogle-maps', 'ngDragDrop', 'DATAMODULE', 'THEMASK'], ['$interpolateProvider',function ($interpolateProvider) {
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
    app.controller("ConsulentiController", ['DataService', '$rootScope',function (DataService, $rootScope) {
        this.consulenti = [];
        this.consDetails = false;
        this.lockChoosenCons = false;
        var that = this;
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
        }

        this.chosenCons = {name: "", totAssigned: "", locs: [], lastUpdate: 0};
        this.chooseCons = function (userid) {
            this.chosenCons = {name: "", totAssigned: "", locs: [], lastUpdate: 0};
            var index = -1;
            for (var i in that.consulenti) {
                if (that.consulenti[i].userid === userid) {
                    index = i;
                    break;
                }
            }
            if (index === -1) {
                return;
            }
            that.chosenCons = {
                userid: userid,
                name: that.consulenti[i].name,
                totAssigned: that.consulenti[i].totAssigned,
                locs: that.consulenti[i].locs,
                index: index,
                lastUpdate: that.consulenti[i].lastUpdate,
                loading: false
            }
            this.consDetails = true;
        };
        this.unchooseCons = function () {
            if (that.lockChoosenCons)
                return;
            if (!that.chosenCons || !that.chosenCons.index || that.chosenCons.index < 0)
                return;
            var sum = 0;
            for (var i in this.chosenCons.locs) {
                if (this.chosenCons.locs[i].loading)
                    return;
                sum += Number(this.chosenCons.locs[i].count);
            }
            this.consulenti[this.chosenCons.index].locs = this.chosenCons.locs;
            this.consulenti[this.chosenCons.index].totAssigned = sum;
            this.chosenCons = {name: "", totAssigned: "", locs: [], lastUpdate: 0};
            this.consDetails = false;
        }
        $rootScope.$on("changeLot", function (event, mass) {
            that.lockChoosenCons = false;
            that.unchooseCons();
            that.consulenti = [];
            that.chosenCons = {};
            that.consDetails = false;
            $rootScope.$emit("confLoaded", {reloading: true});
        });
        this.pipeOp = function (loc, callable, prov, reg, plusminus) {
            if (that.consDetails === false)
                return;
            if (that.chosenCons.loading)
                return;
            that.lockChosenCons = true;
            that.chosenCons.loading = true;
            if (plusminus === "plus") {
                var amount = prompt("In questa località sono disponibili " + callable + " aziende. Quante ne vuoi assegnare?", callable);
            }
            else {
                var amount = prompt("Questo venditore ha " + callable + " aziende nella località " + loc + ". Quante ne vuoi rimuovere?", callable);
            }
            if (isNaN(amount))
                return;
            if (plusminus === "minus") {
                var index = -1;
                for (var i in that.chosenCons.locs) {
                    if (that.chosenCons.locs[i].name === loc && that.chosenCons.locs[i].prov === prov) {
                        index = i;
                        break;
                    }
                }
                if (index === -1) {
                    return;
                }
                else {
                    that.chosenCons.locs[index].count = Math.max(0, Number(that.chosenCons.locs[index].count) - Number(amount));
                }
                var remLocTicket = DataService.postData("pipeCommand", {loc: loc, lot: $rootScope.currentLot, amount: (-1) * Number(amount), vend: that.chosenCons.userid}, "remLocPerformed", {});
                $rootScope.$on("remLocPerformed", function (event, mass) {
                    var update = DataService.find(mass.ticket);
                    that.chosenCons.lastUpdate = update[1].lastResTimestamp;
                    that.consulenti[that.chosenCons.index].lastUpdate = update[1].lastResTimestamp
                    that.chosenCons.locs = [];
                    var sum = 0;
                    for (var i in update[1].response.data) {
                        if (typeof (update[1].response.data[i].Localita) !== "undefined" && typeof (update[1].response.data[i].count) !== "undefined") {
                            that.chosenCons.locs.push({
                                name: update[1].response.data[i].Localita,
                                prov: update[1].response.data[i].Provincia,
                                count: update[1].response.data[i].count,
                                loading: false
                            });
                            sum += Number(update[1].response.data[i].count);
                        }
                    }
                    that.consulenti[that.chosenCons.index].totAssigned = sum;
                    that.chosenCons.totAssigned = sum;
                    that.lockChoosenCons = false;
                });
                $rootScope.$emit("reloadDataOnMap", {});
                that.chosenCons.loading = false;
                return;
            }
            var index = -1;
            for (var i in that.chosenCons.locs) {
                if (that.chosenCons.locs[i].name === loc && that.chosenCons.locs[i].prov === prov) {
                    index = i;
                    break;
                }
            }
            if (index === -1) {
                that.chosenCons.locs.push({
                    name: loc,
                    prov: prov,
                    count: amount,
                    loading: true
                });
            }
            else {
                that.chosenCons.locs[index].count = Number(that.chosenCons.locs[index].count) + Number(amount);
            }
            that.consulenti[that.chosenCons.index].totAssigned = Number(that.consulenti[that.chosenCons.index].totAssigned) + Number(amount);
            that.chosenCons.totAssigned = Number(that.chosenCons.totAssigned) + Number(amount);
            var addLocTicket = DataService.postData("pipeCommand", {loc: loc, lot: $rootScope.currentLot, amount: amount, vend: that.chosenCons.userid}, "addLocPerformed", {});
            $rootScope.$on("addLocPerformed", function (event, mass) {
                var update = DataService.find(mass.ticket);
                that.chosenCons.lastUpdate = update[1].lastResTimestamp;
                that.consulenti[that.chosenCons.index].lastUpdate = update[1].lastResTimestamp
                that.chosenCons.locs = [];
                var sum = 0;
                for (var i in update[1].response.data) {
                    if (typeof (update[1].response.data[i].Localita) !== "undefined" && typeof (update[1].response.data[i].count) !== "undefined") {
                        that.chosenCons.locs.push({
                            name: update[1].response.data[i].Localita,
                            prov: update[1].response.data[i].Provincia,
                            count: update[1].response.data[i].count,
                            loading: false
                        });
                        sum += Number(update[1].response.data[i].count);
                    }
                }
                that.consulenti[that.chosenCons.index].totAssigned = sum;
                that.chosenCons.totAssigned = sum;
                that.lockChoosenCons = false;
            });
            that.chosenCons.loading = false;
            $rootScope.$emit("reloadDataOnMap", {});
            $rootScope.$digest();
        };
        this.removeLoc = function (loc, prov, count) {
            that.pipeOp(loc, count, prov, "", "minus");
        }
        this.loadCommerciali = function (commTicket) {
            commTicket = DataService.find(commTicket);
            that.consulenti = [];
            var data = commTicket[1].response.data;
            for (var i in data) {
                that.consulenti.push({
                    userid: data[i].USERID,
                    name: data[i].NOME + " " + data[i].COGNOME,
                    totAssigned: 0,
                    locs: []
                });
            }
        };
        $rootScope.$on("getCommercialiPerformed", function (event, mass) {
            if (typeof (mass.failure) === "undefined") {
                that.loadCommerciali(mass.ticket);
                var commLoadTicket = DataService.getData("getPipeStatus", {pipe: 1, lot: $rootScope.currentLot}, "getPipeStatusPerformed", {});
            }
        });
        $rootScope.$on("getPipeStatusPerformed", function (event, mass) {
            if (typeof (mass.failure) === "undefined") {
                var commLoadTicket = DataService.find(mass.ticket);
                var data = commLoadTicket[1].response.data;

                for (var i in data) {
                    for (var j in that.consulenti) {
                        if (data[i].vend === that.consulenti[j].userid) {
                            that.consulenti[j].locs.push({
                                name: data[i].Localita,
                                prov: data[i].Provincia,
                                count: data[i].count,
                                loading: false
                            });
                            that.consulenti[j].totAssigned += Number(data[i].count);
                            break;
                        }
                    }
                }
            }
        });
        $rootScope.$on("Clicked Marker", function (event, mass) {
            that.pipeOp(mass.loc, mass.callable, mass.prov, mass.reg, "plus");
        });
        $rootScope.$on("reloadCamp", function (event, mass) {
            var baseurl = document.URL;
            baseurl = baseurl.split("selezionezone/");
            if (baseurl.length < 2) {
                alert("Attenzione: non è stata specificata una campagna, l'applicazione potrebbe presentare comportamenti non previsti e\n\
produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
            }
            var campaign = baseurl[1];

            var campTicket = DataService.getData("getCampaign", {camp: campaign}, "getCampaignPerformed", {camp: campaign});
            $rootScope.$on("getCampaignPerformed", function (event, mass) {
                var data = campTicket[1].response.data;
                if (data.length !== 1) {
                    alert("Attenzione: si è verificato un errore nel recupero della campagna, l'applicazione potrebbe presentare comportamenti \n\
non previsti e produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
                }
                var lots = data[0].lots;
                lots = lots.replace("[", "").replace("]", "");
                $rootScope.camp = mass.camp;
                $rootScope.lots = lots.split(",");
                if (!$rootScope.currentLot || typeof ($rootScope.currentLot) === "undefined" || $rootScope.currentLot < 0)
                    $rootScope.currentLot = ($rootScope.lots.length ? $rootScope.lots[$rootScope.lots.length - 1] : -1);
            });
        });
        $rootScope.$on("confLoaded", function (event, mass) {

            var baseurl = document.URL;
            baseurl = baseurl.split("selezionezone/");
            if (baseurl.length < 2) {
                alert("Attenzione: non è stata specificata una campagna, l'applicazione potrebbe presentare comportamenti non previsti e\n\
produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
            }
            var campaign = baseurl[1];
            var commTicket;
            if (typeof (mass.reloading) === "undefined") {
                var campTicket = DataService.getData("getCampaign", {camp: campaign}, "getCampaignPerformed", {camp: campaign});
                $rootScope.$on("getCampaignPerformed", function (event, mass) {
                    var data = campTicket[1].response.data;
                    if (data.length !== 1) {
                        alert("Attenzione: si è verificato un errore nel recupero della campagna, l'applicazione potrebbe presentare comportamenti \n\
non previsti e produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
                    }
                    var lots = data[0].lots;
                    lots = lots.replace("[", "").replace("]", "");
                    $rootScope.camp = mass.camp;
                    $rootScope.lots = lots.split(",");
                    if (!$rootScope.currentLot || typeof ($rootScope.currentLot) === "undefined" || $rootScope.currentLot < 0)
                        $rootScope.currentLot = ($rootScope.lots.length ? $rootScope.lots[$rootScope.lots.length - 1] : -1);
                    commTicket = DataService.getData("getCommerciali", {}, "getCommercialiPerformed", {});
                });
            }
            else {
                commTicket = DataService.getData("getCommerciali", {}, "getCommercialiPerformed", {});
            }
        });
    }]);
    app.controller("GeoSelController", function () {
        this.geo = {
            regione: "",
            provincia: "",
            locs: [],
            loc: ""
        };
        this.regioni = ["Abruzzo", "Basilicata", "Calabria", "Campania", "Emilia-Romagna", "Friuli-Venezia Giulia", "Lazio",
            "Liguria", "Lombardia", "Marche", "Molise", "Piemonte", "Puglia", "Sardegna", "Sicilia", "Toscana", "Trentino-Alto Adige",
            "Umbria", "Valle d'Aosta", "Veneto"];
        this.province = {
            "Abruzzo": ["L'Aquila", "Teramo", "Chieti", "Pescara"],
            "Basilicata": ["Matera", "Potenza"],
            "Calabria": ["Cosenza", "Reggio Calabria", "Catanzaro", "Crotone", "Vibo Valentia"],
            "Campania": ["Salerno", "Benevento", "Napoli", "Avellino", "Caserta"],
            "Emilia-Romagna": ["Ravenna", "Modena", "Ferrara", "Rimini", "Piacenza", "Forli-Cesena", "Bologna", "Reggio Emilia", "Parma", "Forli"],
            "Friuli-Venezia Giulia": ["Trieste", "Pordenone", "Udine", "Gorizia"],
            "Lazio": ["Latina", "Roma", "Frosinone", "Rieti", "Viterbo"],
            "Liguria": ["Imperia", "Savona", "Genova", "La Spezia"],
            "Lombardia": ["Bergamo", "Monza e della Brianza", "Lodi", "Como", "Sondrio", "Milano", "Lecco", "Brescia", "Pavia", "Mantova", "Cremona", "Varese"],
            "Marche": ["Pesaro Urbino", "Ascoli Piceno", "Macerata", "Ancona", "Fermo"],
            "Molise": ["Isernia", "Campobasso"],
            "Piemonte": ["Torino", "Biella", "Vercelli", "Novara", "Asti", "Verbano-Cusio-Ossola", "Cuneo", "Alessandria"],
            "Puglia": ["Taranto", "Brindisi", "Lecce", "Barletta-Andria-Trani", "Foggia", "Bari"],
            "Sardegna": ["Sassari", "Ogliastra", "Carbonia-Iglesias", "Oristano", "Nuoro", "Cagliari", "Olbia-Tempio", "Medio Campidano"],
            "Sicilia": ["Siracusa", "Messina", "Caltanissetta", "Ragusa", "Enna", "Trapani", "Agrigento", "Palermo", "Catania"],
            "Toscana": ["Pisa", "Livorno", "Arezzo", "Prato", "Massa-Carrara", "Grosseto", "Pistoia", "Lucca", "Firenze", "Siena"],
            "Trentino-Alto Adige": ["Trento", "Bolzano"],
            "Umbria": ["Terni", "Perugia"],
            "Valle d'Aosta": ["Aosta"],
            "Veneto": ["Venezia", "Padova", "Vicenza", "Treviso", "Belluno", "Verona", "Rovigo"]
        };
        this.regioniLoaded = true;
        var that = this;
        this.getRegione = function () {
            if (!that.regioniLoaded) {

            }
            else {
                return that.regioni;
            }
        }
    });
    app.controller("ZoneController", ['DataService', '$scope', '$rootScope', '$timeout',function (DataService, $scope, $rootScope, $timeout) {
        $rootScope.showNotShow = {geoDetails: false};
        var that = this;
        this.geo = {
            regione: "",
            provincia: ""
        };
        this.geocoder = new google.maps.Geocoder();
        this.codeAddress = function (address, map) {
            this.geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    map.setZoom(10);
                } else {
                    alert("Geocode was not successful for the following reason: " + status);
                }
            });
        }
        $rootScope.$on("reloadDataOnMap", function (event, mass) {
            that.showRegionOnMap();
        });
        this.showRegionOnMap = function () {
            if (typeof (this.geo.provincia) === "undefined" || this.geo.provincia === "") {
                return;
            }
            var address = this.geo.provincia;
            this.codeAddress(address, this.mapObj);
            var ticket = DataService.getData("getLocalitaPerProvincia", {provincia: this.geo.provincia}, "getLocalitaPerProvinciaPerformed", {provincia: this.geo.provincia});

            $rootScope.$on("getLocalitaPerProvinciaPerformed", function (event, mass) {
                $rootScope.$$listeners.getLocalitaPerProvinciaPerformed = [];
                that.geo.locs = ticket[1].response.data[0];
                console.log(that.geo.locs);
                for (var i in that.markers.markers) {
                    that.markers.markers[i].setMap(null);
                }
                that.markers = {markers: [], contentStrings: [], infowindows: []};
                var addresses = [];
                for (var i in that.geo.locs) {
                    that.markers.markers[i] = new google.maps.Marker({
                        position: new google.maps.LatLng(Number(that.geo.locs[i].LAT), Number(that.geo.locs[i].LNG)),
                        map: that.mapObj,
                        title: that.geo.locs[i].loc,
                        draggable: false
                    });
                    that.setMarkerDetails(that.markers.markers[i], that.geo.locs[i].loc, that.geo.locs[i].count, that.geo.locs[i].callable, that.geo.regione, that.geo.provincia);
                }
                $rootScope.$emit("gotNewList", {list: ticket[1].response.data});
            });

        };

        this.setMarkerDetails = function (marker, loc, count, callable, reg, prov) {
            var infowindow = new google.maps.InfoWindow({
                content: '<div id="content" style="height: auto;"><div class=""><h3>' + loc + '</h3></div>' +
                        '<div class="">Aziende: ' + count + '</div>'
                        + '</div>',
                maxWidth: 200
            });
            google.maps.event.addListener(marker, 'mouseover', function () {
                $rootScope.showNotShow.geoDetails = true;
                $rootScope.geoDetails = {
                    loc: loc,
                    count: count,
                    callable: callable,
                    prov: prov,
                    reg: reg
                };
                $rootScope.$digest();
            });
            google.maps.event.addListener(marker, 'mouseout', function () {
                $rootScope.showNotShow.geoDetails = false;
            });
            google.maps.event.addListener(marker, 'click', function () {
                $rootScope.$emit("Clicked Marker", {
                    loc: loc,
                    callable: callable,
                    prov: prov,
                    reg: reg
                });
            });
        }
        this.mapObj = {};
        this.mapControl = {};
        this.markers = {markers: [], contentStrings: [], infowindows: []};
        $timeout(function () {
            that.mapObj = that.mapControl.getGMap();
        }, 2000);
        this.map = {center: {latitude: 45.464407, longitude: 9.22683}, zoom: 16};
        this.options = {scrollwheel: false};
        this.drawingManagerOptions = {
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
                fillColor: '#ffff00',
                fillOpacity: 0.5,
                strokeWeight: 5,
                zIndex: 1
            }
        };
        $scope.markersAndCircleFlag = true;
        this.drawingManagerControl = {};
        $scope.$watch('markersAndCircleFlag', function () {
            if (!that.drawingManagerControl.getDrawingManager) {
                return;
            }
            var controlOptions = angular.copy(this.drawingManagerOptions);
            if (!that.markersAndCircleFlag) {
                controlOptions.drawingControlOptions.drawingModes.shift();
                controlOptions.drawingControlOptions.drawingModes.shift();
            }
            that.drawingManagerControl.getDrawingManager().setOptions(controlOptions);
        });
        var baseurl = document.URL;
        baseurl = baseurl.split("selezionezone");
        baseurl = baseurl[0] + "selezionezone.ws";
        DataService.initialize(baseurl);

        $rootScope.$on("confFailure", function () {
            alert("Self configuration failed. The page may not work properly. Please reload. If this is not the first time please contact an admin.");
        });
    }]);
    app.controller("LocListController", ['$rootScope',function ($rootScope) {
        this.list = [];
        var that = this;
        $rootScope.$on("gotNewList", function (event, mass) {
            that.list = mass.list;
        });
    }]);
    app.controller("CampController", ['DataService', '$rootScope',function (DataService, $rootScope) {
        this.newLot = function () {
            var newLotTicket = DataService.postData("newLot", {camp: $rootScope.camp}, "newLotPerformed", {});
            $rootScope.$on("newLotPerformed", function (event, mass) {
                $rootScope.$$listeners.newLot = [];
                $rootScope.$emit("reloadCamp", {});
            });
        };
        this.changeLot = function () {
            $rootScope.$emit("changeLot", {});
        }
    }]);
})();



