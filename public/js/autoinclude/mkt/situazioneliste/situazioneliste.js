(function () {
    var app = angular.module("SituazioneListe", ['DATAMODULE', 'THEMASK', 'SMARTBUTTON', 'TSMKT', 'COMMON'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.controller("ListeController", ["DataService", "$scope", "$rootScope", "$timeout", "TheMask", "$interval", "SmartButton", "userInfoFilter", "Common",
        function (DataService, $scope, $rootScope, $timeout, TheMask, $interval, SmartButton, userInfoFilter, Common) {
            var that = this;
            this.mascheraCampagna = TheMask.newMask();
            this.mascheraCercaLead = TheMask.newMask();
            this.campaign = "";
            this.campaigns = [];

            $scope.st = Common.whereAmI().register();
            $scope.st = Common.whereAmI($scope.st);
            $scope.st.put("liste");

            this.destroyPage = function () {
                this.errorMask = true;
                $(".container").find(".row").children().not("error-mask").each(function () {
                    $(this).hide();
                });
            }

            $scope.tmkBtns = {};
            $scope.leadOpBtns = {
                disable: SmartButton.newBtn(),
                enable: SmartButton.newBtn(),
                libera: SmartButton.newBtn(),
                coda: SmartButton.newBtn(),
                personale: SmartButton.newBtn(),
                prenota: SmartButton.newBtn(),
                sprenota: SmartButton.newBtn()
            };
            $scope.leadOpBtnsEnabler = SmartButton.getGroupEnableCallback($scope.leadOpBtns);
            for (var i in $scope.leadOpBtns) {
                if ($scope.leadOpBtns.hasOwnProperty(i)) {
                    SmartButton.getBtn($scope.leadOpBtns[i][0]).setWhatToShow({disabled: "wait"});
                }
            }
            SmartButton.getBtn($scope.leadOpBtns.disable[0]).setClickFcn(
                    function () {
                        $scope.leadOpBtnsEnabler(false);
                        that.mktLeadDisable(1);
                    }
            );
            SmartButton.getBtn($scope.leadOpBtns.enable[0]).setClickFcn(
                    function () {
                        $scope.leadOpBtnsEnabler(false);
                        that.mktLeadDisable(0);
                    }
            );
            SmartButton.getBtn($scope.leadOpBtns.libera[0]).setClickFcn(
                    function () {
                        $scope.leadOpBtnsEnabler(false);
                        that.mktLeadChangeStatus(1);
                    }
            );
            SmartButton.getBtn($scope.leadOpBtns.coda[0]).setClickFcn(
                    function () {
                        $scope.leadOpBtnsEnabler(false);
                        that.mktLeadChangeStatus(2, $scope.opPar.changingLeadTmk, $scope.opPar.changingLeadVend, $scope.opPar.changingLeadLot);
                    }
            );
            SmartButton.getBtn($scope.leadOpBtns.personale[0]).setClickFcn(
                    function () {
                        $scope.leadOpBtnsEnabler(false);
                        that.mktLeadChangeStatus(3, $scope.opPar.changingLeadTmk, $scope.opPar.changingLeadVend, $scope.opPar.changingLeadLot);
                    }
            );
            SmartButton.getBtn($scope.leadOpBtns.prenota[0]).setClickFcn(
                    function () {
                        $scope.leadOpBtnsEnabler(false);
                        that.mktLeadForce($scope.opPar.changingLeadTmk);
                    }
            );
            SmartButton.getBtn($scope.leadOpBtns.sprenota[0]).setClickFcn(
                    function () {
                        $scope.leadOpBtnsEnabler(false);
                        that.mktLeadForce('0');
                    }
            );
            console.log($scope.leadOpBtns);
            console.log();
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
            };
            DataService.initializeOnCurrentPage(function () {
                var campaign = '1';
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
                    $scope.opPar.lots = lots.split(",");
                    if (!$scope.currentLot || $scope.currentLot < 0)
                        $scope.currentLot = ($scope.opPar.lots.length ? $scope.opPar.lots[$scope.opPar.lots.length - 1] : -1);
                    that.caricaTmk();
                    that.caricaComm();
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
//            var baseurl = document.URL;
//            baseurl = baseurl.split("situazioneliste");
//            baseurl = baseurl[0] + "situazioneliste.ws";
//            DataService.initialize(baseurl,
//                    function () {
//                        that.getCampaigns();
//                    },
//                    function () {
//                        that.destroyPage();
//                    });


            $scope.opPar = {
                camp: "",
                lots: [],
                tmkList: [],
                vendList: [],
                activeTmkIndex: -1,
                pendingRequest: false,
                continueList: false,
                selectedLead: -1,
                selectedPersonalLead: -1,
                selectedRifissoLead: -1,
                selLead: -1,
                showOnlyActive: false,
                timerRicerca: {},
                search: "",
                searched: "",
                searchedPage: 1,
                searching: 1,
                ricercaLeading: false,
                changingLead: false,
                changingLeadVend: -1,
                changingLeadTmk: -1,
                changingLeadLot: -1,
                leadCanChange: {}
            };

            $scope.dataPar = {
                coverage: [],
                leadList: [],
                personalLeadList: [],
                rifissiLeadList: [],
                searchResults: [],
                searchResultDetails: {},
                searchResultDetailsIndex: -1
            };

            this.cercaAzienda = function () {
                $scope.st.put("cercalead");
            };
            this.liste = function () {
                $scope.st.put("liste");
            };
            this.ricercaLeadIcon = function () {
                return $scope.opPar.ricercaLeading ? 'fa-spin fa-spinner' : 'fa-search';
            };
            this.ricercaLead = function () {
                var search = $scope.opPar.search;
                $scope.opPar.ricercaLeading = true;
                if ($scope.opPar.search === search && $scope.opPar.search !== "") {
                    if ($scope.opPar.searched !== $scope.opPar.search) {
                        $scope.opPar.searchedPage = 1;
                        $scope.dataPar.searchResults = [];
                        DataService.getData("cercaLead", {search: $scope.opPar.search},
                        "cercaLeadPerformed", {},
                                function (t) {
                                    $scope.dataPar.searchResults = t[1].response.data;
                                    for (var kkk in $scope.dataPar.searchResults) {
                                        if (!$scope.dataPar.searchResults[kkk].static_lock) {
                                            $scope.dataPar.searchResults[kkk].static_lock = false;
                                        }
                                        else if ($scope.dataPar.searchResults[kkk].static_lock == '0') {
                                            $scope.dataPar.searchResults[kkk].static_lock = false;
                                        }
                                        else {
                                            $scope.dataPar.searchResults[kkk].static_lock = true;
                                        }
                                    }
                                    $scope.opPar.timerRicerca = {};
                                    $scope.opPar.searched = $scope.opPar.search;
                                    $scope.opPar.ricercaLeading = false;
                                },
                                function (t) {
                                    $scope.opPar.timerRicerca = {};
                                    $scope.opPar.searched = $scope.opPar.search;
                                    $scope.opPar.ricercaLeading = false;
                                }
                        );
                    }
                    else {
                        DataService.getData("cercaLead", {search: $scope.opPar.search, page: $scope.opPar.searchedPage + 1},
                        "cercaLeadPerformed", {},
                                function (t) {
                                    for (var i in t[1].response.data) {
                                        $scope.dataPar.searchResults.push(t[1].response.data[i]);
                                    }
                                    $scope.opPar.timerRicerca = {};
                                    $scope.opPar.searchedPage++;
                                    $scope.opPar.ricercaLeading = false;
                                },
                                function (t) {
                                    $scope.opPar.timerRicerca = {};
                                    $scope.opPar.ricercaLeading = false;
                                }
                        );
                    }
                }
                return true;
            };
            this.ricercaLeadDetails = function (index) {
                if ($scope.dataPar.searchResults[index]) {
                    $scope.dataPar.searchResultDetails = jQuery.extend(true, {}, $scope.dataPar.searchResults[index]);
                    $scope.dataPar.searchResultDetailsIndex = index;
                    $scope.opPar.searching = 2;
                    $timeout(function () {
                        if ($scope.dataPar.searchResultDetails.worker) {
                            for (var j in $scope.opPar.vendList) {
                                if ($scope.opPar.vendList[j].USERID == $scope.dataPar.searchResultDetails.worker) {
                                    $scope.opPar.changingLeadVend = $scope.opPar.vendList[j].USERID;
                                    break;
                                }
                            }

                        }
                        if ($scope.dataPar.searchResultDetails.tmklock) {
                            for (var j in $scope.opPar.tmkList) {
                                if ($scope.opPar.tmkList[j].USERID == $scope.dataPar.searchResultDetails.tmklock) {
                                    $scope.opPar.changingLeadTmk = $scope.opPar.tmkList[j].USERID;
                                    break;
                                }
                            }
                        }

                        if ($scope.dataPar.searchResultDetails.lot) {
                            $scope.opPar.changingLeadLot = $scope.dataPar.searchResultDetails.lot;
                        }
                    }, 500);

                    that.leadCanChange();
                }
            };
            this.backToRicercaLead = function () {
                if (!$scope.dataPar.searchResultDetails.PartitaIva)
                    return;
                $scope.dataPar.searchResultDetails = {};
                $scope.dataPar.searchResultDetailsIndex = -1;
                $scope.opPar.searching = 1;
                $scope.opPar.leadCanChange = {};
            }
            this.leadCanChange = function () {
                if (!$scope.dataPar.searchResultDetails.PartitaIva)
                    return;
                $scope.opPar.leadCanChange = {
                    libera: false,
                    coda: false,
                    personale: false, 
                    prenota: false,
                    sprenota: false 
                };
                var lead = $scope.dataPar.searchResultDetails;
                if (!lead.pipe) {
                    $scope.opPar.leadCanChange.coda = true;
                    $scope.opPar.leadCanChange.personale = true;
                    return;
                }
                if (lead.pipe == '1' || lead.pipe == '2' || lead.pipe == '3') {
                    $scope.opPar.leadCanChange.libera = true;
                    $scope.opPar.leadCanChange.personale = true;
                }
                if (lead.pipe == '6') {
                    $scope.opPar.leadCanChange.libera = true;
                    $scope.opPar.leadCanChange.coda = true;
                }
                if ((lead.pipe == '1' || lead.pipe == '2' || lead.pipe == '3' || lead.pipe == '4' || lead.pipe == '6')
                        && (lead.force == '0' || !lead.force)) {
                    $scope.opPar.leadCanChange.libera = true;
                    $scope.opPar.leadCanChange.prenota = true;
                }
                if ((lead.pipe == '1' || lead.pipe == '2' || lead.pipe == '3' || lead.pipe == '4' || lead.pipe == '6')
                        && (lead.force && lead.force != '0')) {
                    $scope.opPar.leadCanChange.libera = true;
                    $scope.opPar.leadCanChange.sprenota = true;
                }
                    
                $scope.opPar.leadCanChange.libera = true;
                
            }
            this.mktLeadChangeStatus = function (newStatus, tmk, vend, lot) {
                if (!$scope.dataPar.searchResultDetails.PartitaIva) {
                    console.log($scope.dataPar.searchResultDetails);
                    return;
                }
                if ($scope.opPar.changingLead)
                    return;
                var verbNewStatus = "";

                switch (newStatus) {
                    case 1:
                    case '1':
                        verbNewStatus = 'libera';
                        break;
                    case 2:
                    case '2':
                        if (typeof (vend) === "undefined" || typeof (lot) === "undefined" || parseInt(vend) <= 0 || parseInt(lot) <= 0
                                || isNaN(parseInt(vend)) || isNaN(parseInt(lot))) {
                            alert("Attenzione: per mettere in coda una lead è necessario specificare un venditore valido ed un \n\
lotto valido della campagna corrente");
                            $scope.opPar.changingLead = false;
                            $scope.leadOpBtnsEnabler(true);
                            return;
                        }

                        verbNewStatus = 'coda';
                        break;
                    case 3:
                    case '3':
                        if (typeof (vend) === "undefined" || typeof (tmk) === "undefined" || typeof (lot) === "undefined" ||
                                parseInt(vend) <= 0 || parseInt(lot) <= 0 || parseInt(tmk) <= 0
                                || isNaN(parseInt(vend)) || isNaN(parseInt(lot)) || isNaN(parseInt(tmk))) {
                            alert("Attenzione: per mettere nell'inventario personale una lead è necessario specificare un venditore valido,\n\
un operatore di telemarketing valido ed un lotto valido della campagna corrente");
                            $scope.opPar.changingLead = false;
                            $scope.leadOpBtnsEnabler(true);
                            return;
                        }
                        verbNewStatus = 'personale';
                        break;
                    default:
                        return;
                        break;
                }
                $scope.opPar.changingLead = true;
                var leadChangeStatusVM = {
                    piva: $scope.dataPar.searchResultDetails.PartitaIva,
                    newStatus: verbNewStatus,
                    tmk: tmk,
                    vend: vend,
                    lot: lot
                };
                DataService.getData("mktLeadChangeStatus", leadChangeStatusVM, "mktLeadChangeStatusPerformed", {},
                        function (t) {
                            $scope.opPar.changingLead = false;
                            $scope.leadOpBtnsEnabler(true);
                            var data = t[1].response.data;
                            if (data.aziende_all_map) {
                                that.updateLead(data.aziende_all_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.aziende_all_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            if (data.ganascia_map) {
                                that.updateLead(data.ganascia_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.ganascia_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            if (data.tubo_map) {
                                that.updateLead(data.tubo_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.tubo_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            that.leadCanChange();
                        },
                        function (t) {
                            $scope.opPar.changingLead = false;
                            $scope.leadOpBtnsEnabler(true);
                        });

            };

            this.updateLead = function (newData, oldData) {
                jQuery.extend(oldData, newData);
                if (newData.static_lock) {
                    oldData.static_lock = (newData.static_lock == "1" ? true : false);
                }
            }

            this.searchSpecificLead = function (piva) {
                $scope.opPar.searched = "";
                $scope.opPar.searchedPage = 1;
                $scope.dataPar.searchResults = [];
                $scope.dataPar.searchResultDetailsIndex = -1;
                $scope.opPar.searchResultDetails = {};
                $scope.opPar.searching = 1;
//                this.mascheraCercaLead[1].open();
                $scope.opPar.search = piva;
                $scope.st.put("cercalead");
                this.ricercaLead();

            }

            this.mktLeadDisable = function (disable) {
                if (!$scope.dataPar.searchResultDetails.PartitaIva) {
                    return;
                }
                if ($scope.opPar.changingLead)
                    return;
                $scope.opPar.changingLead = true;
//            return;
                DataService.getData("mktLeadDisable", {
                    piva: $scope.dataPar.searchResultDetails.PartitaIva,
                    disable: disable
                }, "mktLeadDisablePerformed", {},
                        function (t) {
                            $scope.opPar.changingLead = false;
                            $scope.leadOpBtnsEnabler(true);
                            var data = t[1].response.data;
                            if (data.aziende_all_map) {
                                that.updateLead(data.aziende_all_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.aziende_all_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            if (data.ganascia_map) {
                                that.updateLead(data.ganascia_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.ganascia_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            if (data.tubo_map) {
                                that.updateLead(data.tubo_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.tubo_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            that.leadCanChange();
                        },
                        function (t) {
                            $scope.opPar.changingLead = false;
                            $scope.leadOpBtnsEnabler(true);
                        });

            };

            this.mktLeadForce = function (tmk) {
                if (!$scope.dataPar.searchResultDetails.PartitaIva) {
                    return;
                }
                if ($scope.opPar.changingLead)
                    return;
                $scope.opPar.changingLead = true;
//            return;
                DataService.getData("mktLeadForce", {
                    piva: $scope.dataPar.searchResultDetails.PartitaIva,
                    tmk: tmk
                }, "mktLeadForcePerformed", {},
                        function (t) {
                            $scope.opPar.changingLead = false;
                            $scope.leadOpBtnsEnabler(true);
                            var data = t[1].response.data;
                            if (data.aziende_all_map) {
                                that.updateLead(data.aziende_all_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.aziende_all_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            if (data.ganascia_map) {
                                that.updateLead(data.ganascia_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.ganascia_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            if (data.tubo_map) {
                                that.updateLead(data.tubo_map, $scope.dataPar.searchResultDetails);
                                that.updateLead(data.tubo_map, $scope.dataPar.searchResults[$scope.dataPar.searchResultDetailsIndex]);
                            }
                            that.leadCanChange();
                        },
                        function (t) {
                            $scope.opPar.changingLead = false;
                            $scope.leadOpBtnsEnabler(true);
                        });

            };
//        console.log(TheMask.getMask(that.mascheraCercaLead[0]));
            TheMask.getMask(that.mascheraCercaLead[0]).setBtnCallback(0, function () {
                that.mascheraCercaLead[1].close();
            });



//            TheMask.getMask(that.mascheraCampagna[0]).setBtnEnableCondition(0, function () {
//                return (that.campaign !== "" && !isNaN(that.campaign));
//            });
//            TheMask.getMask(that.mascheraCampagna[0]).setBtnCallback(0, function () {
//                if (that.campaign === "" || isNaN(that.campaign)) {
//                    that.mascheraCampagna[1].close();
//                    alert("Attenzione: si è verificato un errore nella scelta della campagna, l'applicazione potrebbe presentare comportamenti \n\
//non previsti e produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
//                }
//                else {
//                    that.mascheraCampagna[1].close();
//                    var campaign = that.campaign;
//                    if (isNaN(campaign)) {
//                        alert("Attenzione: si è verificato un errore nel recupero della campagna, l'applicazione potrebbe presentare comportamenti \n\
//non previsti e produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
//                        return;
//                    }
//                    var campTicket = DataService.getData("getCampaign", {camp: campaign}, "getCampaignPerformed", {camp: campaign},
//                    function (t) {
//                        var data = t[1].response.data;
//                        if (data.length !== 1) {
//
//                            alert("Attenzione: si è verificato un errore nel recupero della campagna, l'applicazione potrebbe presentare comportamenti \n\
//non previsti e produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
//                            return;
//                        }
//                        var lots = data[0].lots;
//                        lots = lots.replace("[", "").replace("]", "");
//                        $rootScope.camp = t[1].message.messagePars.camp;
//                        $scope.opPar.lots = lots.split(",");
//                        $scope.opPar.camp = t[1].message.messagePars.camp;
//                        $scope.opPar.lots = lots.split(",");
//                        $rootScope.$emit("Configuration Done", {});
//
//                        DataService.makePing();
//                    },
//                            function (t) {
//                                alert("Attenzione: si è verificato un errore nel recupero della campagna, l'applicazione potrebbe presentare comportamenti \n\
//non previsti e produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
//                                return;
//                            }
//                    );
//                }
//            });






//            this.getCampaigns = function () {
//                DataService.getData("getCampaigns", {}, "getCampaignsPerformed", {},
//                        function (t) {
//                            var ticket = t;
//                            that.campaigns = [];
//                            that.campaigns = ticket[1].response.data;
//                            for (var i in that.campaigns) {
//                                if (that.campaigns[i].status !== "0") {
//                                    that.campaigns.splice(i, 1);
//                                }
//                            }
//                            for (var i in that.campaigns) {
//                                that.campaigns[i].exp = that.campaigns[i].id + " " + that.campaigns[i].name + "   lotti:" + that.campaigns[i].lots;
//                            }
//                            that.mascheraCampagna[1].open();
//                        },
//                        function (t) {
//                            alert("Attenzione: si è verificato un errore nel recupero delle campagne, l'applicazione potrebbe presentare comportamenti \n\
//non previsti e produrre dati non corretti. Si consiglia di non continuare e di contattare un amministratore");
//                            return;
//                        }
//                );
//            };








//            $rootScope.$on("Configuration Done", function (event, mass) {
//                that.caricaTmk();
//                that.caricaComm();
//            });

            this.caricaTmk = function () {
                DataService.getData("getTelemarketing", {}, "getTelemarketingPerformed", {},
                        function (t) {

                            for (var i in t[1].response.data) {
                                $scope.tmkBtns[i] = SmartButton.newBtn();
                            }
                            $scope.tmkBtnsEnabler = SmartButton.getGroupEnableCallback($scope.tmkBtns);
                            $scope.opPar.tmkList = t[1].response.data;
                            for (var i in $scope.opPar.tmkList) {
                                (function (n) { // << questa chiusura serve per portare il parametro i all'interno del callback
                                    // altrimenti, per qualche motivo, tutti i pulsanti rispondono all'ultimo callback
                                    // impostato nel ciclo
                                    SmartButton.getBtn($scope.tmkBtns[n][0]).setWhatToShow({disabled: "wait"});
                                    SmartButton.getBtn($scope.tmkBtns[n][0]).setClickFcn(
                                            function () {
                                                $scope.tmkBtnsEnabler(false);
                                                that.selectTmk(n);
                                            });
                                })(i);

                            }

                        },
                        function (t) {
                            alert("Ops! Qualcosa è andato storto tentando di recuperare il nome degli operatori telemarketing.");
                            that.destroyPage();
                        }
                );
            };
            this.caricaComm = function () {
                DataService.getData("getCommerciali", {}, "getCommercialiPerformed", {},
                        function (t) {
                            $scope.opPar.vendList = t[1].response.data;
                        },
                        function (t) {
                            alert("Ops! Qualcosa è andato storto tentando di recuperare il nome di consulenti commerciali.");
                            that.destroyPage();
                        }
                );
            };

            $interval(function () {
                if (!$scope.opPar.pendingRequest && $scope.opPar.activeTmkIndex && $scope.opPar.activeTmkIndex != -1 && $scope.opPar.continueList) {
                    that.selectTmk($scope.opPar.activeTmkIndex);
                }
            }, 5000);

            this.selectTmk = function (index) {
                if ($scope.opPar.pendingRequest) {
                    return;
                }

                if (typeof ($scope.opPar.tmkList[index]) !== "undefined") {
                    $scope.opPar.activeTmkIndex = index;
                    $scope.opPar.pendingRequest = true;
                    DataService.getData("getJobCoverage", {}, "getJobCoveragePerformed", {},
                            function (t) {
                                var data = t[1].response.data;
                                $scope.dataPar.coverage = [];
                                for (var i in data) {
                                    if (data[i].tmk == $scope.opPar.tmkList[$scope.opPar.activeTmkIndex].USERID) {
                                        $scope.dataPar.coverage.push({loc: data[i].loc, vend: data[i].vend});
                                    }
                                }

                                DataService.postData("listaPerTelemarketing", {
                                    tmk: $scope.opPar.tmkList[$scope.opPar.activeTmkIndex].USERID,
                                    lots: $scope.opPar.lots.join("|"),
                                    locs: (
                                            function () {
                                                var locs = [];
                                                for (var i in $scope.dataPar.coverage) {
                                                    locs.push($scope.dataPar.coverage[i].loc);
                                                }
                                                return locs.join("|");
                                            })(),
                                    worker: ($scope.dataPar.coverage.length ? $scope.dataPar.coverage[0].vend : 0)
                                },
                                "listaPerTelemarketingPerformed", {},
                                        function (t) {
                                            $scope.dataPar.leadList = [];
                                            $scope.dataPar.leadList = [];
                                            $scope.dataPar.leadList = t[1].response.data;
                                            DataService.postData("listaPersonaliTelemarketing", {
                                                tmk: $scope.opPar.tmkList[$scope.opPar.activeTmkIndex].USERID,
                                                lots: $scope.opPar.lots.join("|")
                                            },
                                            "listaPersonaliTelemarketingPerformed", {},
                                                    function (t) {
                                                        $scope.dataPar.personalLeadList = [];
                                                        $scope.dataPar.personalLeadList = t[1].response.data;
                                                        DataService.postData("listaRifissiTelemarketing", {
                                                            lots: $scope.opPar.lots.join("|")
                                                        },
                                                        "listaPersonaliTelemarketingPerformed", {},
                                                                function (t) {
                                                                    $scope.dataPar.rifissiLeadList = [];
                                                                    $scope.opPar.selectedLead = -1;
                                                                    $scope.opPar.selectedPersonalLead = -1;
                                                                    $scope.opPar.selectedRifissoLead = -1;
                                                                    $scope.dataPar.rifissiLeadList = t[1].response.data;
                                                                    $scope.opPar.pendingRequest = false;
                                                                    $scope.tmkBtnsEnabler(true);
                                                                    for (var bIndex in $scope.tmkBtns) {
                                                                        if ($scope.tmkBtns.hasOwnProperty(bIndex)) {
                                                                            SmartButton.getBtn($scope.tmkBtns[bIndex][0]).getObj().remClass("btn-success");
                                                                            SmartButton.getBtn($scope.tmkBtns[bIndex][0]).getObj().setClass("btn-info");
                                                                        }
                                                                    }
                                                                    SmartButton.getBtn($scope.tmkBtns[index][0]).getObj().remClass("btn-info");
                                                                    SmartButton.getBtn($scope.tmkBtns[index][0]).getObj().setClass("btn-success");

                                                                },
                                                                function (t) {
                                                                    $scope.dataPar = {};

                                                                    alert("Ops! Qualcosa è andato storto tentando di recuperare l'elenco dei rifissi. Riprovare e se il problema persiste contattare\n\
un amministratore.");
                                                                    $scope.opPar.pendingRequest = false;
                                                                    $scope.tmkBtnsEnabler(true);
                                                                }
                                                        );
                                                    },
                                                    function (t) {
                                                        $scope.dataPar = {};

                                                        alert("Ops! Qualcosa è andato storto tentando di recuperare le lead personali. Riprovare e se il problema persiste contattare\n\
un amministratore.");
                                                        $scope.opPar.pendingRequest = false;
                                                        $scope.tmkBtnsEnabler(true);
                                                    }
                                            );

                                        },
                                        function (t) {
                                            $scope.dataPar = {};

                                            alert("Ops! Qualcosa è andato storto tentando di recuperare la coda delle lead. Riprovare e se il problema persiste contattare\n\
un amministratore.");
                                            $scope.opPar.pendingRequest = false;
                                            $scope.tmkBtnsEnabler(true);
                                        }
                                );

                            },
                            function (t) {
                                alert("Ops! Qualcosa è andato storto tentando di recuperare le assegnazioni tmk -> venditore. Riprovare e se il problema persiste contattare\n\
un amministratore.");
                                $scope.opPar.pendingRequest = false;
                            }
                    );
                }
            };

            this.classTmk = function (index) {
                if ($scope.opPar.activeTmkIndex === index) {
                    return "btn-success";
                }
                else {
                    return "btn-info";
                }
            }



            this.classLead = function (lead) {
                var ret = "";
                if (lead.working == "1") {
                    ret += " lead-lavorazione";
                }
                else if (lead.force && lead.force != "0") {
                    ret += " lead-prenotata";
                }
                if (lead.static_lock == "1") {
                    ret += " lead-inattiva";
                }
                if (lead.unlock_timestamp && lead.unlock_timestamp.length > 1) {
                    ret += " lead-posticipata";
                    var unlock_timestamp = new Date(lead.unlock_timestamp);
                    var now = new Date();
                    if (unlock_timestamp > now) {
                        ret += " lead-inattiva";
                    }
                }

                switch (lead.FormaGiuridica) {
                    case 'SRL':
                    case 'SPA':
                    case 'SRL a socio unico':
                    case 'SPA a socio unico':
                        ret += " lead-3factories";
                        break;
                    case 'SNC':
                    case 'SAS':
                        ret += " lead-2factories";
                        break;
                    case 'DITTA INDIVIDUALE':
                    case 'SDF E SOCIETA SEMPLICI':
                    case 'SCARLPA':
                    case 'SCARL':
                    case 'Societa\' consortile a responsabilita\' limitata':
                    case 'Societa\' cooperativa consortile':
                    case 'Consorzio':
                    case 'Cooperativa sociale':
                    case 'Societa\' consortile per azioni':
                    case 'Ente':
                    case 'COOPERATIVE':
                    case 'ALTRO ENTE/ ASSOCIAZIONE':
                    case 'PICC SCARL':
                    case 'Fondazione':
                    case 'SAPA':
                    case 'Piccola cooperativa':
                    case 'GEIE':
                    case 'Consorzio senza attivita\' esterna':
                    case 'SCARI':
                    case 'Societa\' di fatto':
                    case 'CONSORZI':
                    case 'Societa\' semplice':
                    case 'Associazione':
                    case 'Societa\' estera':
                    case 'Mutua assicurazione':
                    case 'Societa\' anonima':
                    case 'ASSOCIAZIONE FRA PROFESSIONISTI':
                    case 'STUDIO ASSOCIATO':

                        ret += " lead-1factory";
                        break;
                    default:
                        ret += " lead-0factory";
                        break;
                }
                switch (lead.ClasseDipendenti) {
                    case '5':
                    case '6':
                    case '7':
                    case '8':
                    case '9':
                        ret += " lead-3employees";
                        break;
                    case '3':
                    case '4':
                        ret += " lead-2employees";
                        break;
                    case '1':
                    case '2':
                        ret += " lead-1employee";
                        break;
                    default:
                        ret += " lead-0employee";
                        break;
                }
                switch (lead.FasciaFatturato) {
                    case '4':
                    case '5':
                    case '6':
                    case '7':
                    case '8':
                    case '9':
                    case '10':
                        ret += " lead-3euros";
                        break;
                    case '3':
                        ret += " lead-2euros";
                        break;
                    case '1':
                    case '2':
                        ret += " lead-1euro";
                        break;
                    default:
                        ret += " lead-0euro";
                        break;
                }

                if (ret === "") {
                    ret = "lead-libera";
                }
                return ret;
            }

            this.highlightLead = function (index, type) {
                switch (type) {
                    case 1:
                        if ($scope.opPar.selectedLead != index && $scope.dataPar.leadList[index]) {
                            $scope.opPar.selectedLead = index;
                            $scope.opPar.selectedPersonalLead = -1;
                            $scope.opPar.selectedRifissoLead = -1;
                        }
                        break;
                    case 2:
                        if ($scope.opPar.selectedPersonalLead != index && $scope.dataPar.personalLeadList[index]) {
                            $scope.opPar.selectedLead = -1;
                            $scope.opPar.selectedPersonalLead = index;
                            $scope.opPar.selectedRifissoLead = -1;
                        }
                        break;
                    case 3:
                        if ($scope.opPar.selectedRifissoLead != index && $scope.dataPar.rifissiLeadList[index]) {
                            $scope.opPar.selectedLead = -1;
                            $scope.opPar.selectedPersonalLead = -1;
                            $scope.opPar.selectedRifissoLead = index;
                        }
                        break;
                }

            }
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



