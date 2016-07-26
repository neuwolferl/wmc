(function() {
    var app = angular.module("ConfermaProgetto", [], function($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
//        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

    });


    app.filter("emptyDatum", function() {
        return function(input) {
            if (!input || input === "") {
                return "Dato mancante";
            }
            else
                return input;
        }
    });
    app.filter("showStatus", function() {
        return function(input, flagActual) {
            switch (input) {
                case 0:
                case '0':
                    return 'budget';
                    break;
                case 1:
                case '1':
                    if (flagActual + "" === '1')
                        return 'actual';
                    else
                        return 'forecast';
                    break;
                default:
                    return 'dato mancante';
                    break;
            }
        }
    });

    app.filter("filterOnStatus", function() {
        return function(input, status) {
            var ret = [];
            for (var i in input) {
                if (input[i].status + "" === status + "") {
                    ret.push(input[i]);

                }
            }
            return ret;
        }
    });
    app.filter("filterOnFlowStatus", function() {
        return function(input, flowstatus) {
            var ret = [];
            for (var i in input) {
                if (input[i].flowstatus + "" === flowstatus + "") {
                    ret.push(input[i]);

                }
            }
            return ret;
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

    app.controller("CommentiController", function($rootScope, $http, $timeout) {
        this.commenti = [];
        this.nuovo = "";
        var pippo = this;

        this.checkEmptyNuovo = function() {
            if (pippo.nuovo === "")
                return false;
            else
                return true;
        }
        this.push = function() {

            var url = document.URL;
            url = url.replace("#", "").replace("conferma", "commenti");
            url = url + ".ws";
            data = {"op": "push", "Contenuto": pippo.nuovo};
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'data=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
                        console.log(data);
                        pippo.nuovo = "";
                        pippo.sync();
                    })
                    .error(function(data, status, headers, config) {
                    });
        }

        this.sync = function() {
            $timeout(function() {
                pippo.sync();
            }, 5000);
            if ($rootScope.commenti + 1)
                return;
            var url = document.URL;
            url = url.replace("#", "").replace("conferma", "commenti");
            url = url + ".ws";
            data = {"op": "get"};
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'data=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
                        pippo.commenti = [];
                        for (var i in data["commenti"]) {
                            for (var j in data["utenti"]) {
                                if (data["utenti"][j].Userid === data["commenti"][i].Autore) {
                                    pippo.commenti.push({"Contenuto": data["commenti"][i].Contenuto, "Data creazione": data["commenti"][i]["Data creazione"], "Autore": data["utenti"][j]["Nome"] + " " + data["utenti"][j]["Cognome"]});
                                }
                            }

                        }

                    })
                    .error(function(data, status, headers, config) {
                        $rootScope.flowerizzatore = 1;
                    });
        }

        $timeout(function() {
            pippo.sync();
        }, 500);
    });
    app.controller("PulsantieraController", function($rootScope, $http) {


        this.conferma = function() {
            var data = {};
            $rootScope.elaborazione = 2;
            data["esito"] = "1";
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            data["tipo"] = $rootScope.tipoConferma;
            var url = document.URL;
            url = url.split("conferma");
            var urlred = url[0] + "progetti_da_confermare/";
            url = url[0] + "salvaConferma.ws";
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'data=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
                        $rootScope.confermaConferma = data;
                        $rootScope.confermataConferma = true;
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        };
        this.respingi = function() {
            var data = {};
            $rootScope.elaborazione = 2;
            data["esito"] = "-1";
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            data["tipo"] = $rootScope.tipoConferma;
            var url = document.URL;
            url = url.split("conferma");
            var urlred = url[0] + "progetti_da_confermare/";
            url = url[0] + "salvaConferma.ws";
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'data=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
                        $rootScope.confermaConferma = data;
                        $rootScope.confermataConferma = true;
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        };

        this.edita = function() {
            $rootScope.elaborazione = 0;
        };
        this.backToList = function() {
            var url = document.URL;
            url = url.split("conferma");
            url = url[0] + "progetti_da_confermare/1";
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
        this.ckEmptyDate = function(string) {
            if (!string || string === "" || string === "0000-00-00")
                return false;
            return true;
        };
        this.salvaEsecuzione = function() {
            var data = {};
            var j = 0;
            for (var i in $rootScope.nuovo.esecuzione) {
                data["" + Number(j)] = $rootScope.nuovo.esecuzione[i];
                j = Number(j) + 1;
            }
            data["numrows"] = $rootScope.nuovo.esecuzione.length;
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            var url = document.URL;
            url = url.split("avanzamento");
            url = url[0] + "salvaEsecuzione.ws";
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
            var j = 0;
            for (var i in $rootScope.nuovo.fatturazione) {
                data["" + Number(j)] = $rootScope.nuovo.fatturazione[i];
                j = Number(j) + 1;
            }
            data["numrows"] = $rootScope.nuovo.fatturazione.length;
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            data["salesorderid"] = $rootScope.rawdata["Pk_so"];
            var url = document.URL;
            url = url.split("avanzamento");
            url = url[0] + "salvaFatturazione.ws";
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
            var j = 0;
            for (var i in $rootScope.nuovo.incasso) {
                data["" + Number(j)] = $rootScope.nuovo.incasso[i];
                j = Number(j) + 1;
            }
            data["numrows"] = $rootScope.nuovo.incasso.length;
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            data["salesorderid"] = $rootScope.rawdata["Pk_so"];
            var url = document.URL;
            url = url.split("avanzamento");
            url = url[0] + "salvaIncasso.ws";
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
            var j = 0;
            for (var i in $rootScope.nuovo.attivita) {
                data["" + Number(j)] = $rootScope.nuovo.attivita[i];
                j = Number(j) + 1;
            }
            data["numrows"] = $rootScope.nuovo.attivita.length;
            data["projectid"] = $rootScope.rawdata["Pk_project"];
            var url = document.URL;
            url = url.split("avanzamento");
            url = url[0] + "salvaAttivita.ws";
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
        var responsePromise = $http.get(url)
                .success(function(data, status, headers, config) {
                    $rootScope.rawdata = data[0];
                    $rootScope.tipoConferma = (data[0]["Stato progetto"] === "initiated" ? "AVVIO" : "AVANZAMENTO");
                    $rootScope.tipoConfermaNumero = (data[0]["Stato progetto"] === "initiated" ? 1 : 2);
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
                    pippo.specchiettoLoaded = true;

                    $timeout(function() {
                        var urlContatti = document.URL;
                        urlContatti = urlContatti.split("conferma/");
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
                    $rootScope.budgetApertura = {"budgetDate": $rootScope.rawdata["Data apertura progetto"], "actualDate": "", "label": "Apertura progetto", "status": "budget"};
                })
                .error(function(data, status, headers, config) {
                    alert("AJAX failed!");
                });
        $rootScope.moreInfoLoaded = false;
        $timeout(function() {
            var url = document.URL.replace("#", "").replace("conferma", "confermaMoreInfo") + '.ws';
            var responsePromise = $http.get(url)
                    .success(function(data, status, headers, config) {
                        $rootScope.stringone = "1";
                        $rootScope.infoEsecuzione = data["esecuzione"];
                        $rootScope.infoAttivita = data["attivita"];
                        $rootScope.infoFatturazione = data["fatturazione"];
                        $rootScope.infoIncasso = data["incasso"];
                        $rootScope.moreInfoLoaded = true;
                        if (!($rootScope.nuovo)) {
                            $rootScope.nuovo = {};
                        }
                        $rootScope.nuovo.esecuzione = [];
                        $rootScope.nuovo.attivita = [];
                        $rootScope.nuovo.fatturazione = [];
                        $rootScope.nuovo.incasso = [];
                        for (var i in $rootScope.infoEsecuzione) {
                            $rootScope.infoEsecuzione[i].isactual = parseInt($rootScope.infoEsecuzione[i].isactual);
                            if ($rootScope.infoEsecuzione[i].isactual === 1) {
                                $rootScope.nuovo.esecuzione.push($rootScope.infoEsecuzione[i]);
                            }
                            else {
                                $rootScope.infoEsecuzione[i].clonable = 1;
                            }
                            $rootScope.infoEsecuzione[i].index = i;
                        }
                        for (var i in $rootScope.infoAttivita) {
                            $rootScope.infoAttivita[i].index = i;
                            if ($rootScope.infoAttivita[i].projecttaskprogress === "da completare") {
                                $rootScope.infoAttivita[i].clonable = 1;
                                $rootScope.infoAttivita[i].locked = 0;
                            }
                            else {
                                var att = {};
                                for (var j in $rootScope.infoAttivita[i]) {
                                    att[j] = $rootScope.infoAttivita[i][j];
                                }
                                $rootScope.infoAttivita[i].clonable = 0;
                                $rootScope.infoAttivita[i].locked = 1;
                                att.locked = 1;
                                $rootScope.nuovo.attivita.push(att);
                            }
                        }
                        for (var i in $rootScope.infoFatturazione) {
                            $rootScope.infoFatturazione[i].isactual = parseInt($rootScope.infoFatturazione[i].isactual);
                            $rootScope.infoFatturazione[i].amount = Number($rootScope.infoFatturazione[i].amount);
                            $rootScope.infoFatturazione[i].actualamount = Number($rootScope.infoFatturazione[i].actualamount);
                            if ($rootScope.infoFatturazione[i].isactual === 1) {
                                $rootScope.nuovo.fatturazione.push($rootScope.infoFatturazione[i]);
                            }
                            else {
                                $rootScope.infoFatturazione[i].clonable = 1;
                            }
                            $rootScope.infoFatturazione[i].index = i;
                        }
                        for (var i in $rootScope.infoIncasso) {
                            $rootScope.infoIncasso[i].isactual = parseInt($rootScope.infoIncasso[i].isactual);
                            $rootScope.infoIncasso[i].amount = Number($rootScope.infoIncasso[i].amount);
                            $rootScope.infoIncasso[i].actualamount = Number($rootScope.infoIncasso[i].actualamount);
                            if ($rootScope.infoIncasso[i].isactual === 1) {
                                $rootScope.nuovo.incasso.push($rootScope.infoIncasso[i]);
                            }
                            else {
                                $rootScope.infoIncasso[i].clonable = 1;
                            }
                            $rootScope.infoIncasso[i].index = i;
                        }
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        }, 2000);
    });
    app.controller("AttivitaController", function($rootScope, $http) {

        this.clone = function(i) {
            var index = $rootScope.nuovo.attivita.length;
            if (index !== 0) {
                if ($rootScope.nuovo.attivita[index - 1].projecttaskname === "")
                    return;
            }
            if ($rootScope.infoAttivita[i]) {
                var att = {};
                for (var j in $rootScope.infoAttivita[i]) {
                    att[j] = $rootScope.infoAttivita[i][j];
                }
                var index = $rootScope.nuovo.attivita.push(att);
                $rootScope.nuovo.attivita[index - 1].cloned = i;
                $rootScope.infoAttivita[i].clonable = 0;
            }
        }
        this.remove = function() {
            var index = $rootScope.nuovo.attivita.length;
            if (index === 0)
                return;
            index--;
            if ($rootScope.nuovo.attivita[index].locked)
                return;
            if ($rootScope.nuovo.attivita[index].cloned) {
                $rootScope.infoAttivita[$rootScope.nuovo.attivita[index].cloned].clonable = 1;
            }
            $rootScope.nuovo.attivita.splice(index, 1);
        }
        this.add = function() {
            var index = $rootScope.nuovo.attivita.length;
            if (index !== 0) {
                if ($rootScope.nuovo.attivita[index - 1].projecttaskname === "")
                    return;
            }
            var att = {"projecttaskname": "", "description": "", "projecttaskprogress": "da completare", "locked": 0};
            $rootScope.nuovo.attivita.push(att);
        }

    });
    app.controller("IncassoController", function($rootScope, $http) {
        this.show = 0; //0= mostra budget, 1 = mostra forecast
        this.changeShow = function(show) {
            this.show = show;
        }
        this.showClass = function(tab) {
            if (tab === this.show) {
                return 'active';
            }
            else
                return '';
        }
        this.clone = function(i) {
            var cloned = {};
            for (var j in $rootScope.infoIncasso[i]) {
                cloned[j] = $rootScope.infoIncasso[i][j];
            }

            $rootScope.infoIncasso[i].clonable = 0;
            cloned.cloned = i;
            $rootScope.nuovo.incasso.push(cloned);
        }
        this.delete = function(i) {
            if ($rootScope.infoIncasso[$rootScope.nuovo.incasso[i].cloned])
                $rootScope.infoIncasso[$rootScope.nuovo.incasso[i].cloned].clonable = 1;
            $rootScope.nuovo.incasso.splice(i, 1);
        }
        this.add = function() {
            for (var i in $rootScope.nuovo.incasso) {
                if ($rootScope.nuovo.incasso.date === "") {
                    return;
                }
            }
            $rootScope.nuovo.incasso.push({"date": "", "amount": "", "type": "", "status": "1", "isactual": 0, "actualdate": "", "actualamount": "", "actualtype": ""});
        }

    });
    app.controller("FatturazioneController", function($rootScope, $http) {
        this.show = 0; //0= mostra budget, 1 = mostra forecast

        this.changeShow = function(show) {
            this.show = show;
        }

        this.showClass = function(tab) {
            if (tab === this.show) {
                return 'active';
            }
            else
                return '';
        }
        this.clone = function(i) {
            var cloned = {};
            for (var j in $rootScope.infoFatturazione[i]) {
                cloned[j] = $rootScope.infoFatturazione[i][j];
            }

            $rootScope.infoFatturazione[i].clonable = 0;
            cloned.cloned = i;
            $rootScope.nuovo.fatturazione.push(cloned);
        }
        this.delete = function(i) {
            if ($rootScope.infoFatturazione[$rootScope.nuovo.fatturazione[i].cloned])
                $rootScope.infoFatturazione[$rootScope.nuovo.fatturazione[i].cloned].clonable = 1;
            $rootScope.nuovo.fatturazione.splice(i, 1);
        }
        this.add = function() {
            for (var i in $rootScope.nuovo.fatturazione) {
                if ($rootScope.nuovo.fatturazione.date === "") {
                    return;
                }
            }
            $rootScope.nuovo.fatturazione.push({"date": "", "amount": "", "status": "1", "isactual": 0, "actualdate": "", "actualamount": ""});
        }

    });
    app.controller("EsecuzioneController", function($rootScope, $http) {

        this.show = 0; //0= mostra budget, 1 = mostra forecast
        this.changeShow = function(show) {
            this.show = show;
        }
        this.showClass = function(tab) {
            if (tab === this.show) {
                return 'active';
            }
            else
                return '';
        }
        this.clone = function(i) {
            var cloned = {};
            for (var j in $rootScope.infoEsecuzione[i]) {
                cloned[j] = $rootScope.infoEsecuzione[i][j];
            }

            $rootScope.infoEsecuzione[i].clonable = 0;
            cloned.cloned = i;
            $rootScope.nuovo.esecuzione.push(cloned);
        }
        this.delete = function(i) {
            if ($rootScope.infoEsecuzione[$rootScope.nuovo.esecuzione[i].cloned])
                $rootScope.infoEsecuzione[$rootScope.nuovo.esecuzione[i].cloned].clonable = 1;
            $rootScope.nuovo.esecuzione.splice(i, 1);
        }
        this.add = function() {
            for (var i in $rootScope.nuovo.esecuzione) {
                if ($rootScope.nuovo.esecuzione.date === "") {
                    return;
                }
            }
            $rootScope.nuovo.esecuzione.push({"date": "", "label": "", "status": "1", "isactual": 0, "actualdate": "", "actuallabel": ""});
        }
    });
})();

        