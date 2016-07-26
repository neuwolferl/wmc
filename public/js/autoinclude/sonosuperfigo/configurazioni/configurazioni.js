(function () {
    var app = angular.module("Configurazioni", ['ui.bootstrap', 'DATAMODULE'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.run(function (ConfiguratorService, $rootScope) {
        var url = document.URL + ".ws";
        ConfiguratorService.get(url);
    });
    app.controller("SitesController", function ($rootScope, $timeout, ConfiguratorService, GetDataService, SendDataService) {
        this.sites = [];
        this.selectedSite = -1;
        this.sitesVM = {};
        this.sitesLoaded = {};
        var that = this;
        this.selectSite = function (index) {
            this.selectedSite = index;
            $rootScope.$emit("selectSite",[this.sites[this.selectedSite]]);
        };
        this.isSelectedSite = function (index) {
            return (this.selectedSite === index);
        };
        this.load = function () {
            this.sites = [];
            GetDataService("getSites", this.sitesVM, this.sitesLoaded, "sitesLoaded", []);
            $rootScope.$on("sitesLoaded", function (event, mass) {
                console.log(that.sitesVM);
                var sites = that.sitesVM.data.sites;
                var sitesnames = sites.all.split("|");
                var k = 0;
                for (var i in sitesnames) {
                    that.sites.push({index: k++, name: sitesnames[i], domain: sites[sitesnames[i]].domain});
                }
            });
        }




        $rootScope.$on("confLoaded", function () {
            that.load();
        });
    });
    
    
    app.controller("MainController", function ($rootScope, $timeout, ConfiguratorService, GetDataService, SendDataService) {
        this.selectedSite = "";
        this.confs = [];
        this.selectedConf = "/";
        this.confsVM = {};
        this.confsLoaded = {};
        var that = this;
        this.loadConfs = function(){
            GetDataService("getAllConfs", this.confsVM, this.confsLoaded, "confsLoaded", []);
        }
        $rootScope.$on("confsLoaded",function(event,mass){
            console.log(that.confsVM);
            that.confs = that.confsVM.arrayData;
        });
        $rootScope.$on("selectSite",function(event,mass){
            if (typeof(mass.length) !== "undefined" && mass.length > 0){
                that.selectedSite = mass[0];
                that.loadConfs();
            }
        });
    });
})();


