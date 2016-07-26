(function() {
    var app = angular.module('IMieiProgetti', ['ui.bootstrap'], function($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.filter('pagina', function() {
        return function(input, start) {
            if (input) {
                start = +start;
                return input.slice(start);
            }
            return [];
        };
    });
    //filtro per visualizzare correttamente la situaizone del progetto
    app.filter('showSituazione', function() {
        return function(input) {
            switch (input) {
                case '1':
                case 1:
                    return 'In attesa di conferma';
                    break;
                default:
                    return 'Permette avanzamento';
                    break;
            }
        };
    });
    //filtro per limitare visualizzazione del progetto
    app.filter('filterSituazione', function() {
        return function(input, selector) {
            switch (selector) {
                case '1':
                case 1:
//                    console.log(input);
                    var ret = [];
                    for (var i in input) {

                        if (!(input[i]["Situazione progetto"]) || input[i]["Situazione progetto"] + "" === "0") {
                            ret[i] = input[i];
                        }
                    }
                    return ret;
                    break;
                case '2':
                case 2:
                    var ret = [];
                    for (var i in input) {
                        if (input[i]["Situazione progetto"] + "" === "1") {
                            ret[i] = input[i];
                        }
                    }
                    return ret;
                    break;
                default:
                    return input;
                    break;
            }
        };
    });
    app.controller("ListoneController", function($scope, $http, $timeout, $rootScope) {
        $scope.data = [];
        $scope.statusFilter = "0";
        this.listoneLoaded = false;
        this.currentPage = 0;
        this.totalNumberOfPages = 0;
        $scope.itemsInAPage = 10
        this.previousIsShown = false;
        this.nextIsShown = false;
        this.pages = [];
        this.ricerca = "";
        var pippo = this;
        this.filter = function() {
            $timeout(function() {
                if (!$rootScope.filtered.length)
                    pippo.totalNumberOfPages = 0;
                else
                    pippo.totalNumberOfPages = Math.ceil($rootScope.filtered.length / $scope.itemsInAPage);
                pippo.currentPage = 0;
                pippo.previousIsShown = (pippo.currentPage > 0);
                pippo.nextIsShown = (pippo.currentPage < pippo.totalNumberOfPages - 1);
                pippo.pages = [];
                for (var i = 0; i < pippo.totalNumberOfPages; i++) {
                    pippo.pages.push((i + 1));
                }
            }, 100);
        };

        this.currentPageToClass = function(page) {
            if (pippo.currentPage == page - 1) {
                return "active";
            }
            else {
                return "";
            }
        }

        this.goBack = function() {
            if (this.currentPage > 0)
                this.currentPage--;
            this.previousIsShown = (this.currentPage > 0);
            this.nextIsShown = (this.currentPage < this.totalNumberOfPages);
        }
        this.goFirst = function() {
            this.currentPage = 0;
        }

        this.goForw = function() {
            if (this.currentPage < this.totalNumberOfPages - 1)
                this.currentPage++;
            this.nextIsShown = (this.currentPage < this.totalNumberOfPages - 1);
            this.previousIsShown = (this.currentPage > 0);
        }
        this.goLast = function() {
            this.currentPage = this.totalNumberOfPages - 1;
        }

        this.linkURL = document.URL;
        this.linkURL = this.linkURL.split("i_miei_progetti");
        this.linkURL = this.linkURL[0] + "avanzamento/";
        this.goToPage = function(page) {
            this.currentPage = page;
        };
        this.organizeData = function() {

            data = $scope.rawdata;
            pippo.totalNumberOfPages = Math.floor((data.length - 1) / ($scope.itemsInAPage)) + 1;
            pippo.currentPage = 0;
            pippo.ricerca = "";
            pippo.nascondiLoader = false;
            pippo.nextIsShown = (pippo.currentPage < (pippo.totalNumberOfPages - 1));
            tmpdata = [];
            var i = -1;
            var j = 0;
            for (var k = 0; k < data.length; k++) {
                tmpdata[k] = data[k];
                tmpdata[k]["index"] = k;
                tmpdata[k]["link"] = pippo.linkURL + tmpdata[k]["Pk_project"];
            }
            $scope.data = tmpdata;
            pippo.nascondiLoader = true;
        };
        this.avvia = function(link, datum) {
            console.log(datum);
            if ("" + datum["Situazione progetto"] !== "1") {
                window.location.href = link;
            }

        }
        var url = document.URL.replace("#", "") + '.ws';
        $timeout(function() {
            var responsePromise = $http.get(url)
                    .success(function(data, status, headers, config) {
                        pippo.totalNumberOfPages = Math.floor((data.length - 1) / ($scope.itemsInAPage)) + 1;
                        for (var i = 0; i < pippo.totalNumberOfPages; i++) {
                            pippo.pages.push((i + 1));
                        }
                        $scope.rawdata = data;
                        console.log(data);
                        pippo.organizeData();
                        pippo.listoneLoaded = true;
                        pippo.nextIsShown = (pippo.currentPage < (pippo.totalNumberOfPages - 1));
                    })
                    .error(function(data, status, headers, config) {
                        alert("AJAX failed!");
                    });
        }, 500);

    });
})();

