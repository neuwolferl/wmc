(function () {
    var app = angular.module("SituazioneAppuntamenti", ['DATAMODULE', 'SMARTBUTTON', 'COMMON', 'SINOTTICO', 'LOGGER'], ['$interpolateProvider',
        function ($interpolateProvider) {
            $interpolateProvider.startSymbol("--__");
            $interpolateProvider.endSymbol("__--");
        }]);
    app.controller("AppController", ['Logger', 'DataService', '$scope', '$rootScope', '$timeout', '$interval', 'SmartButton', 'Sin', 'Common',
        function (Logger, DataService, $scope, $rootScope, $timeout, $interval, SmartButton, Sin, Common) {
            var that = this;
            $scope.opPar = {};
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
                that.reloadLog = function (piva) {
                    Logger.getLogger(that.logger[0]).stopLogRefresher();
                    Logger.getLogger(that.logger[0]).startLogRefresher(10000, //scommentare in produzione
                            {
                                piva: piva
                            });

                    Logger.getLogger(this.logger[0]).setLogDescriptor("logGeneral", $scope.opPar.logGeneralDesc, $scope.opPar.userList);

                };
                Logger.getLogger(this.logger[0]).startChangeMirroring();

            };
            this.configurazioneLogger();



            $scope.appuntamentiRaw = [];
            $scope.ore = [];
            for (var i = 0; i < 24; i++) {
                $scope.ore.push((i < 10 ? "0" : "") + i);
                i = Number(i);
            }
            $scope.minuti = [];
            for (var i = 0; i < 60; i++) {
                $scope.minuti.push((i < 10 ? "0" : "") + i);
                i = Number(i);
            }

            this.destroyPage = function () {
                this.errorMask = true;
                $("error-mask").siblings().each(function () {
                    $(this).hide();
                });
            }
            DataService.initializeOnCurrentPage(
                    function () {
                        DataService.getData("getTmkLogDesc", {}, "getTmkLogDescPerformed", {},
                                function (t) {
                                    $scope.opPar.logGeneralDesc = t[1].response.data.tmklog;
                                    $scope.opPar.userList = t[1].response.data.users;
//                                    that.configura();
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

//            Restangular.one("sales_comapp", 90545).getList().then(function (accounts) {
//                $scope.prova = accounts;
//            }
//            );
//            console.log(Restangular.one("sales_comapp", 90545).getRequestedUrl());
//            console.log(Restangular.getRequestedUrl());

            /*
             * Sinottico
             */
            this.configurazioneSinottico = function () {
                this.sinottico = Sin.newCal();
                this.sinLabelCallback = function (obj) {
                    var ticket = DataService.getData("getCommerciali", {}, "getCommercialiPerformed", {},
                            function (t) {
                                obj.labels = [];

                                for (var i in t[1].response.data) {
                                    obj.labels.push(t[1].response.data[i].NOME + " " + t[1].response.data[i].COGNOME);
                                }
                                $rootScope.commerciali = [];
                                for (var i in obj.labels) {
                                    $rootScope.commerciali.push(obj.labels[i]);
                                }
                            },
                            function (t) {
                                obj.labels = [];
                                console.log("Errore nello scaricamento delle etichette del calendario.");
                            });
                };
                Sin.getCal(this.sinottico[0]).setLabelGetter(that.sinLabelCallback);
//                Sin.getCal(this.sinottico[0]).setLabelRetriever(that.sinLabelRetrieveCallback);
                this.sinDataCallback = function (obj) {
                    var labels = [];
                    for (var i in obj.labels) {
                        if (obj.labels[i].active) {
                            labels.push(obj.labels[i].label);
                        }
                    }
                    if (!labels || !labels.length)
                        return; 
                    var startTime = Common.dateMultiRep(obj.renderPars.startTime).dateEngString;
                    var endTime = Common.dateMultiRep(obj.renderPars.endTime).dateEngString;
                    DataService.postData("getAppuntamenti2", {commerciali: labels, startTime: startTime, endTime: endTime}, "getAppuntamenti2Performed", {startTime: startTime, endTime: endTime},
                    function (t) {
                        $scope.appuntamentiRaw = [];
                        var data = t[1].response.data;
                        var out = [];
                        for (var i in data) {
                            $scope.appuntamentiRaw.push(data[i]);
                            var element = {
                                label: data[i].venditore,
                                error: []
                            };
                            if (data[i].activities) {
                                if (data[i].activities && data[i].activities.length === 0) {
                                    element.error.push("no act");
                                    element.details = angular.copy(data[i]);
                                    out.push(element);
                                    continue;
                                }
                                if (data[i].activities && data[i].activities.length > 1)
                                    element.error.push("too many acts");
                                var act = data[i].activities[0];
//                            console.log(act.data_inizio.date, act.orario_inizio, act.data_fine.date, act.orario_fine);
                            }
                            element.start = Common.strToDate(act.data_inizio.date.replace("00:00:00", "") + act.orario_inizio.replace("/(\d){2}:(\d){2}:00/", "#1:#2"));
                            element.end = Common.strToDate(act.data_fine.date.replace("00:00:00", "") + act.orario_fine.replace("/(\d){2}:(\d){2}:00/", "#1:#2"));
//                            console.log(element.start, element.end);
                            element.details = angular.copy(data[i]);
                            out.push(element);
                        }
                        obj.data = out;
                    },
                            function (t) {
                                alert("Spiacenti, si é verificato un errore nello scaricamento degli appuntamenti. Se il problema persiste consultare un amministratore.")
                            }
                    );
                    return "getAppuntamentiPerformed";
                };
                Sin.getCal(this.sinottico[0]).setDataGetter(that.sinDataCallback);
                $scope.annullaSceltaAppuntamento = function () {
                    $scope.appuntamento = {};
                }
                $scope.prepareAppForEdit = function (args) {
                    try {
                        $scope.appuntamento = {};
                        $scope.appuntamento.details = args.data.details;
                        $scope.appuntamento.id_opportunita = args.data.details.id_opportunita;
                        $scope.appuntamento.prossimo_step = args.data.details.prossimo_step;
                        $scope.appuntamento.venditore = args.data.details.venditore;
                        $scope.appuntamento.ragione_sociale = args.data.details.account.ragione_sociale;
                        $scope.appuntamento.partita_iva = args.data.details.account.partita_iva;
                        $scope.appuntamento.telefono = args.data.details.account.telefono;
                        $scope.appuntamento.indirizzo = args.data.details.activities[0].indirizzo;
                        $scope.appuntamento.telemarketing = args.data.details.telemarketing;
                        $scope.appuntamento.contatti = args.data.details.contacts;
                        $scope.appuntamento.indirizzoaccount = args.data.details.indirizzoaccount;
                        $scope.appuntamento.fatturato = [args.data.details.account.fatturato, args.data.details.account.fascia_fatturato];
                        $scope.appuntamento.dipendenti = [args.data.details.account.dipendenti, args.data.details.account.classe_dipendenti];
                        $scope.appuntamento.indirizzoaccount = args.data.details.indirizzoaccount;
                        $scope.appuntamento.data = args.data.details.activities[0].data_inizio.date.replace(" 00:00:00", "");
                    }
                    catch (e) {
                        console.log(args);
                    }
                    var orario = args.data.details.activities[0].orario_inizio;
                    orario = orario.split(":");
                    $scope.appuntamento.ora = orario[0];
                    $scope.appuntamento.minuti = orario[1];
                    console.log($scope.appuntamento.ora);
                    $scope.appuntamento.stato = 3;
                    if (args.data.details.stadio_vendita === "Qualification") {
                        if (args.data.details.esito_conferma === "Non confermato")
                            $scope.appuntamento.stato = 2;
                        else
                            $scope.appuntamento.stato = 0;
                    }
                    else if (args.data.details.stadio_vendita === "Prospecting") {
                        if ($scope.appuntamento.prossimo_step === "Visita venditore") {
                            $scope.appuntamento.stato = 1;
                        }
                    }
                    that.reloadLog(args.data.details.account.partita_iva);
//                    $scope.$digest();
                };
                Sin.getCal(this.sinottico[0]).setOnClick($scope.prepareAppForEdit);

                $scope.cambiaDataOra = function () {
                    var pad10 = Common.pad10;
                    var sdata = $scope.appuntamento.data;
                    var dateExp = /\d{4}-\d{2}-\d{2}/;
                    if (angular.isString(sdata)) {
                        if (!dateExp.test(sdata)) {
                            alert("C'è un problema con la data");
                            return;
                        }
                    }
                    else if (sdata.getTime && !isNaN(sdata.getTime())) {
                        sdata = sdata.getFullYear() + "-" + pad10(sdata.getMonth() + 1) + "-" + pad10(sdata.getDate());
                    }

                    DataService.getData("cambiaDataOra", {
                        potentialid: $scope.appuntamento.id_opportunita,
                        data: sdata,
                        ora: $scope.appuntamento.ora,
                        minuti: $scope.appuntamento.minuti,
                    }, "cambiaDataOraPerformed", {},
                            function (t) {
                                that.sinottico[1].update();
                                $scope.appuntamento = {};
//                                window.location.reload();
                            },
                            function (t) {
                                var err = "";
                                if (angular.isString(t[1].response)) {
                                    err += t[1].response;
                                }
                                else if (angular.isString(t[1].response.data)) {
                                    err += t[1].response.data;
                                }
                                alert("Qualcosa è andato storto! " + err);
                                $scope.appuntamento = {};
//                                window.location.reload();
                            });
                    $scope.appuntamento = {};
                };
                $scope.cambiaCommerciale = function () {

                    DataService.getData("CambiaCommerciale", {
                        potentialid: $scope.appuntamento.id_opportunita,
                        commerciale: $scope.appuntamento.venditore
                    }, "CambiaCommercialePerformed", {},
                            function (t) {
                                that.sinottico[1].update();
                                $scope.appuntamento = {};
//                                window.location.reload();
                            },
                            function (t) {
                                alert("Qualcosa è andato storto!");
                                $scope.appuntamento = {};
                                window.location.reload();
                            });
                    $scope.appuntamento = {};
                };
                $scope.confermaAppuntamento = function () {
                    $scope.sendMail();
                    DataService.getData("ComAppStatus", {
                        potentialid: $scope.appuntamento.id_opportunita,
                        status: "_COMAPP_QUALIFIED"
                    }, "ComAppStatusPerformed", {},
                            function (t) {
                                that.sinottico[1].update();
                                $scope.appuntamento = {};
//                                window.location.reload();
                            },
                            function (t) {
                                alert("Qualcosa è andato storto!");
                                $scope.appuntamento = {};
                                window.location.reload();
                            });
                    $scope.appuntamento = {};
                };
                $scope.sconfermaAppuntamento = function () {

                    DataService.getData("ComAppStatus", {
                        potentialid: $scope.appuntamento.id_opportunita,
                        status: "_COMAPP_UNQUALIFIED"
                    }, "ComAppStatusPerformed", {},
                            function (t) {
                                that.sinottico[1].update();
                                $scope.appuntamento = {};
//                                window.location.reload();
                            },
                            function (t) {
                                alert("Qualcosa è andato storto!");
                                $scope.appuntamento = {};
                                window.location.reload();
                            });
                    $scope.appuntamento = {};
                };

                $scope.sendMail = function () {
                    DataService.postData("sendComMail", {
                        azienda: $scope.appuntamento.ragione_sociale,
                        location: $scope.appuntamento.indirizzo,
                        tmk: $scope.appuntamento.telemarketing,
                        venditore: $scope.appuntamento.venditore
                    },
                    "sendComMailPerformed",
                            {},
                            function (t) {
                                alert("Email inviata correttamente.");
                            },
                            function (t) {
                                alert("Email non inviata correttamente, riprovare.");
                            }
                    )
                }

                Sin.getCal(this.sinottico[0]).setOnMouseMove(function (args) {
//            var datum = Sin.getCal(that.sinottico[0]).getShapeUnderlyingData(args.shape.index);

                });
                Sin.getCal(this.sinottico[0]).setShapeInfoDisplayer(function (obj) {

                    var datum = obj.details;
                    var string = "Appuntamento di <b>" + datum.venditore + "</b> presso: <br>";
                    string += "<b>" + "datum.account.ragione_sociale" + "</b><br>";
                    string += "<i>" + "datum.account.partita_iva" + "</i><br>";
                    string += "<i>" + "datum.activities[0].indirizzo" + "</i><br>";
                    string += "<i>" + "datum.phone" + "</i><br>";
                    string += "da: " + "datum.start" + "<br>";
                    string += "a: " + "datum.end" + "<br>";
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
                $scope.calendarShapeInfo = {shapeIndex: -1, info: ""};
                Sin.getCal(this.sinottico[0]).setShapeInfoPublisher(function (arg) {
                    var oldShapeIndex = $scope.calendarShapeInfo.shapeIndex;
                    if (oldShapeIndex !== arg.shapeIndex) {

//                        if (arg.obj.error.length) {
//                            $scope.calendarShapeInfo = "errore";
//                        }
                        if (arg.obj.details.stadio_vendita === "Prospecting") {
                            if (arg.obj.details.nextstep === "Ripasso venditore") {
                                $scope.calendarShapeInfo.stato = "ripasso";
                            }
                            else {
                                $scope.calendarShapeInfo.stato = "confermato"
                            }
                        }
                        else {
                            $scope.calendarShapeInfo.stato = "nonconfermato"
                        }


                        $scope.calendarShapeInfo.shapeIndex = arg.shapeIndex;
                        $scope.calendarShapeInfo.info = arg.obj;
                        $scope.$digest();
                    }
                });
                this.sinottico[1].renderPars.startTime = new Date();
                this.sinottico[1].renderPars.startTime.setHours(6);
                this.sinottico[1].renderPars.endTime = new Date();
                this.sinottico[1].renderPars.endTime.setHours(20);
                this.sinottico[1].renderPars.showLegend = true;
                this.sinottico[1].renderPars.colors = {
                    "confermato": "green", "da confermare": "yellow", "nonconfermato": "orange", "sovrapposto": "black", "ripasso": "blue", "errore": "red", "altro/da richiamare": "grey"
                };
                this.sinottico[1].renderPars.colorer = function (elm, all) {
                    var colorer = "altro/da richiamare";
                    if (elm.error.length) {
                        colorer = "errore";
                        return;
                    }
                    else if (elm.details.stadio_vendita === "Prospecting") {
                        if (elm.details.prossimo_step === "Ripasso venditore") {
                            colorer = "ripasso";
                        }
                        else {
                            colorer = "confermato"
                        }
                    }
                    else {
                        if (elm.details.esito_conferma === "Non confermato") {
                            colorer = "nonconfermato";
                        }
                        else if (elm.details.esito_conferma === "Da confermare") {
                            colorer = "da confermare";
                        }

                    }
                    return colorer;
                };
                this.sinottico[1].renderPars.title.title = function (calObj, dateObj) {
                    return 'Sinottico degli appuntamenti per il giorno ' + dateObj.dateItaString;
                }
            }
            this.configurazioneSinottico();
            $timeout(function () {
                that.sinottico[1].open();
            }, 3000);



            this.reportToAdmin = function (app) {
                DataService.postData("sendReportMail",
                        {
                            dove: "Situazione appuntamenti",
                            cosa: "Segnalato appuntamento con errore",
                            commento: app
                        },
                "sendReportMailPerformed",
                        {},
                        function (t) {
                            alert("La segnalazione é stata inviata.");
                            return;
                        },
                        function (t) {
                            alert("La segnalazione non é stata inviata correttamente, è possibile riprovare.");
                            return;
                        }
                );
            };



        }]);
    app.filter("userInfo", function () {
        return function (input, list, format) {
            if (typeof (format) === "undefined") {
                format = "";
            }
            for (var i in list) {
                if (list[i].USERID && list[i].NOME && list[i].COGNOME && list[i].USERID == input) {
                    switch (format) {
                        case 'N':
                            return list[i].NOME;
                            break;
                        case 'C':
                            return list[i].COGNOME;
                            break;
                        default:
                            return list[i].NOME + " " + list[i].COGNOME;
                            break;
                    }
                }
            }
            return input;
        }
    });
    app.filter("statoLead", function () {
        return function (input) {
            if (input.working == "1")
                return "Attualmente in lavorazione";
            if (input.force && input.force != '0')
                return "Prenotata";
            if (input.static_lock == "1")
                return "Disattivata manualmente";
            if (input.unlock_timestamp != "0") {
                var unlock_timestamp = new Date(input.unlock_timestamp);
                var now = new Date();
                var date = "";
                date += (unlock_timestamp.getDate() < 10 ? "0" : "") + unlock_timestamp.getDate();
                date += "-";
                date += (unlock_timestamp.getMonth() < 9 ? "0" : "") + (1 + unlock_timestamp.getMonth());
                date += "-";
                date += unlock_timestamp.getFullYear();
                date += " ";
                date += unlock_timestamp.getHours();
                date += ":";
                date += (unlock_timestamp.getMinutes() < 9 ? "0" : "") + unlock_timestamp.getMinutes();
                date += ":";
                date += (unlock_timestamp.getSeconds() < 9 ? "0" : "") + unlock_timestamp.getSeconds();
                return "Posticipata.Sarà disponibile @ " + date;
            }
            else if (input.donotcallbefore != "0") {
                var unlock_timestamp = new Date(input.donotcallbefore);
                var now = new Date();
                var date = "";
                date += (unlock_timestamp.getDate() < 10 ? "0" : "") + unlock_timestamp.getDate();
                date += "-";
                date += (unlock_timestamp.getMonth() < 9 ? "0" : "") + (1 + unlock_timestamp.getMonth());
                date += "-";
                date += unlock_timestamp.getFullYear();
                date += " ";
                date += unlock_timestamp.getHours();
                date += ":";
                date += (unlock_timestamp.getMinutes() < 9 ? "0" : "") + unlock_timestamp.getMinutes();
                date += ":";
                date += (unlock_timestamp.getSeconds() < 9 ? "0" : "") + unlock_timestamp.getSeconds();
                return "Posticipata.Sarà disponibile @ " + date;
            }
            return "In attesa";
        }
    });
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
    });
    app.filter("esclusione", function () {
        return function (input) {
            var ret = [];
            input = Number(input);
            if (input & 128)
                ret.push("Ateco");
            if (input & 64)
                ret.push("Tmk");
            if (input & 32)
                ret.push("Direzione");
            if (input & 16)
                ret.push("Fatturato");
            if (input & 8)
                ret.push("Chiamata recente");
            if (input & 4)
                ret.push("Visita recente");
            if (input & 2)
                ret.push("Selezionata");
            if (input & 1)
                ret.push("Progetto");
            return ret.join(", ");
        }
    });
    app.filter("onlyActive", function () {
        return function (input, flag) {
            var out = [];
            for (var i in input) {
                if (!flag) {
                    out.push(input[i]);
                    continue;
                }
                else if (input[i].static_lock != '1') {
                    if (input[i].unlock_timestamp == '0') {
                        out.push(input[i]);
                        continue;
                    }
                    var unlock_timestamp = new Date(input[i].unlock_timestamp);
                    var now = new Date();
                    if (now > unlock_timestamp) {
                        out.push(input[i]);
                        continue;
                    }
                }
            }
            return out;
        }
    });
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
    }


})();



