(function() {
    var app = angular.module("AssegnazioneProgetto", [], function($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
//        $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

    });


    app.controller("PulsantieraController", function($rootScope, $http, $timeout) {


        this.isSoConfirmed = function() {
            var so = $rootScope.nuovaSo;
            for (var i in $rootScope.soList) {

                if (so === $rootScope.soList[i]["val"] && ($rootScope.soList[i]["approved"] === '1' || $rootScope.soList[i]["approved"] === 1)) {
                    return true;

                }
            }
            return false;
        }
        this.consIdToName = function(id) {
            var name = "";
            for (var i in $rootScope.consList) {
                if ($rootScope.consList[i].val == id) {
                    name = $rootScope.consList[i].name;
                    break;
                }
            }

            return name;
        }
        this.soIdToName = function(id) {
            var name = "";
            for (var i in $rootScope.soList) {
                if ($rootScope.soList[i].val == id) {
                    name = $rootScope.soList[i].name;
                    break;
                }
            }

            return name;
        }
        this.conferma = function() {
            $rootScope.elaborazione = 1;
        };
        this.backToList = function() {
            var url = document.URL;
            url = url.split("assegnazione");
            url = url[0] + "progetti_da_assegnare/1";
            window.location.href = url;
        }
        this.edita = function() {
            $rootScope.elaborazione = 0;
        };
        this.salva = function(assegna) {
            $rootScope.elaborazione = 2;
            $rootScope.confermataSalvataggio = false;
            var data = {};
            data.info = {};
            if ($rootScope.nuovoNomeProgetto && $rootScope.nuovoNomeProgetto !== $rootScope.rawdata["Nome progetto"])
                data.info.projectname = $rootScope.nuovoNomeProgetto;
            if ($rootScope.nuovoTipoProgetto && $rootScope.nuovoTipoProgetto !== $rootScope.rawdata["Tipo progetto"])
                data.info.projecttype = $rootScope.nuovoTipoProgetto;
            if ($rootScope.nuovaDataProgetto && $rootScope.nuovaDataProgetto !== $rootScope.rawdata["Data apertura progetto"])
                data.info.startdate = $rootScope.nuovaDataProgetto;
            if ($rootScope.nuovoAssegnatoA && $rootScope.nuovoAssegnatoA !== $rootScope.rawdata["Pk_consulenti"])
                data.info.assigned_user_id = "19x" + $rootScope.nuovoAssegnatoA;
            if ($rootScope.nuovaSo && $rootScope.nuovaSo !== $rootScope.rawdata["Pk_so"])
                data.info.cf_691 = $rootScope.nuovaSo;
            data.projectid = $rootScope.rawdata["Pk_project"];
            data.assegna = assegna;
            var url = document.URL;
            url = url.split("assegnazione");
            url = url[0] + "salvaAssegnazione.ws";
            var responsePromise = $http({
                method: 'POST',
                url: url,
                data: 'data=' + JSON.stringify(data),
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
                    .success(function(data, status, headers, config) {
                        $rootScope.confermaSalvataggio = data;
                        $rootScope.confermataSalvataggio = true;
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        };
        this.ckEmpty = function(string) {
            if (!string || string === "")
                return false;
            return true;
        };
        this.salvaEsecuzione = function() {

        };
    });



    app.controller("SpecchiettoController", function($rootScope, $http, $timeout) {
        this.specchiettoLoaded = false;
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
        this.data = [];
        this.contattoAttivo = 0;
        this.changeContattoAttivo = function(i) {
            this.contattoAttivo = i;
        }
        var pippo = this;
        var url = document.URL.replace("#", "") + '.ws';
        var responsePromise = $http.get(url)
                .success(function(data, status, headers, config) {
                    $rootScope.rawdata = data[0];
                    var k = 0;
                    for (var i in data[0]) {
                        pippo.data[k] = {"key": i, "value": data[0][i]};
                        k++;
                    }
                    $rootScope.tipoProgettoList = data["tipoprogetto"];
                    pippo.specchiettoLoaded = true;
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
                        urlContatti = urlContatti.split("assegnazione/");
                        urlContatti = urlContatti[0] + "contatti/" + $rootScope.rawdata["Pk_account"] + ".ws";
                        var responsePromise = $http.get(urlContatti)
                                .success(function(data, status, headers, config) {
                                    $rootScope.contatti = [];
                                    $rootScope.nomicontatti = [];
                                    for (var i in data) {
                                        $rootScope.contatti[i] = [];
                                        $rootScope.nomicontatti[i] = data[i]["Nome"]+" "+data[i]["Cognome"];
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


                    $rootScope.nuovoNomeProgetto = $rootScope.rawdata["Nome progetto"];
                    $rootScope.nuovoTipoProgetto = $rootScope.rawdata["Tipo progetto"];
                    $rootScope.nuovaDataProgetto = $rootScope.rawdata["Data apertura progetto"];

                    $rootScope.soList = [];
                    $timeout(function() {
                        var urlSO = document.URL;
                        urlSO = urlSO.split("assegnazione/");
                        urlSO = urlSO[0] + "accountToSo/" + $rootScope.rawdata["Pk_account"] + ".ws";
                        var responsePromise = $http.get(urlSO)
                                .success(function(data, status, headers, config) {
                                    var k = 0;
                                    for (var i in data) {
                                        $rootScope.soList[k] = {"val": data[i]["val"], "name": data[i]["nome"], "approved": data[i]["approved"]};
                                        k++;
                                    }
                                    var actualSo = $rootScope.rawdata["Pk_so"];
                                    if (actualSo && actualSo !== "") {
                                        for (var i in $rootScope.soList) {
                                            if (actualSo === $rootScope.soList[i]["val"]) {
                                                $timeout(function() {
                                                    $rootScope.nuovaSo = actualSo;
                                                }, 2000);
                                                break;
                                            }
                                        }
                                    }
                                    $rootScope.soLoaded = true;
                                })
                                .error(function(data, status, headers, config) {
                                    alert("AJAX failed!");
                                });


                    }, 2000);

                })
                .error(function(data, status, headers, config) {
                    alert("AJAX failed!");
                });
    });
    app.controller("FormController", function($rootScope, $http, $timeout) {
        $timeout(function() {
            $rootScope.consList = [];
            var urlCons = document.URL;
            urlCons = urlCons.split("assegnazione/");
            urlCons = urlCons[0] + "consulenti.ws";
            var responsePromise = $http.get(urlCons)
                    .success(function(data, status, headers, config) {
                        var k = 0;
                        for (var i in data) {
                            $rootScope.consList[k] = {"val": data[i]["val"], "name": data[i]["nome"]};
                            k++;
                        }
                        var actualCons = $rootScope.rawdata["Pk_consulenti"];
                        var trovato = false;
                        if (actualCons && actualCons !== "") {
                            for (var i in $rootScope.consList) {
                                if (actualCons === $rootScope.consList[i]["val"]) {
                                    trovato = true;
                                    $timeout(function() {
                                        $rootScope.nuovoAssegnatoA = actualCons;
                                    }, 2000);
                                    break;
                                }
                            }
                            if (!trovato) {
                                $rootScope.consList.unshift({"val": actualCons, "name": $rootScope.rawdata["Assegnato a"]});
                                $timeout(function() {
                                    $rootScope.nuovoAssegnatoA = actualCons;
                                }, 2000);
                            }
                        }
                        $rootScope.consLoaded = true;
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        }, 3000);

    });
})();

        