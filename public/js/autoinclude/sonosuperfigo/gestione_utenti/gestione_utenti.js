(function () {
    var app = angular.module("GestioneUtenti", ['ui.bootstrap', 'DATAMODULE'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    })
            .run(function ($rootScope) {
                $rootScope.nuovoUtente = {"user_name": "", "user_password": "", "confirm_user_password": "", "first_name": "", "last_name": "", "active": false};
                $rootScope.emptyString = "";
                $rootScope.showNotShow = {nuovoUtente: false, listaUtenti: true};
            });

    app.filter("utentiAttivi", function () {
        return function (input, show) {
            var ret = [];
            for (var i in input) {
                if (input[i].value.active || !show)
                    ret.push(input[i]);
            }
            return ret;
        }
    });


    app.controller("MainController", function ($timeout, ConfiguratorService, GetDataService, GetResUriService, $rootScope, SendDataService) {
        var url = document.URL + ".ws";
        this.showOnlyActiveUsers = false;
        ConfiguratorService.get(url);
        this.utenti = {};
        this.utentiLoaded = {};

        this.showOnlyActiveUsers = true;

        var pippo = this;
        this.load = function (force) {
            if (typeof (force) === "undefined" || force || pippo.utentiLoaded === {}) {
                $rootScope.showNotShow.corpoListaUtenti = false;
                GetDataService("getUtenti", this.utenti, this.utentiLoaded, "usersLoaded", []);
            }
        }

        this.loadImages = function () {
            $rootScope.resUri = {};
            $rootScope.resUriLoaded = {};
            GetResUriService("img.plus", $rootScope.resUri, $rootScope.resUriLoaded);

        }
        this.nuovoUtente = function () {
            $rootScope.nuovoUtente = {"user_name": "", "user_password": "", "confirm_user_password": "", "first_name": "", "last_name": "", "active": false};
            $rootScope.showNotShow.nuovoUtente = true;
            $rootScope.showNotShow.listaUtenti = false;
        }
        this.chiudiNuovoUtente = function () {
            $rootScope.nuovoUtente = {"user_name": "", "user_password": "", "confirm_user_password": "", "first_name": "", "last_name": "", "active": false};
            $rootScope.showNotShow.nuovoUtente = false;
            $rootScope.showNotShow.listaUtenti = true;
        }

        this.activateUser = function (userid, active) {
            if (active)
                active = '0';
            else
                active = '1';
            var dataObject = {userid: userid, active: active};
            SendDataService("activateUser", dataObject, {}, "activateUser", [userid]);
        }
        this.associateVTUser = function (index) {

            var userid = pippo.utenti.arrayData[index].value.id;
            if (pippo.utenti.arrayData[index].value.vt) {
                console.log("disassocio");
                $rootScope.$emit("disassociateVT", {userid: userid});
            }
            else {
                console.log("associo");
                $rootScope.showNotShow.listaUtenti = false;
                $rootScope.showNotShow.listaUtentiVT = true;
                $rootScope.$emit("associateVT", {userid: userid, utenti: pippo.utenti.arrayData});
            }
        }

        $rootScope.$on('confLoaded', function () {
            pippo.load();
        });

        $timeout(function () {
            pippo.loadImages();
        }, 1500);


        $rootScope.$on('newUser', function () {
            pippo.load();
        });
        $rootScope.$on('associatedVT', function (event, mass) {
            pippo.load();
        });
        $rootScope.$on('activateUser', function () {
            pippo.load();
        });
        $rootScope.$on('usersLoaded', function () {
            if (typeof (pippo.utenti) === "undefined")
                return;
            if (typeof (pippo.utenti.arrayData) === "undefined")
                return;
            for (var i in pippo.utenti.arrayData) {
                if (typeof (pippo.utenti.arrayData[i].value.active) === "undefined")
                    return;
                if (pippo.utenti.arrayData[i].value["active"] === "0")
                    pippo.utenti.arrayData[i].value["active"] = false;
                else if (pippo.utenti.arrayData[i].value["active"] === "1")
                    pippo.utenti.arrayData[i].value["active"] = true;
                if (typeof (pippo.utenti.arrayData[i].value.vt) === "undefined")
                    return;
                if (pippo.utenti.arrayData[i].value.vt === "0")
                    pippo.utenti.arrayData[i].value.vt = false;
                else if (pippo.utenti.arrayData[i].value.vt === "1")
                    pippo.utenti.arrayData[i].value.vt = true;
            }
            $rootScope.showNotShow.corpoListaUtenti = true;
        });
    });
    app.controller("NuovoUtenteController", function ($rootScope, SendDataService, $timeout) {
        this.sendResult = {};
        this.suggEnable = false;
        this.sugg = [];
        var pippo = this;
        this.enableSugg = function () {
            if ($rootScope.nuovoUtente.first_name !== "" && $rootScope.nuovoUtente.last_name !== "")
                this.suggEnable = true;
        };
        this.disableSugg = function () {
            if ($rootScope.nuovoUtente.first_name !== "" && $rootScope.nuovoUtente.last_name !== "")
                this.suggEnable = false;
        };
        this.suggest = function () {
            if ($rootScope.nuovoUtente.first_name === "" || $rootScope.nuovoUtente.last_name === "")
                return;
            var f = $rootScope.nuovoUtente.first_name ? $rootScope.nuovoUtente.first_name.toLowerCase() : "";
            var l = $rootScope.nuovoUtente.last_name ? $rootScope.nuovoUtente.last_name.toLowerCase() : "";
            this.sugg = [];
            this.sugg.push(f + "." + l);
            if (f.length > 1) {
                this.sugg.push(f.charAt(0) + "." + l);
                this.sugg.push(f.charAt(0) + l);
            }
            if (l.length > 1) {
                this.sugg.push(f + "." + l.charAt(0));
                this.sugg.push(f + l.charAt(0));
            }
            this.enableSugg();
        };
        this.useSuggest = function (index) {
            console.log(index);
            $rootScope.nuovoUtente.user_name = this.sugg[index];
            this.disableSugg();
        }
        this.addUser = function () {
            //validate
            pippo.sendResult = {};
            SendDataService("addUser", $rootScope.nuovoUtente, pippo.sendResult);
            $timeout(function () {
                if (pippo.sendResult.success) {
                    $rootScope.$emit('newUser', []);
                    $rootScope.showNotShow.nuovoUtente = false;
                    $rootScope.showNotShow.listaUtenti = true;
                }
            }, 3000);
        }
    });

    app.controller("UtentiVTController", function ($timeout, ConfiguratorService, GetDataService, GetResUriService, $rootScope, SendDataService) {
        this.utentiVT = {};
        this.utentiVTLoaded = {};
        this.association = {};
        $rootScope.showNotShow.corpoListaUtentiVT = false;
//        this.showOnlyActiveUsers = true;

        var pippo = this;
        this.load = function (force) {
            if (typeof (force) === "undefined" || force || pippo.utentiVTLoaded === {}) {
                $rootScope.showNotShow.corpoListaUtentiVT = false;

                GetDataService("getUtentiVT", this.utentiVT, this.utentiVTLoaded, "usersVTLoaded", []);
            }
        }
        this.associate = function (vtid) {
            var dataObject = {userid: pippo.association.userid, vtuserid: vtid};
            pippo.association = {};
            SendDataService("associateVT", dataObject, {}, "associatedVT", [pippo.association.userid]);
        }
        this.cancel = function (vtid) {
            console.log("cancel");
            $rootScope.$emit("associatedVT", [pippo.association.userid]);
            pippo.association = {};
        }
        this.disassociate = function () {
            var dataObject = {userid: pippo.association.userid, vtuserid: "delete"};
            pippo.association = {};
            SendDataService("associateVT", dataObject, {}, "associatedVT", [pippo.association.userid]);
        }
        $rootScope.$on('associateVT', function (event, mass) {

            if (typeof (mass) === "undefined" || typeof (mass.userid) === undefined || typeof (mass.utenti) === undefined) {
                $rootScope.showNotShow.corpoListaUtentiVT = false;
                $rootScope.showNotShow.listaUtentiVT = false;
                $rootScope.showNotShow.corpoListaUtenti = true;
                $rootScope.showNotShow.listaUtenti = true;
                return;
            }
            pippo.association = mass;
            pippo.load();
        });
        $rootScope.$on('disassociateVT', function (event, mass) {
            pippo.association = mass;
            pippo.disassociate();
        });
        $rootScope.$on('associatedVT', function (event, mass) {
            $rootScope.showNotShow.corpoListaUtentiVT = false;
            $rootScope.showNotShow.listaUtentiVT = false;
            $rootScope.showNotShow.corpoListaUtenti = true;
            $rootScope.showNotShow.listaUtenti = true;
        });
        $rootScope.$on('usersVTLoaded', function () {

            if (typeof (pippo.utentiVT) === "undefined")
                return;
            if (typeof (pippo.utentiVT.arrayData) === "undefined")
                return;
            for (var i in pippo.utentiVT.arrayData) {
                pippo.utentiVT.arrayData[i].value.locked = false;
                for (var j in pippo.association.utenti) {
                    if (pippo.association.utenti[j].value.vt && pippo.association.utenti[j].value.vtuid === pippo.utentiVT.arrayData[i].value.id) {
                        pippo.utentiVT.arrayData[i].value.locked = pippo.association.utenti[j].value.id;
                        break;
                    }
                }
            }
            $rootScope.showNotShow.corpoListaUtentiVT = true;
        });
    });

})();


