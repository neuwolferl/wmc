(function () {
    var app = angular.module("AssegnazioneLavoro", ['ngDragDrop', 'DATAMODULE', 'THEMASK'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.filter("telemarketing_associati", function () {
        return function (input, filt, filt2) {
            if (!filt.USERID)
                return [];
            var out = [];
            var comId = filt.USERID;
//            console.log("comId =" + comId);
            var tmk = [];
            for (var i in filt2) {
                if (comId == filt2[i].vend) {
                    tmk.push({lot: filt2[i].lot, loc: filt2[i].loc, tmk: filt2[i].tmk});
                }
            }
//            console.log("<<<");

            var assTmk = {};
            for (var i in input) {
//                console.log("++");
//                console.log(input[i]);
                var tmkId = input[i].USERID;
                for (var j in tmk) {
//                    console.log(tmk[j]);
                    if (tmkId == tmk[j].tmk) {
                        var index = -1;
                        for (var k in out) {
                            if (out[k].USERID == tmkId) {
                                index = k;
                                break;
                            }
                        }
//                        console.log("index = " + index);
                        if (index === -1) {
                            out.push({
                                USERID: tmkId,
                                NOME: input[i].NOME + " " + input[i].COGNOME,
                                LOCS: [tmk[j].loc]
                            });
                        }
                        else {
                            out[index].LOCS.push(tmk[j].loc);
                        }
                    }
                }
            }
//            console.log(out);
            return out;
        };
    });
    app.filter("telemarketing_liberi", function () {
        return function (input, filt, truefalse) {
            var out = [];
            for (var i in input) {
                var flag = truefalse;
                for (var j in filt) {
                    if (input[i].USERID === filt[j].tmk) {
                        flag = !(truefalse);
                    }
                }
                if (flag) {
                    out.push(input[i]);
                }
            }
            return out;
        };
    });
    app.controller("JobController", function (DataService, $scope, $rootScope, $timeout, TheMask, telemarketing_associatiFilter) {
        var that = this;
        this.campaign = "1";
        this.campaigns = [];
        $scope.tmkAssociati;
        this.mascheraCampagna = TheMask.newMask();
        this.mascheraAssocia = TheMask.newMask();
        this.mascheraModificaAssociazione = TheMask.newMask();
        $scope.commerciali = [];
        $scope.commercialeSelezionato = {};
        $scope.telemarketingSelezionato = {};
        $scope.assegnazioneCommerciali = {};
        $scope.telemarketing = [];
        $scope.associazioni = [];
        this.makeAssociazione = {};
//        var baseurl = document.URL;
//        baseurl = baseurl.split("jobcoverage");
//        baseurl = baseurl[0] + "jobcoverage.ws";
//        DataService.initialize(baseurl);
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
            var campaign = 1;
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
                that.loadCommerciali();
                that.loadTelemarketing();
            },
                    function (t) {
                        that.errorOnCampaign(2);
                        return;
                    });
        }, function () {
            that.errorOnCampaign(1);
            that.destroyPage();
        });

        this.loadCommerciali = function () {
            DataService.getData("getCommerciali", {}, "getCommercialiPerformed", {},
                    function (t) {
                        var com = t[1].response.data;
                        for (var i in com) {
                            $scope.commerciali.push(com[i]);
                        }
                        that.loadWorkerAssignment();
                    },
                    function (t) { /////////////////////////////
                    }
            );
        };
        this.loadWorkerAssignment = function () {
            DataService.getData("getWorkerAssignment", {lots: $scope.lots.join("|")}, "getWorkerAssignmentPerformed", {},
                    function (t) {
                        var wa = t[1].response.data;
                        $scope.assegnazioneCommerciali = {};
                        for (var i in $scope.commerciali) {
                            $scope.assegnazioneCommerciali[$scope.commerciali[i].USERID] = [];
                        }
                        for (var i in wa) {
                            if (!$scope.assegnazioneCommerciali[wa[i].worker]) {
                                continue;
                            }
                            $scope.assegnazioneCommerciali[wa[i].worker].push({loc: wa[i].Localita, count: wa[i].countForWorker, lot: wa[i].lot});
                        }
                    },
                    function (t) { /////////////////////////////////

                    });
        };
        this.loadTelemarketing = function () {
            DataService.getData("getTelemarketing", {}, "getTelemarketingPerformed", {},
                    function (t) {
                        var tmk = t[1].response.data;
                        for (var i in tmk) {
                            $scope.telemarketing.push(tmk[i]);
                        }
                        that.loadJobCoverage();
                    },
                    function (t) { /////////////////////////////
                    });
        }
        this.loadJobCoverage = function () {
            DataService.getData("getJobCoverage", {}, "getJobCoveragePerformed", {},
                    function (t) {
                        var ass = t[1].response.data;
                        $scope.associazioni = [];
                        for (var i in ass) {
                            if (ass[i].camp === that.campaign) {
                                $scope.associazioni.push(ass[i]);
                            }
                        }
                        if ($scope.commercialeSelezionato.USERID) {
                            var id = $scope.commercialeSelezionato.USERID;
                            that.clickOnVendor(id);
                            that.clickOnVendor(id);
                        }
                    },
                    function (t) { ////////////////////////////////
                    });
        }
        this.loadPipeStatus = function () {
            DataService.getData("getPipeStatus", {pipe: "1"}, "getPipeStatusPerformed", {});
        };

        this.clickOnVendor = function (vendorId) { //ok
            if (!$scope.commercialeSelezionato.USERID) {
                var index = -1;
                for (var i in $scope.commerciali) {
                    if ($scope.commerciali[i].USERID == vendorId) {
                        index = i;
                        break;
                    }
                }
                if (index === -1) {
                    console.log($scope.commerciali);
                    console.log("Errore: questo commerciale non esiste. " + vendorId);
                    return;
                }
                $scope.commercialeSelezionato = $scope.commerciali[index];
//                console.log("cliccato commerciale: " + that.commercialeSelezionato.NOME + " " + that.commercialeSelezionato.COGNOME);
                $scope.tmkAssociati = telemarketing_associatiFilter($scope.telemarketing, $scope.commercialeSelezionato, $scope.associazioni);
            }
            else {
                $scope.commercialeSelezionato = {};
                $scope.tmkAssociati = [];
            }
        }
//        this.clickOnTelemarketingAssociato = function (tmkId) {
////controllo se il tmk esiste:
//            var tmkIndex = -1;
////            console.log(that.telemarketing);
//            for (var i in $scope.telemarketing) {
//                if ($scope.telemarketing[i].USERID == tmkId) {
//                    tmkIndex = i;
//                    break;
//                }
//            }
//            if (tmkIndex === -1) {
//                console.log("Telemarketing inestistente");
//                return;
//            }
////controllo se è associato:
//            var vendorId = -1;
//            for (var i in $scope.associazioni) {
//                if ($scope.associazioni[i].tmk == tmkId) {
//                    vendorId = $scope.associazioni[i].vend;
//                    break;
//                }
//            }
//            if (vendorId > -1) { //associato
//                that.mascheraAssocia[1].open();
//                if ($scope.commercialeSelezionato.USERID) {
//                    if ($scope.commercialeSelezionato.USERID != vendorId) {
//                        that.clickOnVendor($scope.commercialeSelezionato.USERID);
//                        that.clickOnVendor(vendorId);
//                    }
//                }
//                else {
//
//                    that.clickOnVendor(vendorId);
//                }
//                var ll = [];
//                for (var i in $scope.associazioni) {
//                    if ($scope.associazioni[i].camp == that.campaign
//                            && $scope.associazioni[i].tmk == tmkId
//                            && $scope.associazioni[i].vend == vendorId) {
//                        ll.push($scope.associazioni[i].loc);
//                    }
//                }
//                that.makeAssociazione = {
//                    vendorName: $scope.commercialeSelezionato.NOME + " " + $scope.commercialeSelezionato.COGNOME,
//                    tmkName: $scope.telemarketing[tmkIndex].NOME + " " + $scope.telemarketing[tmkIndex].COGNOME,
//                    vendorId: $scope.commercialeSelezionato.USERID,
//                    tmkId: tmkId,
//                    locs: []
//                };
//                for (var k in $scope.assegnazioneCommerciali[vendorId]) {
//                    that.makeAssociazione.locs.push({
//                        loc: $scope.assegnazioneCommerciali[vendorId][k].loc,
//                        count: $scope.assegnazioneCommerciali[vendorId][k].count,
//                        assigned: (ll.indexOf($scope.assegnazioneCommerciali[vendorId][k].loc) > -1)
//                    });
//                }
//            }
//            else {
//                return;
//            }
//        }
        this.clickOnTelemarketing = function (tmkId) {
//controllo se il tmk esiste:
            var tmkIndex = -1;
//            console.log(that.telemarketing);
            for (var i in $scope.telemarketing) {
                if ($scope.telemarketing[i].USERID == tmkId) {
                    tmkIndex = i;
                    break;
                }
            }
            if (tmkIndex === -1) {
                console.log("Telemarketing inestistente");
                return;
            }
            if ($scope.telemarketingSelezionato && $scope.telemarketingSelezionato.USERID === tmkId)
                $scope.telemarketingSelezionato = null;
            else
                $scope.telemarketingSelezionato = $scope.telemarketing[i];
//controllo se è associato:
            var vendorId = -1;
            for (var i in $scope.associazioni) {
                if ($scope.associazioni[i].tmk == tmkId) {
                    vendorId = $scope.associazioni[i].vend;
                    break;
                }
            }
            if (vendorId > -1) { //associato
                if ($scope.commercialeSelezionato.USERID) {
                    if ($scope.commercialeSelezionato.USERID == vendorId) {
                        return;
                    }
                    else {
                        that.clickOnVendor($scope.commercialeSelezionato.USERID);
                        that.clickOnVendor(vendorId);
                    }
                }
                else {

                    that.clickOnVendor(vendorId);
                }
            }
//            else { //libero
////se c'è un venditore aperto associo
//                if ($scope.commercialeSelezionato.USERID) {
//                    that.mascheraAssocia[1].open();
//                    that.makeAssociazione = {
//                        vendorName: $scope.commercialeSelezionato.NOME + " " + $scope.commercialeSelezionato.COGNOME,
//                        tmkName: $scope.telemarketing[tmkIndex].NOME + " " + $scope.telemarketing[tmkIndex].COGNOME,
//                        vendorId: $scope.commercialeSelezionato.USERID,
//                        tmkId: tmkId,
//                        locs: []
//                    };
//                    for (var k in $scope.assegnazioneCommerciali[$scope.commercialeSelezionato.USERID]) {
//                        that.makeAssociazione.locs.push({
//                            loc: $scope.assegnazioneCommerciali[$scope.commercialeSelezionato.USERID][k].loc,
//                            count: $scope.assegnazioneCommerciali[$scope.commercialeSelezionato.USERID][k].count,
//                            assigned: false
//                        });
//                    }
//                }
//                else {
//                    return;
//                }
//            }
        };
        this.assegnaLoc = function (loc) {
            if (!that.makeAssociazione.vendorId || !that.makeAssociazione.tmkId || !that.makeAssociazione.locs) {
                return;
            }
            for (var i in that.makeAssociazione.locs) {
                if (that.makeAssociazione.locs[i].loc === loc) {
                    that.makeAssociazione.locs[i].assigned = !(that.makeAssociazione.locs[i].assigned);
                    return;
                }
            }
        };
        this.associaTmkVend = function (loc) {
            if (!$scope.commercialeSelezionato || !$scope.telemarketingSelezionato)
                return;
            var TMK = angular.copy($scope.telemarketingSelezionato); // sicurezza contro utenti dal click nevrastenico
            var VEND = angular.copy($scope.commercialeSelezionato);
            //controllo se é associato ad un altro venditore
            for (var i in $scope.associazioni) {
                if ($scope.associazioni[i].tmk == TMK.USERID &&
                        $scope.associazioni[i].vend != VEND.USERID) {
                    console.log("TMK già associato ad altro venditore, liberare prima.");
                    return;
                }
            }
            var found = false;
            for (var i in $scope.associazioni) {
                if ($scope.associazioni[i].tmk == TMK.USERID &&
                        $scope.associazioni[i].vend == VEND.USERID
                        && $scope.associazioni[i].loc == loc) {
                    $scope.associazioni.splice(i, 1);
                    found = true;
                    break;
                }
            }
            if (!found) {
                $scope.associazioni.push({
                    camp: that.campaign,
                    loc: loc,
                    tmk: TMK.USERID,
                    vend: VEND.USERID
                });
            }


            var ll = [];
            for (var i in $scope.associazioni) {
                if ($scope.associazioni[i].tmk == TMK.USERID) {
                    ll.push($scope.associazioni[i].loc);
                }
            }

            DataService.postData("setTmkVendAssociation", {camp: that.campaign, tmk: TMK.USERID, vend: VEND.USERID, locs: ll.join("|")}, "setTmkVendAssociationPerformed", {},
                    function (t) {
                        that.clickOnVendor(VEND.USERID);
                        that.clickOnVendor(VEND.USERID);
                    },
                    function (t) { ////////////////////////////////////

                    });
        };
        this.sceltaCampagna = function () {
            $rootScope.$on("TheMask said something", function (event, mass) {
                if (typeof (mass) === "object" && typeof (mass.maskid) !== "undefined" && typeof (mass.btn) !== "undefined") {
                    if (mass.maskid === that.mascheraAssocia[0]) {
                        if (mass.btn === 0) {
                            that.associaTmkVend(that.makeAssociazione.tmkId, that.makeAssociazione.vendorId, that.makeAssociazione.locs);
                            that.mascheraAssocia[1].close();
                        }
                        else if (mass.btn === 1) {
                            that.makeAssociazione = {};
                            that.mascheraAssocia[1].close();
                        }
                    }
                }
            });
        };

    });
})();



