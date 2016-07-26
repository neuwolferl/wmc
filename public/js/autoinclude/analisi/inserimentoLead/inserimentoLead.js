(function () {
    var app = angular.module("InserimentoLead", ['DATAMODULE', 'COMMON', 'TSNWINCLUDE'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.controller("InserimentoLeadController", ["DataService", "$scope", "$rootScope", "$timeout", "Common", "Tsnw",
        function (DataService, $scope, $rootScope, $timeout, Common, Tsnw) {
            var that = this;
            DataService.initializeOnCurrentPage(function (a) {
            }, function () {
                that.destroyPage();
            });
            this.destroyPage = function () {
                this.errorMask = true;
                $("error-mask").siblings().each(function () {
                    $(this).hide();
                });
            };
            this.validateClass = function (sc, fn, paranoid) {
                if (sc && sc.newlead && sc.newlead[fn]) {
                    if (paranoid)
                        return (sc.newlead[fn].$valid ? 'has-success' : 'has-error');
                    else
                        return (sc.newlead[fn].$invalid && sc.newlead[fn].$touched ? 'has-error' : 'has-success');
                }
                return '';
            }
            this.initInsert = function () {
                that.newLead = {
                    partitaiva: $scope.pivaSearchString,
                    codicefiscale: $scope.pivaSearchString,
                    classedipendenti: "10",
                    fasciafatturato: "11",
                    formagiuridica: "NON ASSEGNATA",
                };
                that.newLeadAux = {
                    ateco: [],
                    regioni: Common.getRegioniItaliane(),
                    province: [],
                    classeDip: Tsnw.accountUtilities.getClasseDipendentiValuedList(),
                    fasciaFat: Tsnw.accountUtilities.getFasciaFatturatoValuedList(),
                    formaGiu: Tsnw.accountUtilities.getFormaGiuridicaList(),
                    resetRegione: function () {
                        delete(that.newLead.localita);
                        delete(that.newLead.cap);
                        that.newLeadAux.province = Common.getProvinceItaliane(that.newLead.regione);
                        that.newLeadAux.localita = [];
                        return true;
                    },
                    resetProvincia: function () {
                        if (!that.newLead.provincia || !that.newLead.provincia.length || !that.newLead.regione || !that.newLead.regione.length)
                            that.newLeadAux.localita = [];
                        delete(that.newLead.localita);
                        delete(that.newLead.cap);
                        DataService.getData("getLocalita",
                                {provincia: that.newLead.provincia,
                                    regione: that.newLead.regione},
                        "getLocalitaPerformed",
                                {},
                                function (t) {
                                    that.newLeadAux.localita = t[1].response.data[0];
                                },
                                function (t) {
                                    that.newLeadAux.localita = [];
                                }
                        );
                    }
                };
                this.formatAteco = function (ateco) {
                    return ateco.sottocategoria + " - " + ateco.descriz;
                }
                DataService.getData("getAteco", {}, "getAtecoPerformed", {}, function (t) {
                    if (angular.isArray(t[1].response.data)) {
                        that.newLeadAux.ateco = t[1].response.data[0];
                    }

                }, function (t) {

                });
            }
            this.cancelInsert = function () {
                delete(that.newLead);
                delete(that.newLeadAux);
                delete(that.reportLead);
                delete($scope.pivaSearch);
            }
            this.reportToAdmin = function () {
                DataService.postData("sendReportMail",
                        {
                            dove: "Inserimento lead",
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
            }
            this.makeInsertStop = function () {
                that.creatingLead = false;
                if (that.createLeadTimed.stopRequestEffectsCbk && angular.isFunction(that.createLeadTimed.stopRequestEffectsCbk)) {
                    that.createLeadTimed.stopRequestEffectsCbk();
                }
            };
            this.makeInsert = function () {
                var telefonoPattern = /\d+(\s*,\s*\d+)*/;
                if (!that.newLead.partitaiva || !Common.checkPiva(that.newLead.partitaiva)) {
                    alert("Partita iva mancante o non valida");
                    throw Error("Invalid partita iva");
                    return;
                }
                if (!that.newLead.ragionesociale) {
                    alert("Ragione sociale mancante o non valida");
                    throw Error("Invalid ragionesociale");
                    return;
                }
                if (!that.newLead.telefono || !telefonoPattern.test(that.newLead.telefono)) {
                    alert("Telefono mancante o non valido");
                    throw Error("Invalid telefono");
                    return;
                }
                if (!that.newLead.localita) {
                    alert("Dati geo mancanti o non validi");
                    throw Error("Invalid geo data");
                    return;
                }
                if (!that.newLead.localita.regione) {
                    alert("Regione mancante o non valida");
                    throw Error("Invalid regione");
                    return;
                }
                if (!that.newLead.localita.provincia) {
                    alert("Provincia mancante o non valida");
                    throw Error("Invalid provincia");
                    return;
                }
                if (!that.newLead.localita.localita) {
                    alert("Localita mancante o non valida");
                    throw Error("Invalid localita");
                    return;
                }
                if (!that.newLead.cap) {
                    alert("Cap mancante o non valido");
                    throw Error("Invalid cap");
                    return;
                }
                if (!that.newLead.indirizzo) {
                    alert("Indirizzo mancante o non valido");
                    throw Error("Invalid indirizzo");
                    return;
                }

                var lead_vm = {
                    partitaiva: that.newLead.partitaiva,
                    ragionesociale: that.newLead.ragionesociale,
                    indirizzo: that.newLead.indirizzo,
                    localita: that.newLead.localita.localita,
                    cap: that.newLead.localita.cap,
                    provincia: that.newLead.localita.provincia,
                    regione: that.newLead.localita.regione,
                    email: that.newLead.email,
                    homepage: that.newLead.homepage,
                    formagiuridica: that.newLead.formagiuridica,
                    fasciafatturato: that.newLead.fasciafatturato,
                    classedipendenti: that.newLead.classedipendenti,
                    attivita_ateco: that.newLead.ateco,
                    telefono: that.newLead.telefono
                };
                console.log("provo ad inserire", lead_vm);
                that.creatingLead = true;
                DataService.postData("createLead",
                        {lead_vm: lead_vm},
                "createLeadPerformed",
                        {},
                        function (t) {
                            that.creatingLead = false;
                            that.cancelInsert();
                        },
                        function (t) {
                            if (angular.isArray(t[1].response.data)) {
                                if (angular.isString(t[1].response.data[1])) {
                                    var res = t[1].response.data[1];
                                    if (res.indexOf("Duplicate entry") > -1 && res.indexOf("PartitaIva") > -1 && res.indexOf("key") > -1) {
                                        alert("Sembra che tu abbia provato a inserire una partita iva già presente.\n" +
                                                "Se questo è dovuto ad un errore sarà ora possibile riprovare.\n" +
                                                "In caso contrario é possibile inviare una segnalazione all'amministratore usando l'apposito pulsante.");
                                    }
                                    that.creatingLead = false;
                                    that.reportLead = true;
                                    return;
                                }
                            }
                            that.creatingLead = false;
                            that.cancelInsert();
                        },
                        function (timed, cancelCbk) {
                            if (!that.createLeadTimed)
                                that.createLeadTimed = timed;
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
                DataService.getData("searchLeadByPiva", {piva: $scope.pivaSearchString}, "searchLeadByPivaPerformed", {},
                        function (t) {
                            $scope.pivaSearch = t[1].response.data;
                            for (var i in $scope.pivaSearch) {
                                $scope.pivaSearch[i]["pivaCheck"] = Common.checkPiva($scope.pivaSearch[i].partitaiva);
                                $scope.pivaSearch[i]["escWorking"] = false;
                                if ($scope.pivaSearch[i].ultimachiamata && $scope.pivaSearch[i].ultimachiamata.date)
                                    $scope.pivaSearch[i].ultimachiamata.date = new Date($scope.pivaSearch[i].ultimachiamata.date);
                            }
                            that.searchingPiva = false;
                            delete(that.searchPivaTimed);
                            console.log("OK",$scope.pivaSearch);
                        },
                        function (t) {
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



