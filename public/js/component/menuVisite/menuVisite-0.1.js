var app = angular.module("MenuVisite", ['DATAMODULE', 'COMMON', 'ngAnimate', 'ngTouch'], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);

app.directive("menuVisite", ['Common', '$compile', '$timeout', function (Common, $compile, $timeout) {
        var registerCol = function (colTS, displayDate, registry) {
            if (!registry.cols) {
                registry.cols = {};
            }
            var search = _.find(registry.cols, {tS: colTS});
            if (!search) {
                var flag = true;
                while (flag) {
                    var id = Common.randomString(10);
                    if (!registry.cols[id]) {
                        registry.cols[id] = {tS: colTS, count: 0, displayDate: displayDate};
                        return id;
                        flag = false;
                    }
                    else {
                        continue;
                    }
                }
            }
            else {
                console.error("Fatal error - menuVisite: Duplicate date w.r.t. given timeStep");
                throw Error("Fatal error - menuVisite: Duplicate date w.r.t. given timeStep");
            }
        }
        var registerApp = function (app, timeStep, ider, registry) {
            if (!registry || !angular.isObject(registry)) {
                console.error("Fatal error - menuVisite: Invalid Registry");
                return false;
            }
            if (!app || !angular.isObject(app)) {
                console.error("Fatal error - menuVisite: Invalid App", app);
                return false;
            }
            if (!timeStep) {
                console.error("Fatal error - menuVisite: Invalid App - missing or invalid date", timeStep, app);
                return false;
            }
            if (!registry.app) {
                registry.app = {};
            }
            if (!registry.app[timeStep]) {
                registry.app[timeStep] = [];
            }
            if (!registry.appHash) {
                registry.appHash = [];
            }
            if (app[ider] && registry.appHash[app[ider]]) {
                console.error("WARNING registerApp fcn: duplicate id " + id + ". Assigning new one.");
                id = void(0);
            }
            if (!app[ider]) {
                while (true) {
                    app[ider] = Common.randomString(10);
                    if (!registry.appHash[app[ider]]) {
                        break;
                    }
                    else {
                        continue;
                    }
                }
            }
            registry.app[timeStep].push(app);
            registry.appHash[app[ider]] = app;
            return [app[ider], timeStep];
        };
        var innerHtml = function () {
            var html = '<div class="row" id="nav"><nav class="col-md-12 hidden-xs"><ul class="pager">';
            html += '<li class="previous"><a><span aria-hidden="true">&larr;</span> Precedente</a></li>';
            html += '<li class="center-block"><a ng-click="menuStatus.showingSearch = !menuStatus.showingSearch">';
            html += '<span ng-show="!menuStatus.showingSearch">Cerca</span><span ng-show="menuStatus.showingSearch">Chiudi cerca</span></a></li>';
            html += '<li class="next"><a>Successivo <span aria-hidden="true">&rarr;</span></a></li></ul></nav></div>';
            html += '<div class="row" id="test">';
            html += '</div>';
            html += '<div class="row" id="pippo" ng-swipe-left="gotoNextDate()" ng-swipe-right="gotoPreviousDate()">';
            html += '</div>';
            return html;
        };
        var composeColumn = function (tS, innerTemplate, ider, id) {
            var html = '<giorno-visite id="' + id + '" tS="' + tS + '" ider="' + ider + '" apps="registry.app[\'' + tS + '\']"';
            html += ' ng-show="registry.cols[\'' + id + '\'].visible"';
            html += ' ng-class="{\'col-md-3\': menuStatus.showingSearch,\'col-md-4\': !menuStatus.showingSearch}">';
            html += innerTemplate;
            html += '</giorno-visite>';
            return {tS: tS, html: html};
        };
        return {
            restrict: "E",
            scope: {
                conf: "="
            },
            compile: function (tElem, tAttrs) {
//                console.log("compile 1");
                var today = new Date();
                today.setHours(0);
                today.setMinutes(0);
                today.setSeconds(0);
                var innerTemplate = tElem.html();
                tElem.empty();
//                innerTemplate = innerTemplate.replace(/&apos;/g, "'");
//                console.log(innerTemplate);

                return {
                    pre: function preLink(scope, iElem, iAttrs, controller) {

                        if (!scope.Common)
                            scope.Common = Common;
                        if (!scope.conf) {
                            scope.conf = {};
                        }
                        if (!scope.conf.innerTemplate) {
                            scope.conf.innerTemplate = innerTemplate;
                        }
                        scope.getAppDate = function (app) {
                            return Common.extractFromObject(app, scope.conf.dater);
                        };
                        scope.getAppDisplayDate = function (app) {
                            return Common.extractFromObject(app, scope.conf.displayDate);
                        };
                        scope.getAppTimeStep = function (app) {
                            var time = Common.extractFromObject(app, scope.conf.dater);
                            if (!Common.isValidDate(time)) {
                                time = new Date(time);
                            }
                            if (!Common.isValidDate(time)) {
                                console.error(app);
                                console.error(time);
                                throw Error("getAppTimeStep fcn --- specified date is not valid");
                            }
                            return parseInt(time.getTime() / scope.conf.timeStep);
                        };
                        scope.gotoPreviousDate = function () {
                            var dist = [];
                            var cols = scope.registry.cols;
                            for (var id in cols) {
                                if (scope.menuStatus.currentDate > cols[id].tS) {
                                    dist.push({
                                        id: id,
                                        tS: cols[id].tS,
                                        diff: Math.abs(scope.menuStatus.currentDate - cols[id].tS),
                                        date: cols[id].displayDate
                                    });
                                }
                            }
                            if (!dist.length)
                                return;
                            dist = _.sortBy(dist, "diff");
                            scope.menuStatus.currentDate = dist[0].tS;
                        };
                        scope.gotoNextDate = function () {
                            var dist = [];
                            var cols = scope.registry.cols;
                            for (var id in cols) {
                                if (scope.menuStatus.currentDate < cols[id].tS) {
                                    dist.push({
                                        id: id,
                                        tS: cols[id].tS,
                                        diff: Math.abs(scope.menuStatus.currentDate - cols[id].tS),
                                        date: cols[id].displayDate
                                    });
                                }
                            }
                            if (!dist.length)
                                return;
                            dist = _.sortBy(dist, "diff");
                            scope.menuStatus.currentDate = dist[0].tS;
                        };

                        Common.applyDefaultObject({loaded: false, appList: "apps", dater: "date", ider: "id", timeStep: 24 * 60 * 60 * 1000, displayDate: "date",
                            searchFcn: Common.rawSearchInObject, searchSugg: 'cerca', propagated: {}, filters: [], actions: {},
                            update: function () {
                                scope.updateVisible(scope.menuStatus.currentDate, scope.menuStatus.globalSearch);
                            },
                            init: function () {
                                scope.conf.loaded = false;
//                                console.log("recupero apps");
                                iElem.empty();
                                delete(scope.apps);
                                var apps = Common.findInParentScope(scope, scope.conf.appList, "object");
                                if (apps) {
                                    scope.apps = apps[1];
                                }
                                else {
                                    console.error("Can't find apps");
                                    return;
                                }
//                                console.log(scope.apps);
//                                console.log(scope);
//                                console.log(apps[0]);

                                iElem.html($compile(innerHtml())(scope));
                                scope.loadData();
                                scope.manageCols();
//                                console.log("init done");
//                                console.log(iElem.get(0));
//                                console.log(iElem.parent());
                                scope.conf.loaded = true;
                            }
                        }, scope.conf);
                        for (var i in scope.conf.filters) {
                            var f = scope.conf.filters[i];
                            if (angular.isString(f)) {
                                var ff = Common.findInParentScope(scope, f, "function");
                                if (ff) {
                                    scope.conf.filters[i] = ff[1];
                                }
                            }
                            else if (angular.isFunction(f)) {
                                //ok
                            }
                            else {
                                scope.conf.filters.splice(i, 1);
                            }
                        }
                        var currentDate = new Date();
                        currentDate = parseInt(currentDate.getTime() / scope.conf.timeStep);
                        scope.menuStatus = {
                            showingDetails: null,
                            currentDate: currentDate,
                            showingSearch: false
                        };
                        scope.updateVisible = function (currentDate, searchString) {
//                            console.log("update visible");
                            var dist = [];
                            if (!scope.registry || !scope.registry.cols || !scope.registry.app)
                                return;
                            var cols = scope.registry.cols;
                            var apps = scope.registry.app;
                            for (var date in apps) {
//                                console.log(date, apps[date].length + " apps");
                                for (var i in apps[date]) {
//                                    console.log(apps[date][i]);
                                    if (!searchString || searchString === "") {
                                        apps[date][i].visible = true;
                                    }
                                    else if (scope.conf.searchFcn(apps[date][i], searchString)) {
                                        apps[date][i].visible = true;
                                    }
                                    else {
                                        apps[date][i].visible = false;
                                    }

                                    for (var fI in scope.conf.filters) {
                                        apps[date][i].visible = apps[date][i].visible && angular.isFunction(scope.conf.filters[fI]) && scope.conf.filters[fI](apps[date][i], scope);
                                    }
//                                    console.log("visible: ", apps[date][i].visible);
                                }
                            }
                            for (var id in cols) {
                                var count = _.countBy(apps[cols[id].tS], 'visible');
                                if (count[true])
                                    dist.push({
                                        id: id,
                                        tS: cols[id].tS,
                                        diff: Math.abs(currentDate - cols[id].tS),
                                        date: cols[id].displayDate
                                    });
                            }
                            dist = _.sortBy(dist, "diff");
                            var visible = [];
                            var i = 0;
                            while (dist.length && i++ < 3) {
                                var col = dist.shift();
                                visible.push(col.id);
                            }
                            for (var id in cols) {
                                if (visible.indexOf(id) > -1) {
                                    cols[id].visible = true;
                                }
                                else {
                                    cols[id].visible = false;
                                }
                            }
                        }
                        scope.$watch("menuStatus.currentDate", function (n, o) {
                            scope.updateVisible(n, scope.menuStatus.globalSearch);
                        });
                        scope.$watch("menuStatus.globalSearch", function (n, o) {
                            $timeout(function () {
                                if (n === scope.menuStatus.globalSearch)
                                    scope.updateVisible(scope.menuStatus.currentDate, scope.menuStatus.globalSearch);
                            }, 1000);


                        });

                        scope.loadData = function () {
//                            console.log("loadData");
//                            console.log(scope.apps);
                            scope.registry = {};
                            scope.registry.dates = [];
                            for (var i in scope.apps) {
                                var app = scope.apps[i];
                                var tS = scope.getAppTimeStep(app);
                                var registered = registerApp(app, tS, scope.conf.ider, scope.registry);
                                if (_.findIndex(scope.registry.dates, {tS: tS}) === -1) {
                                    scope.registry.dates.push({tS: tS, display: scope.getAppDisplayDate(app)});
                                }
                            }
                            _.sortBy(scope.registry.dates, "tS");

//                            console.log("end loadData", scope.registry);
                        };
                        scope.manageCols = function () {
//                            console.log("manageCols");
                            iElem.html($compile(innerHtml())(scope));
                            var colsToAdd = [];
                            for (var i in scope.registry.dates) {
                                var tS = scope.registry.dates[i].tS;
                                var displayDate = scope.registry.dates[i].display;
                                if (scope.registry.app.hasOwnProperty(tS)) {
                                    var col = composeColumn(tS, innerTemplate, scope.conf.ider, registerCol(tS, displayDate, scope.registry));
                                    colsToAdd.push(col);
                                }
                            }
                            colsToAdd = _.sortBy(colsToAdd, "tS");
//                            console.log("aggiungo colonne");
                            for (var i in colsToAdd) {
//                                console.log(i);
                                iElem.find("#pippo").append($compile(colsToAdd[i].html)(scope));
                            }
//                            var colonnaNulla = '<div class="col-md-12" ng-show=><h3>Non ci sono dati da visualizzare.</h3></div>';
//                            console.log("navigatore");

                            iElem.find("li.previous").empty()
                                    .append($compile('<a ng-click="gotoPreviousDate()" style="cursor:pointer;"><span aria-hidden="true">&larr;</span> Precedente</a>')(scope));
                            iElem.find("li.next").empty()
                                    .append($compile('<a ng-click="gotoNextDate()" style="cursor:pointer;">Successivo <span aria-hidden="true">&rarr;</span></a>')(scope));
//                            console.log(iElem.find("li.previous"));
//                            console.log(iElem.find("li.next"));
//                            console.log("aggiungo navcol");
                            var navCol = '<div id="navcol" class="col-md-3" ng-show="menuStatus.showingSearch">';
                            navCol += '<form id="menuVisiteSearch">';

                            navCol += '<fieldset><legend>Cerca/Filtra</legend>';
                            navCol += '<div class="form-group">';
                            navCol += '<label for="globalSearch">Cerca: </label>';
                            navCol += '<input type="search" class="form-control" id="menuVisiteGlobalSearch" ng-model="menuStatus.globalSearch" name="globalSearch" ';
                            navCol += 'placeholder="ricerca veloce" style="width: 100%" ng-focus="globalSearchHints = true" ng-blur="globalSearchHints = false">';
                            navCol += '<span class="help-block" ng-show="globalSearchHints">' + scope.conf.searchSugg + '</span>';
//                        navCol += '<pre>--__menuStatus.globalSearch__--</pre>';
//                        navCol += '<pre>--__registry|json__--</pre>';
                            navCol += '</div>';
                            navCol += '<fieldset>';
                            navCol += '<form>';
                            navCol += '</div>';
                            iElem.find("#pippo").prepend($compile(navCol)(scope));
//                            console.log("end manageCols");
//                            iElem.find("#test").append($compile('<pre>--__menuStatus|json__--</pre>')(scope));
                            var currentDate = new Date();
                            currentDate = parseInt(currentDate.getTime() / scope.conf.timeStep);
                            scope.menuStatus = {
                                showingDetails: null,
                                currentDate: currentDate,
                                showingSearch: false
                            };
                            scope.updateVisible(currentDate, "");
                            scope.toggleShowingSearch = function () {
                                scope.menuStatus.showingSearch = !scope.menuStatus.showingSearch;
                                if (scope.menuStatus.showingSearch) {
                                    $timeout(function () {
                                        $('html,body').animate({
                                            scrollTop: iElem.offset().top},
                                        'slow');
                                    }, 500);
                                }
//                                    $("#menuVisiteSearch").get(0).scrollIntoView({block: "end", behavior: "smooth"});
//                                    $("#menuVisiteSearch").get(0).scrollTop = $("#menuVisiteSearch").get(0).scrollTop + 60;
                            }
                            scope.conf.toggleShowingSearch = scope.toggleShowingSearch;
                        }

                    },
//                    post: function postLink(scope, iElem, iAttrs, controller) {
//                        var currentDate = new Date();
//                        currentDate = parseInt(currentDate.getTime() / scope.conf.timeStep);
//                        scope.menuStatus = {
//                            showingDetails: null,
//                            currentDate: currentDate,
//                            showingSearch: false
//                        };
//                        var cols = scope.registry.cols;
//                        for (var id in cols) {
//                            if (scope.menuStatus.currentDate === cols[id].tS) {
//                                console.log(cols[id]);
//                            }
//                        }
//                        scope.updateVisible(currentDate, "");
//
//
//                    }
                }
            }
        }
    }]);
app.directive("giornoVisite", ['Common', '$compile', function (Common, $compile) {

        return {
            restrict: "E",
            scope: {
            },
            compile: function (tElem, tAttrs) {
//                tElem.addClass("col-md-3");
                var now = new Date();
//                var date = new Date(tAttrs.data);
//                var d = Common.verbItaDateNoYear(date, true);
                var innerTemplate = tElem.html();
//                var html = '<h3 class="page-header">' + d[1] + ' <small>' + d[0] + '</small>&nbsp;<span class="countOnDay badge" style="display:none"></span></h3>';
                var html = '<h3 class="page-header">&nbsp;&nbsp;--__date.displayDate[1]__-- <small>--__date.displayDate[0]__--&nbsp;<span class="countOnDay text-info" style="display:none"></span></small></h3>';
                tElem.empty().html(html);
                return {
                    pre: function preLink(scope, iElem, iAttrs, controller) {

                        scope.date = {
                            tS: iAttrs.tS || iAttrs.ts,
                            displayDate: []
                        }
//                        console.log("renderizzo: ", scope.date);
                        scope.id = iElem.attr("id");
                        var registry = Common.findInParentScope(scope, "registry", "object");
                        if (registry && registry[1].cols && registry[1].cols[scope.id]) {
                            registry = registry[1];
                        }
                        else {
                            console.error(registry);
                            throw Error("Fatal Error: giornoVisite PreLink fcn: can't find registry ");
                            return void(0);
                        }
                        scope.apps = registry.app[scope.date.tS];
//                        console.log("app: ", scope.apps);
                        scope.superConf = Common.findInParentScope(scope, "conf", "object");
                        if (scope.superConf) {
                            scope.superConf = scope.superConf[1];
                        }
                        else {
                            console.error(scope.superConf);
                            throw new Error("Fatal Error: giornoVisite PreLink fcn: can't find super conf");
                            return void(0);
                        }
                        scope.conf = {
                            innerTemplate: scope.superConf.innerTemplate,
                            ider: scope.superConf.ider,
                            dater: scope.superConf.dater,
                            propagated: scope.superConf.propagated,
                            actions: scope.superConf.actions
                        }
                        registry.cols[scope.id].count = scope.apps.length;


                        var date = _.find(registry.dates, function (x) {
                            return (typeof x.tS !== "undefined" && x.tS == scope.date.tS);
                        });
                        if (date) {
                            scope.date.displayDate = date.display;
                        }
                        else {
                            console.error(registry);
                            throw Error("Fatal Error: giornoVisite PreLink fcn: can't find date " + scope.date.tS + " in registry - removing col");
                            iElem.empty();
                            return void(0);
                        }
                        iElem.find(".countOnDay").replaceWith('<span class="countOnDay text-info">(--__apps.length__--)</span>');
                        iElem.html($compile(iElem.html())(scope));
                        scope.menuStatus = Common.findInParentScope(scope, "menuStatus", "object");

                    },
                    post: function postLink(scope, iElem, iAttrs, controller) {
                        var registry = Common.findInParentScope(scope, "registry", "object");
                        if (registry && registry[1].cols && registry[1].cols[scope.id]) {
                            registry[1].cols[scope.id].count = scope.apps.length;
                        }
                        scope.$watch("apps.length", function () {
                            var registry = Common.findInParentScope(scope, "registry", "object");
                            if (registry && registry[1].cols && registry[1].cols[scope.id]) {
                                registry[1].cols[scope.id].count = scope.apps.length;
                            }
                            scope.apps.sort(function (a, b) {
                                var d1 = Common.extractFromObject(a, scope.superConf.dater);

                                var d2 = Common.extractFromObject(b, scope.superConf.dater);
                                if (!Common.isValidDate(d1)) {
                                    d1 = new Date(d1);
                                }
                                if (!Common.isValidDate(d2)) {
                                    d2 = new Date(d2);
                                }
                                return (d1 > d2);
                            })
                        });

                        iElem.append($compile('<appuntamento ng-repeat="app in apps|visibleApps" app="app"></appuntamento> ')(scope));
//                        iElem.append($compile('<pre>--__apps|json__--</pre>')(scope));
                    }
                }
            }
        }
    }]);
app.filter("visibleApps", function () {
    return function (input) {
        var out = _.filter(input, function (x) {
            return x.visible;
        });
        return out;
    }
})
app.directive("appuntamento", ['$interval', '$compile', 'Common', function ($interval, $compile, Common) {
        var expCalcLocal = function (exp) {
            var now = new Date();

            var os = (now.getTimezoneOffset() / (-60));
            os = ((os > 0) ? ("+") : ("")) + os;
            if (exp && Common.isValidDate(exp)) {
                var localExp;
                localExp = [parseInt((exp.getTime() - now.getTime()) / (1000 * 60)), "minuti"];
                if (localExp[0] && localExp[0] > 120 && localExp[0] < 2880) {
                    localExp = [(localExp[0] / 60).toPrecision(2), "ore"];
                }
                if (localExp[0] && localExp[0] > 2880) {
                    localExp = [(localExp[0] / 1440).toPrecision(2), "giorni"];
                }
            }
            return localExp;
        }

        return {
            restrict: "E",
            scope: {
                app: "="
            },
            link: function (scope, iElem, iAttrs) {
//                console.log("renderizzo app: ", scope.app);
                scope.do = function (act) {
                    if (!act || !act.length) {
                        return void(0);
                    }
                    if (scope.conf.actions && scope.conf.actions[act]) {
                        console.log(scope.conf.actions[act]);
                        var fcn = Common.findInParentScope(scope, scope.conf.actions[act].func, "function");
                        if (fcn) {
                            fcn = fcn[1];
                        }
                        else {
                            console.error("Trying to call undefined function ", scope.conf.actions[act].func);
                            return;
                        }
                        var args = [];
                        if (scope.conf.actions[act].args) {
                            for (var a in scope.conf.actions[act].args) {
                                var arg = Common.findInParentScope(scope, scope.conf.actions[act].args[a], "all");
                                if (arg) {
                                    args.push(arg[1]);
                                }
                            }
                            if (args.length) {
                                return fcn.apply(this, args);
                            }
                            else {
                                return fcn();
                            }
                        }
                    }
                    else {
                        console.error("Trying to call undefined action ", act);
                        return;
                    }
                    return;

                };
                scope.Common = Common;
                scope.menuStatus = Common.findInParentScope(scope, "menuStatus", "object");
                if (scope.menuStatus) {
                    scope.menuStatus = scope.menuStatus[1];
                }
                scope.conf = Common.findInParentScope(scope, "conf", "object");
                if (scope.conf) {
                    scope.conf = scope.conf[1];
                }
                else {
                    console.error(scope.conf);
                    throw Error("Fatal error Appuntamento LINK fcn: can't find conf");
                    return void(0);
                }
//                console.log("Appuntamento LINK",scope,scope.conf);
                iElem.attr("id", scope.app[scope.conf.ider]);
                var panelAuxStyle = Common.findInParentScope(scope, "panelAuxStyle", "function");
                if (panelAuxStyle) {
                    scope.panelAuxStyle = function () {
                        return panelAuxStyle[1](scope.app, scope);
                    }
                }
                else {
                    scope.panelAuxStyle = function () {
                        return {};
                    };
                }
                scope.toggleShowDetails = function () {
                    scope.menuStatus.showingDetails = (scope.menuStatus.showingDetails === scope.app[scope.conf.ider] ? null : scope.app[scope.conf.ider]);
                }
                var i = 0;
                var now = new Date();
                var progress = Common.findInParentScope(scope, "progress", "function");
                if (progress) {
                    scope.progress = function (x) {
                        return progress[1](x, scope);
                    }
                }
                else {
                    scope.progress = function () {
                        return void(0);
                    }
                }
                var expSet = Common.findInParentScope(scope, "expSet", "function");
                if (expSet) {
                    scope.expSet = function (x) {
                        return expSet[1](x, scope);
                    }
                }
                else {
                    scope.expSet = function () {
                        scope.app.exp = new Date();
                        scope.app.exp.setTime(scope.app[scope.conf.dater].getTime ? scope.app[scope.conf.dater].getTime() : now.getTime());
                    };
                }
                var timeStatus = Common.findInParentScope(scope, "timeStatus", "function");
                if (timeStatus) {
                    scope.timeStatus = function (x) {
                        return timeStatus[1](x, scope);
                    }
                }
                else {
                    scope.timeStatus = function () {
                        return null;
                    };
                }
                scope.panelClass = function (a) {
                    return "panel-" + a;
                }
                scope.progressClass = function (a) {
                    return "progress-bar-" + a;
                }
                scope.progressValue = function (a) {
                    return {width: a + "%"};
                }
                scope.labelClass = function (a) {
                    return "label-" + a;
                }
                var appDate = Common.extractFromObject(scope.app, scope.conf.dater);
                if (!Common.isValidDate(appDate)) {
                    appDate = new Date(appDate);
                }
                if (!Common.isValidDate(appDate)) {
                    console.error(scope.app);
                    throw Error("Appuntamento directive - Invalid date in app");
                }
                scope.expSet(scope.app, scope);
                scope.exp = expCalcLocal(scope.localExp);
                scope.timeStatus(scope.app, scope);
                scope.prog = scope.progress(scope.app, scope);
                scope.expSet(scope.app, scope);
                scope.exp = expCalcLocal(scope.localExp);
                scope.timeStatus(scope.app, scope);
                scope.prog = scope.progress(scope.app, scope);
                $interval(function () {
                    scope.expSet(scope.app, scope);
                    scope.exp = expCalcLocal(scope.localExp);
                    scope.timeStatus(scope.app, scope);
                    scope.prog = scope.progress(scope.app, scope);
                }, 10000);


                iElem.append($compile(scope.conf.innerTemplate)(scope));
                //creazione azioni
                var buttons = [];
                if (scope.conf.actions && angular.isObject(scope.conf.actions)) {
                    for (var i in scope.conf.actions) {
                        if (scope.conf.actions.hasOwnProperty(i)) {
                            var action = scope.conf.actions[i];
                            if (action.viewIf) { // controllo possibilità di viusalizzare il controllo
                                if ((angular.isFunction(action.viewIf) && !action.viewIf(scope)) || !action.viewIf)
                                    continue;
                            }

                            var buttonHTML = '<a class="btn btn-default"';
                            if (action.enableIf && ((angular.isFunction(action.enableIf) && !action.enableIf(scope)) || !action.enableIf)) { // controllo richiesta di disabilitare il controllo
                                buttonHTML += ' disabled';
                            }
                            else {

                                buttonHTML += (action.func ? ' ng-click="do(\'' + i + '\')"' : '');
                            }
                            buttonHTML += ' role="button">';
                            buttonHTML += (action.userLabel ? action.userLabel : i) + '</a>';
                        }
                        buttons.push(buttonHTML);
                    }
                    var actionsHTML = "";
                    for (var i = 0; i < buttons.length; ) {
                        if (buttons[i]) {
                            actionsHTML += '<p class="btn-group btn-group-justified " role="group" aria-label="...">';
                            actionsHTML += buttons[i];
                        }
                        if (buttons[i + 1]) {
                            actionsHTML += buttons[i + 1];
                        }
                        actionsHTML += '</p>';
                        i += 2;
                    }
                    iElem.find("actions").replaceWith($compile(actionsHTML)(scope));
                }
            }


        }

    }]);