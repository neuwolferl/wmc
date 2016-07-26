(function () {
    var app = angular.module("CodeMkt", ['DATAMODULE', 'THEMASK'], ['$interpolateProvider', function ($interpolateProvider) {
            $interpolateProvider.startSymbol("--__");
            $interpolateProvider.endSymbol("__--");
        }]);
    app.controller("NewCampaignsController", ['DataService', '$scope', '$rootScope', '$timeout',
        function (DataService, $scope, $rootScope, $timeout) {
            this.newCampaign = function () {
                var name = prompt("Specificare il nome della nuova campagna.", "");
                this.newCampTicket = DataService.postData("newCamp", {name: name}, "newCampPerformed", {});
                $rootScope.$on("newCampPerformed", function (event, mass) {
                    window.location.reload();
                });
            }
        }]);
    app.controller("CampaignsController", ['DataService', '$scope', '$rootScope', '$timeout', function (DataService, $scope, $rootScope, $timeout) {
            this.campaigns = {};
            this.isAvail = {status: "0"};
            this.isSusp = {status: "1"};
            this.isClosed = {status: "2"};
            $rootScope.showNotShow = {};
            var that = this;
            this.loadCampaigns = function () {
                this.campaigns = {};
                this.campTicket = DataService.getData("getCampaigns", {}, "getCampaignsPerformed", {});
                $rootScope.$on("getCampaignsPerformed", function (event, mass) {
                    that.campaigns = that.campTicket[1].response.data;
                    for (var i in that.campaigns) {
                        if (typeof (that.campaigns[i].lots) === "string") {
                            that.campaigns[i].lots = that.campaigns[i].lots.replace("[", "").replace("]", "");
                        }
                    }
                });
            };



            this.goToCampaign = function (campId) {
                var baseurl = document.URL;
                baseurl = baseurl.split("codemkt");
                baseurl = baseurl[0];
                var index = -1;
                for (var i in this.campaigns) {
                    if (this.campaigns[i].id == campId) {
                        index = i;
                        break;
                    }
                }
                if (index === -1)
                    return;
                if (Number(this.campaigns[index].status))
                    return;
                window.location.href = baseurl + "selezionezone/" + campId;
            }
            this.closeCampaign = function (campId) {
                DataService.postData("closeCamp", {camp: campId}, "closeCampPerformed", {});

            }
            $rootScope.$on("closeCampPerformed", function (event, mass) {
                window.location.reload();
            });
            this.suspendCampaign = function (campId) {
                DataService.postData("suspendCamp", {camp: campId}, "suspendCampPerformed", {});

            }
            $rootScope.$on("suspendCampPerformed", function (event, mass) {
                window.location.reload();
            });
            this.restoreCampaign = function (campId) {
                DataService.postData("restoreCamp", {camp: campId}, "restoreCampPerformed", {});

            }
            $rootScope.$on("restoreCampPerformed", function (event, mass) {
                window.location.reload();
            });
            var baseurl = document.URL;
            baseurl = baseurl.split("codemkt");
            baseurl = baseurl[0] + "codemkt.ws";
            DataService.initialize(baseurl);
            $rootScope.$on("confFailure", function () {
                alert("Self configuration failed. The page may not work properly. Please reload. If this is not the first time please contact an admin.");
            });
            $rootScope.$on("confLoaded", function () {
                that.loadCampaigns();
            });
            $rootScope.$on("reloadCampaigns", function () {
                that.loadCampaigns();
            });
        }]);
})();



