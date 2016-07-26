var version = "0.1";
var Logger = angular.module("LOGGER", [], ["$interpolateProvider", function (a) {
        a.startSymbol("--__");
        a.endSymbol("__--")
    }]);
Logger.directive("logGeneral", ["Logger", function (a) {
        return{templateUrl: function () {
                var b = document.URL;
                b = b.split("public");
                return b[0] + "public/js/core/logger/logGeneral-" + version + ".html"
            }, restrict: "E", scope: {logId: "=logid", logDescriptor: "@logdescriptor"}, controller: ["$scope", "$element", "$attrs", "$rootScope", "$timeout", "$interpolate", "$filter", function (e, d, c, b, g, i, h) {
                    var f = new Date();
                    e.dateObj = {date: f, dateEngString: f.getFullYear() + "-" + (f.getMonth() < 9 ? "0" : "") + (f.getMonth() + 1) + "-" + (f.getDate() < 10 ? "0" : "") + f.getDate(), dateItaString: (f.getDate() < 10 ? "0" : "") + f.getDate() + "-" + (f.getMonth() < 9 ? "0" : "") + (f.getMonth() + 1) + "-" + f.getFullYear()};
                    e.logged = [];
                    e.formatLogged = [];
                    e.openLogRow = function (j) {
                        if (e.formatLogged[j]) {
                            if (!e.formatLogged[j].comments && !e.formatLogged[j].children.length) {
                                return
                            }
                            e.formatLogged[j].open = true
                        }
                    };
                    e.closeLogRow = function (j) {
                        if (e.formatLogged[j]) {
                            e.formatLogged[j].open = false
                        }
                    };
                    e.format = function () {
                        e.formatLogged = [];
                        var o = a.getLogger(e.logId).getLogDescriptor(e.logDescriptor);
                        var q = a.getLogger(e.logId).getLogDescriptor("users");
                        for (var k in e.logged) {
                            var l = e.logged[k];
                            if (!o[e.logged[k].code_main] || !o[e.logged[k].code_main][e.logged[k].code_detail] || !o[e.logged[k].code_main][e.logged[k].code_detail][e.logged[k].code_type] || !o[e.logged[k].code_main][e.logged[k].code_detail][e.logged[k].code_type][e.logged[k].what_element]) {
                                continue
                            }
                            if (q) {
                                if (q[l.who]) {
                                    l.who = q[l.who].NOME + " " + q[l.who].COGNOME
                                }
                            }
                            var n = o[e.logged[k].code_main][e.logged[k].code_detail][e.logged[k].code_type][e.logged[k].what_element].row;
                            if (!n) {
                                continue
                            }
                            l.row = i(n);
                            l.row = l.row((e.logged[k]));
                            if (l.parent != null) {
                                for (var m in e.formatLogged) {
                                    if (e.formatLogged[m].log_id == l.parent) {
                                        l.parent = m;
                                        l.level = [];
                                        for (var j in e.formatLogged[m].level) {
                                            l.level.push("x")
                                        }
                                        l.level.push("x");
                                        break
                                    }
                                }
                            } else {
                                l.level = []
                            }
                            if (l.comment) {
                                l.children.push("c" + l.log_id)
                            }
                            e.formatLogged.push(l);
                            if (l.comment) {
                                var p = {log_id: "c" + l.log_id, parent: e.formatLogged.length - 1, children: [], level: [], row: "NOTE: " + l.comment};
                                for (var j in l.level) {
                                    p.level.push("x")
                                }
                                p.level.push("x");
                                e.formatLogged.push(p)
                            }
                        }
                    };
                    a.getLogger(e.logId).addOnChange(function (j) {
                        e.logged = j.adjustedData;
                        e.format();
                        var k = d.parents(".modal");
                        if (k.length) {
                            k = k.eq(0);
                            if (k.trigger) {
                                k.trigger("adjust")
                            }
                            g(function () {
                                e.adjustForMask()
                            }, 100)
                        }
                    });
                    e.adjustForMask = function () {
                        d.parents(".dimensionatore").children().each(function () {
                            $(this).height(d.parents(".dimensionatore").height())
                        });
                        d.height(d.parents(".dimensionatore").height())
                    }
                }]}
    }]);
Logger.provider("Logger", function () {
    var a = this;
    this.randomString = function (d) {
        var e = "";
        var b = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        for (var c = 0; c < d; c++) {
            e += b.charAt(Math.floor(Math.random() * b.length))
        }
        return e
    };
    this.adjustData = function (h, l) {
        var d = [];
        for (var j in h) {
            var e = new Date(h[j]["timestamp"]);
            var c = new Date();
            var n = new Date(h[j].now);
            var f = "";
            if (e.getDate() === c.getDate() && e.getMonth() === c.getMonth() && e.getFullYear() === c.getFullYear()) {
                f = "oggi"
            } else {
                if (e.getDate() === (c.getDate() - 1) && e.getMonth() === c.getMonth() && e.getFullYear() === c.getFullYear()) {
                    f = "ieri"
                } else {
                    f = "il " + (e.getDate() < 10 ? "0" : "") + e.getDate() + "-" + (e.getMonth() < 9 ? "0" : "") + (e.getMonth() + 1) + "-" + e.getFullYear()
                }
            }
            var m = (c.getHours() - n.getHours());
            var k = (e.getHours() + m < 10 ? "0" : "") + (e.getHours() + m) + ":" + (e.getMinutes() < 10 ? "0" : "") + e.getMinutes() + ":" + (e.getSeconds() < 9 ? "0" : "") + e.getSeconds();
            console.log(k);
            var g = {log_id: h[j].log_id, comment: h[j].comment, data: f, ora: k, code_detail: h[j].code_detail, what_detail: h[j].what_detail, what_element: h[j].what_element, code_main: h[j].code_main, what_main: h[j].what_main, code_type: h[j].code_type, who: h[j].who_vtiger, parameters: h[j].parameters, parent: h[j].parent, diff: m, children: []};
            if (g.parent === "0") {
                g.parent = null
            }
            if (typeof (g.parameters) === "string" && g.parameters.length > 0) {
                g.parameters = JSON.parse(g.parameters)
            }
            d.push(g);
            if (g.parent && g.parent != "0") {
                for (var b in d) {
                    if (d[b].log_id == g.parent) {
                        d[b].children.push(g.log_id)
                    }
                }
            }
        }
        return d
    };
    this.setTicket = function () {
        var b = true;
        while (b) {
            var c = this.randomString(10);
            if (typeof (this.hash[c]) === "undefined") {
                b = false
            }
        }
        this.hash[c] = {piva: "", adjustedData: null, data: null, previousData: null, pars: {}, getLog: function () {
            }, putLog: function () {
            }, onChange: [], changePromise: null, refreshPromise: null, logDescriptors: {}};
        return[c, this.hash[c]]
    };
    this.hash = [];
    this.$get = ["$filter", "$rootScope", "$interval", function (d, b, e) {
            var c = this;
            return{getLogger: function (f) {
                    return{setData: function (g) {
                            if (!c.hash[f].data || g.length !== c.hash[f].data.length) {
                                c.hash[f].data = g;
                                c.hash[f].adjustedData = c.adjustData(g)
                            }
                        }, setPiva: function (g) {
                        }, setPars: function (g) {
                        }, setLogGetter: function (g) {
                            c.hash[f].getLog = function (h) {
                                return g(h)
                            }
                        }, setLogPutter: function (g) {
                            c.hash[f].putLog = function (h) {
                                return g(h)
                            }
                        }, startLogRefresher: function (h, g) {
                            if (c.hash[f].refreshPromise !== null) {
                                e.cancel(c.hash[f].refreshPromise)
                            }
                            c.hash[f].getLog(g);
                            c.hash[f].refreshPromise = e(function () {
                                c.hash[f].getLog(g)
                            }, h)
                        }, stopLogRefresher: function () {
                            if (c.hash[f].refreshPromise !== null) {
                                e.cancel(c.hash[f].refreshPromise);
                                c.hash[f].refreshPromise = null
                            }
                        }, addOnChange: function (g) {
                            c.hash[f].onChange.push(function () {
                                var h = {data: c.hash[f].data, adjustedData: c.hash[f].adjustedData};
                                return g(h)
                            })
                        }, startChangeMirroring: function () {
                            if (c.hash[f].changePromise !== null) {
                                e.cancel(c.hash[f].changePromise)
                            }
                            c.hash[f].changePromise = e(function () {
                                var g = false;
                                if (c.hash[f].previousData === null && c.hash[f].data !== null) {
                                    g = true;
                                    c.hash[f].previousData = c.hash[f].data
                                } else {
                                    if (c.hash[f].previousData !== null && c.hash[f].previousData.length !== c.hash[f].data.length) {
                                        g = true;
                                        c.hash[f].previousData = c.hash[f].data
                                    }
                                }
                                if (g) {
                                    for (var h in c.hash[f].onChange) {
                                        c.hash[f].onChange[h]()
                                    }
                                }
                            }, 2000)
                        }, stopChangeMirroring: function () {
                            e.cancel(c.hash[f].changePromise);
                            c.hash[f].changePromise = null
                        }, setLogDescriptor: function (g, k, m) {
                            var l = {};
                            for (var h in k) {
                                if (typeof (l[k[h].what_main]) === "undefined") {
                                    l[k[h].what_main] = {}
                                }
                                if (typeof (l[k[h].what_main][k[h].what_detail]) === "undefined") {
                                    l[k[h].what_main][k[h].what_detail] = {}
                                }
                                if (typeof (l[k[h].what_main][k[h].what_detail][k[h].what_type]) === "undefined") {
                                    l[k[h].what_main][k[h].what_detail][k[h].what_type] = {}
                                }
                                l[k[h].what_main][k[h].what_detail][k[h].what_type][k[h].what_element] = {row: k[h].logGeneralTemplate, translation: k[h].what_translation}
                            }
                            c.hash[f].logDescriptors[g] = l;
                            if (typeof (m) !== "undefined") {
                                var j = {};
                                for (var h in m) {
                                    if (m[h].USERID) {
                                        j[m[h].USERID] = m[h]
                                    }
                                }
                                c.hash[f].logDescriptors.users = j
                            } else {
                                c.hash[f].logDescriptors.users = null
                            }
                        }, getLogDescriptor: function (g) {
                            return c.hash[f].logDescriptors[g]
                        }}
                }, getAll: function () {
                    return c.hash
                }, isDefined: function (f) {
                    return(typeof (c.hash[f]) !== "undefined")
                }, newLogger: function () {
                    return c.setTicket()
                }}
        }]
});