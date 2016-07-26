
var version = "0.1";
var leadTableModule = angular.module("LEADTABLEMODULE", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
leadTableModule.directive('leadTableInventario', ['$rootScope', function ($rootScope) {
        function link(scope, element, attrs) {
        }
        ;
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/leadTable/leadTableInventario-" + version + ".html";
            },
            restrict: 'E',
            replace: true,
            scope: {
                data: '=data',
                dblclick: '='
            },
            link: function (scope, element, attrs) {
                scope.classLead = classLeadInventario;
            },
            controller: function ($scope, $element, $attrs, $rootScope, $timeout, $interval) {
                var pippo = this;
                this.adjustForMask = function () {
                    console.log($element.parents(".dimensionatore"));
                    console.log($element.parents(".dimensionatore").height());
                    console.log($element.height());

                    $element.parents(".dimensionatore").children().each(function () {
                        $(this).height($element.parents(".dimensionatore").height());
                    });

                    $element.height($element.parents(".dimensionatore").height());
                };
                $scope.$watch("data", function () {
                    var modal = $element.parents(".modal");
                    if (modal.length) {
                        modal = modal.eq(0);
                        if (modal.trigger)
                            modal.trigger("adjust");

                        $timeout(function () {
                            pippo.adjustForMask();
                        }, 100);
                    }

                });
            }
        };
    }]);
leadTableModule.directive('leadTableCompact', [function () {
        function link(scope, element, attrs) {
        }
        ;
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/leadTable/leadTableCompact-" + version + ".html";
            },
            restrict: 'E',
            scope: {
                data: '=data'
            },
            controller: function ($scope, $element, $attrs, $rootScope, $timeout, $interval) {

            }
        };
    }]);


leadTableModule.filter("statoLeadInventario", function (userInfoFilter) {
    return function (lead, tmkList, vendList) {
        var stato = {
            venditore: "Libera",
            donotcallbefore: "",
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


var classLeadInventario = function (lead) {
    var ret = "";
    switch (lead.lead.formagiuridica) {
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
    switch (lead.lead.classedipendenti) {
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
    switch (lead.lead.fasciafatturato) {
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