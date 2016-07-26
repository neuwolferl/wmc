(function() {
    var app = angular.module("AvvioProgetto", [], function($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
//        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

    });

    app.factory("getData", ['$http']);

    app.filter("emptyDatum", function() {
        return function(input) {
            if (!input || input === "") {
                return "Dato mancante";
            }
            else
                return input;
        }
    });
    app.filter("tipoIncasso", function() {
        return function(input) {
            switch (input) {
                case 1:
                case '1':
                    return 'Assegno';
                    break;
                case 2:
                case '2':
                    return 'Bonifico';
                    break;
                case 3:
                case '3':
                    return 'Contante';
                    break;
                default:
                    return 'Dato mancante';
                    break;
            }
        }
    });

    app.controller("PulsantieraController", function($rootScope, $http) {
        this.conferma = function() {
            $rootScope.elaborazione = 1;
        };
        this.edita = function() {
            $rootScope.elaborazione = 0;
        };
        this.backToList = function() {
            var url = document.URL;
            url = url.split("avvia");
            url = url[0] + "progetti_da_avviare/1";
            window.location.href = url;
        }
        this.salva = function() {
            $rootScope.elaborazione = 2;
            $rootScope.confermataEsecuzione = false;
            $rootScope.confermataAttivita = false;
            $rootScope.confermataFattura = false;
            $rootScope.confermataIncasso = false;
            this.salvaEsecuzione();
            this.salvaAttivita();
            this.salvaFatturazione();
            this.salvaIncasso();
        };
        this.ckEmpty = function(string) {
            if (!string || string === "")
                return false;
            return true;
        };
        this.salvaEsecuzione = function() {
            var data = {};
//            console.log($rootScope.rawdata);
            data[0] = $rootScope.budgetApertura;
            var j = 0;
            for (var i in $rootScope.budgetEsecuzione) {
                data[Number(i) + 1] = $rootScope.budgetEsecuzione[i];
                j++;
            }
            data[Number(j) + 1] = $rootScope.budgetChiusura;
            j++;
            data["numrows"] = Number(j) + 1;
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            var url = document.URL;
            url = url.split("avvia");
            url = url[0] + "salvaEsecuzioneAvvio.ws";
//            $rootScope.confermaEsecuzione = data;
//            return;
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'esecuzione=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
//                        alert("Eseguito!");
                        $rootScope.confermaEsecuzione = data;
                        $rootScope.confermataEsecuzione = true;
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        };
        this.salvaFatturazione = function() {
            var data = {};
//            console.log($rootScope.rawdata);
            var j = 0;
            for (var i in $rootScope.budgetFattura) {
                data[Number(i)] = $rootScope.budgetFattura[i];
                j++;
            }
            data["numrows"] = Number(j) + 1;
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            data["salesorderid"] = $rootScope.rawdata["Pk_so"];
            var url = document.URL;
            url = url.split("avvia");
            url = url[0] + "salvaFatturazioneAvvio.ws";
//            $rootScope.confermaFattura = data;
//            return;
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'fatturazione=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
//                        alert("Eseguito!");
                        $rootScope.confermaFattura = data;
                        $rootScope.confermataFattura = true;
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        };
        this.salvaIncasso = function() {
            var data = {};
//            console.log($rootScope.rawdata);
            var j = 0;
            for (var i in $rootScope.budgetIncasso) {
                data[Number(i)] = $rootScope.budgetIncasso[i];
                j++;
            }
            data["numrows"] = Number(j) + 1;
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            data["salesorderid"] = $rootScope.rawdata["Pk_so"];
            var url = document.URL;
            url = url.split("avvia");
            url = url[0] + "salvaIncassoAvvio.ws";
//            $rootScope.confermaIncasso = data;
//            return;
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'incasso=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
//                        alert("Eseguito!");
                        $rootScope.confermaIncasso = data;
                        $rootScope.confermataIncasso = true;
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        };
        this.salvaAttivita = function() {
            var data = {};
//            console.log($rootScope.rawdata);
            var j = 0;
            for (var i in $rootScope.budgetAttivita) {
                data[Number(i)] = $rootScope.budgetAttivita[i];
                j++;
            }
            data["numrows"] = Number(j);
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            var url = document.URL;
            url = url.split("avvia");
            url = url[0] + "salvaAttivitaAvvio.ws";
//            $rootScope.confermaAttivita = data;
//            return;
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'attivita=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
//                        alert("Eseguito!");
                        $rootScope.confermaAttivita = data;
                        $rootScope.confermataAttivita = true;
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        };
    });



    app.controller("SpecchiettoController", function($rootScope, $http, $timeout) {
        this.schemaProgetto = [
            {"label": "Numero progetto", "field": "Numero progetto", "type": "string", "content": ""},
            {"label": "Numero SO", "field": "Numero SO", "type": "string", "content": ""},
            {"label": "Nome progetto", "field": "Nome progetto", "type": "string", "content": ""},
            {"label": "Assegnato a", "field": "Assegnato a", "type": "string", "content": ""},
            {"label": "Tipologia progetto", "field": "Tipo progetto", "type": "string", "content": ""},
            {"label": "Stato progetto", "field": "Stato progetto", "type": "string", "content": ""},
            {"label": "Descrizione", "field": "Descrizione", "type": "string", "content": ""},
            {"label": "Totale progetto i.i.", "field": "Totale progetto ii", "type": "currency", "content": ""},
            {"label": "Totale progetto i.e.", "field": "Totale progetto ie", "type": "currency", "content": ""},
            {"label": "Data prevista apertura progetto", "field": "Data apertura progetto", "type": "date", "content": ""},
            {"label": "Analista", "field": "Analista", "type": "date", "content": ""}
        ];
        this.schemaCliente = [
            {"label": "Numero cliente", "field": "Numero cliente", "type": "string", "content": ""},
            {"label": "Nome cliente", "field": "Azienda cliente", "type": "string", "content": ""},
            {"label": "Fatturato", "field": "Fatturato", "type": "currency", "content": ""},
            {"label": "Partita Iva", "field": "Partita Iva", "type": "string", "content": ""},
            {"label": "Telefono", "field": "Telefono cliente", "type": "string", "content": ""},
            {"label": "Ev. altro telefono", "field": "Ev.le altro telefono", "type": "string", "content": ""},
            {"label": "Email", "field": "Email", "type": "string", "content": ""},
            {"label": "Fax", "field": "Fax", "type": "string", "content": ""},
            {"label": "Dipendenti", "field": "Dipendenti", "type": "string", "content": ""},
            {"label": "Indirizzo", "field": "Indirizzo", "type": "string", "content": ""},
            {"label": "Citt\u00e0", "field": "Citta", "type": "string", "content": ""},
            {"label": "Cap", "field": "Cap", "type": "string", "content": ""},
            {"label": "Provincia", "field": "Provincia", "type": "string", "content": ""},
        ];
        this.schemaContatto = [
            {"label": "Numero contatto", "field": "Numero Contatto", "type": "string", "content": ""},
            {"label": "Nome", "field": "Nome", "type": "string", "content": ""},
            {"label": "Cognome", "field": "Cognome", "type": "string", "content": ""},
            {"label": "Telefono", "field": "Telefono", "type": "string", "content": ""},
            {"label": "Cellulare", "field": "Cellulare", "type": "string", "content": ""},
            {"label": "Email", "field": "Email", "type": "string", "content": ""}
        ];
        this.contattoAttivo = 0;
        this.changeContattoAttivo = function(i) {
            this.contattoAttivo = i;
        }
        this.data = [];
        var pippo = this;
        var url = document.URL.replace("#", "") + '.ws';
        this.specchiettoLoaded = false;
        var responsePromise = $http.get(url)
                .success(function(data, status, headers, config) {
                    $rootScope.rawdata = data[0];
                    var k = 0;
                    for (var i in data[0]) {
//                        if (i === "Nome progetto"){
//                            data[0][i] = "asdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbashasdhjabshdbahsbdjhasbdjhasbdjhbasjdhbash";
//                        }
                        pippo.data[k] = {"key": i, "value": data[0][i]};
                        k++;
                    }
                    pippo.nascondiLoader = true;
                    for (var i in pippo.schemaProgetto) {
                        if (typeof ($rootScope.rawdata[pippo.schemaProgetto[i].field]) !== "undefined") {
                            pippo.schemaProgetto[i].content = $rootScope.rawdata[pippo.schemaProgetto[i].field];
                        }
                    }
                    for (var i in pippo.schemaCliente) {
                        if (typeof ($rootScope.rawdata[pippo.schemaCliente[i].field]) !== "undefined") {
                            pippo.schemaCliente[i].content = $rootScope.rawdata[pippo.schemaCliente[i].field];
                        }
                    }

                    $timeout(function() {
                        var urlContatti = document.URL;
                        urlContatti = urlContatti.split("avvia/");
                        urlContatti = urlContatti[0] + "contatti/" + $rootScope.rawdata["Pk_account"] + ".ws";
                        var responsePromise = $http.get(urlContatti)
                                .success(function(data, status, headers, config) {
                                    $rootScope.contatti = [];
                                    $rootScope.nomicontatti = [];
                                    for (var i in data) {
                                        $rootScope.contatti[i] = [];
                                        $rootScope.nomicontatti[i] = data[i]["Nome"] + " " + data[i]["Cognome"];
                                        for (var j in data[i]) {
                                            $rootScope.contatti[i].push({"key": j, "value": data[i][j]});
                                        }
                                    }

                                    $rootScope.contattiLoaded = true;
                                })
                                .error(function(data, status, headers, config) {
                                    alert("AJAX failed!");
                                });


                    }, 500);

                    $timeout(function() {
                        var urlMoreInfo = document.URL;
                        urlMoreInfo = urlMoreInfo.split("avvia/");
                        urlMoreInfo = urlMoreInfo[0] + "avanzamentoMoreInfo/" + $rootScope.rawdata["Pk_project"] + ".ws";
                        var responsePromise = $http.get(urlMoreInfo)
                                .success(function(data, status, headers, config) {
                                    var esecuzione = data.esecuzione;
                                    var fatturazione = data.fatturazione;
                                    var incasso = data.incasso;
                                    //cerco apertura;
                                    var apertura = null;
                                    for (var i in esecuzione) {
                                        if (esecuzione[i].label === "Apertura progetto") {
                                            $rootScope.budgetApertura = {budgetDate: esecuzione[i].date, actualDate: esecuzione[i].actualdate, label: "Apertura progetto", status: "budget"};
                                            apertura = i;
                                            break;
                                        }
                                    }
                                    if (apertura)
                                        esecuzione.splice(apertura, 1);

                                    //cerco chiusura;
                                    var chiusura = null;
                                    for (var i in esecuzione) {
                                        if (esecuzione[i].label === "Chiusura progetto") {
                                            $rootScope.budgetChiusura = {budgetDate: esecuzione[i].date, actualDate: esecuzione[i].actualdate, label: "Chiusura progetto", status: "budget"};
                                            chiusura = i;
                                            break;
                                        }
                                    }
                                    if (chiusura)
                                        esecuzione.splice(chiusura, 1);
                                    for (var i in esecuzione) {
                                        $rootScope.budgetEsecuzione.push({"budgetDate": esecuzione[i].date, "actualDate": esecuzione[i].actualdate, "label": esecuzione[i].label, status: "budget"});
                                    }

                                    for (var i in fatturazione) {
                                        $rootScope.budgetFattura.push({"date": fatturazione[i].date, "amount": Number(fatturazione[i].amount), "status": "budget"});
                                    }
                                    for (var i in incasso) {
                                        $rootScope.budgetIncasso.push({"date": incasso[i].date, "amount": Number(incasso[i].amount), "type": incasso[i].type, "status": "budget"});
                                    }

                                })
                                .error(function(data, status, headers, config) {
                                    alert("AJAX failed!");
                                });


                    }, 1000);



//                    pippo.data = $scope.rawdata;
                    $rootScope.budgetApertura = {"budgetDate": $rootScope.rawdata["Data apertura progetto"], "actualDate": "", "label": "Apertura progetto", "status": "budget"};
                    pippo.specchiettoLoaded = true;
                })
                .error(function(data, status, headers, config) {
                    alert("AJAX failed!");
                });
    });
    app.controller("AttivitaController", function($rootScope, $http) {
        this.attivita = {"name": "", "description": ""};
        $rootScope.budgetAttivita = [];
        this.canAddMiddle = function() {
            var flag = true;
            for (var i in $rootScope.budgetAttivita) {
            }
            return flag;
        }
        this.addMiddleBudget = function() {
            if (!this.canAddMiddle())
                return;
            var nuovaAttivita = {};
            for (var i in this.attivita) {
                nuovaAttivita[i] = this.attivita[i];
            }
            nuovaAttivita.status = "budget";
            $rootScope.budgetAttivita.push(nuovaAttivita);
        }
        this.removeMiddleBudget = function() {
            if ($rootScope.budgetAttivita.length > 0)
                $rootScope.budgetAttivita.pop();
        }

    });
    app.controller("IncassoController", function($rootScope, $http) {
        this.incasso = {"date": "", "amount": "", "type": "", "status": ""};
        $rootScope.budgetIncasso = [];
        this.canAddMiddle = function() {
            var flag = true;
            for (var i in $rootScope.budgetIncasso) {
                if ($rootScope.budgetIncasso[i]["date"] === "") {
                    flag = false;
                }

            }
            return flag;
        }
        this.addMiddleBudget = function() {
            if (!this.canAddMiddle())
                return;
            var nuovoIncasso = {};
            for (var i in this.incasso) {
                nuovoIncasso[i] = this.incasso[i];
            }
            nuovoIncasso.status = "budget";
            $rootScope.budgetIncasso.push(nuovoIncasso);
        }
        this.removeMiddleBudget = function() {
            if ($rootScope.budgetIncasso.length > 0)
                $rootScope.budgetIncasso.pop();
        }

    });
    app.controller("FatturazioneController", function($rootScope, $http) {
        this.fattura = {"date": "", "amount": "", "status": ""};
        $rootScope.budgetFattura = [];
        this.canAddMiddle = function() {
            var flag = true;
            for (var i in $rootScope.budgetFattura) {
                if ($rootScope.budgetFattura[i]["date"] === "") {
                    flag = false;
                }

            }
            return flag;
        }
        this.addMiddleBudget = function() {
            if (!this.canAddMiddle())
                return;
            var nuovaFattura = {};
            for (var i in this.fattura) {
                nuovaFattura[i] = this.fattura[i];
            }
            nuovaFattura.status = "budget";
            $rootScope.budgetFattura.push(nuovaFattura);
        }
        this.removeMiddleBudget = function() {
            if ($rootScope.budgetFattura.length > 0)
                $rootScope.budgetFattura.pop();
        }

    });
    app.controller("EsecuzioneController", function($rootScope, $http) {
        $rootScope.budgetEsecuzione = [];
        this.scadenza = {"budgetDate": "", "actualDate": "", "label": "", "status": "budget"};
        $rootScope.budgetApertura = {"budgetDate": "", "actualDate": "", "label": "Apertura progetto", "status": "budget"};
        $rootScope.budgetChiusura = null;
        this.canAddMiddle = function() {
            var flag = true;
            for (var i in $rootScope.budgetEsecuzione) {
                if ($rootScope.budgetEsecuzione[i]["budgetDate"] === "") {

                    flag = false;
                }

            }
            return flag;
        }
        this.addMiddleBudget = function() {
            if (!this.canAddMiddle())
                return;
            var nuovaScadenza = {};
            for (var i in this.scadenza) {
                nuovaScadenza[i] = this.scadenza[i];
            }
            nuovaScadenza.label = "SAL " + ($rootScope.budgetEsecuzione.length + 1);
            $rootScope.budgetEsecuzione.push(nuovaScadenza);
        }
        this.removeMiddleBudget = function() {
            if ($rootScope.budgetEsecuzione.length > 0)
                $rootScope.budgetEsecuzione.pop();
        }
        this.addLastBudget = function() {
            if ($rootScope.budgetChiusura === null)
                $rootScope.budgetChiusura = {"date": "inserire data", "label": "Chiusura progetto", "status": "budget"};
        }
        this.removeLastBudget = function() {
            $rootScope.budgetChiusura = null;
        }
    });
})();

        