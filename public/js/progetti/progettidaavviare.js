(function() {
    var app = angular.module('ProgettiDaAvviare', ['ui.bootstrap'], function($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    
    app.filter("showSituazione", function (){
        return function (input){
            switch (input){
                case '0':
                case 0:
                    return 'Avvio disponibile';
                    break;
                case '1':
                case 1:
                    return 'Attesa conferma';
                    break;
                case '2':
                case 2:
                    return 'Avvio respinto';
                    break;
                default:
                    return 'Dato non disponibile';
            }
        }
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
    app.controller("ListoneController", function($scope, $http, $timeout) {
        $scope.data = [];
        this.nascondiLoader = false;
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
                pippo.totalNumberOfPages = Math.ceil($scope.filtered.length / $scope.itemsInAPage);
                pippo.currentPage = 0;
                pippo.pages = [];
                for (var i = 0; i < pippo.totalNumberOfPages; i++) {
                    pippo.pages.push((i + 1));
                }
            }, 100);
        };

        this.currentPageToClass = function(page) {
            if (pippo.currentPage == page-1) {
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
        this.linkURL = this.linkURL.split("progetti_da_avviare");
        this.linkURL = this.linkURL[0] + "avvia/";
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
            if (""+datum["Situazione progetto"] !== "1"){
                window.location.href = link;
            }
            
        }
        var url = document.URL.replace("#", "") + '.ws';
        var responsePromise = $http.get(url)
                .success(function(data, status, headers, config) {
                    pippo.totalNumberOfPages = Math.floor((data.length - 1) / ($scope.itemsInAPage)) + 1;
                    for (var i = 0; i < pippo.totalNumberOfPages; i++) {
                        pippo.pages.push((i + 1));
                    }
                    $scope.rawdata = data;
                    pippo.organizeData();
                    pippo.nascondiLoader = true;
                    pippo.nextIsShown = (pippo.currentPage < (pippo.totalNumberOfPages - 1));
                })
                .error(function(data, status, headers, config) {
                    alert("AJAX failed!");
                });
    });
})();

