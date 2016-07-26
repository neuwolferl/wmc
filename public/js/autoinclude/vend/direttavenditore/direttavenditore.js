(function () {
    var app = angular.module("InserimentoLead", ['DATAMODULE', 'COMMON', 'TSNWINCLUDE'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.controller("InserimentoLeadController", ["DataService", "$scope", "$rootScope", "$timeout", "Common", "Tsnw",
        function (DataService, $scope, $rootScope, $timeout, Common, Tsnw) {
            var that = this;
            DataService.initializeOnCurrentPage(function (a) {
                DataService.getData("authorizeGestioneVendite", {}, "authorizeGestioneVenditePerformed", {},
                        function (t) {
                            $scope.user = t[1].response.data.user;
                            $scope.auth = t[1].response.data.auth;
                            $scope.authvendors = $scope.auth.vendors;
                        }
                , function (t) {

                });
            }, function () {
                that.destroyPage();
            });
            this.destroyPage = function () {
                this.errorMask = true;
                $("error-mask").siblings().each(function () {
                    $(this).hide();
                });
            };

            this.lockCauses = [
                {cause: "ATECO", value: "128", bit: "7"},
                {cause: "TMK", value: "64", bit: "6"},
                {cause: "DIREZIONE", value: "32", bit: "5"},
                {cause: "FATTURATO", value: "16", bit: "4"},
                {cause: "CHIAMATA", value: "8", bit: "3"},
                {cause: "VENDITA", value: "4", bit: "2"},
                {cause: "TUBO", value: "2", bit: "1"},
                {cause: "ANALISI", value: "1", bit: "0"}
            ];
            this.lockIcon = function (esclusione, val) {
                if ((esclusione & val) == val) {
                    return 'fa-lock';
                }
                else {
                    return 'fa-unlock';
                }
            }

            this.validateClass = function (sc, fn, formname, paranoid) {
                if (sc && sc[formname] && sc[formname][fn]) {
                    if (paranoid)
                        return (sc[formname][fn].$valid ? 'has-success' : 'has-error');
                    else
                        return (sc[formname][fn].$invalid && sc[formname][fn].$touched ? 'has-error' : 'has-success');
                }
                return '';
            };
            this.newApp = {};
            this.retrievePreviousMktData = function (piva, sCallback, eCallback) {
                DataService.getData("getLead", {piva: piva}, "getLeadPerformed", {},
                        function (t) { //azienda registrata in VT
                            if (t[1].response && t[1].response.data && angular.isArray(t[1].response.data) && t[1].response.data[0])
                                var res = t[1].response.data[0];
                            if (res.lead) {
                                that.newApp.newAccount = {
                                    ragioneSociale: res.lead.ragionesociale,
                                    indirizzo: res.lead.indirizzo,
                                    localita: res.lead.localita,
                                    cap: res.lead.cap,
                                    provincia: res.lead.provincia,
                                    telefono: res.lead.telefono[0],
                                    email: res.lead.email_ai
                                }
                            }
                            if (sCallback && angular.isFunction(sCallback))
                                sCallback(t);
                        },
                        function (t) { //azienda non registrata in VT o errore
                            alert("Attenzione qualcosa é andato storto nel recupero delle informazioni registrate. Meglio ricaricare la pagina e riprovare.");
                            if (eCallback && angular.isFunction(eCallback))
                                eCallback(t);
                        });
            };
            this.retrievePreviousData = function (piva, sCallback, eCallback) {
                DataService.getData("retrieveAccountByPiva", {piva: piva}, "retrieveAccountByPivaPerformed", {},
                        function (t) { //azienda registrata in VT
                            if (t[1].response && t[1].response.data && angular.isArray(t[1].response.data) && t[1].response.data[0])
                                that.newAppPreviousData = t[1].response.data[0];
                            if (that.newAppPreviousData && that.newAppPreviousData.account && that.newAppPreviousData.account.partita_iva) {
                                that.newApp.id_cliente = that.newAppPreviousData.account.id_cliente;
                                that.newAppStatus.put(1);
                            }
                            if (that.newApp && that.newApp.contatto && that.newApp.contatto.id_contatto) {
                                that.newAppStatus.put(1);
                            }
                            if (sCallback && angular.isFunction(sCallback))
                                sCallback(t);
                        },
                        function (t) { //azienda non registrata in VT o errore
                            if (t[1].status == 404) { //azienda nn trovata in VT
                                that.retrievePreviousMktData(piva);
                            }
                            else {
                                alert("Attenzione qualcosa é andato storto nel recupero delle informazioni registrate. Meglio ricaricare la pagina e riprovare.");

                            }
                            if (eCallback && angular.isFunction(eCallback))
                                eCallback(t);
                        });
            };
            this.registerNewAccount = function () {
                console.log("REGISTRO ACCOUNT", that.newApp.newAccount);
                if (!that.newApp.newAccount || !that.newApp.newAccount.ragioneSociale || !that.newApp.newAccount.ragioneSociale.length) {
                    alert("Completare correttamente il form per inserire un nuovo account.");
                    return;
                }
                var account_vm = {
                    ragione_sociale: that.newApp.newAccount.ragioneSociale,
                    telefono: that.newApp.newAccount.telefono,
                    email: that.newApp.newAccount.email,
                    via: that.newApp.newAccount.indirizzo,
                    citta: that.newApp.newAccount.localita,
                    provincia: that.newApp.newAccount.provincia,
                    cap: that.newApp.newAccount.cap,
                    partita_iva: that.newApp.piva,
                    venditore: $scope.user.nome + " " + $scope.user.cognome,
                    telemarketing: "Diretta Venditore"
                };
                that.registeringAcc = true;
                DataService.postData("addAccount", {account_vm: account_vm}, "addAccountPerformed", {},
                        function (t) {
                            if (t[1].response && t[1].response.data && t[1].response.data.id) {
                                that.newApp.id_cliente = t[1].response.data.id.replace("11x", "");
                            }
                            else {
                                alert("Attenzione qualcosa é andato storto nel recupero delle informazioni registrate. Meglio ricaricare la pagina e riprovare.");
                            }
                            that.retrievePreviousData(that.newApp.piva);
                            that.newApp.newContatto = {
                                nome: "",
                                cognome: "",
                                ruolo: "",
                                telefono: "",
                                cellulare: "",
                                fax: "",
                                email: ""
                            };
                            that.registeringAcc = false;
                        },
                        function (t) {
                            alert("Qualcosa é andato storto nell'inserimento del contatto. Riprovare.");
                            that.registeringAcc = false;
                        });
            };
            this.updateAccount = function (sCallback, eCallback) {
                console.log("AGGIRONO ACCOUNT");
                if (!that.newApp.id_cliente) {
                    alert("Qualcosa é andato storto, non capisco quale sia l'account giusto.");
                    return;
                }
                var account_vm = {
                    id_cliente: that.newApp.id_cliente,
                    venditore: $scope.user.nome + " " + $scope.user.cognome,
                    telemarketing: "Diretta Venditore"
                };
                that.registeringAcc = true;
                DataService.postData("editAccount", {account_vm: account_vm}, "editAccountPerformed", {},
                        function (t) {
                            that.registeringAcc = false;
                            if (sCallback && angular.isFunction(sCallback))
                                return sCallback();
                        },
                        function (t) {
                            alert("Qualcosa é andato storto nell'update dell'anagrafica cliente. Riprovare.");
                            that.registeringAcc = false;
                        });
            };
            this.closeAllActivities = function (sCallback, eCallback) {
                console.log("CHIUDO ATTIVITÀ PRECEDENTI");
                if (!that.newApp.id_cliente) {
                    alert("Qualcosa é andato storto, non capisco quale sia l'account giusto.");
                    return;
                }
                that.registeringAct = true;
                DataService.getData("closeAllActivities", {accountid: that.newApp.id_cliente}, "closeAllActivities", {},
                        function (t) {
                            that.registeringAct = false;
                            if (sCallback && angular.isFunction(sCallback))
                                return sCallback();
                        },
                        function (t) {
                            alert("Qualcosa é andato storto nell'update delle attività.");
                            that.registeringAct = false;
                        });
            };
            this.registerNewContact = function () {
                console.log("REGISTRO CONTATTO", that.newApp.newContatto, that.newApp.id_cliente);
                if (!that.newApp.newContatto || !that.newApp.newContatto.cognome || !that.newApp.newContatto.cognome.length
                        || !that.newApp.newContatto.ruolo || !that.newApp.newContatto.ruolo.length) {
                    alert("Completare correttamente il form per inserire un nuovo contatto.");
                    return;
                }
                var contact_vm = angular.copy(that.newApp.newContatto);
                contact_vm.id_cliente = that.newApp.id_cliente;
                DataService.postData("addContact", {contact_vm: contact_vm}, "addContactPerformed", {},
                        function (t) {
                            that.retrievePreviousData(that.newApp.piva);
                            that.newApp.newContatto = {
                                nome: "",
                                cognome: "",
                                ruolo: "",
                                telefono: "",
                                cellulare: "",
                                fax: "",
                                email: ""
                            };
                        },
                        function (t) {
                            alert("Qualcosa é andato storto nell'inserimento del contatto. Riprovare.");
                        });
            };
            this.initInsertApp = function (piva) {
                that.newAppStatus = Common.whereAmI().register();
                that.newAppStatus = Common.whereAmI(that.newAppStatus);
                that.newAppStatus.put(0);

                var adesso = new Date();
                adesso.setMilliseconds(0);
                adesso.setSeconds(0);
                this.newApp = {
                    piva: piva,
                    id_cliente: null,
                    id_contatto: null,
                    data: adesso,
                    note: "",
                    newContatto: {
                        nome: "",
                        cognome: "",
                        ruolo: "",
                        telefono: "",
                        cellulare: "",
                        fax: "",
                        email: ""
                    },
                    newAccount: {
                        ragione_sociale: "",
                        indirizzo: "",
                        localita: "",
                        cap: "",
                        provincia: "",
                        email: "",
                        sito: "",
                        sito: "",
                    },
                    dataAppuntamento: "",
                    oraAppuntamento: ""
                };
                that.retrievePreviousData(piva);
                this.newAppAux = {};

            };

            this.registerApp = function () {
                var pad10 = Common.pad10;
                if (!that.newApp.data || !that.newApp.data.getTime || isNaN(that.newApp.data.getTime())) {
                    alert("Hai inserito un data non valida");
                    return;
                }
                else {
                    var data = that.newApp.data.getFullYear() + "-" + pad10(that.newApp.data.getMonth() + 1) + "-" + pad10(that.newApp.data.getDate());
                    var orario_inizio = pad10(that.newApp.data.getHours()) + ":" + pad10(that.newApp.data.getMinutes()) + ":00"
                    var orario_fine = pad10(that.newApp.data.getHours() + 1) + ":" + pad10(that.newApp.data.getMinutes()) + ":00"
                    var potential_vm = {
                        tipo_opportunita: "New Business PMI",
                        fonte_lead: "DB Marketing",
                        nome_opportunita: "Analisi",
                        ammontare: 600,
                        data_chiusura_attesa: data,
                        prossimo_step: "Visita venditore",
                        stadio_vendita: "Prospecting",
                        probabilita: "10",
                        ammontare_pesato: 60,
                        telemarketing: "Diretta Venditore",
                        venditore: $scope.user.nome + " " + $scope.user.cognome,
                        id_cliente: that.newApp.id_cliente,
                        descrizione: that.newApp.note
                    };
                    var indirizzo = that.newAppPreviousData.account.via + " " + that.newAppPreviousData.account.cap + " " + that.newAppPreviousData.account.citta + " " + that.newAppPreviousData.account.provincia;
                    var activity_vm = {
                        tipo_attivita: "Meeting",
                        nome_attivita: ($scope.user.cognome + " - " + that.newAppPreviousData.account.ragione_sociale),
                        eventstatus: "Planned",
                        data_inizio: data,
                        data_fine: data,
                        orario_inizio: orario_inizio,
                        orario_fine: orario_fine,
                        durata_ore: "1",
                        indirizzo: indirizzo,
                        descrizione: "Visita venditore - Note\r" + that.newApp.note,
                        venditore: ($scope.user.nome + " " + $scope.user.cognome),
                        telemarketing: "Diretta Venditore",
                        tipo_attivita_business: "New Business PMI",
                        id_cliente: that.newApp.id_cliente,
                        id_contatto: that.newApp.id_contatto,
                        id_opportunita: "???",
                    };

                    //controllo appuntamenti precedenti
                    var toBeKilled = [];
                    var openStati = ["_COMAPP_QUALIFICATION", "_COMAPP_QUALIFIED", "_COMAPP_PASSAGAIN"];
                    var openStatiIta = ["Attesa conferma supervisione TMK", "Visita da effettuare o in attesa di esito", "Ripasso"];
                    for (var i in that.newAppPreviousData.opportunitaAnalisi) {
                        var opp = that.newAppPreviousData.opportunitaAnalisi[i];
                        if (openStati.indexOf(opp.stato) > -1) {
                            toBeKilled.push(opp);
                        }
                    }
                    if (toBeKilled.length) {
                        var toBeKilledString = "Attenzione, é stata rilevata la presenza di altri appuntamenti commerciali aperti con la stessa azienda.\n";
                        toBeKilledString += "Le attuali regole di processo non permettono questa situazione. Per procedere sarà necessario chiudere gli appuntamenti precedenti.\n";
                        toBeKilledString += "Di seguito un riepilogo di tali appuntamenti:\n";
                        for (var i in toBeKilled) {
                            toBeKilledString += "App #" + i + ":\n";
                            toBeKilledString += "C.Commerciale:" + toBeKilled[i].venditore + "\n";
                            toBeKilledString += "Op.Telemarketing:" + toBeKilled[i].telemarketing + "\n";
                            toBeKilledString += "Data app:" + (toBeKilled[i].data_chiusura_attesa && toBeKilled[i].data_chiusura_attesa.date ? toBeKilled[i].data_chiusura_attesa.date : "ND") + "\n";
                        }
                        toBeKilledString += "Chiudere tutti gli appuntamenti precedenti e crearne uno nuovo?\n";
                        var conf = confirm(toBeKilledString);
                        if (!conf) {
                            return;
                        }
                        else {
                            for (var i in toBeKilled) {
                                DataService.getData("ComAppStatus", {potentialid: toBeKilled[i].id_opportunita, status: "_COMAPP_SYSTEMCANCELLED"}, "ComAppStatusPerformed", {});
                            }
                        }
                    }

                    console.log("OPP DA CHIUDERE", toBeKilled);
                    console.log("INSERISCO POT", potential_vm);
                    that.registeringApp = true;
                    that.updateAccount(function () {
                        that.closeAllActivities(function () {
                            DataService.postData("addPotential", {potential_vm: potential_vm}, "addPotentialPerformed", {},
                                    function (t) {
                                        console.log("INSERITA POT", t[1].response.data);
                                        if (t[1].response && t[1].response.data && t[1].response.data.id) {
                                            activity_vm.id_opportunita = t[1].response.data.id.replace("13x", "");
                                            console.log("INSERISCO ACT", activity_vm);
                                            DataService.postData("addActivity", {activity_vm: activity_vm}, "addActivityPerformed", {},
                                                    function () {
                                                        if (t[1].response && t[1].response.data && t[1].response.data.id) {
                                                            that.registeringApp = false;
                                                            alert("Appuntamento registrato con successo");
                                                            that.cancelInsertApp();
                                                        }
                                                        else {
                                                            that.registeringApp = false;
                                                            alert("Qualcosa é andato storto. E' possibile riprovare");
                                                        }
                                                    },
                                                    function () {
                                                        that.registeringApp = false;
                                                        alert("Qualcosa é andato storto. E' possibile riprovare");
                                                    });
                                        }
                                        else {
                                            that.registeringApp = false;
                                            alert("Qualcosa é andato storto. E' possibile riprovare");
                                        }
                                    }, function (t) {
                                that.registeringApp = false;
                                alert("Qualcosa é andato storto. E' possibile riprovare");
                            });
                        });
                    });





                }
            }

            this.cancelInsertApp = function () {
                delete(that.newApp);
                delete(that.newAppStatus);
                delete(that.newAppPreviousData);

            }


            this.reportToAdmin = function () {
                DataService.postData("sendReportMail",
                        {
                            dove: "Diretta venditore",
                            cosa: "Inserimento fallito: piva " + that.newLead.partitaiva,
                            commento: that.newLead
                        },
                "sendReportMailPerformed",
                        {},
                        function (t) {
                            alert("Mail inviata correttamente.");
                            that.creatingLead = false;
                            that.reportLead = false;
                            that.cancelInsert();
                            return;
                        },
                        function (t) {
                            alert("Mail non inviata correttamente, è possibile riprovare.");
                            return;
                        }
                );
            };


            this.searchingPiva = false;
            this.searchingPivaIcon = function () {
                return this.searchingPiva ? 'fa-spin fa-spinner' : 'fa-search';
            };
            this.checkPivaIcon = function (x) {
                return x.pivaCheck ? 'fa-check' : 'fa-remove';
            };
            this.searchPivaTimed = null;
            this.searchPivaStop = function () {
                delete($scope.pivaSearch);
                that.searchingPiva = false;
                if (that.searchPivaTimed.stopRequestEffectsCbk && angular.isFunction(that.searchPivaTimed.stopRequestEffectsCbk)) {
                    that.searchPivaTimed.stopRequestEffectsCbk();
                }
                delete(that.searchPivaTimed);
            };
            this.searchPiva = function () {
                that.searchingPiva = true;
///
                console.log("CERCO PIVA " + $scope.pivaSearchString);
////
                var offset = 0;
                if ($scope.lastSearch && $scope.lastSearch == $scope.pivaSearchString) {
                    offset = $scope.pivaSearch.length;
                    offset = $scope.pivaSearch[offset - 1].internal_id;
                }
                DataService.getData("searchLeadByPiva", {piva: $scope.pivaSearchString, offset: offset}, "searchLeadByPivaPerformed", {},
                        function (t) {
                            $scope.lastSearch = $scope.pivaSearchString;
                            if ($scope.lastSearch && $scope.lastSearch == $scope.pivaSearchString && $scope.pivaSearch && $scope.pivaSearch.length) {
                                $scope.pivaSearch.concat(t[1].response.data);
                            }
                            else {
                                $scope.pivaSearch = t[1].response.data;
                            }

                            for (var i in $scope.pivaSearch) {
                                $scope.pivaSearch[i]["pivaCheck"] = Common.checkPiva($scope.pivaSearch[i].partitaiva);
                                $scope.pivaSearch[i]["escWorking"] = false;
                                if ($scope.pivaSearch[i].ultimachiamata && $scope.pivaSearch[i].ultimachiamata.date)
                                    $scope.pivaSearch[i].ultimachiamata.date = new Date($scope.pivaSearch[i].ultimachiamata.date);
                            }
                            that.searchingPiva = false;
                            delete(that.searchPivaTimed);
                            console.log("OK", $scope.pivaSearch);
                        },
                        function (t) {
                            $scope.lastSearch = $scope.pivaSearchString;
                            $scope.pivaSearch = [];
                            that.searchingPiva = false;
                            delete(that.searchPivaTimed);
                        },
                        function (timed, cancelCbk) {
                            if (!that.searchPivaTimed)
                                that.searchPivaTimed = timed;
                        }
                );
            }
            this.toggleEsc = function (piva, bit) {
                var leadIndex = _.findIndex($scope.pivaSearch, {partitaiva: piva});
                if (leadIndex === -1) {
                    return;
                }
                var lead = $scope.pivaSearch[leadIndex];
                lead.escWorking = true;
                var val = parseInt(Math.pow(2, bit));
                DataService.getData("setEsclusioneBit",
                        {piva: piva, bit: parseInt(bit), disable: !((lead.esclusione & val) == val)},
                "setEsclusioneBitPerformed",
                        {},
                        function (t) {
                            if (angular.isArray(t[1].response.data)) {
                                var leadUpd = t[1].response.data[0];
                                if ($scope.pivaSearch[leadIndex].partitaiva === leadUpd.partitaiva) {
                                    $scope.pivaSearch[leadIndex] = angular.copy(leadUpd);
                                }
                            }
                            lead.escWorking = false;
                        },
                        function (t) {
                            lead.escWorking = false;
                        });
            }

        }]);
})();



