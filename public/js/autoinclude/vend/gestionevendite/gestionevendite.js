(function () {
    var app = angular.module("GestioneVendite", ['ngMessages', 'DATAMODULE', 'COMMON', 'MenuVisite', 'ngAnimate', 'ngTouch', 'LOGGER', 'SUPERFORM', 'TSNWINCLUDE'], ['$interpolateProvider', function ($interpolateProvider) {
            $interpolateProvider.startSymbol("--__");
            $interpolateProvider.endSymbol("__--");
        }]);
    app.controller("GestioneVenditeController", ['DataService', '$scope', '$rootScope', '$timeout', 'Common', 'Logger', '$filter', '$interval', '$compile', 'Tsnw',
        function (DataService, $scope, $rootScope, $timeout, Common, Logger, $filter, $interval, $compile, Tsnw) {
            var that = this;
            this.destroyPage = function () {
                this.errorMask = true;
                $("error-mask").siblings().each(function () {
                    $(this).hide();
                });
            };
            $scope.st = Common.whereAmI().register();
            $scope.st = Common.whereAmI($scope.st);
            $scope.st.put("init");
            this.Common = Common;
            that.selectedAppDetails = {};
            $scope.getConPrev = function () {
                var con = parseInt(that.selectedAppDetails.attivita.id_contatto);
                if (!isNaN(con) && con > 0)
                    return con;
                else
                    return 0;
            }
            $scope.selectApp = function (app, goto, callback) {
                if (app) {
                    $scope.selectedApp = angular.copy(app);
                    console.log($scope.selectedApp);
                    DataService.getData("getDetails", {piva: app.piva, potid: app.id_opportunita, actid: app.id_attivita},
                    "getDetailsPerformed", {},
                            function (t) {
                                for (var i in that.selectedAppDetails) {
                                    delete(that.selectedAppDetails[i]);
                                }
                                for (var i in t[1].response.data) {
                                    if (t[1].response.data.hasOwnProperty(i)) {
                                        that.selectedAppDetails[i] = t[1].response.data[i];
                                    }
                                }
                                var exp = new Date($scope.expCalculator($scope.selectedApp));
                                var pad10 = Common.pad10;
                                that.selectedAppDetails.exp = exp;
                                that.selectedAppDetails.expDisplay = exp.getFullYear() + "-" + pad10(exp.getMonth() + 1) + "-" + pad10(exp.getDate());
                                console.log(that.selectedAppDetails);
                                if (that.selectedAppDetails.account) {
                                    if (goto) {
                                        $scope.st.put(goto);
                                    }
                                    if (callback)
                                        callback();
                                }
                                else {
                                    alert("Qualcosa é andato storto durante lo scaricamento dei dettagli per questa azienda. E' possibile riprovare, se il problema non si dovesse risolvere, contattare un amministratore fornendo i dettagli del problema.");
                                    console.error("Qualcosa é andato storto durante lo scaricamento dei dettagli per questa azienda.");
                                }
                            },
                            function (t) {
                                alert("Impossibile scaricare dettagli per questa azienda. E' possibile riprovare, se il problema non si dovesse risolvere, contattare un amministratore fornendo i dettagli del problema.");
                                console.error("Impossibile scaricare dettagli per questa azienda.");
                            });
                    return true;
                }
            };
            $scope.goToAgenda = function (remove) {
                if (remove) {
                    $scope.selectedApp = void(0);
                }
                $scope.st.put("agenda");
            }
            $scope.goToRifisso = function (app) {
                $scope.selectApp(app, "rifisso");
            }
            $scope.goToRipasso = function (app) {
                $scope.selectApp(app, "ripasso");
            }
            $scope.goToEsito = function (app) {
                $scope.selectApp(app, "esito", function () {
                    if (angular.isFunction(that.op.esito.goToStep))
                        that.op.esito.goToStep(0);
                    that.op.esito.selectedAppDetails = that.selectedAppDetails;
                    that.op.esito.editDatiContatti = false;
                    that.op.esito.editDatiCliente = false;
                    that.op.esito.fasciaFatturatoList = Tsnw.accountUtilities.getFasciaFatturatoList();
                    that.op.esito.classeDipendentiList = Tsnw.accountUtilities.getClasseDipendentiList();
                    that.op.esito.formaGiuridicaList = Tsnw.accountUtilities.getFormaGiuridicaList();
                    that.op.esito.modelOptions = {
                        timezone: timezone
                    };
                    that.op.esito.updating = false;
                    that.op.esito.model.supervisione = false;
                });
            };
            $scope.goToSupervisione = function (app) {
                $scope.selectApp(app, "esito", function () {
                    if (angular.isFunction(that.op.esito.goToStep))
                        that.op.esito.goToStep("Supervisione");
                    that.op.esito.selectedAppDetails = that.selectedAppDetails;
                    that.op.esito.editDatiContatti = false;
                    that.op.esito.editDatiCliente = false;
                    that.op.esito.fasciaFatturatoList = Tsnw.accountUtilities.getFasciaFatturatoList();
                    that.op.esito.classeDipendentiList = Tsnw.accountUtilities.getClasseDipendentiList();
                    that.op.esito.formaGiuridicaList = Tsnw.accountUtilities.getFormaGiuridicaList();
                    that.op.esito.modelOptions = {
                        timezone: timezone
                    };
                    that.op.esito.updating = false;
                    that.op.esito.model.supervisione = true;
                    that.op.esito.model.supervisioneScadenza = that.selectedAppDetails.exp;
                });
            };
            $scope.goToStorico = function (app) {
                $scope.selectApp(app, "storico", function () {
                    that.reloadLog(app.piva);
                });
            };
            $scope.menuVisiteConf = {
                loaded: false,
                appList: "apps",
                dater: function (x) { // stringa o function
                    if (angular.isString(x.data_chiusura_attesa.date)) {
                        var date = x.data_chiusura_attesa.date.replace(" 00:00:00", " ") + x.ora_inizio + ":00";
                        date = new Date(date);
                    }
                    else {
                        var date = x.data_chiusura_attesa.date;
                        var hour = x.ora_inizio.trim().split(":");
                        if (hour[0])
                            date.setHours(parseInt(hour[0]));
                        if (hour[1])
                            date.setMinutes(parseInt(hour[1]));
                        if (hour[2])
                            date.setSeconds(parseInt(hour[1]));
                    }
                    return date;
                },
                ider: 'numero_opportunita', timeStep: 24 * 60 * 60 * 1000,
                displayDate: function (app) { //funzione costante wrt timeStep
                    if (angular.isString(app.data_chiusura_attesa.date)) {
                        var date = app.data_chiusura_attesa.date.replace(" 00:00:00", " ") + app.ora_inizio + ":00";
                        date = new Date(date);
                    }
                    else {
                        var date = app.data_chiusura_attesa.date;
                        date.setHours(0);
                        date.setMinutes(0);
                        date.setSeconds(0);
                        date.setMilliseconds(0);
                    }
                    return Common.verbItaDateNoYear(date, true);
                }, searchFcn: function (app, string) {
                    if (!string)
                        return true;
                    var searchArr = string.split(",");
                    var out = false;
                    for (var i in searchArr) {
                        if (!searchArr[i])
                            continue;
                        searchArr[i] = searchArr[i].trim();
                        out = out || app.ragione_sociale.toLowerCase().indexOf(searchArr[i].toLowerCase()) > -1;
                        if (app.altridati) {
                            if (app.altridati.indirizzo)
                                out = out || app.altridati.indirizzo.toLowerCase().indexOf(searchArr[i].toLowerCase()) > -1;
                            if (app.altridati.telefono)
                                out = out || app.altridati.telefono.toLowerCase().indexOf(searchArr[i].toLowerCase()) > -1;
                            if (app.altridati.email)
                                out = out || app.altridati.email.toLowerCase().indexOf(searchArr[i].toLowerCase()) > -1;
                        }
                    }

                    return out;
                },
                searchSugg: 'Ricerca libera per ragione sociale, indirizzo, email, telefono. Separa i valori con la virgola per affinare la tua ricerca',
                propagated: {//elenco di funzioni da eseguire in questo scope ma richiamabili da scope interni al menu
                    //vengono propagate automaticamente tramite Common lib, ma la sintassi è scomoda per il template
                    goToAgenda: $scope.goToAgenda,
                    goToEsito: $scope.goToEsito,
                    goToRipasso: $scope.goToRipasso,
                    goToRifisso: $scope.goToRifisso,
                    goToStorico: $scope.goToStorico,
                    reloadCountdown: function () {
                        return $scope.reloadCountdown
                    }
                },
                actions: {
                    esito: {userLabel: "Esito", func: "goToEsito", args: ["app", "show"],
                        viewIf: function (s) {
//                            console.log(arguments);
                            return s.show.esito;
                        }},
                    rifisso: {userLabel: "Rifisso", func: "goToRifisso", args: ["app", "show"],
                        viewIf: function (s) {
                            return s.show.rifisso;
                        }},
                    storico: {userLabel: "Storico", func: "goToStorico", args: ["app"],
                        viewIf: function (s) {
                            return true;
                        }},
                    supervisione: {userLabel: "Supervisione", func: "goToSupervisione", args: ["app"],
                        viewIf: function (s) {
                            return ($rootScope.supervisione ? true : false);
                        },
                        enableIf: function (s) {
                            return ($rootScope.supervisione ? true : false);
                        }},
                },
                filters: [
                    "filterByVendors"
                ]
            };
            $scope.validator = Common.wsDataAnalyze().register();
            $scope.validator = Common.wsDataAnalyze($scope.validator);
            $scope.validator.addChecker(function (datum, errFcn) {
                if (!datum.id_opportunita) {
                    errFcn({error: "Errore id opportunita non valido", datum: angular.copy(datum)});
                    return false;
                }
                if (!datum.numero_opportunita) {
                    errFcn({error: "Errore numero opportunita non valido", datum: angular.copy(datum)});
                    return false;
                }
                if (!datum.ora_inizio) {
                    errFcn({error: "Errore ora inizio non valido", datum: angular.copy(datum)});
                    return false;
                }
                datum.ora_inizio = datum.ora_inizio.replace(/:\d0:00/, ":00");
                if (!datum.piva || datum.piva.length !== 11) {
                    errFcn({error: "Errore partita iva non valido", datum: angular.copy(datum)});
                    return false;
                }
                if (!datum.ragione_sociale) {
                    errFcn({error: "Errore ragione sociale non valido", datum: angular.copy(datum)});
                    return false;
                }
                if (!datum.telemarketing) {
                    errFcn({error: "Errore telemarketing non valido", datum: angular.copy(datum)});
                    return false;
                }
                if (!datum.venditore) {
                    errFcn({error: "Errore venditore non valido", datum: angular.copy(datum)});
                    return false;
                }
                return true;
            });
            $scope.validator.addChecker(function (datum, errFcn) {
                if (datum.status === "NO ACTIVITY ERROR") {
                    errFcn({error: "Errore calendario", datum: angular.copy(datum)});
                    return false;
                }
                var stati = ["_COMAPP_NOCHECK", "_COMAPP_CHECK", "_COMAPP_SYSTEMCANCELLED", "_COMAPP_AUTOCLOSE",
                    "_COMAPP_PASSAGAIN", "_COMAPP_CALLAGAIN", "_COMAPP_UNQUALIFIED", "_COMAPP_QUALIFIED", "_COMAPP_QUALIFICATION"];
                if (!datum.status || stati.indexOf(datum.status) === -1) {
//                    console.log(datum.status, stati.indexOf(datum.status));
                    errFcn({error: "Errore stato non valido", datum: angular.copy(datum)});
                    return false;
                }
                return true;
            });
            $scope.validator.addChecker(function (datum, errFcn) {
                if (!angular.isObject(datum)) {
                    errFcn({error: "Invalid object", datum: angular.copy(datum)});
                    return false;
                }
                if (!datum.data_chiusura_attesa || !datum.data_chiusura_attesa.date) {
                    errFcn({error: "Errore data chiusura attesa", datum: angular.copy(datum)});
                    return false;
                }
                if (angular.isString(datum.data_chiusura_attesa.date)) {
                    datum.data_chiusura_attesa.date = new Date(datum.data_chiusura_attesa.date);
                }
                if (!Common.isValidDate(datum.data_chiusura_attesa.date)) {
                    errFcn({error: "Errore data chiusura attesa", datum: angular.copy(datum)});
                    console.log(datum.data_chiusura_attesa, datum.data_chiusura_attesa.date, typeof (datum.data_chiusura_attesa.date));
                    return false;
                }
                return true;
            });
            $scope.validator.addChecker(function (datum, errFcn) {
                if (!datum.data_inizio || !datum.data_inizio.date) {
                    errFcn({error: "Errore data inizio", datum: angular.copy(datum)});
                    return false;
                }
                if (angular.isString(datum.data_inizio.date)) {
                    datum.data_inizio.date = new Date(datum.data_inizio.date);
                }
                if (!Common.isValidDate(datum.data_inizio.date)) {
                    errFcn({error: "Errore data inizio", datum: angular.copy(datum)});
                    return false;
                }
                return true;
            });
            $scope.validated = {};
            $scope.opOnSelected_setCheck = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "check6") { ///<<<<<<
                    console.log("busy for opOnSelected_ripassoAct - skip");
                    return;
                }
                DataService.getData(
                        "ComAppStatus",
                        {potentialid: that.selectedAppDetails.opportunita.id_opportunita, status: "_COMAPP_CHECK"},
                "ComAppStatusPerformed",
                        {},
                        function (t) {

                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_setNocheck = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "nocheck3") {
                    console.log("busy for opOnSelected_ripassoAct - skip");
                    return;
                }
                DataService.getData(
                        "ComAppStatus",
                        {
                            potentialid: that.selectedAppDetails.opportunita.id_opportunita,
                            status: "_COMAPP_NOCHECK",
                            esito_visita: that.op.esito.model.motivazioneNoCheck,
                            comment: that.op.esito.model.noteNoCheck
                        },
                "ComAppStatusPerformed",
                        {},
                        function (t) {

                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_setRipasso = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "ripasso4") {
                    console.log("busy for opOnSelected_ripassoAct - skip");
                    return;
                }
                DataService.getData(
                        "ComAppStatus",
                        {
                            potentialid: that.selectedAppDetails.opportunita.id_opportunita,
                            status: "_COMAPP_PASSAGAIN",
                            comment: that.op.esito.model.noteRipasso
                        },
                "ComAppStatusPerformed",
                        {},
                        function (t) {
                            var pad10 = Common.pad10;
                            var oraRipasso = that.op.esito.model.oraRipasso;
                            var dataRipasso = that.op.esito.model.dataRipasso;
                            dataRipasso = new Date(dataRipasso);
                            var dataRipasso1 = dataRipasso.getFullYear() + "-" + pad10(dataRipasso.getMonth() + 1) + "-" + pad10(dataRipasso.getDate());
                            oraRipasso = new Date(oraRipasso);
                            var oraRipassoH = oraRipasso.getHours();
                            var oraRipassoM = oraRipasso.getMinutes();
                            var orarioRipasso1 = pad10(oraRipassoH) + ":" + pad10(oraRipassoM);
                            var ripassoTime = dataRipasso1 + " " + orarioRipasso1;

                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_setRifisso = function (sCallback, eCallback) {
                if (that.op.rifisso.model.updating !== "rifisso3") {
                    console.log("busy for opOnSelected_setRifisso - skip");
                    return;
                }
                var esiti_visita = {
                    "1": "Assente",
                    "2": "Non decisionale",
                    "3": "Imp Commerciale",
                }
                DataService.getData(
                        "ComAppStatus",
                        {
                            potentialid: that.selectedAppDetails.opportunita.id_opportunita,
                            status: "_COMAPP_CALLAGAIN",
                            esito_visita: esiti_visita[that.op.rifisso.model.motivazioneRifisso],
                            comment: that.op.rifisso.model.noteRifisso
                        },
                "ComAppStatusPerformed",
                        {},
                        function (t) {
//                            var logged = that.logger[1].putLog({
//                                piva: that.selectedAppDetails.account.partita_iva,
//                                what: ['op', "Vtiger", "potentials", "Setcallagain"],
//                                parameters: {
//                                    tmk: that.selectedAppDetails.account.telemarketing,
//                                    com: that.selectedAppDetails.account.venditore,
//                                    cause: esiti_visita[that.op.rifisso.model.motivazioneRifisso]
//                                },
//                                comment: that.op.rifisso.model.noteRifisso,
//                                esito: true,
//                                parent: 0
//                            });
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_ripassoAppendPotDescription = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "ripasso3") {
                    console.log("busy for opOnSelected_ripassoAct - skip");
                    return;
                }
                var pad10 = Common.pad10;
                var dataRipasso = that.op.esito.model.dataRipasso;
                dataRipasso = new Date(dataRipasso);
                var dataRipasso1 = dataRipasso.getFullYear() + "-" + pad10(dataRipasso.getMonth() + 1) + "-" + pad10(dataRipasso.getDate());
                var desc = that.selectedAppDetails.opportunita.descrizione;
                if (desc){
                var i1 = desc.indexOf("[INIZIORAPPORTOVENDITA]");
                var i2 = desc.indexOf("[FINERAPPORTOVENDITA]");
                var sub = desc.substring(i1, i2 + 21);
                desc = desc.replace(sub, "");
                }
                else {
                    desc = "";
                } 
                desc += "\r\n[Ripasso venditore]\r\n" + that.op.esito.model.noteRipasso;
                desc += "\r\n[INIZIORAPPORTOVENDITA]\r\n" + that.op.esito.model.polpettone + "\r\n[FINERAPPORTOVENDITA]\r\n";
                DataService.postData(
                        "editPotential",
                        {potential_vm: {id_opportunita: that.selectedAppDetails.opportunita.id_opportunita,
                                data_chiusura_attesa: dataRipasso1,
                                description: desc
                            }
                        },
                "editPotentialPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_checkAppendPotDescription = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "check5") { ///<<<<<<<
                    console.log("busy for opOnSelected_checkAppendPotDescription - skip");
                    return;
                }

                DataService.postData(
                        "editPotential",
                        {potential_vm: {id_opportunita: that.selectedAppDetails.opportunita.id_opportunita,
                                description: that.selectedAppDetails.opportunita.descrizione + "\r\n[Check]\r\n"}},
                "editPotentialPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_nocheckAppendPotDescription = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "nocheck2") {
                    console.log("busy for opOnSelected_nocheckAppendPotDescription - skip");
                    return;
                }
                var desc = that.selectedAppDetails.opportunita.descrizione;
                if (desc){
                var i1 = desc.indexOf("[INIZIORAPPORTOVENDITA]");
                var i2 = desc.indexOf("[FINERAPPORTOVENDITA]");
                var sub = desc.substring(i1, i2 + 21);
                desc = desc.replace(sub, "");
            }
            else {
                desc = "";
            }
                desc += "\r\n[No check]\r\n" + that.op.esito.model.noteNoCheck;
                desc += "\r\n[INIZIORAPPORTOVENDITA]\r\n" + that.op.esito.model.polpettone + "\r\n[FINERAPPORTOVENDITA]\r\n";
                DataService.postData(
                        "editPotential",
                        {potential_vm: {
                                id_opportunita: that.selectedAppDetails.opportunita.id_opportunita,
                                description: desc
                            }
                        },
                "editPotentialPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_rifissoAppendPotDescription = function (sCallback, eCallback) {
                if (that.op.rifisso.model.updating !== "rifisso2") {
                    console.log("busy for opOnSelected_rifissoAppendPotDescription - skip");
                    return;
                }
                var desc = that.selectedAppDetails.opportunita.descrizione;
                desc += "\r\n[Rifisso]\r\n" + that.op.esito.model.noteRifisso;
                DataService.postData(
                        "editPotential",
                        {potential_vm: {
                                id_opportunita: that.selectedAppDetails.opportunita.id_opportunita,
                                description: desc
                            }
                        },
                "editPotentialPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.autoValuePolpettone = function () {
                if (that.op.esito.model.polpettone && that.op.esito.model.polpettone !== "")
                    return that.op.esito.model.polpettone;

                var previousDesc = that.selectedAppDetails.opportunita.descrizione;
                if (previousDesc) {
                    var previousDescSplit = previousDesc.split("[INIZIORAPPORTOVENDITA]");
                    var previousPolpettone = null;
                    if (previousDescSplit.length > 1) {
                        for (var i in previousDescSplit) {
                            if (previousDescSplit[i].indexOf("[FINERAPPORTOVENDITA]") > -1) {
                                previousPolpettone = previousDescSplit[i].split("[FINERAPPORTOVENDITA]");
                                previousPolpettone = previousPolpettone[0].trim();
                                break;
                            }
                        } 
                    }
                    if (previousPolpettone) {
                        return previousPolpettone;
                    }
                }
                var out = "[Note su soci, titolari e gruppo direttivo]\r";
                out += "Segnalare le notizie rilevanti sulle relazioni identificate (Scrivere qui sotto)\r";
                out += "\rAZIENDA\r[Situazione finanziaria]\r";
                out += "Elementi riguardanti la situazione finanziaria aziendale (Scrivere qui sotto)\r";
                out += "\r[Motivazione per l'autorizzazione all'analisi]\r";
                out += "Di cosa ha bisogno l'azienda e perché (Scrivere qui sotto)\r";
                out += "\r[Note e osservazioni sull'azienda]\r";
                out += "Ulteriori informazioni sull'azienda rilevanti ai fini dell'eventuale analisi (Scrivere qui sotto)\r";
                return out;
            }
            $scope.opOnSelected_checkAct = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "check2") { /// <<<<<
                    console.log("busy for opOnSelected_checkAct - skip");
                    return;
                }
                var pad10 = Common.pad10;
                var oraEsito = that.op.esito.model.oraEsito;
                var dataEsito = that.op.esito.model.dataEsito;
                dataEsito = new Date(dataEsito);
                var dataEsito1 = dataEsito.getFullYear() + "-" + pad10(dataEsito.getMonth() + 1) + "-" + pad10(dataEsito.getDate());
                oraEsito = new Date(oraEsito);
                var oraEsitoH = oraEsito.getHours();
                var oraEsitoM = oraEsito.getMinutes();
                var orarioEsito1 = pad10(oraEsitoH) + ":" + pad10(oraEsitoM);
                var orarioEsito2 = pad10(oraEsitoH + 8) + ":" + pad10(oraEsitoM);
                console.log(orarioEsito1, orarioEsito2);
                var contattoAUT = _.find(that.selectedAppDetails.contatti, {id_contatto: that.op.esito.model.autorizzatoreAnalisi});
                var contattoAUTVerb = (contattoAUT ? "\r\rRiferimento\r"
                        + contattoAUT.nome
                        + " "
                        + contattoAUT.cognome
                        + " "
                        + contattoAUT.ruolo
                        + "\rTel: "
                        + contattoAUT.telefono
                        + "\rFax: "
                        + contattoAUT.fax
                        + "\rCel: "
                        + contattoAUT.cellulare
                        + "\rEmail: "
                        + contattoAUT.email : "");
                var valoreDaIncassare = Number(that.op.esito.model.valoreEsito) - Number(that.op.esito.model.statoCaparraEsito) * Number(that.op.esito.model.caparraEsito);
                var projectDetails = that.op.esito.model.polpettone;
                var eventDetails = "Valore analisi da incassare: ";
                eventDetails += valoreDaIncassare;
                eventDetails += "+IVA";
                eventDetails += "\rRagione sociale: ";
                eventDetails += that.selectedAppDetails.account.ragione_sociale;
                eventDetails += "\rPIVA: ";
                eventDetails += that.selectedAppDetails.account.partita_iva;
                eventDetails += "\rSettore: ";
                eventDetails += "---";
                eventDetails += "\rDipendenti: ";
                eventDetails += that.selectedAppDetails.account.classe_dipendenti;
                eventDetails += "\rFatturato: ";
                eventDetails += that.selectedAppDetails.account.fascia_fatturato;
                eventDetails += contattoAUTVerb;
                eventDetails += "\r\rDETTAGLI\r";
                eventDetails += projectDetails;
                var nuovaAttivita = {
                    data_fine: dataEsito1,
                    data_inizio: dataEsito1,
                    descrizione: eventDetails,
                    durata_ore: "8",
                    id_opportunita: (that.selectedAppDetails.opportunitaPRO ? that.selectedAppDetails.opportunitaPRO.id.replace("18x", "") : ""),
                    indirizzo: that.selectedAppDetails.attivita.indirizzo,
                    nome_attivita: "Analisi - " + that.selectedAppDetails.account.ragione_sociale,
                    orario_fine: orarioEsito2,
                    orario_inizio: orarioEsito1,
                    stato: "Planned",
                    telemarketing: that.selectedAppDetails.attivita.telemarketing,
                    tipo_attivita: "Meeting",
                    venditore: that.selectedAppDetails.attivita.venditore,
                    id_cliente: that.selectedAppDetails.account.id_cliente,
                    id_contatto: that.op.esito.model.autorizzatoreAnalisi,
                    cf_651: "New Business PMI"
                };
                DataService.postData(
                        "addActivity",
                        {
                            activity_vm: nuovaAttivita
                        },
                "addActivityPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_ripassoAct = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "ripasso1") {
                    console.log("busy for opOnSelected_ripassoAct - skip");
                    return;
                }
                var pad10 = Common.pad10;
                var oraRipasso = that.op.esito.model.oraRipasso;
                var dataRipasso = that.op.esito.model.dataRipasso;
                dataRipasso = new Date(dataRipasso);
                var dataRipasso1 = dataRipasso.getFullYear() + "-" + pad10(dataRipasso.getMonth() + 1) + "-" + pad10(dataRipasso.getDate());
                oraRipasso = new Date(oraRipasso);
                var oraRipassoH = oraRipasso.getHours();
                var oraRipassoM = oraRipasso.getMinutes();
                var orarioRipasso1 = pad10(oraRipassoH) + ":" + pad10(oraRipassoM);
                var orarioRipasso2 = pad10(oraRipassoH + 1) + ":" + pad10(oraRipassoM);
                console.log(orarioRipasso1, orarioRipasso2);
                var nuovaAttivita = {
                    data_fine: dataRipasso1,
                    data_inizio: dataRipasso1,
                    descrizione: that.selectedAppDetails.attivita.descrizione + "\r[Ripasso venditore]\r" + that.op.esito.model.noteRipasso,
                    durata_ore: "1",
                    id_opportunita: that.selectedAppDetails.opportunita.id_opportunita,
                    indirizzo: that.selectedAppDetails.attivita.indirizzo,
                    nome_attivita: that.selectedAppDetails.attivita.nome_attivita,
                    orario_fine: orarioRipasso2,
                    orario_inizio: orarioRipasso1,
                    stato: "Planned",
                    telemarketing: that.selectedAppDetails.attivita.telemarketing,
                    tipo_attivita: "Meeting",
                    venditore: that.selectedAppDetails.attivita.venditore,
                    id_cliente: that.selectedAppDetails.account.id_cliente,
                    id_contatto: that.op.esito.model.autorizzatoreAnalisi
                };
                DataService.postData(
                        "addActivity",
                        {
                            activity_vm: nuovaAttivita
                        },
                "addActivityPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_potPro = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "check1") { //<<<<<<
                    console.log("busy for opOnSelected_potPro - skip");
                    return;
                }
                var pad10 = Common.pad10;
                var dataEsito = that.op.esito.model.dataEsito;
                dataEsito = new Date(dataEsito);
                var dataEsito1 = dataEsito.getFullYear() + "-" + pad10(dataEsito.getMonth() + 1) + "-" + pad10(dataEsito.getDate());
                var nuovaOpportunita = {
                    nome_opportunita: "PROGETTO",
                    fonte_lead: that.selectedAppDetails.opportunita.fonte_lead,
                    ammontare: "10000",
                    data_chiusura_attesa: dataEsito1,
                    prossimo_step: "Visita analista",
                    stadio_vendita: "Prospecting",
                    probabilita: "50",
                    ammontare_pesato: "5000",
                    telemarketing: that.selectedAppDetails.opportunita.telemarketing,
                    venditore: that.selectedAppDetails.opportunita.venditore,
                    analista: "Da assegnare",
                    esito_conferma: "Da confermare",
                    pot_analisi_collegata: that.selectedAppDetails.opportunita.id_opportunita,
                    id_cliente: that.selectedAppDetails.account.id_cliente,
                    descrizione: that.op.esito.model.polpettone
                };
                DataService.postData(
                        "addPotential",
                        {
                            potential_vm: nuovaOpportunita
                        },
                "addPotentialPerformed",
                        {},
                        function (t) {
                            if (angular.isObject(t[1].response.data)) {
                                that.selectedAppDetails.opportunitaPRO = angular.copy(t[1].response.data);
                                console.log(that.selectedAppDetails.opportunitaPRO);
                            }
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_quoAna = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "check3") { //<<<<<<
                    console.log("busy for opOnSelected_quoAna - skip");
                    return;
                }
                var pad10 = Common.pad10;
                var dataEsito = that.op.esito.model.dataEsito;
                dataEsito = new Date(dataEsito);
                var dataEsito1 = dataEsito.getFullYear() + "-" + pad10(dataEsito.getMonth() + 1) + "-" + pad10(dataEsito.getDate());
                var nuovaQuo = {
                    id_cliente: that.selectedAppDetails.account.id_cliente,
                    nome_quote: "Analisi",
                    id_contatto: that.op.esito.model.autorizzatoreAnalisi,
                    citta: that.selectedAppDetails.account.citta,
                    cap: that.selectedAppDetails.account.cap,
                    provincia: that.selectedAppDetails.account.provincia,
                    indirizzo: that.selectedAppDetails.account.via,
                    id_opportunita: that.selectedAppDetails.opportunita.id_opportunita,
                    stato_quote: "Delivered",
                    descrizione: "Preventivo per analisi aziendale",
                    valido_fino: dataEsito1,
                    formato_tassazione: "group",
                    telemarketing: that.selectedAppDetails.opportunita.telemarketing,
                    venditore: that.selectedAppDetails.opportunita.venditore,
                    analista: "Da assegnare",
                    consulente: "Da assegnare",
                    listPrice1: that.op.esito.model.valoreEsito
                };
                DataService.postData(
                        "addQuote",
                        {
                            quote_vm: nuovaQuo
                        },
                "addQuotePerformed",
                        {},
                        function (t) {
//                            var logged = that.logger[1].putLog({
//                                piva: that.selectedAppDetails.account.partita_iva,
//                                what: ['op', "Vtiger", "quotes", "Creation"],
//                                parameters: {
//                                    tmk: that.selectedAppDetails.account.telemarketing,
//                                    com: that.selectedAppDetails.account.venditore,
//                                    pot: that.selectedAppDetails.opportunita.id_opportunita
//                                },
//                                comment: that.op.rifisso.model.noteRifisso,
//                                esito: true,
//                                parent: 0
//                            });
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_checkCloseAct = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "check4") { /////<<<<<<
                    console.log("busy for opOnSelected_checkCloseAct - skip");
                    return;
                }
                DataService.postData(
                        "editActivity",
                        {
                            activity_vm: {
                                id_attivita: that.selectedAppDetails.attivita.id_attivita,
                                description: that.selectedAppDetails.attivita.descrizione + "\r[Check]\r",
                                stato: "Held"
                            }
                        },
                "editActivityPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_ripassoCloseAct = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "ripasso2") {
                    console.log("busy for opOnSelected_ripassoAct - skip");
                    return;
                }
                DataService.postData(
                        "editActivity",
                        {
                            activity_vm: {
                                id_attivita: that.selectedAppDetails.attivita.id_attivita,
                                description: that.selectedAppDetails.attivita.descrizione + "\r[Ripasso venditore]\r" + that.op.esito.model.noteRipasso,
                                stato: "Held"
                            }
                        },
                "editActivityPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_nocheckcloseAct = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "nocheck1") {
                    console.log("busy for opOnSelected_nocheckcloseAct - skip");
                    return;
                }
                DataService.postData(
                        "editActivity",
                        {
                            activity_vm: {
                                id_attivita: that.selectedAppDetails.attivita.id_attivita,
                                description: that.selectedAppDetails.attivita.descrizione + "\r[No check]\r" + that.op.esito.model.noteNoCheck,
                                stato: "Held"
                            }
                        },
                "editActivityPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_rifissocloseAct = function (sCallback, eCallback) {
                if (that.op.rifisso.model.updating !== "rifisso1") {
                    console.log("busy for opOnSelected_rifissocloseAct - skip");
                    return;
                }
                DataService.postData(
                        "editActivity",
                        {
                            activity_vm: {
                                id_attivita: that.selectedAppDetails.attivita.id_attivita,
                                description: that.selectedAppDetails.attivita.descrizione + "\r[Rifisso]\r" + that.op.rifisso.model.noteRifisso,
                                stato: "Held"
                            }
                        },
                "editActivityPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_rifissoMovePipe = function (sCallback, eCallback) {
                if (that.op.rifisso.model.updating !== "rifisso4") {
                    console.log("busy for opOnSelected_rifissoMovePipe - skip");
                    return;
                }
                var vendId = _.find($scope.vendors, function (v) {
                    return v.NOME + " " + v.COGNOME === that.selectedAppDetails.account.venditore;
                });
                if (vendId) {
                    vendId = vendId.USERID;
                }
                else {
                    throw Error("Cant find vendor userid");
                }
                DataService.postData(
                        "setLeadPipeStatus",
                        {
                            piva: that.selectedAppDetails.account.partita_iva,
                            newpipe: "4",
                            otherpars: {
                                venditore: vendId
                            }
                        },
                "setLeadPipeStatusPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_checkMovePipe = function (sCallback, eCallback) {
                if (that.op.esito.model.updating !== "check7") {
                    console.log("busy for opOnSelected_checkMovePipe - skip");
                    return;
                }
                var vendId = _.find($scope.vendors, function (v) {
                    return v.NOME + " " + v.COGNOME === that.selectedAppDetails.account.venditore;
                });
                if (vendId) {
                    vendId = vendId.USERID;
                }
                else {
                    throw Error("Cant find vendor userid");
                }
                DataService.postData(
                        "setLeadPipeStatus",
                        {
                            piva: that.selectedAppDetails.account.partita_iva,
                            newpipe: "10",
                            otherpars: {
                                venditore: vendId
                            }
                        },
                "setLeadPipeStatusPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.opOnSelected_noCheckMovePipe = function (sCallback, eCallback) {
                if (that.op.rifisso.model.updating !== "nocheck4") {
                    console.log("busy for opOnSelected_noCheckMovePipe - skip");
                    return;
                }
                var vendId = _.find($scope.vendors, function (v) {
                    return v.NOME + " " + v.COGNOME === that.selectedAppDetails.account.venditore;
                });
                if (vendId) {
                    vendId = vendId.USERID;
                }
                else {
                    throw Error("Cant find vendor userid");
                }
                DataService.postData(
                        "setLeadPipeStatus",
                        {
                            piva: that.selectedAppDetails.account.partita_iva,
                            newpipe: "0",
                            otherpars: {
                                venditore: vendId
                            }
                        },
                "setLeadPipeStatusPerformed",
                        {},
                        function (t) {
                            (sCallback ? sCallback() : "");
                        },
                        function (t) {
                            (eCallback ? eCallback() : "");
                        });
            };
            $scope.submitRifisso = function () {
                console.log(that.op.rifisso);
                if (that.op.rifisso.model.updating) { //blocco per evitare richieste multiple
                    return;
                }

                var model = that.op.rifisso.model;
                console.log("RIFISSO!");
                that.op.rifisso.buttons["Imposta rifisso"]["Conferma"].enabled = false;
                that.op.rifisso.buttons["Imposta rifisso"]["Annulla e torna ad agenda"].enabled = false;
                that.op.rifisso.model.updating = "rifisso1";
                $scope.opOnSelected_rifissocloseAct(
                        function () {
                            that.op.rifisso.model.updating = "rifisso2";
                            $scope.opOnSelected_rifissoAppendPotDescription(
                                    function () {
                                        that.op.rifisso.model.updating = "rifisso3";
                                        $scope.opOnSelected_setRifisso(function () {
                                            that.op.rifisso.model.updating = "rifisso4";
                                            $scope.opOnSelected_rifissoMovePipe(function () {
                                                location.reload();
                                            }, function () {
                                                alert("L'operazione non é andata a buon fine, dopo il reload della pagina sarà possibile riprovare")
                                                location.reload();
                                            });
                                        }, function () {
                                            alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                            that.op.rifisso.model.updating = false;
                                        });
                                    }, function () {
                                alert("L'operazione non é andata a buon fine, é possibile riprovare.")
                                that.op.rifisso.model.updating = false;
                            });
                        }, function () {
                    alert("L'operazione non é andata a buon fine, é possibile riprovare.")
                    that.op.rifisso.model.updating = false;
                });
            }
            $scope.opOnSelected_changeGrace = function (diff, sCallback, eCallback) {
                DataService.postData(
                        "editPotential",
                        {potential_vm: {id_opportunita: that.selectedAppDetails.opportunita.id_opportunita,
                                grace_scadenza: diff}},
                "editPotentialPerformed", {},
                        function () {
                            that.op.esito.model.updating = false;
                            (sCallback ? sCallback() : "");
                            that.op.esito.buttons["Supervisione"]["Applica"].enabled = true;
                            that.op.esito.buttons["Supervisione"]["Annulla e torna ad agenda"].enabled = true;
                            that.op.esito.buttons["Supervisione"]["Supervisione esito"].enabled = true;
                        }, function () {
                    alert("L'operazione non è andata a buon fine. E' possibile riprovare.");
                    that.op.esito.model.updating = false;
                    that.op.esito.buttons["Supervisione"]["Applica"].enabled = true;
                    that.op.esito.buttons["Supervisione"]["Annulla e torna ad agenda"].enabled = true;
                    that.op.esito.buttons["Supervisione"]["Supervisione esito"].enabled = true;
                    (eCallback ? eCallback() : "");
                });
            }
            $scope.submitGraceOnly = function () {
                if (that.op.esito.model.updating) { //blocco per evitare richieste multiple
                    return;
                }
                that.op.esito.buttons["Supervisione"]["Applica"].enabled = false;
                that.op.esito.buttons["Supervisione"]["Annulla e torna ad agenda"].enabled = false;
                that.op.esito.buttons["Supervisione"]["Supervisione esito"].enabled = false;
                that.op.esito.model.updating = "graceonly";
                var model = that.op.esito.model;
                console.log("NEW GRACE");
                console.log(model.supervisioneScadenza);
                console.log(that.selectedAppDetails.exp);
                var diff = model.supervisioneScadenza.getTime() - that.selectedAppDetails.exp.getTime();
                diff = parseInt(diff / (1000 * 60 * 60 * 24));
                if (that.selectedAppDetails.opportunita.grace_scadenza) {
                    diff += that.selectedAppDetails.opportunita.grace_scadenza;
                }
                $scope.opOnSelected_changeGrace(diff);
            }
            $scope.submitEsito = function () {
                console.log(that.op.esito);
                if (that.op.esito.model.updating) { //blocco per evitare richieste multiple
                    return;
                }

                var model = that.op.esito.model;
                switch (model.esito) {
                    case 'check':
                        console.log("CHECK!");
                        that.op.esito.buttons["Check - Venduto"]["Conferma"].enabled = false;
                        that.op.esito.buttons["Check - Venduto"]["Annulla e torna ad agenda"].enabled = false;
                        that.op.esito.model.updating = "check1";
                        $scope.opOnSelected_potPro(function () {
                            that.op.esito.model.updating = "check2";
                            $scope.opOnSelected_checkAct(function () {
                                that.op.esito.model.updating = "check3";
                                $scope.opOnSelected_quoAna(function () {
                                    that.op.esito.model.updating = "check4";
                                    $scope.opOnSelected_checkCloseAct(function () {
                                        that.op.esito.model.updating = "check5";
                                        $scope.opOnSelected_checkAppendPotDescription(function () {
                                            that.op.esito.model.updating = "check6";
                                            $scope.opOnSelected_setCheck(function () {
                                                that.op.esito.model.updating = "check7";
                                                $scope.opOnSelected_checkMovePipe(function () {
                                                    location.reload();
                                                }, function () {
                                                    alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                                    that.op.esito.buttons["Check - Venduto"]["Conferma"].enabled = true;
                                                    that.op.esito.buttons["Check - Venduto"]["Annulla e torna ad agenda"].enabled = true;
                                                    that.op.esito.model.updating = false;
                                                });
                                            }, function () {
                                                alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                                that.op.esito.buttons["Check - Venduto"]["Conferma"].enabled = true;
                                                that.op.esito.buttons["Check - Venduto"]["Annulla e torna ad agenda"].enabled = true;
                                                that.op.esito.model.updating = false;
                                            });
                                        }, function () {
                                            alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                            that.op.esito.buttons["Check - Venduto"]["Conferma"].enabled = true;
                                            that.op.esito.buttons["Check - Venduto"]["Annulla e torna ad agenda"].enabled = true;
                                            that.op.esito.model.updating = false;
                                        });
                                    }, function () {
                                        alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                        that.op.esito.buttons["Check - Venduto"]["Conferma"].enabled = true;
                                        that.op.esito.buttons["Check - Venduto"]["Annulla e torna ad agenda"].enabled = true;
                                        that.op.esito.model.updating = false;
                                    });
                                }, function () {
                                    alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                    that.op.esito.buttons["Check - Venduto"]["Conferma"].enabled = true;
                                    that.op.esito.buttons["Check - Venduto"]["Annulla e torna ad agenda"].enabled = true;
                                    that.op.esito.model.updating = false;
                                });
                            }, function () {
                                alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                that.op.esito.buttons["Check - Venduto"]["Conferma"].enabled = true;
                                that.op.esito.buttons["Check - Venduto"]["Annulla e torna ad agenda"].enabled = true;
                                that.op.esito.model.updating = false;
                            });
                        }, function () {
                            alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                            that.op.esito.buttons["Check - Venduto"]["Conferma"].enabled = true;
                            that.op.esito.buttons["Check - Venduto"]["Annulla e torna ad agenda"].enabled = true;
                            that.op.esito.model.updating = false;
                        });
                        break;
                    case 'nocheck':
                        console.log("NO CHECK!");
                        that.op.esito.buttons["No Check"]["Conferma"].enabled = false;
                        that.op.esito.buttons["No Check"]["Annulla e torna ad agenda"].enabled = false;
                        if (that.op.esito.model.esclusioneNoCheck) {
                            DataService.getData("setEsclusione32",
                                    {
                                        piva: that.selectedAppDetails.account.partita_iva,
                                        disable: true
                                    }, "setEsclusione32Performed", {});
                        }
                        that.op.esito.model.updating = "nocheck1";
                        $scope.opOnSelected_nocheckcloseAct(
                                function () {
                                    that.op.esito.model.updating = "nocheck2";
                                    $scope.opOnSelected_nocheckAppendPotDescription(
                                            function () {
                                                that.op.esito.model.updating = "nocheck3";
                                                $scope.opOnSelected_setNocheck(function () {
                                                    that.op.rifisso.model.updating = "nocheck4";
                                                    $scope.opOnSelected_noCheckMovePipe(function () {
                                                        location.reload();
                                                    }, function () {
                                                        alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                                        that.op.esito.buttons["No Check"]["Conferma"].enabled = true;
                                                        that.op.esito.buttons["No Check"]["Annulla e torna ad agenda"].enabled = true;
                                                        that.op.esito.model.updating = false;
                                                    });
                                                }, function () {
                                                    alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                                    that.op.esito.buttons["No Check"]["Conferma"].enabled = true;
                                                    that.op.esito.buttons["No Check"]["Annulla e torna ad agenda"].enabled = true;
                                                    that.op.esito.model.updating = false;
                                                });
                                            }, function () {
                                        alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                        that.op.esito.buttons["No Check"]["Conferma"].enabled = true;
                                        that.op.esito.buttons["No Check"]["Annulla e torna ad agenda"].enabled = true;
                                        that.op.esito.model.updating = false;
                                    });
                                }, function () {
                            alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                            that.op.esito.buttons["No Check"]["Conferma"].enabled = true;
                            that.op.esito.buttons["No Check"]["Annulla e torna ad agenda"].enabled = true;
                            that.op.esito.model.updating = false;
                        });
                        break;
                    case 'ripasso':
                        console.log("RIPASSO!");
                        that.op.esito.buttons["Impostazione ripasso"]["Conferma"].enabled = false;
                        that.op.esito.buttons["Impostazione ripasso"]["Annulla e torna ad agenda"].enabled = false;
                        that.op.esito.model.updating = "ripasso1";
                        $scope.opOnSelected_ripassoAct(
                                function () {
                                    that.op.esito.model.updating = "ripasso2";
                                    $scope.opOnSelected_ripassoCloseAct(
                                            function () {
                                                that.op.esito.model.updating = "ripasso3";
                                                $scope.opOnSelected_ripassoAppendPotDescription(
                                                        function () {
                                                            that.op.esito.model.updating = "ripasso4";
                                                            $scope.opOnSelected_setRipasso(function () {
                                                                location.reload();
                                                            }, function () {
                                                                alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                                                that.op.esito.buttons["Impostazione ripasso"]["Conferma"].enabled = true;
                                                                that.op.esito.buttons["Impostazione ripasso"]["Annulla e torna ad agenda"].enabled = true;
                                                                that.op.esito.model.updating = false;
                                                            });
                                                            that.op.esito.model.updating = false;
                                                        }, function () {
                                                    alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                                    that.op.esito.buttons["Impostazione ripasso"]["Conferma"].enabled = true;
                                                    that.op.esito.buttons["Impostazione ripasso"]["Annulla e torna ad agenda"].enabled = true;
                                                    that.op.esito.model.updating = false;
                                                });
                                            }, function () {
                                        alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                                        that.op.esito.buttons["Impostazione ripasso"]["Conferma"].enabled = true;
                                        that.op.esito.buttons["Impostazione ripasso"]["Annulla e torna ad agenda"].enabled = true;
                                        that.op.esito.model.updating = false;
                                    });
                                }, function () {
                            alert("L'operazione non é andata a buon fine, é possibile riprovare.");
                            that.op.esito.buttons["Impostazione ripasso"]["Conferma"].enabled = true;
                            that.op.esito.buttons["Impostazione ripasso"]["Annulla e torna ad agenda"].enabled = true;
                            that.op.esito.model.updating = false;
                        });
                        //change status pot analisi
                        break;
                    default:
                        break;
                }
            }


            $scope.initEditDatiCliente = function () {
                that.op.esito.newDatiCliente = {account: angular.copy(that.selectedAppDetails.account)};
                that.op.esito.updatingCliente = false;
                that.op.esito.editDatiCliente = true;
                that.op.esito.buttons["Dati cliente"]["Dati corretti"].enabled = false;
                that.op.esito.buttons["Dati cliente"]["Annulla e torna ad agenda"].enabled = false;
            };
            $scope.cancelEditDatiCliente = function () {
                //salvataggio
                delete(that.op.esito.newDatiCliente);
                that.op.esito.editDatiCliente = false;
                that.op.esito.buttons["Dati cliente"]["Dati corretti"].enabled = true;
                that.op.esito.buttons["Dati cliente"]["Annulla e torna ad agenda"].enabled = true;
            };
            $scope.saveEditDatiCliente = function () {
                //salvataggio
                that.op.esito.updatingCliente = true;
                DataService.postData("editAccount", {account_vm: {
                        id_cliente: that.op.esito.newDatiCliente.account.id_cliente,
                        ragione_sociale: that.op.esito.newDatiCliente.account.ragione_sociale,
                        via: that.op.esito.newDatiCliente.account.via,
                        citta: that.op.esito.newDatiCliente.account.citta,
                        cap: that.op.esito.newDatiCliente.account.cap,
                        provincia: that.op.esito.newDatiCliente.account.provincia,
                        regione: that.op.esito.newDatiCliente.account.regione,
                        email: that.op.esito.newDatiCliente.account.email,
                        sito: that.op.esito.newDatiCliente.account.sito,
                        fascia_fatturato_com: that.op.esito.newDatiCliente.account.fascia_fatturato_com,
                        forma_giuridica_com: that.op.esito.newDatiCliente.account.forma_giuridica_com,
                        classe_dipendenti_com: that.op.esito.newDatiCliente.account.classe_dipendenti_com
                    }}, "editAccountPerformed", {}, function (t) {

                    $scope.selectApp($scope.selectedApp, null, function () {
                        that.op.esito.updatingCliente = false;
                        that.op.esito.editDatiCliente = false;
                        that.op.esito.buttons["Dati cliente"]["Dati corretti"].enabled = true;
                        that.op.esito.buttons["Dati cliente"]["Annulla e torna ad agenda"].enabled = true;
                    });
                }, function (t) {
                    alert("Attenzione, si è verificato un errore nell'aggiornamento dei dati cliente, riprovare.");
                    $scope.cancelEditDatiCliente();
                }
                );
            };
            $scope.initEditDatiContatti = function (index) {
                that.op.esito.newDatiContatti = angular.copy(_.find(that.selectedAppDetails.contatti, {id_contatto: index}));
                that.op.esito.editDatiContatti = index;
                that.op.esito.buttons["Contatti"]["Contatti corretti"].enabled = false;
                that.op.esito.buttons["Contatti"]["Annulla e torna ad agenda"].enabled = false;
            };
            $scope.initCreaNuovoContatto = function () {
                that.op.esito.editDatiContatti = -1;
                that.op.esito.newDatiContatti = {
                    cellulare: "",
                    cognome: "",
                    email: "",
                    fax: "",
                    id_contatto: "-1",
                    nome: "",
                    ruolo: "",
                    telefono: ""
                };
                that.op.esito.buttons["Contatti"]["Contatti corretti"].enabled = false;
                that.op.esito.buttons["Contatti"]["Annulla e torna ad agenda"].enabled = false;
            };
            $scope.cancelEditDatiContatti = function () {
                //salvataggio
                delete(that.op.esito.newDatiContatti);
                that.op.esito.editDatiContatti = false;
                that.op.esito.buttons["Contatti"]["Contatti corretti"].enabled = true;
                that.op.esito.buttons["Contatti"]["Annulla e torna ad agenda"].enabled = true;
            };
            $scope.saveEditDatiContatti = function () {
                //salvataggio

                that.op.esito.updatingContatti = true;
                var service = "";
                if (!that.op.esito.newDatiContatti.id_contatto || that.op.esito.newDatiContatti.id_contatto === "-1" || that.op.esito.newDatiContatti.id_contatto === "") {
                    service = "addContact";
                }
                else {
                    service = "editContact";
                }
                DataService.postData(service, {contact_vm: {
                        id_contatto: that.op.esito.newDatiContatti.id_contatto,
                        nome: that.op.esito.newDatiContatti.nome,
                        cognome: that.op.esito.newDatiContatti.cognome,
                        email: that.op.esito.newDatiContatti.email,
                        fax: that.op.esito.newDatiContatti.fax,
                        ruolo: that.op.esito.newDatiContatti.ruolo,
                        telefono: that.op.esito.newDatiContatti.telefono,
                        cellulare: that.op.esito.newDatiContatti.cellulare,
                        id_cliente: that.selectedAppDetails.account.id_cliente
                    }}, service + "Performed", {}, function (t) {
                    $scope.selectApp($scope.selectedApp, null, function () {
                        that.op.esito.updatingContatti = false;
                        that.op.esito.editDatiContatti = false;
                        that.op.esito.buttons["Contatti"]["Contatti corretti"].enabled = true;
                        that.op.esito.buttons["Contatti"]["Annulla e torna ad agenda"].enabled = true;
                    });
                }, function (t) {
                    alert("Attenzione, si è verificato un errore nell'aggiornamento dei dati cliente, riprovare.");
                    $scope.cancelEditDatiCliente();
                }
                );
            };
            var timezone = new Date().getTimezoneOffset();
            timezone = parseInt(timezone / 60);
            var absTimezone = Math.abs(timezone);
            if (timezone < 0) {
                timezone = '-' + (absTimezone < 10 ? '0' : '') + absTimezone + '00';
            }
            else {
                timezone = '+' + (absTimezone < 10 ? '0' : '') + absTimezone + '00';
            }
            this.op = {
                esito: {
                    selectedAppDetails: that.selectedAppDetails,
                    editDatiContatti: false,
                    editDatiCliente: false,
                    fasciaFatturatoList: Tsnw.accountUtilities.getFasciaFatturatoList(),
                    classeDipendentiList: Tsnw.accountUtilities.getClasseDipendentiList(),
                    formaGiuridicaList: Tsnw.accountUtilities.getFormaGiuridicaList(),
                    modelOptions: {
                        timezone: timezone
                    },
                    updating: false
                },
                ripasso: {},
                rifisso: {
                },
                fs: {
                    rifisso: [
                        {
                            legend: "Imposta rifisso",
                            name: "f__rifisso",
                            buttons: [
                                {
                                    label: "Conferma",
                                    action: "do('submitRifisso')",
                                    enableFcn: 'validateStep'
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                }
                            ],
                            validationMess: [
                                {
                                    type: "radio",
                                    name: "motivazioneRifisso",
                                    required: "Non &eacute; stata scelta una motivazione valida."
                                },
                                {
                                    type: "textarea",
                                    name: "noteRifisso",
                                    required: "&Eacute; necessario compilare il campo note."
                                }
                            ]
                        }
                    ],
                    esito: [
                        {
                            legend: "Dati cliente",
                            name: "f__daticliente",
                            buttons: [
                                {
                                    label: "Dati corretti",
                                    action: "setInModel('updating',false); goToStep('Contatti')"
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                }
                            ]
                        },
                        {
                            legend: "Contatti",
                            name: "f__contatti",
                            buttons: [
                                {
                                    label: "Contatti corretti",
                                    action: "setInModel('autorizzatoreAnalisi',do('getConPrev')); setInModel('polpettone',do('autoValuePolpettone')); setInModel('contattiList',do('makeContattiList')); goToStep('Rapporto di vendita');"
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                }
                            ]
                        },
                        {
                            legend: "Esito vendita",
                            name: "f__esito",
                            buttons: [
                                {
                                    label: "Check",
                                    action: "goToStep('Check - Data analisi'); setInModel('esito','check');"
                                },
                                {
                                    label: "No Check",
                                    action: "goToStep('No Check'); setInModel('esito','nocheck');"
                                },
                                {
                                    label: "Richiedi ripasso",
                                    action: "goToStep('Impostazione ripasso'); setInModel('esito','ripasso');"
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                }
                            ]
                        },
                        {
                            legend: "No Check",
                            name: "f__nocheck",
                            buttons: [
                                {
                                    label: "Conferma",
                                    action: "do('submitEsito')",
                                    enableFcn: 'validateStep'
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                }
                            ],
                            validationMess: [
                                {
                                    type: "radio",
                                    name: "motivazioneNoCheck",
                                    required: "Campo obbligatorio"
                                },
                                {
                                    type: "textarea",
                                    name: "noteNocheck",
                                    required: "Campo obbligatorio"
                                }
                            ]
                        },
                        {
                            legend: "Check - Data analisi",
                            name: "f__checkdataanalisi",
                            buttons: [
                                {
                                    label: "Avanti",
                                    action: "goToStep('Check - Venduto')",
                                    enableFcn: 'validateStep'
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                }
                            ],
                            validationMess: [
                                {
                                    type: "date",
                                    name: "dataesito",
                                    required: "Campo obbligatorio"
                                },
                                {
                                    type: "time",
                                    name: "oraesito",
                                    required: "Campo obbligatorio"
                                }
                            ]
                        },
                        {
                            legend: "Check - Venduto",
                            name: "f__checkvenduto",
                            buttons: [
                                {
                                    label: "Conferma",
                                    action: "do('submitEsito')",
                                    enableFcn: 'validateStep'
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                },
                                {
                                    label: "Set stato pot check",
                                    action: "do('opOnSelected_setCheck')"
                                },
                                {
                                    label: "Crea pot pro",
                                    action: "do('opOnSelected_potPro')"
                                },
                                {
                                    label: "Crea quo ana",
                                    action: "do('opOnSelected_quoAna')"
                                },
                                {
                                    label: "Crea nuova att",
                                    action: "do('opOnSelected_checkAct')"
                                },
                                {
                                    label: "Aggiorna vecchia att",
                                    action: "do('opOnSelected_checkCloseAct')"
                                }
                            ]
                        },
                        {
                            legend: "Rapporto di vendita",
                            name: "f__rapportovendita",
                            buttons: [
                                {
                                    label: "Avanti",
                                    action: "goToStep('Esito vendita')",
//                                    action: "(model.esito === 'check' ? goToStep('Check - Data analisi') : (model.esito === 'nocheck' ? goToStep('No Check') : goToStep('Richiedi ripasso')))",
                                    enableFcn: 'validateStep'
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                }
                            ],
                            validationMess: [
                                {
                                    type: "radio",
                                    name: "autorizzatoreAnalisi",
                                    required: "Campo obbligatorio"
                                },
                                {
                                    type: "textarea",
                                    name: "polpettone",
                                    required: "Campo obbligatorio"
                                }
                            ]
                        },
                        {
                            legend: "Impostazione ripasso",
                            name: "f__ripasso",
                            buttons: [
                                {
                                    label: "Conferma",
                                    action: "do('submitEsito')",
                                    enableFcn: 'validateStep'
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                }
                            ],
                            validationMess: [
                                {
                                    type: "date",
                                    name: "dataRipasso",
                                    required: "Campo obbligatorio"
                                },
                                {
                                    type: "time",
                                    name: "oraRipasso",
                                    required: "Campo obbligatorio"
                                },
                                {
                                    type: "textarea",
                                    name: "noteRipasso",
                                    required: "Campo obbligatorio"
                                }
                            ]
                        },
                        {
                            legend: "Supervisione",
                            name: "f__supervisione",
                            buttons: [
                                {
                                    label: "Supervisione esito",
                                    action: "goToStep('Dati cliente')",
                                    enableFcn: 'validateStep'
                                },
                                {
                                    label: "Annulla e torna ad agenda",
                                    action: "do('goToAgenda')"
                                },
                                {
                                    label: "Applica",
                                    action: "do('submitGraceOnly')"
                                }
                            ]
                        },
                    ]
                }
            };
            $scope.globalStatus = {
                analisiDati: true, agenda: false, esito: false, rifisso: false, ripasso: false, storico: false
            }
            $scope.setGlobalStatus = function (asd) {
                if (typeof ($scope.globalStatus[asd]) !== "undefined") {
                    for (var i in $scope.globalStatus) {
                        if ($scope.globalStatus.hasOwnProperty(i)) {
                            $scope.globalStatus[i] = false;
                        }
                    }
                    $scope.globalStatus[asd] = true;
                }
            }



            $scope.counter = 0;
            $scope.progress = function (app, scope) {
                if (!scope.exp) {
                    return null;
                }
                var linMapOre = Common.linMap(24, 2, 0, 50, true);
                var linMapMinuti = Common.linMap(120, 0, 50, 100, true);
                if (scope.exp[1] === "ore") {
                    if (scope.exp[0] > 24) {
                        return ["success", 0];
                    }
                    if (scope.exp[0] > 6) {
                        return ["success", linMapOre(scope.exp[0])];
                    }
                    return ["warning", linMapOre(scope.exp[0])];
                }
                if (scope.exp[1] === "minuti") {
                    if (scope.exp[0] > 0) {
                        return ["danger progress-bar-striped active", linMapMinuti(scope.exp[0])];
                    }
                    return ["danger", 100];
                }
            }
            $scope.expSet = function (app, scope) {
                var updated = new Date();
                if (scope.updated && scope.updated.getTime && Math.abs((scope.updated.getTime() - updated.getTime())) < 8000) {
                    return true;
                }

                if (app.status === "_COMAPP_NOCHECK" || app.status === "_COMAPP_CHECK" || app.status === "_COMAPP_SYSTEMCANCELLED"
                        || app.status === "_COMAPP_AUTOCLOSE") {
                    scope.updated = new Date();
                    return true;
                }
                var appDate = Common.extractFromObject(app, $scope.menuVisiteConf.dater);
                if (!appDate) {
                    console.error("Fatal error - expSet: invalid date", app);
                    return false;
                }
                scope.localExp = new Date();
                scope.updated = new Date();
                if (!Common.isValidDate(appDate)) {
                    appDate = new Date(appDate);
                }
                if (!Common.isValidDate(appDate)) {
                    console.error(app);
                    throw Error("Appuntamento directive - Invalid date in app");
                }
                if (scope.localStatus && scope.localStatus[0] === "Visita da effettuare") {
                    scope.updated = new Date();
                    scope.localExp.setTime(appDate.getTime());
                    return true;
                }
                else {
                    var grace = app.grace_scadenza;
                    scope.localExp.setTime($scope.expCalculator(app));
                }
                return true;
            }

            $scope.expCalculator = function (app) {
                var grace = app.grace_scadenza;
                var appDate = Common.extractFromObject(app, $scope.menuVisiteConf.dater);
                return appDate.getTime() + (1000 * 60 * 60 * 24 * (1 + (grace ? grace : 0)));
            }

            $scope.timeStatus = function (app, scope) {
                var now = new Date();
                if (angular.isArray(app.status)) {
//                    console.log("STATUS ARRAY", app.status);
                    if (app.status[0] === "Risolto") {
                        scope.localStatus = ["Risolto", "success", "success", app.status[1], 1];
                        scope.show = {esito: false, ripasso: false, rifisso: false, storia: false, bar: false};
                        app.correcting = 2;
                        return;
                    }
                    else if (app.status[0] === "Errore") {
                        scope.localStatus = ["Errore", "danger", "danger", app.status[1], 1];
                        scope.show = {esito: false, ripasso: false, rifisso: false, storia: false, bar: false};
                        app.correcting = 3;
                        return;
                    }
                }
                if (app.status === "_COMAPP_SYSTEMCANCELLED") {
                    scope.localStatus = ["Bloccato", "danger", "danger"];
                    scope.show = {esito: false, ripasso: false, rifisso: false, storia: true, bar: false};
                    return;
                }
                if (app.status === "_COMAPP_AUTOCLOSE") {
                    scope.localStatus = ["Chiuso", "danger", "danger"];
                    scope.show = {esito: false, ripasso: false, rifisso: false, storia: true, bar: true};
                    return;
                }
                if (app.status === "_COMAPP_NOCHECK") {
                    scope.localStatus = ["Persa", "default", "danger"];
                    scope.show = {esito: false, ripasso: false, rifisso: false, storia: true, bar: false};
                    return;
                }
                if (app.status === "_COMAPP_CHECK") {
                    scope.localStatus = ["Vinto", "success", "success"];
                    scope.show = {esito: false, ripasso: false, rifisso: false, storia: true, bar: false};
                    return;
                }

                if (app.status === "_COMAPP_CALLAGAIN") {
                    scope.localStatus = ["Richiesto rifisso", "info", "info"];
                    scope.show = {esito: false, ripasso: false, rifisso: false, storia: true, bar: false};
                    return;
                }
                var appDate = Common.extractFromObject(app, $scope.menuVisiteConf.dater);
                if (!Common.isValidDate(appDate)) {
                    appDate = new Date(appDate);
                }
                if (!Common.isValidDate(appDate)) {
                    console.error(app);
                    throw Error("Appuntamento directive - Invalid date in app");
                }
                var diff = (appDate.getTime() - now.getTime());
                if (app.status === "_COMAPP_QUALIFIED") {
                    if (diff > 0) {
                        scope.localStatus = ["Visita da effettuare", "primary", "primary", "APPUNTAMENTO TRA"];
                        scope.show = {esito: false, ripasso: false, rifisso: true, storia: true, bar: true};
                        return;
                    }
                    else if (diff <= 0 && diff >= -(60 * 60 * 1000)) {
                        scope.localStatus = ["Visita in corso", "primary", "primary"];
                        scope.show = {esito: false, ripasso: false, rifisso: true, storia: true, bar: false};
                        return;
                    }
                    else {
                        scope.localStatus = ["Attesa esito", "warning", "warning", "ESITABILE ENTRO"];
                        scope.show = {esito: true, ripasso: true, rifisso: true, storia: true, bar: true};
                        return;
                    }
                }
                if (app.status === "_COMAPP_PASSAGAIN") {
                    if (diff > 0) {
                        scope.localStatus = ["Visita da effettuare - Ripasso", "info", "info", "APPUNTAMENTO TRA"];
                        scope.show = {esito: true, ripasso: true, rifisso: false, storia: true, bar: true};
                        return;
                    }
                    else if (diff <= 0 && diff >= -(60 * 60 * 1000)) {
                        scope.localStatus = ["Visita in corso - Ripasso", "info", "info"];
                        scope.show = {esito: false, ripasso: false, rifisso: false, storia: true, bar: false};
                        return;
                    }
                    else {
                        scope.localStatus = ["Attesa esito - Ripasso", "info", "info", "ESITABILE ENTRO"];
                        scope.show = {esito: true, ripasso: true, rifisso: false, storia: true, bar: true};
                        return;
                    }
                }
                scope.localStatus = ["Errore", "danger", "danger", app.status, 1];
                scope.show = {esito: false, ripasso: false, rifisso: false, storia: true, bar: false};
            }
            $scope.panelAuxStyle = function (app, scope) {
                var out = {};
                if (scope.menuStatus.showingDetails === app[scope.conf.ider]) {
                    out["box-shadow"] = "10px 10px 5px";
                }
                return out;
            }
            $scope.filterByVendors = function (app, scope) {
                if (!app.venditore || $filter("userInfo")($scope.selectedVendors, $scope.vendors).indexOf(app.venditore) === -1)
                    return false;
                return true;
            }
            $scope.transDel = function (index) {
                return {'transition-delay': index * 500 + 'ms', '-webkit-transition-delay': index * 500 + 'ms'};
            }
            $scope.$watch("validated.errors.length", function (n, o) {
                if (!n && o && $scope.st.get() === "analisiDati") {
                    $scope.st.put("richiestaDati");
                    delete($scope.apps);
                    delete($scope.validated);
                    $scope.loadAppuntamenti();
                }
            })
            $scope.loadAppuntamenti = function () {
                $scope.st.put("richiestaDati");
                DataService.getData("getAppuntamenti", {vendors: JSON.stringify($scope.selectedVendors)}, "getAppuntamentiPerformed", {},
                        function (t) {
                            $scope.st.put("analisiDati");
                            $timeout(function () {
                                $scope.validated = {};
                                $scope.validator.check(t[1].response.data, $scope.validated);
                                if (!$scope.validated.errors || !$scope.validated.errors.length) {
                                    $scope.apps = $scope.validated.good;
                                    $scope.st.put("agenda");
                                    $timeout(function () {
                                        $scope.menuVisiteConf.init();
                                    }, 500);
                                }
                            }, 500);
                            return;
                        },
                        function (t) {
                            alert("Attenzione, non é stato possibile scaricare i dati degli appuntamenti");
                            throw Error("Attenzione, non é stato possibile scaricare i dati degli appuntamenti");
                            that.destroyPage();
                        }
                );
            };
            $scope.makeContattiList = function () {
                var cont = that.selectedAppDetails.contatti;
                var out = [];
                for (var i in cont) {
                    console.log(cont[i]);
                    out.push({id: cont[i].id_contatto, nome: cont[i].nome + " " + cont[i].cognome + " (" + cont[i].ruolo + ")"});
                }
                console.log(out);
                return out;
            }

            $scope.correctApp = function (errorIndex) {
                if (!$scope.validated || !$scope.validated.errors || !$scope.validated.errors[errorIndex]) {
                    return;
                }
                var error = $scope.validated.errors[errorIndex];
                $scope.validated.errors[errorIndex].working = true;
                var app = error.datum;
                var problem = error.error;
//                console.log("correctApp");
//                console.log(app);
//                console.log(problem);
                switch (problem) {
                    case 'Errore calendario':
                        DataService.getData("corrNoActivityError", {potId: app.id_opportunita}, "corrNoActivityErrorPerformed", {},
                                function (t) {
                                    var response;
                                    if (t[1].response.data) {
                                        response = Common.superRealTrim(t[1].response.data.toString().replace(/"/g, ''));
                                    }
                                    else {
                                        response = Common.superRealTrim(t[1].response.toString().replace(/"/g, ''));
                                    }
                                    $scope.validated.errors[errorIndex].error = response;
                                    delete($scope.validated.errors[errorIndex].unsolved);
                                    delete($scope.validated.errors[errorIndex].working);
                                    $scope.validated.errors[errorIndex].solved = true;
                                    $timeout(function () {
                                        $scope.validated.errors.splice(errorIndex, 1);
                                    }, 1000);
                                },
                                function (t) {
                                    var response;
                                    if (t[1].response.data) {
                                        response = Common.superRealTrim(t[1].response.data.toString().replace(/"/g, ''));
                                    }
                                    else {
                                        response = Common.superRealTrim(t[1].response.toString().replace(/"/g, ''));
                                    }
                                    $scope.validated.errors[errorIndex].error = response;
                                    delete($scope.validated.errors[errorIndex].solved);
                                    delete($scope.validated.errors[errorIndex].working);
                                    $scope.validated.errors[errorIndex].unsolved = true;
                                });
                        break;
                    case 'Errore stato non valido':
                        DataService.getData("inferComAppStatus", {potentialid: app.id_opportunita}, "inferComAppStatusPerformed", {},
                                function (t) {
                                    if (t[1].response.data) {
//                                        console.log("--" + t[1].response.data + "--");
                                        var status = Common.superRealTrim(t[1].response.data.toString().replace(/"/g, ''));
//                                        console.log(status);
                                        DataService.getData("comAppStatus", {potentialid: app.id_opportunita, status: status, comment: "autocorrection"},
                                        "comAppStatusPerformed", {},
                                                function (t) {
                                                    var response;
                                                    if (t[1].response.data) {
                                                        response = Common.superRealTrim(t[1].response.data.toString().replace(/"/g, ''));
                                                    }
                                                    else {
                                                        response = Common.superRealTrim(t[1].response.toString().replace(/"/g, ''));
                                                    }
                                                    $scope.validated.errors[errorIndex].error = response;
                                                    delete($scope.validated.errors[errorIndex].unsolved);
                                                    delete($scope.validated.errors[errorIndex].working);
                                                    $scope.validated.errors[errorIndex].solved = true;
                                                    $timeout(function () {
                                                        $scope.validated.errors.splice(errorIndex, 1);
                                                    }, 1000);
                                                },
                                                function (t) {
                                                    var response;
                                                    if (t[1].response.data) {
                                                        response = Common.superRealTrim(t[1].response.data.toString().replace(/"/g, ''));
                                                    }
                                                    else {
                                                        response = Common.superRealTrim(t[1].response.toString().replace(/"/g, ''));
                                                    }
                                                    $scope.validated.errors[errorIndex].error = response;
                                                    delete($scope.validated.errors[errorIndex].solved);
                                                    delete($scope.validated.errors[errorIndex].working);
                                                    $scope.validated.errors[errorIndex].unsolved = true;
                                                })
                                    }
                                },
                                function (t) {
                                    console.log(t);
                                });
                        break;
                    default :
                        $scope.validated.errors[errorIndex].error = "La soluzione automatica di questo errore non é supportata. Contatta un amministratore, fornendo i dettagli del problema.";
                        delete($scope.validated.errors[errorIndex].solved);
                        delete($scope.validated.errors[errorIndex].working);
                        $scope.validated.errors[errorIndex].unsolved = true;
                        break;
                }
            };
            //            $scope.apps = asddata();             $scope.vendors = [];             $scope.selectedVendors = [];
            window.doGlobalSearch = function () {
                $scope.menuVisiteConf.toggleShowingSearch();
            };
            $scope.checkSelectionStatus = function () {
                var vC = $("#vendorChooser");
                $scope.globalStatus.vendorSelectionStatus = vC.hasClass("collapse") && vC.hasClass("in");
            }
            $scope.selectVendors = function (toggle) {
                if (toggle === 0) {
                    $scope.preSelectedVendors = [];
                    for (var i in $scope.selectedVendors) {
                        $scope.preSelectedVendors.push($scope.selectedVendors[i]);
                    }
                    $("#vendorChooser").show(500);
                }
                else if (toggle === 1) {
                    if ($scope.preSelectedVendors && angular.isArray($scope.preSelectedVendors) && $scope.preSelectedVendors.length) {
//                        console.log("selVend change");
                        delete($scope.apps);
                        delete($scope.validated);
                        $scope.selectedVendors = $scope.preSelectedVendors;
                        $scope.loadAppuntamenti();
                        $("#vendorChooser").hide(500);
                    }
                }
                else if (toggle === 2) {
                    $("#vendorChooser").hide(500);
                }
            };
//            $scope.$watch("selectedVendors", function (n, o) {
//                if (angular.isArray(n) && n.length) {
//                    console.log("selVend change", n, o);
//                    delete($scope.apps);
//                    delete($scope.validated);
//                    $scope.loadAppuntamenti();
//                }
//            }, true);
            $scope.opPar = {};
            DataService.initializeOnCurrentPage(
                    function () {
                        DataService.getData("authorizeGestioneVendite", {}, "authorizeGestioneVenditePerformed", {},
                                function (t) {
                                    var auth = t[1].response.data.auth;
                                    if (auth.supervisione) {
                                        $rootScope.supervisione = true;
                                    }
                                    DataService.getData("getCommerciali", {}, "getCommercialiPerformed", {},
                                            function (t) {
                                                $scope.vendors = [];
                                                if (auth.venditori) {
                                                    for (var kkk in t[1].response.data) {
                                                        for (var zzz in auth.venditori) {
                                                            if (auth.venditori[zzz] == t[1].response.data[kkk].USERID) {
                                                                $scope.vendors.push(t[1].response.data[kkk]);
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                                if ($scope.vendors.length === 1) {
                                                    $timeout(function () {
                                                        $scope.selectedVendors = [$scope.vendors[0].USERID];
                                                        $scope.loadAppuntamenti();
                                                    }, 300);
                                                }
                                            },
                                            function (t) {
                                                alert("Attenzione, non é stato possibile scaricare le anagrafiche dei consulenti commerciali.");
                                                that.destroyPage();
                                            }
                                    );
                                },
                                function (t) {
                                    alert("Attenzione, non é stato possibile accertare le autorizzazioni di pagina.");
                                    that.destroyPage();
                                });
                        DataService.getData("getVendLogDesc", {}, "getVendLogDescPerformed", {},
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
            this.configurazioneLogger = function () {
                this.logger = Logger.newLogger();
                Logger.getLogger(this.logger[0]).setLogGetter(function (args) {
                    that.logReloading = true;
                    return DataService.getData("getLog", {
                        piva: args.piva
                    },
                    "getLogPerformed", {},
                            function (t) {
                                Logger.getLogger(that.logger[0]).setData(t[1].response.data);
                                that.logReloading = false;
                            },
                            function (t) {
                                that.logReloading = false;
                            });
                });
                that.stopLog = function () {
                    Logger.getLogger(that.logger[0]).stopLogRefresher();
                    return true;
                }
                that.reloadLog = function (piva) {
                    var logConf = Logger.getLogger(that.logger[0]).getArgs();
                    if (!logConf || !logConf.piva || logConf.piva !== piva) {
                        Logger.getLogger(that.logger[0]).stopLogRefresher();
                        Logger.getLogger(that.logger[0]).startLogRefresher(60000, //scommentare in produzione
                                {
                                    piva: piva
                                });
                        Logger.getLogger(this.logger[0]).setLogDescriptor("logGeneral", $scope.opPar.logGeneralDesc, $scope.opPar.userList);
                    }
                    return true;
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
                    return DataService.postData("newPutLog", payload, "putLogPerformed", {});
                });
            };
            this.configurazioneLogger();
        }]);
    app.filter("listFilt", ['Common', function (Common) {
            return function (input) {
                return Common.listFilt(input);
            }
        }]);
    app.filter("userCanSeeVend", function () {
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
    app.filter("userInfo", function () {
        return function (input, list, format) {
            if (typeof (format) === "undefined") {
                format = "";
            }
            if (angular.isString(input) || !isNaN(input)) {
                for (var i in list) {
                    if (list[i].USERID && list[i].NOME && list[i].COGNOME && list[i].USERID == input) {
                        switch (format) {
                            case 'N':
                            case '1':
                                return list[i].NOME;
                                break;
                            case 'C':
                            case '2':
                                return list[i].COGNOME;
                                break;
                            default:
                                return list[i].NOME + " " + list[i].COGNOME;
                                break;
                        }
                    }
                }
            }
            else if (angular.isArray(input)) {
                var out = [];
                for (var kkk in input) {
                    for (var i in list) {
                        if (list[i].USERID && list[i].NOME && list[i].COGNOME && list[i].USERID == input[kkk]) {
                            switch (format) {
                                case 'N':
                                case '1':
                                    out.push(list[i].NOME);
                                    break;
                                case 'C':
                                case '2':
                                    out.push(list[i].COGNOME);
                                    break;
                                default:
                                    out.push(list[i].NOME + " " + list[i].COGNOME);
                                    break;
                            }
                        }
                    }
                }
                return out;
            }

            return input;
        }
    }
    );
})();



