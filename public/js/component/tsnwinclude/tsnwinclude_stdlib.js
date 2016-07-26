var version = "0.1";
var tsnwinclude = angular.module("TSNWINCLUDE", ['DATAMODULE', 'COMMON'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});

tsnwinclude.provider("Tsnw", [function () {
        var that = this;
        if (typeof (_) !== "function") {
            console.log("Errore, non trovo lodash!");
            return;
        }
        this.linkCtrl = null;
        this.customResources = {};
        this.fits = function (a, b, onlyStruct, strict) {
            if (typeof (a) != typeof (b) || typeof (a.length) != typeof (b.length)) {
                return false;
            }
            if (typeof (a) === "string" || typeof (a) === "number" || typeof (a) === "boolean") {
                return (strict ? ((onlyStruct && typeof (a) === typeof (b)) || a === b) : (onlyStruct || a == b));
            }
            if (typeof (a) === "object") { //vettore o oggetto
                for (var i in a) {
                    if (a.hasOwnProperty(i)) {
                        if (typeof (b[i]) === "undefined")
                            return false;
                        if (!that.fits(a[i], b[i], onlyStruct, strict))
                            return false;
                    }
                }
                return true;
            }
        };
        this.$get = [function giovanni() {
                var pippo = this;
                return {
                    vtigerUser: {
                        getById: function (id) {

                        },
                        getByRole: function (role) {

                        },
                        getByGroup: function (group) {

                        }
                    },
                    accountUtilities: {
                        getFasciaFatturatoList: function () {
                            return [
                                "0 - 0,25 (Ml. euro)",
                                "0,25 - 0,5 (Ml. euro)",
                                "0,5 - 1,5 (Ml. euro)",
                                "1,5 - 2,5 (Ml. euro)",
                                "2,5 - 5 (Ml. euro)",
                                "5 - 13 (Ml. euro)",
                                "13 - 25 (Ml. euro)",
                                "25 - 50 (Ml. euro)",
                                "50 - 100 (Ml. euro)",
                                "> 100 (Ml. euro)",
                                "NON ASSEGNATA"
                            ];
                        },
                        getFasciaFatturatoValuedList: function () {
                            return [
                                {id: "1", value: "0 - 0,25 (Ml. euro)"},
                                {id: "2", value: "0,25 - 0,5 (Ml. euro)"},
                                {id: "3", value: "0,5 - 1,5 (Ml. euro)"},
                                {id: "4", value: "1,5 - 2,5 (Ml. euro)"},
                                {id: "5", value: "2,5 - 5 (Ml. euro)"},
                                {id: "6", value: "5 - 13 (Ml. euro)"},
                                {id: "7", value: "13 - 25 (Ml. euro)"},
                                {id: "8", value: "25 - 50 (Ml. euro)"},
                                {id: "9", value: "50 - 100 (Ml. euro)"},
                                {id: "10", value: "> 100 (Ml. euro)"},
                                {id: "11", value: "NON ASSEGNATA"},
                            ];
                        },
                        getClasseDipendentiList: function () {
                            return [
                                "1",
                                "2 - 5",
                                "6 - 9",
                                "10 - 19",
                                "20 - 49",
                                "50 - 249",
                                "250 - 499",
                                "500 - 1000",
                                "MAGGIORE DI 1000",
                                "NON ASSEGNATA"
                            ];
                        },
                        getClasseDipendentiValuedList: function () {
                            return [
                                {id: "1", value: "1"},
                                {id: "2", value: "2 - 5"},
                                {id: "3", value: "6 - 9"},
                                {id: "4", value: "10 - 19"},
                                {id: "5", value: "20 - 49"},
                                {id: "6", value: "50 - 249"},
                                {id: "7", value: "250 - 499"},
                                {id: "8", value: "500 - 1000"},
                                {id: "9", value: "MAGGIORE DI 1000"},
                                {id: "10", value: "NON ASSEGNATA"},
                            ];
                        },
                        getFormaGiuridicaList: function () {
                            return [
                                "SRL",
                                "SRL a socio unico",
                                "SPA a socio unico",
                                "SPA",
                                "SNC",
                                "SAS",
                                "DITTA INDIVIDUALE",
                                "SDF E SOCIETA SEMPLICI",
                                "NON ASSEGNATA"
                            ];
                        },
                    }
                };
            }]
    }]);






