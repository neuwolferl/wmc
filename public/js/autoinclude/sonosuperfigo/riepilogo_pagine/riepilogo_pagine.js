(function () {
    var app = angular.module("RiepilogoPagine", ['ui.bootstrap', 'DATAMODULE'], function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.run(function () {

    });
    app.filter("showOnlyViews", function () {
        return function (input) {
            var ret = [];
            for (var i in input) {
                if (typeof (input[i]['view']) !== "undefined" && input[i]['view'] !== "") {
                    ret.push(input[i]);
                }
            }
            return ret;
        }
    });
    app.filter("selectedController", function () {
        return function (input, cont) {
            var ret = [];
            for (var i in input) {
                if (typeof (cont) === "undefined" || cont === "" || input[i]['index'] === cont) {
                    ret.push(input[i]);
                }
            }
            return ret;
        }
    });
    app.filter("selectedAction", function () {
        return function (input, cont) {
            var ret = [];
            for (var i in input) {
                if (typeof (cont) === "undefined" || cont === "" || input[i].method === cont) {
                    ret.push(input[i]);
                }
            }
            return ret;
        }
    });

    app.controller("AuthController", function ($rootScope, $timeout, ConfiguratorService, GetDataService, SendDataService) {
        this.controller = "";
        this.action = "";
        this.listaUtenti = [];
        this.googleUsers = {};
        this.googleUsersLoaded = {};
        this.authorizedUsersForPages = {};
        this.authorizedUsersForPagesLoaded = {};
        this.authUserLoaded = {};
        var that = this;
        this.loadUsers = function () {
            GetDataService("getGoogleUsers", that.googleUsers, that.googleUsersLoaded, "googleUsersLoaded", []);
        };
        this.loadAuthorizedUsersForPages = function () {
            GetDataService("getAuthorizedUsersForPages", that.authorizedUsersForPages, that.authorizedUsersForPagesLoaded, "authorizedUsersForPagesLoaded", []);
        };
        this.showUsers = function () {
            $rootScope.showNotShow.listaUtenti = true;
            that.listaUtenti = [];
            var index = 0;
            for (var i in that.googleUsers) {
                if (typeof (that.googleUsers[i].value.id) === "undefined")
                    continue;
                that.listaUtenti[Number(that.googleUsers[i].value.id)] = {id: that.googleUsers[i].value.id, name: that.googleUsers[i].value.display_name};
            }
            for (var i in that.authorizedUsersForPages) {
                if (that.authorizedUsersForPages[i].value.controller === that.controller)
                    if (that.authorizedUsersForPages[i].value.action === that.action) {
//                        console.log("AUTORIZZO "+that.authorizedUsersForPages[i].value.userid);
                        that.listaUtenti[Number(that.authorizedUsersForPages[i].value.userid)].auth = true;
                    }
            }
            var array = []
            for (var k in that.listaUtenti) {
                array.push(that.listaUtenti[k]);
            }
            that.listaUtenti = array;
        }
        this.authnoauth = function (id) {
            
            var index = -1;
            for (var i in that.listaUtenti){
                if (that.listaUtenti[i].id == id){
                    index = i;
                    break;
                }
            }
            if (index === -1) {
                return;
            }
            
            if (that.listaUtenti[index]) {
                if (typeof (that.listaUtenti[index].auth) !== "undefined" && that.listaUtenti[index].auth) {
                    that.authUser(that.controller, that.action, that.listaUtenti[index].id);
                }
                else {
                    that.unAuthUser(that.controller, that.action, that.listaUtenti[index].id);
                }
            }
        }
        this.authUser = function (controller, action, userid) {
            SendDataService("authUser", {controller: controller, action: action, userid: userid, what: "auth"}, that.authUserLoaded, "authUserLoaded", [controller, action, userid]);
        }
        this.unAuthUser = function (controller, action, userid) {
            SendDataService("authUser", {controller: controller, action: action, userid: userid, what: "unauth"}, that.authUserLoaded, "authUserLoaded", [controller, action, userid]);
        }
//        this.authAll = function(controller,action){
//            SendDataService("authAll", {controller: controller, action: action, users: that.googleUsers, what: "auth"}, that.authUserLoaded, "authUserLoaded", [controller, action, userid]);
//        }
//        this.unAuthAll = function(controller,action, userid){
//            SendDataService("authAll", {controller: controller, action: action, users: that.googleUsers, what: "unauth"}, that.authUserLoaded, "authUserLoaded", [controller, action, userid]);
//        }
        this.closeUsers = function () {
            $rootScope.showNotShow.listaUtenti = false;
            this.controller = "";
            this.action = "";
            $rootScope.controller = "";
            $rootScope.action = "";
            this.listaUtenti = [];
            this.googleUsers = {};
            this.googleUsersLoaded = {};
            this.authorizedUsersForPages = {};
            this.authorizedUsersForPagesLoaded = {};
            this.authUserLoaded = {};
        }
        $rootScope.$on("MakeAuthorization", function (event, mass) {
            that.controller = mass[0];
            that.action = mass[1];
            $rootScope.controller = mass[0];
            $rootScope.action = mass[1];
            that.loadUsers();
        });
        $rootScope.$on("googleUsersLoaded", function (event, mass) {
            if (typeof (that.googleUsersLoaded.result) !== "undefined" && that.googleUsersLoaded.result) {
                that.googleUsers = that.googleUsers.arrayData;
                that.googleUsersLoaded = {};
                that.loadAuthorizedUsersForPages();
            }
        });
        $rootScope.$on("authorizedUsersForPagesLoaded", function (event, mass) {
            if (typeof (that.authorizedUsersForPagesLoaded.result) !== "undefined" && that.authorizedUsersForPagesLoaded.result) {
                that.authorizedUsersForPages = that.authorizedUsersForPages.arrayData;
                that.authorizedUsersForPagesLoaded = {};
                that.showUsers();
            }
        });
    });


    app.controller("MainController", function ($rootScope, $timeout, ConfiguratorService, GetDataService, SendDataService) {
        $rootScope.showNotShow = {};
        $rootScope.showNotShow.listaUtenti = false;
        var url = document.URL + ".ws";
        console.log(url);
        this.configure = function () {
            ConfiguratorService.get(url);
        };
        this.pagine = {};
        this.pagineLoaded = {};
        this.pagineDB = {};
        this.pagineDBLoaded = {};
        this.sendPagesLoaded = {};
        this.enablePageLoaded = {};
        this.disablePageLoaded = {};
        this.pagineOrdinate = {};
        this.showOnlyViews = true;
        var that = this;
        this.makeAuthorization = function (c, a) {
            $rootScope.$emit("MakeAuthorization", [c, a]);
        }
        this.disablePage = function (controller, method) {
            SendDataService("disablePage", {controller: controller, action: method}, that.disablePageLoaded, "disablePageLoaded", [controller, method]);
        }
        this.enablePage = function (controller, method) {
            SendDataService("enablePage", {controller: controller, action: method}, that.enablePageLoaded, "enablePageLoaded", [controller, method]);
        }

        this.load = function (force) {
            if (typeof (force) === "undefined" || force || that.pagineLoaded === {}) {
                GetDataService("getElencoPagine", this.pagine, this.pagineLoaded, "getElencoPagineLoaded", []);
                $rootScope.$on("getElencoPagineLoaded", function () {
                    that.pagine = that.pagine.arrayData;
                    GetDataService("checkPage", that.pagineDB, that.pagineDBLoaded, "checkPageLoaded", ["initial"]);
                });
                $rootScope.$on("checkPageLoaded", function (event, mass) {
                    if (typeof (mass) === "object") {
                        if (typeof (mass.result) === "string" && mass.result === "failure") {
                            GetDataService("checkPage", that.pagineDB, that.pagineDBLoaded, "checkPageLoaded", ["initial"]);
                            return;
                        }
                    }
                    if (mass instanceof Array && mass[0] === "initial") {
                        that.pagineOrdinate = [];
                        that.pagineDB = that.pagineDB.data;
                        for (var i in that.pagineDB) {
                            that.pagineOrdinate[that.pagineDB[i].controller + "/" + that.pagineDB[i].action] = {
                                id: that.pagineDB[i].id,
                                isactive: that.pagineDB[i].isactive,
                            };
                        }
                        if (mass[0] === "initial") {
                            var pagesToUpload = [];
                            for (var i in that.pagine) {
//                                console.log(that.pagine[i].index);
                                for (var j in that.pagine[i].value) {
                                    if (that.pagine[i].value[j].view === "")
                                        continue;
                                    if (typeof (that.pagineOrdinate[that.pagine[i].index + "/" + that.pagine[i].value[j].method]) !== "undefined") {
                                        that.pagine[i].value[j].isactive = (that.pagineOrdinate[that.pagine[i].index + "/" + that.pagine[i].value[j].method].isactive === '1');
                                    }
                                    else {
                                        pagesToUpload.push({controller: that.pagine[i].index, action: that.pagine[i].value[j].method});
                                    }
                                }
                            }
                            if (pagesToUpload.length > 0) {
                                SendDataService("addPages", pagesToUpload, that.sendPagesLoaded, "sendPagesLoaded", []);
                            }
                            else {
                                that.pagineLoaded = {};
                                that.pagineDBLoaded = {};
                                that.sendPagesLoaded = {};
                            }
                        }

                    }
                });
            }
        };
        this.configure();
        $rootScope.$on("confLoaded", function () {
            that.load();
        });
        $rootScope.$on("confFailure", function () {
            that.configure();
        });
        $rootScope.$on("sendPagesLoaded", function () {
            if (typeof (that.sendPagesLoaded.success) !== "undefined" && that.sendPagesLoaded.success) {
                that.pagineLoaded = {};
                that.pagineDBLoaded = {};
                that.sendPagesLoaded = {};
                location.reload();
            }

        });
        $rootScope.$on("disablePageLoaded", function (event, mass) {
            if (typeof (that.disablePageLoaded.success) !== "undefined" && that.disablePageLoaded.success) {
                that.disablePageLoaded = {};
                if (typeof (mass[0]) !== "undefined" && typeof (mass[1]) !== "undefined")
                    for (var i in that.pagine) {
                        if (that.pagine[i].index !== mass[0])
                            continue;
                        for (var j in that.pagine[i].value) {
                            if (that.pagine[i].value[j].method !== mass[1])
                                continue;
                            that.pagine[i].value[j].isactive = false;
                        }
                    }
            }

        });
        $rootScope.$on("enablePageLoaded", function (event, mass) {
            if (typeof (that.enablePageLoaded.success) !== "undefined" && that.enablePageLoaded.success) {
                that.enablePageLoaded = {};
                if (typeof (mass[0]) !== "undefined" && typeof (mass[1]) !== "undefined")
                    for (var i in that.pagine) {
                        if (that.pagine[i].index !== mass[0])
                            continue;
                        for (var j in that.pagine[i].value) {
                            if (that.pagine[i].value[j].method !== mass[1])
                                continue;
                            that.pagine[i].value[j].isactive = true;
                        }
                    }
            }

        });
    });
})();


