(function () {
    var app = angular.module("GestioneIncassi", ['ngDragDrop', 'SPLITSO', 'FILTEREDTABLE', 'DATAMODULE', 'TSNWCLIENT', 'THEMASK'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });

    app.controller("FilterController", function () {

    });
    app.controller("ListController", function ($rootScope, $scope, FilteredTable, TheMask, $timeout, ConfiguratorService, GetDataService, SendDataService, $scope) {
        this.showNotShow = {loader: true, tabella: false, splitSo: false}
        this.tabella = FilteredTable.newTable();
        this.mascheraNuovoIncasso = TheMask.newMask();
        this.mascheraNuovoIncassoScelta = TheMask.newMask();
        this.mascheraNuovoIncassoAssegno = TheMask.newMask();
        this.mascheraNuovoIncassoBonifico = TheMask.newMask();
        this.mascheraNuovoIncassoContante = TheMask.newMask();
        this.mascheraNuovaBanca = TheMask.newMask();
        this.mascheraNostraBanca = TheMask.newMask();
        this.mascheraDates = TheMask.newMask();

        this.loaderImagePath = function () {
            var url = document.URL;
            var urlsplit = url.split("/public/");
            return urlsplit[0] + "/public/img/ajax-loader-h.gif";
        };

        this.nostraBancaAllowed = {
            '0': 'Non Assegnata',
            '1': 'CREDEM',
            '2': 'BPM',
            '3': 'BCC',
            '4': 'UNICREDIT'
        };
        this.newCollection = {partial: {cliente: "", suggerimentiCliente: "", abi: "", cab: "", erroreCliente: false},
            ending: {
                amount: "",
                type: "",
                description: "",
                accountid: "",
                state: "",
                ref: "",
                bankid: "",
                emissiondate: "",
                receiptdate: "",
                depositdate: "",
                valuedate: "",
                ourbankid: ""
            },
            aux: {}};
        this.newBank = {partial: {},
            ending: {
                bankcab: "",
                bankabi: "",
                bankname: "",
                bankdescription: "",
                bankstreet: "",
                bankcode: "",
                bankcity: "",
                bankprovince: ""
            },
            aux: {}};
        this.splitSo = {
            partial: {},
            ending: {},
            draw: {},
            aux: {}
        };
        $scope.showLeftBar = true;
        $scope.showRightBar = true;
        var url = document.URL;
        if (url[url.length - 1] === "/")
            url = url.substring(0, url.length - 1);
        url += ".ws";
//        console.log(url);
        ConfiguratorService.get(url);
        this.collections = {};
        this.collectionsLoaded = {};
        var that = this;
        $rootScope.$on("confLoaded", function (event, mass) {
            var fields = ["iid", "accountid", "amount", "type", "description", "state", "ref", "bankid", "emissiondate", "receiptdate", "depositdate", "valuedate", "ourbankid"];
            var txtfilter = ["exact|partial", "exact|partial", "around", "explicit", "none", "explicit", "exact|partial", "bankid", "emissiondate", "receiptdate", "depositdate", "valuedate", "ourbankid"];
            var flags = ["", "", "", "Dato mancante|ASS|BON|CON", "", "", "", "", "", "", "", "", ""];
            var labels = ["N", "Cliente", "Ammontare", "Tipo", "Descrizione", "Stato", "Riferimento", "Banca", "Data emissione", "Data ricezione", "Data versamento", "Data valuta", "Nostra banca"];
            var types = ["integer", "string", "currency", "pl", "longtext", "pl", "string", "string", "date", "date", "date", "date", "string"];
            var aligns = ["center", "left", "right", "center", "left", "center", "left", "left", "left", "left", "left", "left", "left"];
            var operations = {
                deleteCollection: {
                    name: "Cancella incasso",
                    availability: [
                        ["rowSelectionLength === 1", "rowSelectionLength"]
                    ],
                    callback: that.deleteCollection,
                    arguments: ["{selectedRow}"]
                },
                splitSO: {
                    name: "Split SO",
                    availability: [
                        ["rowSelectionLength === 1", "rowSelectionLength"]
                    ],
                    callback: that.proceduraSplitSo,
                    arguments: ["{selectedRow}"]
                },
                changeStatus: {
                    name: "Cambia stato",
                    availability: [
                        ["rowSelectionLength === 1", "rowSelectionLength"]
                    ],
                    callback: that.proceduraCambioStato,
                    arguments: ["{selectedRow}"]

                },
                changeOurB: {
                    name: "Nostra banca",
                    availability: [
                        ["rowSelectionLength === 1", "rowSelectionLength"]
                    ],
                    callback: that.changeOurBank,
                    arguments: ["{selectedRow}"]

                },
                changeDates: {
                    name: "Date",
                    availability: [
                        ["rowSelectionLength === 1", "rowSelectionLength"]
                    ],
                    callback: that.changeDates,
                    arguments: ["{selectedRow}"]
                },
                toDistinta: {
                    name: "Distinta",
                    availability: [
                        ["rowSelectionLength >= 1", "rowSelectionLength"],
                        ["selectedRows.Stato === 'ricevuto'", "selectedRows"]
                    ],
                    callback: that.toDistinta,
                    arguments: ["{selectedRows}"]
                },
                toVersato: {
                    name: "Versato",
                    availability: [
                        ["rowSelectionLength >= 1", "rowSelectionLength"],
                        ["selectedRows.Stato === 'distinta'", "selectedRows"]
                    ],
                    callback: that.toVersato,
                    arguments: ["{selectedRows}"]
                }
            };
            FilteredTable.configureTable(that.tabella[0], {fields: fields, labels: labels, types: types, aligns: aligns, flags: flags, tracking: "N"});
            FilteredTable.setOps(that.tabella[0], operations);
            that.showNotShow.loader = false;
            that.showNotShow.tabella = true;
            GetDataService("getCollections", that.collections, that.collectionsLoaded, "getCollectionsLoaded", []);
        });
        $rootScope.$on("reloadTable", function (event, mass) {
            var fields = ["iid", "accountid", "amount", "type", "description", "state", "ref", "bankid", "emissiondate", "receiptdate", "depositdate", "valuedate", "ourbankid"];
            var txtfilter = ["exact|partial", "exact|partial", "around", "explicit", "none", "explicit", "exact|partial", "bankid", "emissiondate", "receiptdate", "depositdate", "valuedate", "ourbankid"];
            var flags = ["", "", "", "Dato mancante|ASS|BON|CON", "", "", "", "", "", "", "", "", ""];
            var labels = ["N", "Cliente", "Ammontare", "Tipo", "Descrizione", "Stato", "Riferimento", "Banca", "Data emissione", "Data ricezione", "Data versamento", "Data valuta", "Nostra banca"];
            var types = ["integer", "string", "currency", "pl", "longtext", "pl", "string", "string", "date", "date", "date", "date", "string"];
            var aligns = ["center", "left", "right", "center", "left", "center", "left", "left", "left", "left", "left", "left", "left"];
            FilteredTable.configureTable(that.tabella[0], {fields: fields, labels: labels, types: types, aligns: aligns, flags: flags});
            that.showNotShow.loader = false;
            that.showNotShow.tabella = true;
            GetDataService("getCollections", that.collections, that.collectionsLoaded, "getCollectionsLoaded", []);
        });
        $rootScope.$on("ToggleLeftBar", function (event, mass) {
            $scope.showLeftBar = (($scope.showLeftBar) ? false : true);
            $scope.$digest();
        });
        $rootScope.$on("ToggleRightBar", function (event, mass) {
            $scope.showRightBar = (($scope.showRightBar) ? false : true);
            $scope.$digest();
        });
        $rootScope.$on("getCollectionsLoaded", function (event, mass) {

            FilteredTable.setData(that.tabella[0], that.collections.data);

//            console.log(that.collections);
        });
        this.getSoPerformed = {};
        this.getCollSoPerformed = {};
        this.drawSplitFromPartialData = function () {
            that.splitSo.draw.so = [];
            that.splitSo.draw.inv = [];
            var sumSo = 0;
            for (var i in that.splitSo.partial.so) {
                console.log(that.splitSo.partial.so[i]);
                that.splitSo.draw.so[i] = {
                    amountSo: Number(that.splitSo.partial.so[i].ammontareSo),
                    idSo: that.splitSo.partial.so[i].idSo,
                    numSo: that.splitSo.partial.so[i].numSo,
                    subSo: that.splitSo.partial.so[i].soggettoSo,
                    statusSo: that.splitSo.partial.so[i].statoSo,
                    scadSo: that.splitSo.partial.so[i].scadenzaSo,
                };
                that.splitSo.draw.inv[i] = [];
                for (var j in that.splitSo.partial.so[i].fatture) {
                    that.splitSo.draw.inv[i].push({
                        amountInv: Number(that.splitSo.partial.so[i].fatture[j].ammontareInv),
                        dateInv: that.splitSo.partial.so[i].fatture[j].dateInv,
                        idInv: that.splitSo.partial.so[i].fatture[j].idInv,
                        numInv: that.splitSo.partial.so[i].fatture[j].numeroInv,
                        subInv: that.splitSo.partial.so[i].fatture[j].soggettoInv
                    });
                }
                sumSo += Number(that.splitSo.partial.so[i].ammontareSo);
            }
            that.splitSo.aux.sumSo = sumSo;
            for (var i in that.splitSo.partial.so) {
                that.splitSo.draw.so[i].ll = Number(100 * that.splitSo.partial.so[i].ammontareSo / sumSo);
                for (var j in that.splitSo.draw.inv[i]) {
                    that.splitSo.draw.inv[i][j].ll = Number(100 * that.splitSo.draw.inv[i][j].amountInv / that.splitSo.partial.so[i].ammontareSo);
                }
            }
            that.splitSo.draw.collSo = [];
            that.splitSo.draw.collFree = [];
            for (i in that.splitSo.partial.coll) {
                if (that.splitSo.partial.coll[i].col2So === null) {
                    that.splitSo.draw.collFree.push({
                        amountCol: that.splitSo.partial.coll[i].ammontareCol,
                        dateCol: that.splitSo.partial.coll[i].dateCol,
                        idCol: that.splitSo.partial.coll[i].idCol,
                        refCol: that.splitSo.partial.coll[i].refCol,
                        statusCol: that.splitSo.partial.coll[i].statusCol,
                        typeCol: that.splitSo.partial.coll[i].typeCol,
                        ll: 100
//                        ll: Number(100 * that.splitSo.partial.coll[i].ammontareCol / that.splitSo.draw.so[soIndex].amountSo)
                    });
                }
                else if (that.splitSo.partial.coll[i].col2So !== "") {
                    var soIndex = -1;
                    for (j in that.splitSo.draw.so) {
                        console.log("Cerco " + that.splitSo.partial.coll[i].col2So + " in");
                        console.log(that.splitSo.draw.so[j]);
                        if (that.splitSo.draw.so[j].idSo == that.splitSo.partial.coll[i].col2So) {
                            soIndex = j;
                            break;
                        }
                    }
                    if (soIndex === -1) {
                        console.log("Errore coll2so");
                        console.log(that.splitSo.draw.so);
                        console.log(that.splitSo.partial.coll[i].col2So);
                    }
                    if (typeof (that.splitSo.draw.collSo[soIndex]) === "undefined")
                        that.splitSo.draw.collSo[soIndex] = [];
                    that.splitSo.draw.collSo[soIndex].push({
                        amountCol: that.splitSo.partial.coll[i].col2SoAmount,
                        col2So: that.splitSo.partial.coll[i].col2So,
                        dateCol: that.splitSo.partial.coll[i].dateCol,
                        idCol: that.splitSo.partial.coll[i].idCol,
                        refCol: that.splitSo.partial.coll[i].refCol,
                        statusCol: that.splitSo.partial.coll[i].statusCol,
                        typeCol: that.splitSo.partial.coll[i].typeCol,
                        ll: Number(100 * that.splitSo.partial.coll[i].col2SoAmount / that.splitSo.draw.so[soIndex].amountSo)
                    });
                }
            }
            that.showNotShow.loader = false;
            that.showNotShow.splitSo = true;
            console.log(that.splitSo.draw);

        }

        this.toDistinta = function (collections) {
            that.toDistintaPerformed = {};
            var collectionIds = [];
            if (collections.length === 0)
                return;
            for (var i in collections) {
                collectionIds.push(collections[i].N);
            }
            SendDataService("toDistinta", collectionIds, that.toDistintaPerformed, "toDistintaPerformed", []);
        }
        $rootScope.$on("toDistintaPerformed", function (event, mass) {
            window.location.reload();
        });
        this.toVersato = function (collections) {
            that.toVersatoPerformed = {};
            var collectionIds = [];
            if (collections.length === 0)
                return;
            for (var i in collections) {
                collectionIds.push(collections[i].N);
            }
            SendDataService("toVersato", collectionIds, that.toVersatoPerformed, "toVersatoPerformed", []);
        }
        $rootScope.$on("toVersatoPerformed", function (event, mass) {
            window.location.reload();
        });
        this.proceduraSplitSo = function (collection) {
            //trovo azienda
            that.showNotShow.loader = true;
            that.showNotShow.tabella = false;
            $scope.showLeftBar = false;
            $scope.showRightBar = false;
            collection.Cliente = "";
            that.getSoPerformed = {};
            that.getCollSoPerformed = {};
            SendDataService("getSo", collection, that.getSoPerformed, "getSoPerformed", []);
            $rootScope.$on("getSoPerformed", function (event, mass) {
                that.splitSo.partial.so = that.getSoPerformed.result;
                for (var i in that.splitSo.partial.so) {
                    if (typeof (that.splitSo.partial.so[i].fatture) !== "undefined" && that.splitSo.partial.so[i].fatture) {
                        that.splitSo.partial.so[i].fatture = JSON.parse(that.splitSo.partial.so[i].fatture.replace(/\"/g, "\""));
                    }
                    else {
                        that.splitSo.partial.so[i].fatture = {};
                    }
                }
                SendDataService("getCollSo", collection, that.getCollSoPerformed, "getCollSoPerformed", []);
            });
            $rootScope.$on("getCollSoPerformed", function (event, mass) {
                that.splitSo.partial.coll = that.getCollSoPerformed.result;

                that.drawSplitFromPartialData();
            });
        }
        this.col2SoPerformed = {};
        $rootScope.$on("PerformCol2So", function (event, mass) {
            SendDataService("col2So", mass, that.col2SoPerformed, "col2SoPerformed", [mass]);
        });
        this.splitSoBack = function () {
            //trovo azienda
            that.showNotShow.tabella = true;
            that.showNotShow.splitSo = false;
            that.splitSo = {
                partial: {},
                ending: {},
                draw: {},
                aux: {}
            };

        }
        this.deleteCollectionPerformed = {};
        this.deleteCollection = function (collection) {
            if (typeof (collection) !== "object" || !collection || typeof (collection) === "undefined")
                return;
            collection.Cliente = "";
            SendDataService("deleteCollection", collection, that.deleteCollectionPerformed, "deleteCollectionPerformed", []);
            $rootScope.$on("deleteCollectionPerformed", function (event, mass) {
                console.log(that.deleteCollectionPerformed);
                if (that.deleteCollectionPerformed.result) {
                    location.reload();
                }
                else {
                    alert("La cancellazione non è andata a buon fine. Riprovare!");
                }
            });
        }




//        console.log(TheMask.getMask());
        this.tableClass = function () {
            if ($scope.showLeftBar && $scope.showRightBar)
                return "col-sm-10";
            else if (($scope.showLeftBar && !$scope.showRightBar) || (!$scope.showLeftBar && $scope.showRightBar))
                return "col-sm-11";
            else if (!$scope.showLeftBar && !$scope.showRightBar)
                return "col-sm-12";
        };


        this.suggerimentiCliente = {};
        this.suggerimentiClienteLoaded = {};
        this.insertCollectionLoaded = {};
        this.insertBankLoaded = {};
        this.controlSuggerimentiCliente = false;
        this.numeroSuggerimentoCliente = 0;
        this.numeroSuggerimentoClienteRicevuto = 0;
        this.ultimoSuggerimentoClienteRichiesto = "";
        this.ultimoSuggerimentoClienteRicevuto = "";

        this.proceduraNuovoIncasso = function () {
            that.controlSuggerimentiCliente = true;
            that.mascheraNuovoIncasso[1].open();
            that.updateSuggerimentiCliente();
        }
        this.proceduraNuovaBanca = function () {
            that.mascheraNuovaBanca[1].open();
        }
        this.inserisciNuovoIncasso = function () {
            SendDataService("insertCollection", that.newCollection.ending, that.insertCollectionLoaded, "insertCollectionPerformed", []);
            $rootScope.$on("insertCollectionPerformed", function (event, mass) {
                console.log(that.insertCollectionLoaded);
                if (that.insertCollectionLoaded.result) {
                    location.reload();
                }
                else {
                    alert("L'inserimento non è andato a buon fine. Riprovare!");
                }
            })
        }
        this.inserisciNuovaBanca = function () {
            SendDataService("insertBank", that.newBank.ending, that.insertBankLoaded, "insertBankPerformed", []);
            $rootScope.$on("insertBankPerformed", function (event, mass) {
                console.log(that.insertBankLoaded);
                if (that.insertBankLoaded.result) {
                    alert("L'inserimento è andato a buon fine.");
                }
                else {
                    if (typeof (that.insertBankLoaded.error) === "string"
                            && that.insertBankLoaded.error.indexOf("Duplicate entry") > -1) {
                        alert("L'inserimento non è andato a buon fine, la banca inserita potrebbe essere già presente.");
                    }
                    else
                        alert("L'inserimento non è andato a buon fine. Riprovare!");
                }
            })
        }
        this.updateSuggerimentiCliente = function () {
            if (!that.controlSuggerimentiCliente)
                return;
            if (that.newCollection.partial.cliente !== "" && that.suggerimentiCliente.partialname === that.newCollection.partial.cliente
                    && that.suggerimentiCliente.partialname !== that.ultimoSuggerimentoClienteRicevuto
                    && that.suggerimentiCliente.partialname !== that.ultimoSuggerimentoClienteRichiesto) {
                that.getSuggerimentiCliente();
            }
            else {
                that.suggerimentiCliente.partialname = that.newCollection.partial.cliente;
                $timeout(function () {
                    that.updateSuggerimentiCliente();
                }, 1000);
            }
        };
        this.getSuggerimentiCliente = function () {
            that.suggerimentiCliente.partialname = that.suggerimentiCliente.partialname.replace("&", "%26");
            that.ultimoSuggerimentoClienteRichiesto = that.suggerimentiCliente.partialname;

            SendDataService("getSuggerimentiCliente", that.suggerimentiCliente, that.suggerimentiClienteLoaded, "getSuggerimentiClienteLoaded", [this.numeroSuggerimentoCliente, this.suggerimentiCliente.partialname]);
            that.numeroSuggerimentoCliente++;
            $timeout(function () {
                that.updateSuggerimentiCliente();
            }, 1000);
            $rootScope.$on("getSuggerimentiClienteLoaded", function (event, mass) {
                if (typeof (mass) !== "undefined" && typeof (mass.length) !== "undefined" && typeof (mass[0]) !== "undefined") {
                    if (mass[0] >= that.numeroSuggerimentoClienteRicevuto) {
                        that.numeroSuggerimentoClienteRicevuto = mass[0];
//                        console.log(that.suggerimentiClienteLoaded.result);
                        that.newCollection.partial.suggerimentiCliente = that.suggerimentiClienteLoaded.result;
                        that.ultimoSuggerimentoClienteRicevuto = mass[1];
                    }
                }
            });
        };
        this.useSuggerimentoCliente = function (id, name) {
            that.newCollection.ending.accountid = id;
            that.newCollection.partial.cliente = name;
            that.newCollection.aux.accountname = name;
        };
        this.suggerimentiBanca = {};
        this.suggerimentiBancaLoaded = {};
        this.controlSuggerimentiBanca = false;
        this.numeroSuggerimentoBanca = 0;
        this.numeroSuggerimentoBancaRicevuto = 0;
        this.ultimoSuggerimentoBancaRichiesto = "";
        this.ultimoSuggerimentoBancaRicevuto = "";

        this.updateSuggerimentiBanca = function () {
            if (!that.controlSuggerimentiBanca)
                return;
            if ((that.newCollection.partial.abi !== "" || that.newCollection.partial.cab !== "")
                    && that.suggerimentiBanca.partialname === that.newCollection.partial.abi + "|" + that.newCollection.partial.cab
                    && that.suggerimentiBanca.partialname !== that.ultimoSuggerimentoBancaRicevuto
                    && that.suggerimentiBanca.partialname !== that.ultimoSuggerimentoBancaRichiesto) {
                that.getSuggerimentiBanca();
            }
            else {
                that.suggerimentiBanca.partialname = that.newCollection.partial.abi + "|" + that.newCollection.partial.cab;
                $timeout(function () {
                    that.updateSuggerimentiBanca();
                }, 1000);
            }
        };
        this.getSuggerimentiBanca = function () {
            that.ultimoSuggerimentoBancaRichiesto = that.suggerimentiBanca.partialname;
            SendDataService("getSuggerimentiBanca", that.suggerimentiBanca, that.suggerimentiBancaLoaded, "getSuggerimentiBancaLoaded", [this.numeroSuggerimentoBanca, this.suggerimentiBanca.partialname]);
            that.numeroSuggerimentoBanca++;
            $timeout(function () {
                that.updateSuggerimentiBanca();
            }, 1000);
            $rootScope.$on("getSuggerimentiBancaLoaded", function (event, mass) {
                if (typeof (mass) !== "undefined" && typeof (mass.length) !== "undefined" && typeof (mass[0]) !== "undefined") {
                    if (mass[0] >= that.numeroSuggerimentoBancaRicevuto) {
                        that.numeroSuggerimentoBancaRicevuto = mass[0];
//                        console.log(that.suggerimentiClienteLoaded.result);
                        that.newCollection.partial.suggerimentiBanca = that.suggerimentiBancaLoaded.result;
                        that.ultimoSuggerimentoBancaRicevuto = mass[1];
                    }
                }
            });
        };
        this.useSuggerimentoBanca = function (banca) {
            that.newCollection.ending.bankid = banca.bankid;
            that.newCollection.partial.street = banca.bankstreet;
            that.newCollection.partial.city = banca.bankcity;
            that.newCollection.partial.abi = banca.bankabi;
            that.newCollection.partial.cab = banca.bankcab;
            that.newCollection.partial.banca = banca.bankname;
        };
        $rootScope.$on("TheMask said something", function (event, mass) {
            if (typeof (mass) === "object" && typeof (mass.maskid) !== "undefined" && typeof (mass.btn) !== "undefined") {
                if (mass.maskid === that.mascheraNuovoIncasso[0]) { //maschera scelta cliente
                    if (mass.btn === 0) {
                        if (that.newCollection.ending.accountid !== "") { // scelta cliente avanti
                            that.newCollection.partial.erroreCliente = false;
                            that.newCollection.partial.cliente = "";
                            that.newCollection.partial.suggerimentiCliente = "";
                            that.mascheraNuovoIncasso[1].close();
                            that.mascheraNuovoIncassoScelta[1].open();
                        }
                        else {
                            that.newCollection.partial.erroreCliente = true;
                        }
                    }
                    else if (mass.btn === 1) { // scelta cliente annulla
                        that.newCollection.partial.erroreCliente = false;
                        that.mascheraNuovoIncasso[1].close();
                        that.newCollection.ending = {
                            amount: "",
                            type: "",
                            description: "",
                            accountid: "",
                            state: "",
                            ref: "",
                            bankid: "",
                            emissiondate: "",
                            receiptdate: "",
                            depositdate: "",
                            valuedate: "",
                            ourbankid: ""
                        };
                        that.newCollection.partial.cliente = "";
                        that.newCollection.partial.suggerimentiCliente = "";
                        that.controlSuggerimentiCliente = false;
                    }
                }
                if (mass.maskid === that.mascheraNuovoIncassoScelta[0]) { //maschera scelta tipo
                    if (mass.btn === 0) {
                        if (that.newCollection.ending.type !== "") { //scelta tipo avanti
                            that.newCollection.partial.erroreTipo = false;
                            that.mascheraNuovoIncassoScelta[1].close();
                            that.controlSuggerimentiBanca = true;
                            that.updateSuggerimentiBanca();
                            switch (that.newCollection.ending.type) {
                                case 1:
                                case '1':
                                    that.mascheraNuovoIncassoAssegno[1].open();
                                    break;
                                case 2:
                                case '2':
                                    that.mascheraNuovoIncassoBonifico[1].open();
                                    break;
                                case 3:
                                case '3':
                                    that.mascheraNuovoIncassoContante[1].open();
                                    break;
                            }
                        }
                        else {
                            that.newCollection.partial.erroreTipo = true;
                        }
                    }
                    else if (mass.btn === 1) {
                        that.newCollection.partial.erroreTipo = false;
                        that.mascheraNuovoIncassoScelta[1].close();
                        that.newCollection.ending = {
                            amount: "",
                            type: "",
                            description: "",
                            accountid: "",
                            state: "",
                            ref: "",
                            bankid: "",
                            emissiondate: "",
                            receiptdate: "",
                            depositdate: "",
                            valuedate: "",
                            ourbankid: ""
                        };
                    }
                }
                if (mass.maskid === that.mascheraNuovoIncassoAssegno[0]) { //maschera assegno
                    if (mass.btn === 0) {
                        if (that.newCollection.ending.amount !== ""
                                && !isNaN(parseFloat(that.newCollection.ending.amount))
                                && isFinite(that.newCollection.ending.amount)) { //assegno avanti
                            that.newCollection.partial.erroreAmount = false;
                            that.mascheraNuovoIncassoAssegno[1].close();
                            that.controlSuggerimentiBanca = false;
                            that.inserisciNuovoIncasso();
                        }
                        else {
                            that.newCollection.partial.erroreAmount = true;
                        }
                    }
                    else if (mass.btn === 1) { //assegno annulla
                        that.newCollection.partial.erroreAmount = false;
                        that.mascheraNuovoIncassoAssegno[1].close();
                        that.newCollection.partial.street = "";
                        that.newCollection.partial.city = "";
                        that.newCollection.partial.abi = "";
                        that.newCollection.partial.cab = "";
                        that.newCollection.partial.banca = "";
                        that.newCollection.partial.suggerimentiBanca = "";
                        that.newCollection.ending = {
                            amount: "",
                            type: "",
                            description: "",
                            accountid: "",
                            state: "",
                            ref: "",
                            bankid: "",
                            emissiondate: "",
                            receiptdate: "",
                            depositdate: "",
                            valuedate: "",
                            ourbankid: ""
                        };
                    }
                }
                if (mass.maskid === that.mascheraNuovoIncassoBonifico[0]) { //maschera bonifico
                    if (mass.btn === 0) {
                        if (that.newCollection.ending.amount !== ""
                                && !isNaN(parseFloat(that.newCollection.ending.amount))
                                && isFinite(that.newCollection.ending.amount)) { //bonifico avanti
                            that.newCollection.partial.erroreAmount = false;
                            that.mascheraNuovoIncassoBonifico[1].close();
                            that.controlSuggerimentiBanca = false;
                            that.inserisciNuovoIncasso();
                        }
                        else {
                            that.newCollection.partial.erroreAmount = true;
                        }
                    }
                    else if (mass.btn === 1) { //bonifico annulla
                        that.newCollection.partial.erroreAmount = false;
                        that.mascheraNuovoIncassoBonifico[1].close();
                        that.newCollection.partial.street = "";
                        that.newCollection.partial.city = "";
                        that.newCollection.partial.abi = "";
                        that.newCollection.partial.cab = "";
                        that.newCollection.partial.banca = "";
                        that.newCollection.partial.suggerimentiBanca = "";
                        that.newCollection.ending = {
                            amount: "",
                            type: "",
                            description: "",
                            accountid: "",
                            state: "",
                            ref: "",
                            bankid: "",
                            emissiondate: "",
                            receiptdate: "",
                            depositdate: "",
                            valuedate: "",
                            ourbankid: ""
                        };
                    }
                }
                if (mass.maskid === that.mascheraNuovoIncassoContante[0]) { //maschera contante
                    if (mass.btn === 0) {
                        if (that.newCollection.ending.amount !== ""
                                && !isNaN(parseFloat(that.newCollection.ending.amount))
                                && isFinite(that.newCollection.ending.amount)) { //contante avanti
                            that.newCollection.partial.erroreAmount = false;
                            that.mascheraNuovoIncassoContante[1].close();
                            that.controlSuggerimentiBanca = false;
                            that.inserisciNuovoIncasso();
                        }
                        else {
                            that.newCollection.partial.erroreAmount = true;
                        }
                    }
                    else if (mass.btn === 1) { //contante annulla
                        that.newCollection.partial.erroreAmount = false;
                        that.mascheraNuovoIncassoContante[1].close();
                        that.newCollection.ending = {
                            amount: "",
                            type: "",
                            description: "",
                            accountid: "",
                            state: "",
                            ref: "",
                            bankid: "",
                            emissiondate: "",
                            receiptdate: "",
                            depositdate: "",
                            valuedate: "",
                            ourbankid: ""
                        };
                    }
                }
                if (mass.maskid === that.mascheraNuovaBanca[0]) { //maschera nuova banca
                    if (mass.btn === 0) { // nuova banca inserisci
                        if (that.newBank.ending.bankabi.length
                                && that.newBank.ending.bankcab.length
                                && that.newBank.ending.bankname.length) {
                            that.inserisciNuovaBanca();
                            that.mascheraNuovaBanca[1].close();
                        }
                        else {
                            that.newCollection.partial.erroreNuovaBanca = true;
                        }
                    }
                    else if (mass.btn === 1) { //nuova banca annulla
                        that.newCollection.partial.erroreNuovaBanca = false;
                        that.mascheraNuovaBanca[1].close();
                        that.newBank.ending = {
                            bankcab: "",
                            bankabi: "",
                            bankname: "",
                            bankdescription: "",
                            bankstreet: "",
                            bankcode: "",
                            bankcity: "",
                            bankprovince: ""
                        };
                    }
                }
                if (mass.maskid === that.mascheraNuovoStato[0]) { //maschera nuovo stato
                    if (mass.btn === 0) { // nuovo stato cambia
                        that.changeStatus();
                    }
                    else if (mass.btn === 1) { //nuovo stato annulla
                        that.newstatus = {idCol: "", status: "", newstatus: ""};
                        that.nuovoStatoAllowed = {};
                        that.mascheraNuovoStato[1].close();
                    }
                }
                if (mass.maskid === that.mascheraNostraBanca[0]) { //maschera nostra banca
                    if (mass.btn === 0) { // nostra banca assegna
                        SendDataService("changeOurBank", that.nostraBanca, that.changeOurBankLoaded, "changeOurBankLoaded", []);
                    }
                    else if (mass.btn === 1) { //nostra banca annulla
                        that.nostraBanca = {ourbankid: "", idCol: ""};

                    }
                }
                if (mass.maskid === that.mascheraDates[0]) { //maschera date
                    if (mass.btn === 0) { // date conferma
                        if (!(that.dates.emissiond))
                            that.dates.emissiond = "";
                        if (!(that.dates.receiptd))
                            that.dates.receiptd = "";
                        if (!(that.dates.depositd))
                            that.dates.depositd = "";
                        if (!(that.dates.valued))
                            that.dates.valued = "";
                        SendDataService("changeDates", that.dates, that.changeDatesLoaded, "changeDatesLoaded", []);
                    }
                    else if (mass.btn === 1) { //date annulla
                        that.dates = {
                            emissiond: "",
                            receiptd: "",
                            depositd: "",
                            valued: "",
                            idCol: ""
                        };
                        that.mascheraDates[1].close();

                    }
                }

            }
        });

        this.changeDates = function (collection) {

            that.changeDatesLoaded = {};
            that.dates = {
                emissiond: collection["Data emissione"],
                receiptd: collection["Data ricezione"],
                depositd: collection["Data versamento"],
                valued: collection["Data valuta"],
                idCol: collection.N
            };
            that.mascheraDates[1].open();
            $rootScope.$on("changeDatesLoaded", function (event, mass) {
                if (that.changeDatesLoaded.result) {
                    that.mascheraDates[1].close();
                    location.reload();

                }
                else {
                    that.mascheraDates[1].close();
                    alert("L'aggiornamento non è andato a buon fine. Riprovare!");
                }
            });
        }
        this.changeOurBank = function (collection) {
            that.changeOurBankLoaded = {};
            that.nostraBanca = {ourbankid: "", idCol: collection.N};
            that.mascheraNostraBanca[1].open();
            $rootScope.$on("changeOurBankLoaded", function (event, mass) {
                if (that.changeOurBankLoaded.result) {
                    that.mascheraNostraBanca[1].close();
                    location.reload();

                }
                else {
                    that.mascheraNostraBanca[1].close();
                    alert("L'aggiornamento non è andato a buon fine. Riprovare!");
                }
            });
        }

        this.statuses = {
            '1': 'emesso',
            '2': 'ricevuto',
            '3': 'sospeso',
            '4': 'versato',
            '5': 'incassato',
            '6': 'annullato',
            '7': 'garanzia',
            '8': 'protestato',
            '9': 'richiamato',
            '10': 'restituito',
            '11': 'garanzia'
        };
        this.newstatus = {idCol: "", status: "", newstatus: ""};
        this.nuovoStatoAllowed = {};
        this.changeStatusLoaded = {};
        this.mascheraNuovoStato = TheMask.newMask();
        ;
        this.proceduraCambioStato = function (collection) {
            if (typeof (collection) === "object") {
                console.log(collection);
                that.newstatus = {idCol: collection.N, status: collection.statusid, newstatus: ""};
                var allowed = that.changeStatus();
                that.nuovoStatoAllowed = {};
                for (var i in allowed) {
                    that.nuovoStatoAllowed[allowed[i]] = that.statuses[allowed[i]];
                }
                console.log(that.nuovoStatoAllowed);
                that.mascheraNuovoStato[1].open();
                this.newstatus = {idCol: collection.N, status: collection.statusid, newstatus: ""};
            }
        }
        this.changeStatus = function () {
            var allowed = {
                "1": ["2","3","4","5"],
                "2": ["3", "4", "5", "6", "7", "10", "11"],
                "3": ["2"],
                "4": ["5", "8", "9"],
                "5": [],
                "6": [],
                "7": ["2"],
                "8": [],
                "9": [],
                "10": [],
                "11": []
            }
            if (typeof (that.newstatus.newstatus) === "undefined" || that.newstatus.newstatus === "") {
                return allowed[that.newstatus.status];
            }
            else {
                console.log("cambio stato di " + that.newstatus.idCol + " da " + that.newstatus.status + " a " + that.newstatus.newstatus);
                SendDataService("changeStatus", that.newstatus, that.changeStatusLoaded, "changeStatusLoaded", []);
                $rootScope.$on("changeStatusLoaded", function (event, mass) {
                    if (that.changeStatusLoaded.result) {
                        that.mascheraNuovoStato[1].close();
                        location.reload();

                    }
                    else {
                        that.mascheraNuovoStato[1].close();
                        alert("L'aggiornamento non è andato a buon fine. Riprovare!");
                    }
                });
            }
        }
    });
    app.controller("DepositSlipController", function () {
    });


})();

