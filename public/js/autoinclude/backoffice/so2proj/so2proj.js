(function () {
    var app = angular.module("so2proj", ['FILTEREDTABLE', 'TSNWCLIENT'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.filter("FiltraStato", function () {
        return function (input, allowed) {
            var out = [];
            for (var i in input) {
                if (typeof (input[i]["Stato SO"]) !== "undefined" && allowed.indexOf(input[i]["Stato SO"]) > -1) {
                    out.push(input[i]);
                }
            }
            return out;
        }
    });
    app.filter("FiltraTrattato", function () {
        return function (input, allowed) {
            var out = [];
            for (var i in input) {
                if (typeof (input[i]["trattato"]) !== "undefined" && parseInt(input[i]["trattato"]) <= parseInt(allowed)) {
                    out.push(input[i]);
                }
            }
            return out;
        }
    });
    app.filter("FiltraSoggetto", function () {
        return function (input, allowed) {
            var out = [];
            for (var i in input) {
                if (typeof (input[i]["Soggetto SO"]) !== "undefined" && allowed.indexOf(input[i]["Soggetto SO"]) > -1) {
                    out.push(input[i]);
                }
            }
            return out;
        }
    });
    app.controller("ListController", function ($rootScope, $http, $timeout) {
        if (typeof ($rootScope.showNotShow) !== "object") {
            $rootScope.showNotShow = {};
        }
        $rootScope.showNotShow.lista = true;
        $rootScope.showNotShow.detailsLoaded = false;
        this.pages = [];
        this.allowedtrattato = 1;
        this.notallowedstatuses = [];
        this.allowedstatuses = ["Created",
            "Approved",
            "Chiuso archiviato",
            "Sospeso",
            "Chiusura Amministrativa",
            "Chiuso archiviato abortito",
            "Attesa caparra",
            "Delivered",
            "Perizia",
            "Lavori in corso",
            "Chiusura Tecnica",
            "Cancelled"];
        this.notallowedsubjects = [
            "Analisi",
            "Invoice WebService",
            "Web Scouting contratto annuale per Sorgenia",
            "LISPA TRASVERS C18 SERVIZI AUDIT",
            "Sviluppo Processi Operativi",
            "Canone aggiornamento Openwork 2013",
            "Bando consulenza e formazione Prov Bari 04 2012",
            "Progetto Prova",
            "Servizi di Advisor",
            "Sistema di controllo di gestione",
            "Ricerca e selezione Responsabile QA",
            "Attività T&M",
            "CONTRATTO PER ATTIVITA' DI RICERCA E SVILUPPO"
        ];
        this.allowedsubjects = [
            "PROGETTO",
            "PROGETTO CHECK UP BANCARIO",
            "PROGETTO ASSISTENZA POST CHECK UP BANCARIO",
            "PROGETTO UPSELLING"
        ];
        this.totalNumberOfPages = 0;
        $rootScope.soList = [];
        $rootScope.itemsInAPage = 50;
        this.currentPage = 0;
        this.nextIsShown = false;
        this.previousIsShown = false;
        this.addAllowedStatus = function (status) {
            var index = this.notallowedstatuses.indexOf(status);
            if (index > -1) {
                this.notallowedstatuses.splice(index, 1);
                this.allowedstatuses.unshift(status);
            }
        };
        this.removeAllowedStatus = function (status) {
            var index = this.allowedstatuses.indexOf(status);
            if (index > -1) {
                this.allowedstatuses.splice(index, 1);
                this.notallowedstatuses.unshift(status);
            }
        };
        this.addAllowedSubject = function (status) {
            var index = this.notallowedsubjects.indexOf(status);
            if (index > -1) {
                this.notallowedsubjects.splice(index, 1);
                this.allowedsubjects.unshift(status);
            }
        };
        this.removeAllowedSubject = function (status) {
            var index = this.allowedsubjects.indexOf(status);
            if (index > -1) {
                this.allowedsubjects.splice(index, 1);
                this.notallowedsubjects.unshift(status);
            }
        };
        this.scegliSo = function (id) {
            $rootScope.showNotShow.lista = false;
            $rootScope.showNotShow.detailsLoaded = true;
            $rootScope.soScelta = id;
            $rootScope.$emit("SoScelta");
        }
        var that = this;
        this.creaLista = function () {
            $rootScope.soList = [];
            $rootScope.soScelta = "";
            $timeout(function () {
                var url = document.URL.replace("so2proj", "so2projgetsolist.ws");
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.totalNumberOfPages = Math.floor((data.length - 1) / ($rootScope.itemsInAPage)) + 1;
                            for (var i = 0; i < that.totalNumberOfPages; i++) {
                                that.pages.push((i + 1));
                            }
                            $rootScope.soList = data;
                            for (var i in $rootScope.soList){
                                $rootScope.soList[i].trattato = parseInt($rootScope.soList[i].trattato);
                            }
//                            console.log(data);
//                        pippo.organizeData();
                            that.listLoaded = true;
                            that.nextIsShown = (that.currentPage < (that.totalNumberOfPages - 1));
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 500);
        };
        this.creaLista();
        $rootScope.$on("RicaricaLista", function () {
            $timeout(function () {
                that.listLoaded = false;
                $rootScope.showNotShow.detailsLoaded = false;
                that.creaLista();
            }, 500);
        });
    });
    app.controller("ModificaController", function ($rootScope, $http, $timeout) {

        this.updateProj = {
            "projectname": {
                "label": "Nome progetto",
                "mandatory": true,
                "type": "text",
                "value": ""
            },
            "startdate": {
                "label": "Data apertura",
                "mandatory": false,
                "type": "date",
                "value": ""
            },
            "targetenddate": {
                "label": "Data fine prevista",
                "mandatory": false,
                "type": "date",
                "value": ""
            },
            "actualenddate": {
                "label": "Data fine effettiva",
                "mandatory": false,
                "type": "date",
                "value": ""
            },
            "projectstatus": {
                "label": "Stato progetto",
                "mandatory": false,
                "type": "picklist",
                "picklistValues": "--none--|prospecting|initiated|in progress|waiting for feedback|on hold|completed|delivered|archived|annullato",
                "value": ""
            },
            "projecttype": {
                "label": "Tipo progetto",
                "mandatory": false,
                "type": "picklist",
                "picklistValues": "--none--|AMMINISTRAZIONE - ECONOMIA - FINANZA|PRODUZIONE - DISTRIBUZIONE|COMMERCIALE|DIREZIONALE - ORGANIZZATIVO|CHECK-UP BANCARIO|ASSISTENZA POST CHECK-UP BANCARIO",
                "value": ""
            },
            "linktoaccountscontacts": {
                "label": "Collegato a",
                "mandatory": false,
                "type": "disabled",
                "value": ""
            },
            "assigned_user_id": {
                "label": "Assegnato a",
                "type": "picklist",
                "mandatory": true,
                "value": ""
            },
            "project_no": {
                "label": "Numero progetto",
                "mandatory": false,
                "type": "disabled",
                "value": ""
            },
            "projecturl": {
                "label": "Url progetto",
                "mandatory": false,
                "type": "text",
                "value": ""
            },
            "description": {
                "label": "Descrizione",
                "mandatory": false,
                "type": "textarea",
                "value": ""
            },
            "cf_691": {
                "label": "SO collegata",
                "mandatory": false,
                "type": "disabled",
                "value": ""
            },
            "id": {
                "label": "Id progetto",
                "mandatory": false,
                "type": "disabled",
                "value": ""
            }
        };
        this.newProj = {
            "projectname": {
                "label": "Nome progetto",
                "mandatory": true,
                "type": "text",
                "value": ""
            },
            "startdate": {
                "label": "Data apertura",
                "mandatory": false,
                "type": "date",
                "value": ""
            },
            "targetenddate": {
                "label": "Data fine prevista",
                "mandatory": false,
                "type": "date",
                "value": ""
            },
            "actualenddate": {
                "label": "Data fine effettiva",
                "mandatory": false,
                "type": "date",
                "value": ""
            },
            "projectstatus": {
                "label": "Stato progetto",
                "mandatory": false,
                "type": "picklist",
                "picklistValues": "--none--|prospecting|initiated|in progress|waiting for feedback|on hold|completed|delivered|archived|annullato",
                "value": ""
            },
            "projecttype": {
                "label": "Tipo progetto",
                "mandatory": false,
                "type": "picklist",
                "picklistValues": "--none--|AMMINISTRAZIONE - ECONOMIA - FINANZA|PRODUZIONE - DISTRIBUZIONE|COMMERCIALE|DIREZIONALE - ORGANIZZATIVO|CHECK-UP BANCARIO|ASSISTENZA POST CHECK-UP BANCARIO",
                "value": ""
            },
            "linktoaccountscontacts": {
                "label": "Collegato a",
                "mandatory": false,
                "type": "disabled",
                "value": ""
            },
            "assigned_user_id": {
                "label": "Assegnato a",
                "type": "picklist",
                "mandatory": true,
                "value": ""
            },
            "project_no": {
                "label": "Numero progetto",
                "mandatory": false,
                "type": "disabled",
                "value": ""
            },
            "projecturl": {
                "label": "Url progetto",
                "mandatory": false,
                "type": "text",
                "value": ""
            },
            "description": {
                "label": "Descrizione",
                "mandatory": false,
                "type": "textarea",
                "value": ""
            },
            "cf_691": {
                "label": "SO collegata",
                "mandatory": false,
                "type": "disabled",
                "value": ""
            }
        };
        this.tornaALista = function () {
            $rootScope.soScelta = "";
            $rootScope.showNotShow.lista = true;
            $rootScope.$emit("RicaricaLista");
        };
        this.so = {"Soggetto": "NP", "Numero SO": "NP", "accountid": "NP", "Stato": "NP", "Capo progetto": "NP"};
        this.customer = {"Azienda": "NP", "Num Azienda": "NP"};
        this.assProj = {"Nome progetto": "NP", "Numero progetto": "NP", "projectid": "NP", "url": "NP",
            "Assegnato a": "NP", "Stato progetto": "NP", "Tipo progetto": "NP"};
        this.otherProj = [{"Nome progetto": "NP", "Numero progetto": "NP", "projectid": "NP", "url": "NP",
                "Assegnato a": "NP", "Stato progetto": "NP", "Tipo progetto": "NP"}];
        this.inv = [{"Soggetto": "NP", "Data fattura": "NP", "numero": "NP", "Ammontare": "NP"}];
        var that = this;
        this.compilaDati = function () {
            that.so = {"Soggetto": "NP", "Numero SO": "NP", "accountid": "NP", "Stato": "NP", "Capo progetto": "NP"};
            that.customer = {"Azienda": "NP", "Num Azienda": "NP"};
            that.assProj = {"Nome progetto": "NP", "Numero progetto": "NP", "projectid": "NP", "url": "NP",
                "Assegnato a": "NP", "Stato progetto": "NP", "Tipo progetto": "NP"};
            that.otherProj = [{"Nome progetto": "NP", "Numero progetto": "NP", "projectid": "NP", "url": "NP",
                    "Assegnato a": "NP", "Stato progetto": "NP", "Tipo progetto": "NP"}];
            that.inv = [{"Soggetto": "NP", "Data fattura": "NP", "numero": "NP", "Ammontare": "NP"}];
            that.getSO();
            $rootScope.$on("getSOPerformed", function () {
                that.getCustomer();
            });
            $rootScope.$on("getCustomerPerformed", function () {
                that.getAssociatedProject();
            });
            $rootScope.$on("getAssProjPerformed", function () {
                that.getOtherProject();
            });
            $rootScope.$on("getOtherProjPerformed", function () {
                that.getInv();
            });
            $rootScope.$on("getInvPerformed", function () {
                that.getUsers();
            });
            $rootScope.$on("getUsersPerformed", function () {
                $rootScope.showNotShow.detailsLoaded = false;
            });
        };
        this.calcolaFine = function () {
            var tipo = that.newProj.projecttype.value;
            if (that.newProj.startdate.value === "" || that.newProj.startdate.value === "0000-00-00")
                return;
            switch (tipo) {
                case 'AMMINISTRAZIONE - ECONOMIA - FINANZA':
                case 'PRODUZIONE - DISTRIBUZIONE':
                case 'COMMERCIALE':
                case 'DIREZIONALE - ORGANIZZATIVO':
                    var start = new Date(that.newProj.startdate.value);
                    var end = new Date(start.getTime() + 90 * 24 * 60 * 60 * 1000);
                    var day = Number(end.getDate());
                    var month = Number(end.getMonth()) + 1;
                    var year = Number(end.getFullYear());
                    day = "" + day;
                    month = "" + month;
                    year = "" + year;
                    if (day.length < 2) {
                        day = "0" + day;
                    }
                    if (month.length < 2) {
                        month = "0" + month;
                    }
                    that.newProj.targetenddate.value = year + "-" + month + "-" + day;
                    break;
                case 'CHECK-UP BANCARIO':
                    var start = new Date(that.newProj.startdate.value);
                    var end = new Date(start.getTime() + 45 * 24 * 60 * 60 * 1000);
                    var day = Number(end.getDate());
                    var month = Number(end.getMonth()) + 1;
                    var year = Number(end.getFullYear());
                    day = "" + day;
                    month = "" + month;
                    year = "" + year;
                    if (day.length < 2) {
                        day = "0" + day;
                    }
                    if (month.length < 2) {
                        month = "0" + month;
                    }
                    that.newProj.targetenddate.value = year + "-" + month + "-" + day;
                    break;
                case'ASSISTENZA POST CHECK-UP BANCARIO':
                    var start = new Date(that.newProj.startdate.value);
                    var end = new Date(start.getTime() + 150 * 24 * 60 * 60 * 1000);
                    var day = Number(end.getDate());
                    var month = Number(end.getMonth()) + 1;
                    var year = Number(end.getFullYear());
                    day = "" + day;
                    month = "" + month;
                    year = "" + year;
                    if (day.length < 2) {
                        day = "0" + day;
                    }
                    if (month.length < 2) {
                        month = "0" + month;
                    }
                    that.newProj.targetenddate.value = year + "-" + month + "-" + day;
                    break;
                default:
                    return;
            }
        };
        this.calcolaFineUpd = function () {
            var tipo = that.updateProj.projecttype.value;
            if (that.updateProj.startdate.value === "" || that.updateProj.startdate.value === "0000-00-00")
                return;
            switch (tipo) {
                case 'AMMINISTRAZIONE - ECONOMIA - FINANZA':
                case 'PRODUZIONE - DISTRIBUZIONE':
                case 'COMMERCIALE':
                case 'DIREZIONALE - ORGANIZZATIVO':
                    var start = new Date(that.updateProj.startdate.value);
                    var end = new Date(start.getTime() + 90 * 24 * 60 * 60 * 1000);
                    var day = Number(end.getDate());
                    var month = Number(end.getMonth()) + 1;
                    var year = Number(end.getFullYear());
                    day = "" + day;
                    month = "" + month;
                    year = "" + year;
                    if (day.length < 2) {
                        day = "0" + day;
                    }
                    if (month.length < 2) {
                        month = "0" + month;
                    }
                    that.updateProj.targetenddate.value = year + "-" + month + "-" + day;
                    break;
                case 'CHECK-UP BANCARIO':
                    var start = new Date(that.updateProj.startdate.value);
                    var end = new Date(start.getTime() + 45 * 24 * 60 * 60 * 1000);
                    var day = Number(end.getDate());
                    var month = Number(end.getMonth()) + 1;
                    var year = Number(end.getFullYear());
                    day = "" + day;
                    month = "" + month;
                    year = "" + year;
                    if (day.length < 2) {
                        day = "0" + day;
                    }
                    if (month.length < 2) {
                        month = "0" + month;
                    }
                    that.updateProj.targetenddate.value = year + "-" + month + "-" + day;
                    break;
                case'ASSISTENZA POST CHECK-UP BANCARIO':
                    var start = new Date(that.updateProj.startdate.value);
                    var end = new Date(start.getTime() + 150 * 24 * 60 * 60 * 1000);
                    var day = Number(end.getDate());
                    var month = Number(end.getMonth()) + 1;
                    var year = Number(end.getFullYear());
                    day = "" + day;
                    month = "" + month;
                    year = "" + year;
                    if (day.length < 2) {
                        day = "0" + day;
                    }
                    if (month.length < 2) {
                        month = "0" + month;
                    }
                    that.updateProj.targetenddate.value = year + "-" + month + "-" + day;
                    break;
                default:
                    return;
            }
        };
        this.calcolaTipo = function () {
            var soggetto = that.so.Soggetto;
            console.log(soggetto);
            if (soggetto === "")
                return;
            if (soggetto.toLowerCase().indexOf("progetto check up bancario") > -1)
                that.newProj.projecttype.value = "CHECK-UP BANCARIO";
            if (soggetto.toLowerCase().indexOf("assistenza") > -1)
                that.newProj.projecttype.value = "ASSISTENZA POST CHECK-UP BANCARIO";
        };
        this.calcolaStato = function () {
            var stato = that.so.Stato;
            console.log(stato);
            if (stato === "")
                return;
            switch (stato) {
                case 'Created':
                    that.newProj.projectstatus.value = "prospecting";
                    break;
                case 'Approved':
                    if (that.so["Capo progetto"] !== "") {
                        that.newProj.projectstatus.value = "initiated";
                    }
                    else {
                        that.newProj.projectstatus.value = "prospecting";
                    }
                    break;
                case 'Chiuso archiviato':
                    that.newProj.projectstatus.value = "completed";
                    break;
                case 'Sospeso':
                    that.newProj.projectstatus.value = "on hold";
                    break;
                case 'Chiusura Amministrativa':
                    that.newProj.projectstatus.value = "completed";
                    break;
                case 'Chiuso archiviato abortito':
                    that.newProj.projectstatus.value = "annullato";
                    break;
                case 'Attesa caparra':
                    if (that.so["Capo progetto"] !== "") {
                        that.newProj.projectstatus.value = "initiated";
                    }
                    else {
                        that.newProj.projectstatus.value = "prospecting";
                    }
                    break;
                case  'Delivered':
                    that.newProj.projectstatus.value = "--none--";
                    break;
                case 'Perizia':
                    that.newProj.projectstatus.value = "completed";
                    break;
                case  'Lavori in corso':
                    that.newProj.projectstatus.value = "in progress";
                    break;
                case  'Chiusura Tecnica':
                    that.newProj.projectstatus.value = "completed";
                    break;
                case  'Cancelled':
                    that.newProj.projectstatus.value = "annullato";
                    break;

                default:
                    return;
            }
        };
        this.calcolaStatoUpd = function () {
            var stato = that.so.Stato;
            if (stato === "")
                return;
            switch (stato) {
                case 'Created':
                    that.updateProj.projectstatus.value = "prospecting";
                    break;
                case 'Approved':
                    if (that.so["Capo progetto"] !== "") {
                        that.updateProj.projectstatus.value = "initiated";
                    }
                    else {
                        that.updateProj.projectstatus.value = "prospecting";
                    }
                    break;
                case 'Chiuso archiviato':
                    that.updateProj.projectstatus.value = "completed";
                    break;
                case 'Sospeso':
                    that.updateProj.projectstatus.value = "on hold";
                    break;
                case 'Chiusura Amministrativa':
                    that.updateProj.projectstatus.value = "completed";
                    break;
                case 'Chiuso archiviato abortito':
                    that.updateProj.projectstatus.value = "annullato";
                    break;
                case 'Attesa caparra':
                    if (that.so["Capo progetto"] !== "") {
                        that.updateProj.projectstatus.value = "initiated";
                    }
                    else {
                        that.updateProj.projectstatus.value = "prospecting";
                    }
                    break;
                case  'Delivered':
                    that.updateProj.projectstatus.value = "--none--";
                    break;
                case 'Perizia':
                    that.updateProj.projectstatus.value = "completed";
                    break;
                case  'Lavori in corso':
                    that.updateProj.projectstatus.value = "in progress";
                    break;
                case  'Chiusura Tecnica':
                    that.updateProj.projectstatus.value = "completed";
                    break;
                case  'Cancelled':
                    that.updateProj.projectstatus.value = "annullato";
                    break;

                default:
                    return;
            }
        };
        this.imponiSO = function () {
            that.newProj.cf_691.value = "6x" + $rootScope.soScelta;
            that.updateProj.cf_691.value = "6x" + $rootScope.soScelta;
        };
        this.inizializzaNuovo = function () {
            for (var i in that.newProj) {
                if (that.newProj[i].type === "picklist" && typeof (that.newProj[i].picklistValues) === "string") {
                    that.newProj[i].picklistValues = that.newProj[i].picklistValues.split("|");
                }
            }
            this.newProj.cf_691.value = "6x" + $rootScope.soScelta;
            this.newProj.linktoaccountscontacts.value = "11x" + this.so.accountid;
            this.newProj.projectname.value = this.so.Soggetto;
            this.calcolaStato();
            this.calcolaTipo();
        };
        this.modificaQuesto = function (projectid) {
            $timeout(function () {
                for (var i in that.updateProj) {
                    if (that.updateProj[i].type === "picklist" && typeof (that.updateProj[i].picklistValues) === "string") {
                        that.updateProj[i].picklistValues = that.updateProj[i].picklistValues.split("|");
                    }
                }
                var url = document.URL.replace("so2proj", "so2projretrieveproj.ws?projectid=" + projectid);
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
//                            that.so = data[0];
//                            console.log(data);
                            for (var i in that.updateProj) {
//                                console.log(that.updateProj[i]);
                                if (typeof (data[i] !== "undefined")) {
                                    that.updateProj[i].value = data[i];
                                }
                            }
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.getSO = function () {
            that.so = {};
            $timeout(function () {
                var url = document.URL.replace("so2proj", "so2projgetso.ws?salesorderid=" + $rootScope.soScelta);
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.so = data[0];
//                            console.log(data["0"]);
                            $rootScope.$emit("getSOPerformed");
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.getCustomer = function () {
            that.customer = {};
            $timeout(function () {
                var url = document.URL.replace("so2proj", "so2projgetcustomer.ws?accountid=" + that.so.accountid);
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.customer = data[0];
//                            console.log(data[0]);
                            $rootScope.$emit("getCustomerPerformed");
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.getAssociatedProject = function () {
            that.assProj = {};
            $timeout(function () {
                var url = document.URL.replace("so2proj", "so2projgetassproj.ws?salesorderid=" + $rootScope.soScelta);
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.assProj = data[0];
//                            console.log(data[0]);
                            $rootScope.$emit("getAssProjPerformed");
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.getOtherProject = function () {
            that.otherProj = [];
            $timeout(function () {
                var url = document.URL.replace("so2proj", "so2projgetotherproj.ws?salesorderid=" + $rootScope.soScelta + "&accountid=" + that.so.accountid);
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.otherProj = data;
//                            console.log(data);
                            $rootScope.$emit("getOtherProjPerformed");
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.getInv = function () {
            that.inv = [];
            $timeout(function () {
                var url = document.URL.replace("so2proj", "so2projgetinv.ws?salesorderid=" + $rootScope.soScelta);
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.inv = data;
//                            console.log(data);
                            $rootScope.$emit("getInvPerformed");
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.getUsers = function () {
            that.users = [];
            $timeout(function () {
                var url = document.URL.replace("so2proj", "so2projgetusers.ws");
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.users = data;
                            console.log(data);
                            for (var i in that.users) {
                                that.users[i].name = that.users[i].first_name + " " + that.users[i].last_name;
                            }
                            $rootScope.$emit("getUsersPerformed");
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.update = function () {
            $timeout(function () {
                var updateupdate = {};
                for (var i in that.updateProj) {
                    updateupdate[i] = that.updateProj[i].value;
                }
                var url = document.URL.replace("so2proj", "so2projupdate.ws");
                var responsePromise = $http({
                    method: 'POST',
                    url: url,
                    data: 'data=' + JSON.stringify(updateupdate),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                        .success(function (data, status, headers, config) {
                            console.log(data);
                            location.reload();
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.create = function () {

            $timeout(function () {
                var createcreate = {};
                for (var i in that.newProj) {
                    createcreate[i] = that.newProj[i].value;
                }
                var url = document.URL.replace("so2proj", "so2projcreate.ws");
                var responsePromise = $http({
                    method: 'POST',
                    url: url,
                    data: 'data=' + JSON.stringify(createcreate),
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'}
                })
                        .success(function (data, status, headers, config) {
                            console.log(data);
                            location.reload();
                        })
                        .error(function (data, status, headers, config) {
                            alert("AJAX failed!");
                        });
            }, 200);
        };
        this.visibleProject = 0;
        this.shifttoproject = function (index) {
            this.visibleProject = index;
        }
        this.visibleInv = 0;
        this.shifttoinv = function (index) {
            this.visibleInv = index;
        }

        $rootScope.$on("SoScelta", function () {
            $timeout(function () {
                that.compilaDati();
            }, 1000);
        });
    });
})()

