(function () {
    var app = angular.module("GestioneTelefonate", ['uiGmapgoogle-maps', 'DATAMODULE', 'THEMASK', 'SINOTTICO', 'LOGGER', 'SMARTBUTTON', 'COMMON'
                , 'LEADTABLEMODULE'],
            ['$interpolateProvider', function ($interpolateProvider) {
                    $interpolateProvider.startSymbol("--__");
                    $interpolateProvider.endSymbol("__--");
                }]);

    app.controller("CallController",
            ['$rootScope', '$scope', 'DataService', 'TheMask', 'Sin', 'Logger', 'SmartButton', '$timeout', '$interval', 'Common', '$window',
                function ($rootScope, $scope, DataService, TheMask, Sin, Logger, SmartButton, $timeout, $interval, Common, $window) {

                    var that = this;




                    /*
                     * Dati
                     */
                    this.configurazioneDati = function () {
                        $scope.opPar = {
                            tmk: -1,
                            vend: -1,
                            camp: 1,
                            vendorName: "",
                            tmkName: "",
                            sheetLoadTimer: {start: null, now: null, timer: null},
                            logGeneralDesc: null,
                            userList: null,
                            statoInventario: null,
                            selezione: null
                        }
                        $scope.dataPar = {
                            azienda: {},
                            vt: {},
                            numeri: [],
                            show: {},
                            edit: "",
                            inventario: null
                        };
                    };
                    this.configurazioneDati();



                    /*
                     * Logger
                     */
                    this.configurazioneLogger = function () {
                        this.logger = Logger.newLogger();
                        Logger.getLogger(this.logger[0]).setLogGetter(function (args) {
                            return DataService.getData("getLog", {
                                piva: args.piva
                            },
                            "getLogPerformed", {},
                                    function (t) {
                                        Logger.getLogger(that.logger[0]).setData(t[1].response.data);
                                    });
                        });
                        this.reloadLog = function () {
                            Logger.getLogger(that.logger[0]).stopLogRefresher();
                            Logger.getLogger(that.logger[0]).startLogRefresher(10000, //scommentare in produzione
                                    {
                                        piva: $scope.dataPar.azienda.PartitaIva
                                    });

                            Logger.getLogger(this.logger[0]).setLogDescriptor("logGeneral", $scope.opPar.logGeneralDesc, $scope.opPar.userList);

                        };
                        Logger.getLogger(this.logger[0]).startChangeMirroring();
                        Logger.getLogger(this.logger[0]).setLogPutter(function (args) {
                            var payload = {
                                piva: args.piva,
                                what: args.what,
                                parameters: args.parameters,
                                esito: args.esito,
                            };
                            if (typeof (args.comment) !== "undefined")
                                payload.comment = args.comment;
                            if (typeof (args.parent) !== "undefined")
                                payload.parent = args.parent;
                            return DataService.postData("putLog", payload, "putLogPerformed", {});
                        });

                        this.loggerInventario = Logger.newLogger();
                        Logger.getLogger(this.loggerInventario[0]).setLogGetter(function (args) {
                            return DataService.getData("getLog", {
                                piva: args.piva
                            },
                            "getLogPerformed", {},
                                    function (t) {
                                        Logger.getLogger(that.loggerInventario[0]).setData(t[1].response.data);
                                    });
                        });
                        Logger.getLogger(this.loggerInventario[0]).startChangeMirroring();
//            Logger.getLogger(this.logger[0]).startChangeMirroring();
//            Logger.getLogger(this.logger[0]).setLogPutter(function (args) {
//                var payload = {
//                    piva: args.piva,
//                    what: args.what,
//                    parameters: args.parameters,
//                    esito: args.esito,
//                };
//                if (typeof (args.comment) !== "undefined")
//                    payload.comment = args.comment;
//                if (typeof (args.parent) !== "undefined")
//                    payload.parent = args.parent;
//                return DataService.postData("putLog", payload, "putLogPerformed", {});
//            });
                    };
                    this.configurazioneLogger();

                    /*
                     * Static
                     */
                    $scope.staticPar = {
                        regioni: ["Abruzzo", "Basilicata", "Calabria", "Campania", "Emilia-Romagna", "Friuli-Venezia Giulia", "Lazio", "Liguria", "Lombardia", "Marche", "Molise", "Piemonte", "Puglia", "Sardegna", "Sicilia", "Toscana", "Trentino-Alto Adige", "Umbria", "Valle d'Aosta", "Veneto"],
                        province: {
                            "Abruzzo": ["L'Aquila", "Teramo", "Chieti", "Pescara"],
                            "Basilicata": ["Matera", "Potenza"],
                            "Calabria": ["Cosenza", "Reggio Calabria", "Catanzaro", "Crotone", "Vibo Valentia"],
                            "Campania": ["Salerno", "Benevento", "Napoli", "Avellino", "Caserta"],
                            "Emilia-Romagna": ["Ravenna", "Modena", "Ferrara", "Rimini", "Piacenza", "Forlì-Cesena", "Bologna", "Reggio Emilia", "Parma", "Forlì"],
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

                        },
                        editable: [
                            "RagioneSociale",
                            "Indirizzo",
                            "Localita",
                            "Cap",
                            "Provincia",
                            "Regione",
                            "Email",
                            "Sito",
                            "FormaGiuridica",
                            "FasciaFatturato",
                            "ClasseDipendenti"
                        ],
                    };


                    /*
                     * Maschere
                     */
                    this.mascheraNuovoNumero = TheMask.newMask();
                    this.mascheraNuovoContatto = TheMask.newMask();


                    /*
                     * Selezione 
                     */

                    this.mascheraSelezione = TheMask.newMask();
                    this.apriSelezione = function () {
                        that.mascheraSelezione[1].open();
                    }
                    TheMask.getMask(this.mascheraSelezione[0]).setBtnCallback(0, function () {
                        that.mascheraSelezione[1].close();
                    });

                    /*
                     * Sinottico
                     */
                    this.configurazioneSinottico = function () {
                        this.sinottico = Sin.newCal();
                        this.sinLabelCallback = function (ticket) {
                            ticket = DataService.getData("getCommerciali", {}, "getCommercialiPerformed", {});
//            console.log(ticket);
                            return "getCommercialiPerformed";
                        };
                        this.sinLabelRetrieveCallback = function (ticket) {
                            ticket = DataService.find(ticket);
                            var labels = [];
                            for (var i in ticket[1].response.data) {
                                labels.push(ticket[1].response.data[i].NOME + " " + ticket[1].response.data[i].COGNOME)
                            }
                            return labels;
                        };
                        Sin.getCal(this.sinottico[0]).setLabelGetter(that.sinLabelCallback);
                        Sin.getCal(this.sinottico[0]).setLabelRetriever(that.sinLabelRetrieveCallback);
                        this.sinDataCallback = function (arg) {
                            var pad10 = Common.pad10;
                            var engDatePat = /\d{4}-\d{2}-\d{2}/;
                            if (!arg.date)
                                return;
                            if (!engDatePat.test(arg.date)) {
                                if (!(arg.date.getTime && !isNaN(arg.date.getTime()))) {
                                    arg.date = new Date(arg.date);
                                }
                                arg.date = arg.date.getFullYear() + "-" + pad10(arg.date.getMonth() + 1) + "-" + pad10(arg.date.getDate());
                            }
                            arg.ticket = DataService.postData("getAppuntamenti", {commerciali: arg.labels, data: arg.date}, "getAppuntamentiPerformed", {data: arg.date});
                            return "getAppuntamentiPerformed";
                        };
                        this.sinDataRetrieveCallback = function (ticket) {
                            ticket = DataService.find(ticket);
                            return ticket[1].response.data;
                        };
                        Sin.getCal(this.sinottico[0]).setDataGetter(that.sinDataCallback);
                        Sin.getCal(this.sinottico[0]).setDataRetriever(that.sinDataRetrieveCallback);
                        Sin.getCal(this.sinottico[0]).setOnClick(function (args) {
                        });
                        Sin.getCal(this.sinottico[0]).setOnMouseMove(function (args) {
//            var datum = Sin.getCal(that.sinottico[0]).getShapeUnderlyingData(args.shape.index);

                        });
                        Sin.getCal(this.sinottico[0]).setShapeInfoDisplayer(function (datum) {
                            var string = "Appuntamento di <b>" + datum.vend + "</b> presso: <br>";
                            string += "<b>" + datum.accountname + "</b><br>";
                            string += "<i>" + datum.piva + "</i><br>";
                            string += "<i>" + datum.location + "</i><br>";
                            string += "<i>" + datum.phone + "</i><br>";
                            var start = new Date(datum.date_start + " " + datum.time_start + ":00");
                            var end = new Date(datum.due_date + "  " + datum.time_end + ":00");
                            var starts = (start.getDate() < 10 ? "0" : "") + start.getDate() + "-" + (start.getMonth() < 9 ? "0" : "") + (start.getMonth() + 1) + "-" + start.getFullYear() + " " + (start.getHours() < 9 ? "0" : "") + start.getHours() + ":" + (start.getMinutes() < 9 ? "0" : "") + start.getMinutes();
                            var ends = (end.getDate() < 10 ? "0" : "") + end.getDate() + "-" + (end.getMonth() < 9 ? "0" : "") + (end.getMonth() + 1) + "-" + end.getFullYear() + " " + (end.getHours() < 9 ? "0" : "") + end.getHours() + ":" + (end.getMinutes() < 9 ? "0" : "") + end.getMinutes();
                            string += "da: " + starts + "<br>";
                            string += "a: " + ends + "<br>";
                            string += "<br><br>";
                            if (datum.stato == "nonconfermato")
                                string += "L'appuntamento &eacute; in attesa di conferma <br>";
                            else if (datum.stato == "confermato")
                                string += "L'appuntamento &eacute; stato confermato <br>";
                            else if (datum.stato == "ripasso")
                                string += "L'appuntamento &eacute; in stato di ripasso <br>";
                            string += "<br><br>";
                            string += "L'appuntamento è stato fissato da: " + datum.tmk + "<br>";
                            return string;
                        });
                        this.sinottico[1].renderPars.startTime = new Date();
                        this.sinottico[1].renderPars.startTime.setHours(6);
                        this.sinottico[1].renderPars.endTime = new Date();
                        this.sinottico[1].renderPars.endTime.setHours(20);
                        this.sinottico[1].renderPars.colors = {
                            "confermato": "green", "nonconfermato": "yellow", "sovrapposto": "black", "ripasso": "red"
                        };
                        this.sinottico[1].renderPars.colorer = "stato";
                        this.sinottico[1].renderPars.title.title = function (calObj, dateObj) {
                            return 'Sinottico degli appuntamenti per il giorno ' + dateObj.dateItaString;
                        }
                    }
                    this.configurazioneSinottico();


                    /*
                     * inventario
                     */
                    this.configurazioneInventario = function () {
                        this.mascheraInventario = TheMask.newMask();
                        TheMask.getMask(this.mascheraInventario[0]).setBtnCallback(0, function () {
                            Logger.getLogger(that.loggerInventario[0]).stopLogRefresher();
                            that.mascheraInventario[1].close();
                            $scope.opPar.statoInventario = null;
                        });
                        this.apriInventario = function () {
                            DataService.getData("getInventario", {tmk: $scope.opPar.tmk}, "getInventarioPerformed", {},
                                    function (t) {
                                        $scope.dataPar.inventario = t[1].response.data;
                                        $scope.opPar.statoInventario = 1
                                    },
                                    function (t) {
                                    }
                            );
                            that.mascheraInventario[1].open();
                        };
                        this.apriInventarioDettagli = function (index) {
                            $scope.opPar.statoInventario = 2;
                            Logger.getLogger(that.loggerInventario[0]).stopLogRefresher();
                            Logger.getLogger(that.loggerInventario[0]).startLogRefresher(10000,
                                    {
                                        piva: $scope.dataPar.inventario[index].piva
                                    });

                            Logger.getLogger(that.loggerInventario[0]).setLogDescriptor("logGeneral", $scope.opPar.logGeneralDesc, $scope.opPar.userList);
                        };
                        this.chiudiInventarioDettagli = function () {
                            $scope.opPar.statoInventario = 1;
                            Logger.getLogger(that.loggerInventario[0]).stopLogRefresher();
                        }
                    };
                    this.configurazioneInventario();


                    /*
                     * Esiti
                     */
                    this.configurazioneEsiti = function () {
                        this.creazioneAppuntamento = {};
                        this.impostazioneRichiamo = {};
                        this.scartoScheda = {};
                        this.emptyCreazioneAppuntamento = function () {
                            that.creazioneAppuntamento = {
                                formData: {
                                    date: "",
                                    time: "",
                                    contact: "",
                                    note: ""
                                },
                                formDataError: {
                                    date: false,
                                    time: false,
                                    contact: false,
                                    note: false
                                },
                                creaAccount: false,
                                account: {},
                                potential: {},
                                contacts: [],
                                activity: {},
                                result: {account: 0, potential: 0, activity: 0, contacts: 0},
                                pending: {account: false, potential: false, activity: false, contacts: false}
                            };
                        };
                        this.emptyImpostazioneRichiamo = function () {
                            that.impostazioneRichiamo = {
                                formData: {
                                    date: "",
                                    time: "",
                                    note: "",
                                    self: false
                                },
                                formDataError: {
                                    date: false,
                                    time: false,
                                    note: false
                                },
                                result: 0,
                                pending: true
                            };
                        };
                        this.emptyScartoScheda = function () {
                            that.scartoScheda = {
                                formData: {
                                    dettagli: "",
                                    esito: -1
                                },
                                formDataError: {
                                    dettagli: false
                                },
                                result: 0
                            };
                        };
                        this.emptyCreazioneAppuntamento();
                        this.emptyImpostazioneRichiamo();
                        this.emptyScartoScheda();
                        this.mascheraCreazioneAppuntamento = TheMask.newMask();
                        this.mascheraImpostazioneRichiamo = TheMask.newMask();
                        this.mascheraScartaScheda = TheMask.newMask();
                    };
                    this.configurazioneEsiti();

                    /*
                     * workflow
                     */
                    $scope.workFlow = {
                        waitingForConf: true,
                        waitingForSheet: false,
                        preCall: false,
                        calling: false,
                        waitingForCallOutcome: false,
                        waitingForNegotiationOutcome: false,
                        waitingForAppOutcome: false,
                        waitingForRecallOutcome: false,
                        writing: false,
                        confError: false,
                        sheetError: false
                    };
                    $scope.$watch("workFlow", function (newValue, oldValue) {
                        var flag = true;
                        for (var i in newValue) {
                            if (newValue.hasOwnProperty(i) && oldValue.hasOwnProperty(i) && newValue[i] !== oldValue[i]) {
                                flag = false;
                                break;
                            }
                        }
                        if (flag) {
                            return;
                        }
                        if (newValue.waitingForConf) {
                            that.configura();
                        }
                    });
                    this.status = function () { // debug
                        for (var i in $scope.workFlow) {
                            if ($scope.workFlow.hasOwnProperty(i)) {
                                if ($scope.workFlow[i])
                                    return i;
                            }
                        }
                    }

                    this.destroyPage = function () {
                        this.errorMask = true;
                        $(".topcontainer").children().not("error-mask").each(function () {
                            $(this).hide();
                        });
                        if ($scope.opPar.sheetLoadTimer.timer.then)
                            $interval.cancel($scope.opPar.sheetLoadTimer.timer);
                        $scope.opPar.sheetLoadTimer = {start: null, now: null, timer: null};
                    }

                    /*
                     * FINE CONFIGURAZIONE
                     */

                    /*
                     * OPERAZIONI
                     */
                    this.defOp = function () {

                        //scarica configurazione Pagina (venditore - notifiche)
                        this.configura = function () {
//                console.log(DataService.getStaticResource("configuraGestChiam", {}, "asd", {}).success(function () {
//                    console.log("ciao");
//                }));
                            $scope.opPar.sheetLoadTimer.start = new Date();
                            $scope.opPar.sheetLoadTimer.timer = $interval(function () {
                                $scope.opPar.sheetLoadTimer.now = new Date();
                            }, 500);
                            DataService.getData("configuraGestChiam", {}, "configuraGestChiamPerformed", {},
                                    function (t) {
                                        var ticket = t;
                                        $scope.opPar.tmk = Number(ticket[1].response.data.tmk);
                                        $scope.opPar.vend = Number(ticket[1].response.data.vend);
                                        $scope.opPar.tmkName = ticket[1].response.data.tmkName;
                                        $scope.opPar.vendorName = ticket[1].response.data.vendorName;
                                        $scope.opPar.vendors = ticket[1].response.data.vendors;
                                        setWFStatus($scope.workFlow, "waitingForSheet");
                                        that.chiediNuovaScheda(); // da spostare dopo analisi configurazione
                                    },
                                    function (t) {
                                        alert("Attenzione, non è stato possibile configurare correttamente la pagina.");
                                        that.destroyPage();
                                    }
                            );
                        };
                        //Nuova scheda

                        this.chiediNuovaScheda = function () {
                            var date = new Date();
                            $scope.dataParRequest = DataService.getData("getNewSheet", {
                                tmk: $scope.opPar.tmk,
                                vend: $scope.opPar.vend,
                                camp: $scope.opPar.camp,
                                ts: date.getTime(),
                                sel: $scope.opPar.selezione
                            }, "getNewSheetPerformed", {},
                                    function (t) {
                                        that.nuovaScheda(t[0]);
                                    },
                                    function (t) {
                                        alert("Non è arrivata la nuova scheda: " + t[1].response);
                                        setWFStatus($scope.workFlow, "sheetError");
                                    }

                            );
                        };

                    };
                    this.defOp();



                    //scarica configurazione DataService
                    var baseurl = document.URL;
                    baseurl = baseurl.split("gestionechiamate");
                    baseurl = baseurl[0] + "gestionechiamate.ws";
                    DataService.initialize(baseurl,
                            function () {
                                DataService.getData("getTmkLogDesc", {}, "getTmkLogDescPerformed", {},
                                        function (t) {
                                            $scope.opPar.logGeneralDesc = t[1].response.data.tmklog;
                                            $scope.opPar.userList = t[1].response.data.users;
                                            that.configura();
                                        },
                                        function (t) {
                                            alert("Attenzione, non é stato possibile configurare il sistema di log.");
                                            that.destroyPage();
                                        }
                                );

                            },
                            function () {
                                that.destroyPage();
                            }
                    );

                    this.nuovaScheda = function (ticket) {
                        ticket = DataService.find(ticket);
                        var data = ticket[1].response.data[0];
                        $scope.dataPar = {
                            azienda: {},
                            vt: {},
                            numeri: [],
                            show: {},
                            edit: "",
                            kind: {main: "", reserve: "", ric: ""}
                        };
                        $scope.dataPar.azienda = jQuery.extend({}, data);
                        if (!$scope.dataPar.azienda.PartitaIva)
                            return;
                        if ($scope.dataPar.azienda.now) {
                            var dbTime = new Date($scope.dataPar.azienda.now);
                            var clientTime = new Date();
                            var diff = parseInt((dbTime.getTime() - clientTime.getTime()) / 1000 / 3600);
                            $scope.opPar.clientDBTimeDiff = diff;
                        }
                        this.organizeSheetData();
                        $scope.lPiva = $scope.dataPar.azienda.PartitaIva;
                        $scope.lTmk = $scope.opPar.tmkName;
                        $scope.lPipe = $scope.dataPar.azienda.pipe;
                        $scope.lCampaignid = $scope.opPar.camp;
                        var logged = that.logger[1].putLog({
                            piva: $scope.lPiva,
                            what: ['pr', "Mkt", "campaign", "sheetManagement"],
                            parameters: {
                                tmk: $scope.lTmk,
                                pipe: $scope.lPipe,
                                campaignid: $scope.lCampaignid
                            },
                            esito: true,
                            parent: 0
                        });
                        var idRichiesta = logged[0]; // id della richiesta di log per identificarla tra le altre;
                        var idLog = 0;
                        //per catturare la risposta alla richiesta di log:
                        $rootScope.$on("putLogPerformed", function (event, mass) {
                            if (mass.ticket === idRichiesta) {
                                var t = DataService.find(mass.ticket);
                                idLog = t[1].response.data.inserted_id;
                                $scope.dataPar.log = {};
                                $scope.lPrId = idLog;
                            }
                        });
                        that.reloadLog();
                        setWFStatus($scope.workFlow, "preCall");
                        if ($scope.opPar.sheetLoadTimer.timer.then)
                            $interval.cancel($scope.opPar.sheetLoadTimer.timer);
                        $scope.opPar.sheetLoadTimer = {start: null, now: null, timer: null};
                        $timeout(function () {
                            if (!that.checkNumeriDisponibili()) {
                                setWFStatus($scope.workFlow, "waitingForNegotiationOutcome");
                            }
                        }, 2000);
//            setWFStatus($scope.workFlow, "preCall"); //preCall; //waitingForNegotiationOutcome
                    };
                    $scope.regioni = $scope.staticPar.regioni;
                    $scope.province = $scope.staticPar.province;

                    this.adjustTelefoni = Common.splitNoRep(",");
//        this.adjustTelefoni = function (telefoni) {
//            var numeri = [];
//            if (telefoni)
//                telefoni = telefoni.split(",");
//            else
//                telefoni = [];
//            for (var i in telefoni) {
//                if (numeri.indexOf(telefoni[i]) === -1)
//                    numeri.push(telefoni[i]);
//            }
//            return numeri;
//        }
                    this.adjustKind = function (pipe, working, force, donotcallbefore) {
                        var kind = {main: "", reserve: "", ric: ""};
                        switch (pipe) {
                            case '1':
                                kind.main = "Chiamata";
                                break;
                            case '6':
                                kind.main = "Richiamo personale";
                                break;
                            case '4':
                                kind.main = "Rifisso";
                                break;
                            default:
                                kind.main = "Chiamata";
                                break;
                        }
                        switch (working) {
                            case '0':
                                kind.main += "";
                                break;
                            case '1':
                                kind.main += " (scheda precedentemente in lavorazione)";
                                break;
                        }
                        if (force && force != '0') {
                            kind.reserve += "imposto da supervisione"
                        }
                        if (donotcallbefore && donotcallbefore != '' && donotcallbefore != '0'
                                && donotcallbefore != '0000-00-00') {
                            var dncb = donotcallbefore;
                            var hour = dncb.match(/\s\d\d:/);
                            if (hour.length) {
                                var hourbefore = hour[0];
                                var hourbeforenum = parseInt(hourbefore.replace(" ", "").replace(":", ""));
                                var hourafternum = hourbeforenum - parseInt($scope.opPar.clientDBTimeDiff);
                                var hourafter = " " + hourafternum + ":";
                                dncb = dncb.replace(hourbefore, hourafter);
                            }
                            kind.ric = "(ric. dopo " + dncb + ")";
                        }
                        return kind;
                    }


                    this.organizeSheetData = function () {
                        if ($scope.dataPar.azienda.VT && $scope.dataPar.azienda.VT.contatti && $scope.dataPar.azienda.VT.contatti.length) {
                            $scope.dataPar.azienda.VT.contatti = JSON.parse($scope.dataPar.azienda.VT.contatti);
                        }

                        $scope.dataPar.VT = jQuery.extend({}, $scope.dataPar.azienda.VT);
                        delete($scope.dataPar.azienda.VT);

                        $scope.dataPar.numeri = this.adjustTelefoni($scope.dataPar.azienda.Telefoni);
                        delete($scope.dataPar.azienda.Telefoni);

                        $scope.dataPar.amend = jQuery.extend({}, $scope.dataPar.azienda.amend);
                        delete($scope.dataPar.azienda.amend);

//            $scope.dataPar.source = ($scope.dataPar.azienda.source === "virgin" ? 0 : 1);
                        $scope.dataPar.kind = this.adjustKind($scope.dataPar.azienda.pipe, $scope.dataPar.azienda.working, $scope.dataPar.azienda.force, $scope.dataPar.azienda.donotcallbefore)

                        $scope.dataPar.editable = $scope.staticPar.editable;

                        $scope.dataPar.show.azienda = {
                            Punteggio: $scope.dataPar.azienda.Punteggio,
                            AttivitaAteco: $scope.dataPar.azienda.AttivitaAteco, //potrebbe cambiare
                            Cap: $scope.dataPar.azienda.Cap,
                            ClasseDipendenti: $scope.dataPar.azienda.ClasseDipendenti,
                            DataCostituzione: $scope.dataPar.azienda.DataCostituzione,
                            Email: ($scope.dataPar.VT ? $scope.dataPar.VT.Email : 1),
                            FasciaFatturato: $scope.dataPar.azienda.FasciaFatturato,
                            FormaGiuridica: $scope.dataPar.azienda.FormaGiuridica,
                            Indirizzo: $scope.dataPar.azienda.Indirizzo,
                            Localita: $scope.dataPar.azienda.Localita,
                            PartitaIva: $scope.dataPar.azienda.PartitaIva,
                            Provincia: $scope.dataPar.azienda.Provincia,
                            RagioneSociale: $scope.dataPar.azienda.RagioneSociale,
                            Regione: $scope.dataPar.azienda.Regione,
                            SedePrincipale: $scope.dataPar.azienda.SedePrincipale,
                            Sito: ($scope.dataPar.VT ? $scope.dataPar.VT.Sito : $scope.dataPar.azienda.Sito)
                        }
                        $scope.dataPar.show.numeri = [];
                        for (var i in $scope.dataPar.numeri) {
                            $scope.dataPar.show.numeri.push({
                                num: $scope.dataPar.numeri[i],
                                status: 0, // 0: da chiamare, 1: in attesa di esito, 2: rimosso,
                                esito: ""
                            });
                        }
                        $scope.dataPar.show.contatti = [];
                        for (var i in $scope.dataPar.VT.contatti) {
                            $scope.dataPar.show.contatti.push($scope.dataPar.VT.contatti[i]);
                        }
                        if ($scope.dataPar.show.contatti.length) {
                            $scope.dataPar.show.contatti[0].active = true;
                        }
                        $scope.dataPar.show.amend = {
                            numeri: {},
                            azienda: {}
                        }
                        for (var i in $scope.dataPar.amend) {
                            var index = "";
                            for (j in $scope.dataPar.amend[i]) {
                                if (j === "Cancella" || j === "PartitaIva" || j === "id" || j === "user" || j === "Timestamp")
                                    continue;
                                if ($scope.dataPar.amend[i].hasOwnProperty(j) && $scope.dataPar.amend[i][j] !== "") {
                                    index = j;
                                    break;
                                }
                            }
                            if (index !== "") {
                                if ($scope.dataPar.amend[i].Cancella === "1") {
                                    if (index === "Telefono") {
                                        var indexTel = -1;
                                        for (var iTel in $scope.dataPar.show.numeri) {
                                            if ($scope.dataPar.show.numeri[iTel].num == $scope.dataPar.amend[i].Telefono) {
                                                indexTel = iTel;
                                                break;
                                            }
                                        }
                                        if (indexTel === -1) {
                                            continue;
                                        }
                                        else {
                                            $scope.dataPar.show.numeri[indexTel].status = 3;
                                            $scope.dataPar.show.amend.numeri[$scope.dataPar.amend[i].Telefono] = true;
                                        }
                                    }
                                    else {
                                        if ($scope.dataPar.show.azienda.hasOwnProperty(index)) {
                                            $scope.dataPar.show.azienda[index] = "";
                                        }
                                    }
                                }
                                else {
                                    if (index === "Telefono") {
                                        var indexTel = -1;
                                        for (var iTel in $scope.dataPar.show.numeri) {
                                            if ($scope.dataPar.show.numeri[iTel].num == $scope.dataPar.amend[i].Telefono) {
                                                indexTel = iTel;
                                                break;
                                            }
                                        }
                                        if (indexTel === -1) {
                                            $scope.dataPar.show.numeri.push({
                                                num: $scope.dataPar.amend[i].Telefono,
                                                status: 0, // 0: da chiamare, 1: in attesa di esito, 2: rimosso,
                                                esito: ""
                                            });
                                            $scope.dataPar.show.amend.numeri[$scope.dataPar.amend[i].Telefono] = true;
                                        }
                                        else {
                                            continue;
                                        }
                                    }
                                    else {
                                        if ($scope.dataPar.show.azienda.hasOwnProperty(index)) {
                                            $scope.dataPar.show.azienda[index] = $scope.dataPar.amend[i][index];
                                            $scope.dataPar.show.amend.azienda[index] = true;
                                        }
                                    }
                                }
                            }
                            else {
                                continue;
                            }

                        }
                        if ($scope.dataPar.azienda.worker != $scope.opPar.vend) {
                            for (var i in $scope.opPar.vendors) {
                                if ($scope.dataPar.azienda.worker == $scope.opPar.vendors[i].id) {
                                    alert("Attenzione: cambiato il venditore di default: " + $scope.opPar.vendors[i].vendorName);
                                    $scope.opPar.vend = $scope.opPar.vendors[i].id;
                                    $scope.opPar.vendorName = $scope.opPar.vendors[i].vendorName;
                                }
                            }
                        }
                    }




                    //chiamata <-- qui arrivo ogni volta che vado in pre call


                    this.chiama = function (numero) {
                        var index = -1;
                        for (var i in $scope.dataPar.show.numeri) {
                            if ($scope.dataPar.show.numeri[i].num == numero) {
                                index = i;
                                break;
                            }
                        }
                        if (index === -1) {
                            return;
                        }
                        if (document.URL.indexOf("tsnwtest") > -1) {
                            DataService.getData("chiamaNumero", {numero: numero, debug: "debug"}, "chiamaNumeroPerformed", {index: index, numero: numero},
                            function (t) {
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['ev', "Call", "outbound", "call"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid,
                                        numeroChiamato: t[1].message.messagePars.numero
                                    },
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                                $scope.dataPar.show.numeri[t[1].message.messagePars.index].status = 1;
                                setWFStatus($scope.workFlow, "calling");
                            },
                                    function (t) {
                                        alert("Attenzione: sembra che qualcosa sia andato storto nell'inoltro della chiamata. Potrebbe essere \n\
opportuno ricaricare la pagina");
                                    });
                        }
                        else {
                            DataService.getData("chiamaNumero", {numero: numero}, "chiamaNumeroPerformed", {index: index, numero: numero},
                            function (t) {
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['ev', "Call", "outbound", "call"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid,
                                        numeroChiamato: t[1].message.messagePars.numero
                                    },
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                                $scope.dataPar.show.numeri[t[1].message.messagePars.index].status = 1;
                                setWFStatus($scope.workFlow, "calling");
                            },
                                    function (t) {
                                        alert("Attenzione: sembra che qualcosa sia andato storto nell'inoltro della chiamata. Potrebbe essere \n\
opportuno ricaricare la pagina");
                                    });
                        }
                    };
                    this.esitoChiamata = function (numero) {
                        var index = -1;
                        for (var i in $scope.dataPar.show.numeri) {
                            if ($scope.dataPar.show.numeri[i].num == numero) {
                                index = i;
                                break;
                            }
                        }
                        if (index === -1) {
                            return;
                        }
                        var esito = $scope.dataPar.show.numeri[index].esito;
                        switch (esito) {
                            case '':
                            case ' ':
                                return;
                                break;
                            case 'Numero inesistente':
                                this.removeNumero(numero);
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['ev', "Call", "outcome", "invalidNum"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid,
                                        numeroChiamato: numero
                                    },
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                                if (!that.checkNumeriDisponibili()) {
                                    setWFStatus($scope.workFlow, "waitingForNegotiationOutcome");
                                }
                                else {
                                    setWFStatus($scope.workFlow, "preCall");
                                }
                                break;
                            case 'Numero errato':
                                this.removeNumero(numero);
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['ev', "Call", "outcome", "wrongNum"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid,
                                        numeroChiamato: numero
                                    },
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                                if (!that.checkNumeriDisponibili()) {
                                    setWFStatus($scope.workFlow, "waitingForNegotiationOutcome");
                                }
                                else {
                                    setWFStatus($scope.workFlow, "preCall");
                                }
                                break;
                            case 'Era occupato':
                                $scope.dataPar.show.numeri[index].status = 2;
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['ev', "Call", "outcome", "busyNum"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid,
                                        numeroChiamato: numero
                                    },
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                                if (!that.checkNumeriDisponibili()) {
                                    setWFStatus($scope.workFlow, "waitingForNegotiationOutcome");
                                }
                                else {
                                    setWFStatus($scope.workFlow, "preCall");
                                }
                                break;
                            case 'Non ha risposto':
                                $scope.dataPar.show.numeri[index].status = 2;
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['ev', "Call", "outcome", "noAnswer"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid,
                                        numeroChiamato: numero
                                    },
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                                if (!that.checkNumeriDisponibili()) {
                                    setWFStatus($scope.workFlow, "waitingForNegotiationOutcome");
                                }
                                else {
                                    setWFStatus($scope.workFlow, "preCall");
                                }
                                break;
                            case 'Richiamo questo numero':
                                $scope.dataPar.show.numeri[index].status = 0;
                                $scope.dataPar.show.numeri[index].esito = "";
                                setWFStatus($scope.workFlow, "preCall");
                                break;
                            case 'Chiamo un altro numero':
                                $scope.dataPar.show.numeri[index].status = 2;
                                setWFStatus($scope.workFlow, "preCall");
                                break;
                            case 'Ho parlato':
                                $scope.dataPar.show.numeri[index].status = 2;
                                setWFStatus($scope.workFlow, "waitingForNegotiationOutcome");
                                break;
                        }
                    };
                    /*
                     * Modifiche ai dati
                     */

                    this.checkNumeriDisponibili = function () {
                        for (var i in $scope.dataPar.show.numeri) {
                            if ($scope.dataPar.show.numeri[i].status == 0) {
                                return true;
                            }
                        }

                        var userCheck = confirm("Sembra che non ci siano numeri chiamabili. Vuoi inserire manualmente un nuovo numero? \n\
Attenzione: se si sceglie di annullare sarà possibile solo scartare la scheda.");
                        if (userCheck) {
                            that.mascheraNuovoNumero[1].open();
                            return true;
                        }
                        return false;
                    }

                    this.gestisciSchedaFarlocca = function () {
                        var flag = 0;
                        for (var i in $scope.dataPar.show.numeri) {

                            var esito = $scope.dataPar.show.numeri[i].esito;
                            switch (esito) {
                                case 'Era occupato':
                                    flag = Math.max(flag, 1);
                                    break;
                                case 'Non ha risposto':
                                    flag = Math.max(flag, 1);
                                    break;
                                case 'Chiamo un altro numero':
                                    flag = Math.max(flag, 1);
                                    break;
                                case 'Ho parlato':
                                    flag = Math.max(flag, 2);
                                    break;
                            }
                        }
                        return flag;
                    }


                    this.removeNumero = function (numero) { //rende non chiamabile un numero
                        var index = -1;
                        for (var i in $scope.dataPar.show.numeri) {
                            if ($scope.dataPar.show.numeri[i].num == numero) {
                                index = i;
                                break;
                            }
                        }
                        if (index === -1) {
                            return;
                        }
                        var newData = {
                            piva: $scope.dataPar.show.azienda.PartitaIva,
                            cancella: true,
                            user: $scope.opPar.tmk,
                            vm: {"Telefono": numero}
                        }
                        DataService.postData("amendPiva", newData, "amendPivaPerformed", newData);
                    }
                    this.addNumero = function (numero) { //aggiunge un numero
                        var index = -1;
                        for (var i in $scope.dataPar.show.numeri) {
                            if ($scope.dataPar.show.numeri[i].num == numero) {
                                index = i;
                                break;
                            }
                        }
                        if (index === -1) {
                            var newData = {
                                piva: $scope.dataPar.show.azienda.PartitaIva,
                                cancella: false,
                                user: $scope.opPar.tmk,
                                vm: {"Telefono": numero}
                            }
                            DataService.postData("amendPiva", newData, "amendPivaPerformed", newData);
                        }
                        else {
                            alert("Numero già presente");
                        }
                    }

                    $rootScope.$on("amendPivaPerformed", function (event, mass) {
                        console.log(mass);
                        var res = DataService.find(mass.ticket);
                        if (!res[1].result) {
                            alert("Qualcosa è andato storto, riprovare!");
                            return;
                        }
                        if (mass.vm.Telefono) {
                            //ho modificato qualcosa nei telefoni
                            if (!mass.cancella) { //ho aggiunto
                                $scope.dataPar.show.numeri.push({
                                    num: mass.vm.Telefono,
                                    status: 0, // 0: da chiamare, 1: in attesa di esito, 2: rimosso,
                                    esito: ""
                                });
                            }
                            else { // ho tolto
                                var index = -1;
                                for (var i in $scope.dataPar.show.numeri) {
                                    if ($scope.dataPar.show.numeri[i].num == mass.vm.Telefono) {
                                        index = i;
                                        break;
                                    }
                                }
                                if (index === -1) {
                                    return;
                                }
                                $scope.dataPar.show.numeri[index].status = 3;
                            }
                        }
                    });
                    /*
                     * Esito trattativa
                     */
                    this.disableBtnAppuntamento = function () {
                        if (that.creazioneAppuntamento.formData.contact === "") {
                            return true;
                        }
                        if (that.creazioneAppuntamento.formData.date === "") {
                            return true;
                        }
                        if (that.creazioneAppuntamento.formData.time === "") {
                            return true;
                        }
                        if (that.creazioneAppuntamento.formData.note === "") {
                            return true;
                        }
                        return false;
                    };
                    this.impostaCreazioneAppuntamento = function () {

                        $scope.dataPar.edit = "";
                        setWFStatus($scope.workFlow, "waitingForAppOutcome");
                    };
                    this.creaAppuntamento = function () {
                        var phone = "";
                        for (var i in $scope.dataPar.show.numeri) {
                            if ($scope.dataPar.show.numeri[i].status == 2 && $scope.dataPar.show.numeri[i].esito == "Ho parlato") {
                                phone = $scope.dataPar.show.numeri[i].num;
                                break;
                            }
                        }
                        //scommentare in produzione
                        if (phone === "") {
                            alert("Attenzione si è verificato un errore, contattare un amministratore");
                            return;
                        }
//            if (phone === "") {
//                phone = "99999"
//            }
                        //verificare formData <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

                        var logged = that.logger[1].putLog({
                            piva: $scope.lPiva,
                            what: ['op', "Mkt", "esitoTmk", "appCreated"],
                            parameters: {
                                tmk: $scope.lTmk,
                                pipe: $scope.lPipe,
                                campaignid: $scope.lCampaignid,
                                numeroChiamato: phone
                            },
                            comment: that.creazioneAppuntamento.formData.note,
                            esito: true,
                            parent: $scope.lPrId
                        });
                        that.mascheraCreazioneAppuntamento[1].open();
                        that.creaAccount(phone);
//            that.creaActivity();
                    };

                    this.ricreaAppuntamento = function () {
                        that.creazioneAppuntamento.result = {account: 0, potential: 0, activity: 0, contacts: 0};
                        that.creazioneAppuntamento.pending = {account: false, potential: false, activity: false, contacts: false};
                        that.creazioneAppuntamento.account = {};
                        that.creazioneAppuntamento.potential = {};
                        that.creazioneAppuntamento.contacts = [];
                        that.creazioneAppuntamento.activity = {};
                        that.creaAppuntamento();
                    }

                    this.creaAccount = function (phone) {

                        var FASCIAFATTURATO = {
                            '1': '0 - 0,25 (Ml. euro)',
                            '2': '0,25 - 0,5 (Ml. euro)',
                            '3': '0,5 - 1,5 (Ml. euro)',
                            '4': '1,5 - 2,5 (Ml. euro)',
                            '5': '2,5 - 5 (Ml. euro)',
                            '6': '5 - 13 (Ml. euro)',
                            '7': '13 - 25 (Ml. euro)',
                            '8': '25 - 50 (Ml. euro)',
                            '9': '50 - 100 (Ml. euro)',
                            '10': '> 100 (Ml. euro)',
                            '11': 'NON ASSEGNATA'
                        };
                        var CLASSEDIPENDENTI = {
                            '1': '1',
                            '2': '2 - 5',
                            '3': '6 - 9',
                            '4': '10 - 19',
                            '5': '20 - 49',
                            '6': '50 - 249',
                            '7': '250 - 499',
                            '8': '500 - 1000',
                            '9': 'MAGGIORE DI 1000',
                            '10': 'NON ASSEGNATA'
                        };

                        var vm = jQuery.extend({
                            accounttype: "New Business PMI",
                            assigned_user_id: "19x1",
                            Venditore: $scope.opPar.vendorName,
                            Telemarketing: $scope.opPar.tmkName,
                            Telefono: phone,
                            cf_703: FASCIAFATTURATO[$scope.dataPar.show.azienda.FasciaFatturato + ""],
                            cf_704: CLASSEDIPENDENTI[$scope.dataPar.show.azienda.ClasseDipendenti + ""],
                            cf_705: $scope.dataPar.show.azienda.FormaGiuridica,
                        },
                                $scope.dataPar.show.azienda);
                        var piva = $scope.dataPar.azienda.PartitaIva;
                        that.creazioneAppuntamento.pending.account = true;
                        DataService.postData("creaVTAccount", {vm: vm, piva: piva}, "creaVTAccountPerformed", {},
                                function (t) {
                                    var res = t[1].response.data;
                                    that.creazioneAppuntamento.pending.account = false;
                                    that.creazioneAppuntamento.creaAccount = true;
                                    that.creazioneAppuntamento.result.account = 1;
                                    that.creazioneAppuntamento.account = jQuery.extend({}, res);
                                    that.creaContact();
                                },
                                function (t) {
                                    var res = t[1].errorData;
                                    if (res && res.error && res.error_desc) {
                                        console.log(res.error_desc);
                                    }
                                    that.creazioneAppuntamento.creaAccount = false;
                                    that.creazioneAppuntamento.pending.account = false;
                                    that.creazioneAppuntamento.result.account = -1;
                                    that.creazioneAppuntamento.account = {};
                                    return;
                                }
                        );
                    };

                    this.creaContact = function (conIndex) {
                        if (typeof (conIndex) === "undefined") {
                            conIndex = 0;
                        }
                        if (conIndex >= $scope.dataPar.show.contatti.length) {
                            that.creazioneAppuntamento.result.contacts = 1;
                            that.creazioneAppuntamento.pending.contacts = false;
                            that.creaPotential();
                            return;
                        }

                        var vm = {
                            lastname: $scope.dataPar.show.contatti[conIndex].cognome,
                            firstname: $scope.dataPar.show.contatti[conIndex].nome,
                            title: $scope.dataPar.show.contatti[conIndex].ruolo,
                            email: $scope.dataPar.show.contatti[conIndex].email,
                            mobile: $scope.dataPar.show.contatti[conIndex].cel,
                            fax: $scope.dataPar.show.contatti[conIndex].fax,
                            phone: $scope.dataPar.show.contatti[conIndex].phone,
                            mailingstreet: $scope.dataPar.show.azienda.Indirizzo,
                            account_id: that.creazioneAppuntamento.account.id
                        };
                        that.creazioneAppuntamento.pending.contacts = true;
                        DataService.postData("creaVTContact", {vm: vm}, "creaVTContactPerformed", {conIndex: conIndex},
                        function (t) {
                            var res = t[1].response.data;
                            var cIndex = t[1].message.messagePars.conIndex;
                            that.creazioneAppuntamento.creaContact = true;

                            that.creazioneAppuntamento.contacts.push(jQuery.extend({}, res));
                            if ($scope.dataPar.show.contatti[cIndex] === that.creazioneAppuntamento.formData.contact) {
                                that.creazioneAppuntamento.contattoPerActivity = res.id;
                                console.log("that.creazioneAppuntamento.contattoPerActivity");
                                console.log(that.creazioneAppuntamento.contattoPerActivity);
                            }
                            that.creaContact(Number(cIndex) + 1);
                        },
                                function (t) {
                                    var res = t[1].errorData;
                                    that.creazioneAppuntamento.creaContacts = false;
                                    that.creazioneAppuntamento.pending.contacts = false;
                                    that.creazioneAppuntamento.result.contacts = -1;
                                    that.creazioneAppuntamento.contacts.push({});
                                    return;
                                }
                        );
                    };
                    this.creaPotential = function (overwrite) {
                        var pad10 = Common.pad10;
                        var sdate = that.creazioneAppuntamento.formData.date;
                        if (sdate.getTime && !isNaN(sdate.getTime())) {
                            sdate = sdate.getFullYear() + "-" + pad10(sdate.getMonth() + 1) + "-" + pad10(sdate.getDate());
                        }
                        var vm = {
                            OpportunityType: "New Business PMI",
                            assigned_user_id: "19x1",
                            Date: sdate,
                            Venditore: $scope.opPar.vendorName,
                            Telemarketing: $scope.opPar.tmkName,
                            Potentialname: "Analisi",
                            Ammontare: "600",
                            NextStep: "Visita venditore",
                            LeadSource: "DB Marketing",
                            SalesStage: "Qualification",
                            Probability: "10.000",
                            Description: that.creazioneAppuntamento.formData.note,
                            AmmontarePesato: "60.00",
                            RelatedTo: that.creazioneAppuntamento.account.id
                        };
                        that.creazioneAppuntamento.pending.potential = true;
                        if (typeof (overwrite) === "undefined")
                            overwrite = -1;
                        DataService.postData("creaVTPotential", {vm: vm, overwrite: overwrite}, "creaVTPotentialPerformed", {},
                                function (t) {
                                    var res = t[1].response.data;
                                    that.creazioneAppuntamento.result.potential = 1;
                                    that.creazioneAppuntamento.creaPotential = true;
                                    that.creazioneAppuntamento.pending.potential = false;
                                    that.creazioneAppuntamento.potential = jQuery.extend({}, res);
                                    that.creaActivity();
                                },
                                function (t) {
                                    var res = t[1].errorData;
                                    if (res && res.error && res.error_desc) {
                                        console.log(res.error_desc);
                                    }
                                    if (t[1].status == 409) {
                                        console.log(res);
                                        var stato = (res["pot"]["sales_stage"] == "Qualification") ? "Attesa conferma" : "Confermato";
                                        var data = new Date(res["pot"]["closingdate"]);

                                        var actDet = "";
                                        if (res["act"]) {
                                            actDet += (res["act"]["eventstatus"] == "Planned" ? "Appuntamento previsto @ \n\r" : "Appuntamento tenuto @ \n\r");
                                            actDet += res["act"]["time_start"] + " --> " + res["act"]["time_end"] + "\n\r";
                                        }
                                        else {
                                            actDet += "Non sono disponibili informazioni sull'orario o sullo stato dell'appuntamento.\n\r";
                                        }

                                        var overwrite = confirm("Attenzione: è stato rilevato un altro appuntamento aperto per questa azienda. I dettagli dell'appuntamento sono:\n\r"
                                                + "Stato appuntamento: " + stato + "\n\r"
                                                + "Data appuntamento: " + data + "\n\r"
                                                + "Venditore: " + res["pot"]["cf_647"] + "\n\r"
                                                + "Telemarketing: " + res["pot"]["cf_646"] + "\n\r"
                                                + actDet
                                                + "Si vuole sovrascrivere questo appuntamento (in caso contrario verrà creato un nuovo appuntamento)?"
                                                );
                                        if (overwrite) {
                                            that.creaPotential(1);
                                        }
                                        else {
                                            that.creaPotential(0);
                                        }
//                            that.creazioneAppuntamento.result.potential = -1;
//                            that.creazioneAppuntamento.creaPotential = false;
//                            that.creazioneAppuntamento.pending.potential = false;
//                            that.creazioneAppuntamento.potential = {};
                                        return;
                                    }
                                    else {
                                        that.creazioneAppuntamento.result.potential = -1;
                                        that.creazioneAppuntamento.creaPotential = false;
                                        that.creazioneAppuntamento.pending.potential = false;
                                        that.creazioneAppuntamento.potential = {};
                                        return;
                                    }
                                }
                        );
                    };
                    this.creaActivity = function () {
//            console.log(that.creazioneAppuntamento.formData.time);
//            console.log(typeof(that.creazioneAppuntamento.formData.time));
//            return;
                        var pad10 = Common.pad10;
                        var sdate = that.creazioneAppuntamento.formData.date;
                        if (sdate.getTime && !isNaN(sdate.getTime())) {
                            sdate = sdate.getFullYear() + "-" + pad10(sdate.getMonth() + 1) + "-" + pad10(sdate.getDate());
                        }
                        
                        try {
                            var time = that.creazioneAppuntamento.formData.time;
                            if (time.getTime && !isNaN(time.getTime())) { // se è una data
                                var timestart = pad10(Number(time.getHours())) + ":" + pad10(time.getMinutes());
                                var timend = pad10(Number(time.getHours()) + 1) + ":" + pad10(time.getMinutes());
                            }
                            else if (angular.isString(time)) {
                                var timestart = time;
                                time = time.split(":");
                                var timend = (Number(time[0]) + 1) + ":" + time[1];
                            }
                            else {
                                alert("Qualcosa é andato storto con la data.");
                            }
                            
                        }
                        catch (e) {
                            console.log(time);
                            console.log(e);
                        }
                        var vm = {
                            activitytype: "Meeting",
                            subject: ($scope.opPar.vendorName + " - " + $scope.dataPar.show.azienda.RagioneSociale),
                            eventstatus: "Planned",
                            date_start: sdate,//that.creazioneAppuntamento.formData.date,
                            due_date: sdate,//that.creazioneAppuntamento.formData.date,
                            time_start: timestart,
                            time_end: timend,
                            duration_hours: "1",
                            location: $scope.dataPar.show.azienda.Indirizzo,
                            description: that.creazioneAppuntamento.formData.note,
                            cf_650: $scope.opPar.vendorName,
                            cf_649: $scope.opPar.tmkName,
                            cf_651: "New Business PMI",
                            cf_652: "",
                            cf_702: that.creazioneAppuntamento.potential.id.replace("13x", ""),
                            parent_id: that.creazioneAppuntamento.account.id,
                            contact_id: that.creazioneAppuntamento.contattoPerActivity
                        };
                        that.creazioneAppuntamento.pending.activity = true;
                        DataService.postData("creaVTActivity", {vm: vm}, "creaVTActivityPerformed", {},
                                function (t) {
                                    var res = t[1].response.data;
                                    that.creazioneAppuntamento.result.activity = 1;
                                    that.creazioneAppuntamento.creaActivity = true;
                                    that.creazioneAppuntamento.pending.activity = false;
                                    DataService.getData("creatoAppuntamento", {piva: $scope.dataPar.azienda.PartitaIva}, "creatoAppuntamentoPerformed", {},
                                            function (t) {
                                                that.creazioneAppuntamento.creatoAppuntamento = 1;
                                            },
                                            function (t) {
                                                that.creazioneAppuntamento.creatoAppuntamento = -1;
                                            }
                                    );
                                    that.creazioneAppuntamento.activity = jQuery.extend({}, res);
                                },
                                function (t) {
                                    var res = t[1].errorData;
                                    if (res && res.error && res.error_desc) {
                                        console.log(res.error_desc);
                                    }
                                    that.creazioneAppuntamento.result.activity = -1;
                                    that.creazioneAppuntamento.creaActivity = false;
                                    that.creazioneAppuntamento.pending.activity = false;
                                    that.creazioneAppuntamento.activity = {};
                                    return;
                                }
                        );
                    };
//        $rootScope.$on("creatoAppuntamentoPerformed", function (event, mass) {
//            var ticket = DataService.find(mass.ticket);
//            if (ticket[1].response.data.trim() === "true") {
//                that.creazioneAppuntamento.creatoAppuntamento = 1;
//            }
//            else {
//                that.creazioneAppuntamento.creatoAppuntamento = -1;
//            }
//        });
                    TheMask.getMask(that.mascheraCreazioneAppuntamento[0]).setBtnEnableCondition(0, function () {
                        return (that.creazioneAppuntamento.creatoAppuntamento == 1);
                    });

                    TheMask.getMask(that.mascheraCreazioneAppuntamento[0]).setBtnCallback(0, function () {
                        if (that.creazioneAppuntamento.creatoAppuntamento == 1)
                        {
                            console.log("qui");
                            that.creazioneAppuntamento.finito = true;
                            that.emptyCreazioneAppuntamento();
                            that.mascheraCreazioneAppuntamento[1].close();
                            setWFStatus($scope.workFlow, "waitingForConf");
                            that.configura();
                            //azzera e passa avanti
                            return;
                        }
                        else {
                            return;
                        }
                    });
                    this.disableBtnAppuntamento = function () {
                        if (that.creazioneAppuntamento.formData.contact === "") {
                            return true;
                        }
                        if (that.creazioneAppuntamento.formData.date === "") {
                            return true;
                        }
                        if (that.creazioneAppuntamento.formData.time === "") {
                            return true;
                        }
                        if (that.creazioneAppuntamento.formData.note === "") {
                            return true;
                        }
                        return false;
                    };
                    this.disableBtnRichiamo = function () {
                        if (that.impostazioneRichiamo.formData.contact === "") {
                            return true;
                        }
                        if (that.impostazioneRichiamo.formData.date === "") {
                            return true;
                        }
                        if (that.impostazioneRichiamo.formData.time === "") {
                            return true;
                        }
                        if (that.impostazioneRichiamo.formData.note === "") {
                            return true;
                        }
                        return false;
                    };
                    this.impostaRichiamo = function () {
                        $scope.dataPar.edit = "";

                        setWFStatus($scope.workFlow, "waitingForRecallOutcome");
                    };
                    this.ricreaRichiamo = function () {
                        that.impostazioneRichiamo.result = 0;
                        that.impostazioneRichiamo.pending = true;
                        that.creaRichiamo();
                    }
                    this.creaRichiamo = function () {
                        //verificare formData <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
                        that.mascheraImpostazioneRichiamo[1].open();
                        that.impostazioneRichiamo.pending = true;
                        var numLog = 0;
                        for (var i in $scope.dataPar.show.numeri) {
                            if ($scope.dataPar.show.numeri[i].esito === 'Ho parlato') {
                                numLog = $scope.dataPar.show.numeri[i].num;
                                break;
                            }
                        }

                        var logged = that.logger[1].putLog({
                            piva: $scope.lPiva,
                            what: ['op', "Mkt", "esitoTmk", "recallReq"],
                            parameters: {
                                tmk: $scope.lTmk,
                                pipe: $scope.lPipe,
                                campaignid: $scope.lCampaignid,
                                numeroChiamato: numLog,
                                timestampRichiamo: that.impostazioneRichiamo.formData.date + " " + that.impostazioneRichiamo.formData.time,
                                personale: (that.impostazioneRichiamo.formData.self ? "si" : "no")
                            },
                            comment: that.impostazioneRichiamo.formData.note,
                            esito: true,
                            parent: $scope.lPrId
                        });
                        var richiamoDate = new Date(that.impostazioneRichiamo.formData.date + " " + that.impostazioneRichiamo.formData.time);

                        DataService.postData("creaRichiamo", {
                            piva: $scope.dataPar.azienda.PartitaIva,
                            donotcallbefore: richiamoDate.getTime(),
                            note: that.impostazioneRichiamo.formData.note,
                            self: (that.impostazioneRichiamo.formData.self ? "true" : "false")
                        }, "creaRichiamoPerformed", {},
                                function (t) {
                                    that.impostazioneRichiamo.result = 1;
                                    that.impostazioneRichiamo.pending = false;
                                },
                                function (t) {
                                    that.impostazioneRichiamo.result = -1;
                                    that.impostazioneRichiamo.pending = false;
                                }
                        );
                    };
                    this.impostaRichiamoRapido = function (type) {
                        $scope.dataPar.edit = "";
                        setWFStatus($scope.workFlow, "waitingForRecallOutcome");
                        that.mascheraImpostazioneRichiamo[1].open();
                        that.impostazioneRichiamo.pending = true;
                        if (type === 1) {
                            var logged = that.logger[1].putLog({
                                piva: $scope.lPiva,
                                what: ['op', "Mkt", "pipeline", "snoozeForBusy"],
                                parameters: {
                                    tmk: $scope.lTmk,
                                    pipe: $scope.lPipe,
                                    campaignid: $scope.lCampaignid
                                },
                                esito: true,
                                parent: $scope.lPrId
                            });
                            var date = new Date();
                            date.setTime(date.getTime() + (1000 * 60 * 30));
                            DataService.postData("creaRichiamoRapido", {
                                piva: $scope.dataPar.azienda.PartitaIva,
                                donotcallbefore: date.getTime()
                            }, "creaRichiamoPerformed", {},
                                    function (t) {
                                        that.impostazioneRichiamo.result = 1;
                                        that.impostazioneRichiamo.pending = false;
                                    },
                                    function (t) {
                                        that.impostazioneRichiamo.result = -1;
                                        that.impostazioneRichiamo.pending = false;
                                    }
                            );
                        }
                        else if (type === 2) {
                            var logged = that.logger[1].putLog({
                                piva: $scope.lPiva,
                                what: ['op', "Mkt", "pipeline", "snooze"],
                                parameters: {
                                    tmk: $scope.lTmk,
                                    pipe: $scope.lPipe,
                                    campaignid: $scope.lCampaignid
                                },
                                esito: true,
                                parent: $scope.lPrId
                            });
                            var date = new Date();
                            date.setTime(date.getTime() + (1000 * 60 * 240));
                            DataService.postData("creaRichiamoRapido", {
                                piva: $scope.dataPar.azienda.PartitaIva,
                                donotcallbefore: date.getTime()
                            }, "creaRichiamoPerformed", {},
                                    function (t) {
                                        that.impostazioneRichiamo.result = 1;
                                        that.impostazioneRichiamo.pending = false;
                                    },
                                    function (t) {
                                        that.impostazioneRichiamo.result = -1;
                                        that.impostazioneRichiamo.pending = false;
                                    }
                            );
                        }
                    };

                    TheMask.getMask(that.mascheraImpostazioneRichiamo[0]).setBtnEnableCondition(0, function () {
                        return (that.impostazioneRichiamo.result === 1);
                    });
                    TheMask.getMask(that.mascheraImpostazioneRichiamo[0]).setBtnCallback(0, function () {
                        if (that.impostazioneRichiamo.result === 1) {
                            that.impostazioneRichiamo.finito = true;
                            that.emptyImpostazioneRichiamo();
                            that.mascheraImpostazioneRichiamo[1].close();
                            setWFStatus($scope.workFlow, "waitingForConf");
                            that.configura();
                            //azzera e passa avanti
                            return;
                        }
                        else {
                            return;
                        }
                    });

                    this.chiudiScheda = function (esito) {
                        $scope.dataPar.edit = "";
                        that.emptyScartoScheda();
                        that.scartoScheda.formData.esito = Number(esito);
                        $scope.dataPar.chiudiScheda = {};
                        $scope.dataPar.chiudiScheda.esito = Number(esito);
                        that.mascheraScartaScheda[1].open();
                    };

                    TheMask.getMask(that.mascheraScartaScheda[0]).setBtnEnableCondition(0, function () {
                        return (that.scartoScheda.formData.dettagli !== "");
                    });
                    TheMask.getMask(that.mascheraScartaScheda[0]).setBtnCallback(1, function () {
                        that.mascheraScartaScheda[1].close();
                        that.emptyScartoScheda();
                        return;
                    }
                    );
                    TheMask.getMask(that.mascheraScartaScheda[0]).setBtnCallback(0, function () {
                        if (that.scartoScheda.formData.dettagli !== "") {
                            var numLog = 0;
                            for (var i in $scope.dataPar.show.numeri) {
                                if ($scope.dataPar.show.numeri[i].esito === 'Ho parlato') {
                                    numLog = $scope.dataPar.show.numeri[i].num;
                                    break;
                                }
                            }
                            if ($scope.dataPar.chiudiScheda.esito) {

                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['op', "Mkt", "esitoTmk", "stopAtFilter"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid,
                                        numeroChiamato: numLog
                                    },
                                    comment: that.scartoScheda.formData.dettagli,
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                            }
                            else if ($scope.dataPar.chiudiScheda.esito) {
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['op', "Mkt", "esitoTmk", "noInterest"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid,
                                        numeroChiamato: numLog
                                    },
                                    comment: that.scartoScheda.formData.dettagli,
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                            }
                            else if ($scope.dataPar.chiudiScheda.esito) {
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['op', "Mkt", "esitoTmk", "pivaLock"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid
                                    },
                                    comment: that.scartoScheda.formData.dettagli,
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                            }
                            DataService.postData("scartoScheda", {
                                piva: $scope.dataPar.azienda.PartitaIva,
                                esito: (that.scartoScheda.formData.esito ? that.scartoScheda.formData.esito : $scope.dataPar.chiudiScheda.esito),
                                dettagli: that.scartoScheda.formData.dettagli
                            }, "scartoSchedaPerformed", {esito: that.scartoScheda.formData.esito, dettagli: that.scartoScheda.formData.dettagli},
                            function (t) {
                                var logged = that.logger[1].putLog({
                                    piva: $scope.lPiva,
                                    what: ['op', "Mkt", "aziendeAll", "pivaLockTMK"],
                                    parameters: {
                                        tmk: $scope.lTmk,
                                        pipe: $scope.lPipe,
                                        campaignid: $scope.lCampaignid
                                    },
                                    esito: true,
                                    parent: $scope.lPrId
                                });
                                that.mascheraScartaScheda[1].close();
                                that.emptyScartoScheda();
                                setWFStatus($scope.workFlow, "waitingForConf");
                                that.configura();
                            },
                                    function (t) {
                                        alert("Attenzione, qualcosa é andato storto. Riprovare");
                                    }
                            );
                            //azzera e passa avanti
                        }
                        else {
                            return;
                        }
                    });


                    this.cambiaEsito = function () {
                        $scope.dataPar.edit = "";
                        setWFStatus($scope.workFlow, "waitingForNegotiationOutcome");
                        that.emptyCreazioneAppuntamento();
                    }

                    /*
                     * Navigazione
                     */
                    this.apriCalendario = function () {
                        this.sinottico[1].open();
                    };
                    this.cancellaContatto = function (index) {
                        if ($scope.dataPar.show.contatti[index])
                            $scope.dataPar.show.contatti.splice(index, 1);
                    };
                    this.cambiaContatto = function (index) {
                        for (var i in $scope.dataPar.show.contatti) {
                            $scope.dataPar.show.contatti[i].active = false;
                            if (i == index)
                                $scope.dataPar.show.contatti[i].active = true;
                        }
                    };
                    this.proceduraNuovoContatto = function () {
                        that.nuovoContatto = {nome: "", cognome: "", ruolo: "", phone: "", cel: "", fax: "", email: "",
                            errore: {
                                nome: false,
                                cognome: false,
                                ruolo: false,
                                phone: false,
                                cel: false,
                                fax: false,
                                email: false
                            }
                        };
                        that.mascheraNuovoContatto[1].open();
                    };

                    TheMask.getMask(that.mascheraNuovoContatto[0]).setBtnCallback(1, function () {
                        that.nuovoContatto = {};
                        that.mascheraNuovoContatto[1].close();
                    });
                    TheMask.getMask(that.mascheraNuovoContatto[0]).setBtnCallback(0, function () {
                        that.nuovoContatto.errore = {
                            nome: false,
                            cognome: false,
                            ruolo: false,
                            phone: false,
                            cel: false,
                            fax: false,
                            email: false
                        };
                        if (!alpha(that.nuovoContatto.nome)) {
                            that.nuovoContatto.errore.nome = true;
                        }
                        if (that.nuovoContatto.cognome === "" || !alpha(that.nuovoContatto.cognome)) {
                            that.nuovoContatto.errore.cognome = true;
                        }
                        if (that.nuovoContatto.ruolo === "" || !alpha(that.nuovoContatto.ruolo)) {
                            that.nuovoContatto.errore.ruolo = true;
                        }
                        if (that.nuovoContatto.phone === "" && that.nuovoContatto.cel === "") {
                            that.nuovoContatto.errore.phone = true;
                            that.nuovoContatto.errore.cel = true;
                        }
                        if (that.nuovoContatto.phone !== "" && isNaN(that.nuovoContatto.phone)) {
                            that.nuovoContatto.errore.phone = true;
                        }
                        if (that.nuovoContatto.cel !== "" && isNaN(that.nuovoContatto.cel)) {
                            that.nuovoContatto.errore.cel = true;
                        }
                        if (that.nuovoContatto.fax !== "" && isNaN(that.nuovoContatto.fax)) {
                            that.nuovoContatto.errore.fax = true;
                        }
                        var errore = false;
                        for (var k in that.nuovoContatto.errore) {
                            if (that.nuovoContatto.errore.hasOwnProperty(k)) {
                                errore = errore || that.nuovoContatto.errore[k];
                            }
                        }
                        if (errore) {
                            return;
                        }
                        else {
                            for (var i in $scope.dataPar.show.contatti) {
                                $scope.dataPar.show.contatti[i].active = false;
                            }
                            $scope.dataPar.show.contatti.push({
                                nome: that.nuovoContatto.nome,
                                cognome: that.nuovoContatto.cognome,
                                ruolo: that.nuovoContatto.ruolo,
                                phone: that.nuovoContatto.phone,
                                cel: that.nuovoContatto.cel,
                                fax: that.nuovoContatto.fax,
                                email: that.nuovoContatto.email,
                                active: true
                            });
                            that.nuovoContatto = {};
                            that.mascheraNuovoContatto[1].close();
                        }

                    });


                    this.proceduraNuovoNumero = function () {
                        that.nuovoNumero = {numero: "", errore: false};
                        that.mascheraNuovoNumero[1].open();
                    };

                    TheMask.getMask(this.mascheraNuovoNumero[0]).setBtnEnableCondition(0, function () {
                        return !(isNaN(that.nuovoNumero.numero) || that.nuovoNumero.numero === "");
                    });
                    TheMask.getMask(this.mascheraNuovoNumero[0]).setBtnCallback(0, function () {
                        if (isNaN(that.nuovoNumero.numero) || that.nuovoNumero.numero === "") {
                            that.nuovoNumero.errore = true;
                        }
                        else {
                            that.addNumero(that.nuovoNumero.numero);
                            that.nuovoNumero = {numero: "", errore: false};
                            that.mascheraNuovoNumero[1].close();
                        }
                    });
                    TheMask.getMask(this.mascheraNuovoNumero[0]).setBtnCallback(1, function () {
                        that.nuovoNumero = {numero: "", errore: false};
                        that.mascheraNuovoNumero[1].close();
                    });
                    this.editIs = function (numeroCampo) {
                        var nomeCampo = $scope.dataPar.editable[numeroCampo];
                        return ($scope.dataPar && $scope.dataPar.edit && $scope.dataPar.edit.nomeCampo === nomeCampo);
                    }

                    this.modificaCampoAzienda = function (numeroCampo) {
                        var nomeCampo = $scope.dataPar.editable[numeroCampo];
                        console.log("modifico " + numeroCampo + " " + nomeCampo);
                        if ($scope.dataPar.edit !== "") {
                            return;
                        }
                        if (!$scope.dataPar.show.azienda.hasOwnProperty(nomeCampo)) {
                            return;
                        }
                        $scope.dataPar.edit = {
                            cosa: "azienda",
                            nomeCampo: nomeCampo,
                            oldValue: $scope.dataPar.show.azienda[nomeCampo],
                            newValue: $scope.dataPar.show.azienda[nomeCampo]
                        };
                    }
                    this.annullaModificheAzienda = function () {
                        $scope.dataPar.edit = "";
                    }
                    this.salvaModificheAzienda = function () {
                        if ($scope.dataPar.edit === "") {
                            return;
                        }

                        if ($scope.dataPar.edit.newValue) {
                            if ($scope.dataPar.edit.newValue === $scope.dataPar.edit.oldValue) {
                                console.log("no op");
                                return;
                            }
                            var newData = {
                                piva: $scope.dataPar.show.azienda.PartitaIva,
                                cancella: false,
                                user: $scope.opPar.tmk,
                                vm: {},
                                modificaAzienda: true
                            }
                            newData.vm[$scope.dataPar.edit.nomeCampo] = $scope.dataPar.edit.newValue;
                            console.log(newData);
                            DataService.postData("amendPiva", newData, "amendPivaPerformed", newData);
                        }
                        //salva modifiche in db

                        $scope.$watch("dataPar.show.azienda.Sito", function (newvalue, oldvalue) {
                            if (newvalue.indexOf("http") === -1) {
                                $scope.dataPar.show.azienda.Sito = "http://" + $scope.dataPar.show.azienda.Sito;
                            }
                        });
                    }


                    $rootScope.$on("amendPivaPerformed", function (event, mass) {
                        if (mass.modificaAzienda) {
                            that.applicaModificheAzienda(mass);
                        }
                    });
                    this.applicaModificheAzienda = function (mass) {

                        //correggi show

                        var res = DataService.find(mass.ticket);
                        if (!res[1].result) {
                            alert("Qualcosa è andato storto, riprovare!");
                            $scope.dataPar.edit = "";
                            return;
                        }
                        $scope.dataPar.show.azienda[$scope.dataPar.edit.nomeCampo] = $scope.dataPar.edit.newValue;
                        $scope.dataPar.edit = "";
                    }




                    this.renderContact = function (contact) {
                        return contact.nome + " " + contact.cognome + " - " + contact.ruolo;
                    }
                    this.now = function () {
                        return new Date();
                    }

                }]);
    //utilities:

    app.filter("renderNumero", function () {
        return function (input) {
            switch (input) {
                case 0:
                case '0':
                    return "Chiama";
                    break;
                case 1:
                case '1':
                    return "Attendo esito chiamata";
                    break;
                case 2:
                case '2':
                    return "Chiamato";
                    break;
                case 3:
                case '3':
                    return "Rimosso";
                    break;
            }
        }
    });
    app.filter("timeElapsed", function () {
        return function (input) {
            if (!input.now || !input.start || !input.timer)
                return "";
            var now = input.now;
            var start = input.start;
            var nowTS = now.getTime();
            var startTS = start.getTime();
            var elapsed = nowTS - startTS;
            var hours = Math.floor(elapsed / (1000 * 60 * 60));
            elapsed = elapsed - (hours * (1000 * 60 * 60));
            var minutes = Math.floor(elapsed / (1000 * 60));
            elapsed = elapsed - (minutes * (1000 * 60));
            var seconds = Math.floor(elapsed / (1000));
            return hours + ":" + minutes + ":" + minutes;
        }
    })
    function setWFStatus(wf, newStatus) {
        var flag = false;
        if (wf.hasOwnProperty(newStatus)) {
            for (var i in wf) {
                if (wf.hasOwnProperty(i)) {
                    wf[i] = false;
                }
            }
            wf[newStatus] = true;
        }
        else {
            return false;
        }
    }

    function alpha(inputtxt)
    {
        var letters = /^[A-Za-z]*$/;
        if (inputtxt.match(letters))
        {
            return true;
        }
        else
        {
            return false;
        }
    }


    var FASCIAFATTURATO = {
        '1': '0 - 0,25 (Ml. euro)',
        '2': '0,25 - 0,5 (Ml. euro)',
        '3': '0,5 - 1,5 (Ml. euro)',
        '4': '1,5 - 2,5 (Ml. euro)',
        '5': '2,5 - 5 (Ml. euro)',
        '6': '5 - 13 (Ml. euro)',
        '7': '13 - 25 (Ml. euro)',
        '8': '25 - 50 (Ml. euro)',
        '9': '50 - 100 (Ml. euro)',
        '10': '> 100 (Ml. euro)',
        '11': 'NON ASSEGNATA'
    };
    var CLASSEDIPENDENTI = {
        '1': '1',
        '2': '2 - 5',
        '3': '6 - 9',
        '4': '10 - 19',
        '5': '20 - 49',
        '6': '50 - 249',
        '7': '250 - 499',
        '8': '500 - 1000',
        '9': 'MAGGIORE DI 1000',
        '10': 'NON ASSEGNATA'
    };
    app.filter("fasciaFatturato", function () {
        return function (input) {
            if (FASCIAFATTURATO[input])
                return FASCIAFATTURATO[input];
            else
                return "N/D";
        }
    });
    app.filter("classeDipendenti", function () {
        return function (input) {
            if (CLASSEDIPENDENTI[input])
                return CLASSEDIPENDENTI[input];
            else
                return "N/D";
        }
    }
    );
    app.filter("statoGeneraleLead", function (userInfoFilter) {
        return function (lead, tmkList, vendList) {
            var stato = {
                selezione: "Libera",
                chiamata: "",
                assegnazioneTmk: "",
                assegnazioneVend: "",
                attuale: ""
            };
            if (lead.pipe && lead.worker && lead.working) { // è nel tubo
                stato.selezione = "Selezionata";
                switch (lead.pipe) {
                    case '1':
                    case '2':
                    case '3':
                        stato.chiamata = "Coda";
                        break;
                    case '4':
                        stato.chiamata = "Rifisso";
                        break;
                    case '5':
                        stato.chiamata = "Conferma";
                        break;
                    case '6':
                        stato.chiamata = "Riservato";
                        break;
                    default:
                        stato.chiamata = "Oltre Tmk";
                        break;
                }


                if (lead.working == '1' && lead.tmklock != '0' && lead.worker != '0') {
                    stato.attuale = "Lavorazione";
                }
                if (lead.working == '2' && lead.tmklock != '0' && lead.worker != '0') {
                    stato.attuale = "Prenotata";
                }
                if (lead.working == '0') {
                    stato.attuale = "In coda";
                }
                if (lead.unlock_timestamp && lead.unlock_timestamp.length > 1) {
                    stato.attuale += " Ritardata";
                }
                if (lead.static_lock && lead.static_lock == '1') {
                    stato.attuale += " Disattivata";
                }
                if (lead.tmklock != '0') {
                    stato.attuale = "Personale";
                    stato.assegnazioneTmk = userInfoFilter(lead.tmklock, tmkList);
                }
                else if (lead.ttmk && lead.ttmk != '0') {
                    var ttmk = lead.ttmk.split(",");
                    stato.assegnazioneTmk = [];
                    for (var i in ttmk) {
                        stato.assegnazioneTmk.push(userInfoFilter(ttmk[i], tmkList));
                    }
                    stato.assegnazioneTmk = stato.assegnazioneTmk.join(",");
                }
                if (lead.force && lead.force != '0') {
                    stato.attuale += " Prenotata";
                }
                stato.assegnazioneVend = userInfoFilter(lead.worker, vendList);
            }


            return stato;
        }
    });



})();

