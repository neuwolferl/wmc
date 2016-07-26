
var version = "0.1";
var tsmkt = angular.module("TSMKT", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
tsmkt.provider("TsMkt", function () {
    this.randomString = function (le)
    {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        for (var i = 0; i < le; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return text;
    };
    this.setTicket = function () {
        var flag = true;
        while (flag) {
            var btnIndex = this.randomString(10);
            if (typeof (this.hash[btnIndex]) === "undefined") {
                flag = false;
            }
        }
        this.hash[btnIndex] = {
            disable: function () {
            },
            enable: function () {
            },
            click: function () {

            },
            enabled: true,
            clickNo: 0,
            clickTs: [],
            whatToShow: {
                disabled: "",
                enabled: ""
            }
        };
        return [btnIndex, this.hash[btnIndex]];
    }
    this.lock = false;
    this.hash = [];
    this.$get = function ($filter, $rootScope) {
        var that = this;
        return {
            getBtn: function (btnIndex) {
                return {
                }
            },
            getAll: function () {
                return that.hash;
            },
            getGroupEnableCallback: function (btnList) {
            },
            isDefined: function (calIndex) {
            },
            newBtn: function () {
            }
        };
    }
}
);





