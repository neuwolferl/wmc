var app = angular.module("SUPERFORM", ['ngMessages', 'COMMON'], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);


app.directive("formPropagate", ['Common', '$compile', '$timeout', '$interval', function (Common, $compile, $timeout, $interval) {
        var version = "0.1";
        return {
            restrict: "E",
            scope: {
                driver: "=",
                formstep: "="
            },
            compile: function (tElem, tAttrs) {
                var inner = tElem.html();
                tElem.empty();
                inner = $(inner);
                return {
                    pre: function preLink(scope, iElem, iAttrs, controller) {
                        scope.model = {};
                        scope.adesso = new Date();
                        scope.timezone = new Date().getTimezoneOffset();
                        scope.driver.model = scope.model;
//                        console.log(inner);
                        var hForm = inner.get(0);
                        if (!hForm || hForm.tagName !== "FORM") {
                            throw Error("formPropagate link fcn: missing form!");
                            return void(0);
                        }
                        var form = inner.eq(0);
                        if (!form) {
                            throw Error("formPropagate link fcn: missing form!");
                            return void(0);
                        }
                        if (!form.attr("name")) {
                            throw Error("formPropagate link fcn: missing form name!");
                            return void(0);
                        }
                        var mainFormName = form.attr("name");
//                        console.log(form);
                        form.hide();
                        scope.driver.visibleFieldset = "";
//                        console.log(scope.formstep);
                        scope.driver.buttons = {};
                        if (scope.formstep && angular.isArray(scope.formstep)) {
                            form.find("fieldset").each(function (n) {
                                var legend = $(this).find("legend").html();
                                scope.driver.buttons[legend] = {};
//                                console.log("fieldset " + legend);

                                var fsI = _.findIndex(scope.formstep, {legend: legend});
                                var fs = scope.formstep[fsI];
                                $(this).find(".superform-button").each(function (m) {

                                    var buttonLabel = $(this).text().trim();

//                                    console.log("button ", $(this), "label", "|" + buttonLabel + "|");

                                    if (!fs || !fs.buttons)
                                        return;

                                    scope.driver.buttons[legend][buttonLabel] = {enabled: true};
                                    for (var i in fs.buttons) {
                                        if (fs.buttons[i].label === buttonLabel) {
                                            if (fs.buttons[i].enableFcn) {
                                                switch (fs.buttons[i].enableFcn) {
                                                    case 'validateStep':
                                                        $(this).attr("ng-click", "driver.buttons['" + legend + "']['" + buttonLabel + "'].enabled && " + fs.buttons[i].action);
                                                        $(this).attr("ng-show", "driver.buttons['" + legend + "']['" + buttonLabel + "'].enabled && " + (fs.name ? fs.name : 'f__' + fsI) + ".$valid");
                                                }
                                            }
                                            else {
                                                $(this).attr("ng-click", "driver.buttons['" + legend + "']['" + buttonLabel + "'].enabled && " + fs.buttons[i].action);
                                                $(this).attr("ng-show", "driver.buttons['" + legend + "']['" + buttonLabel + "'].enabled");
                                            }
                                            break;
                                        }
                                    }

                                });
//                                $(this).attr("ng-show", "driver.visibleFieldset === '" + legend + "'");
                                var p = $(this).parent();
                                var subFormName = (fs.name ? fs.name : 'f__' + fsI);
                                $(this).replaceWith($compile($('<form ng-show="driver.visibleFieldset === \'' + legend + '\'" name="' + subFormName + '">' + this.innerHTML + '</form>'))(scope));
                                var subform = p.find('form[name="' + subFormName + '"]');
                                console.log(subform);
                                console.log(fs);
                                if (subform && fs.validationMess) {
                                    for (var kkk in fs.validationMess) {
                                        var vM = fs.validationMess[kkk];
                                        if (vM.type && vM.name) {
                                            var attachHere;

                                            switch (vM.type) {
                                                case 'radio':
                                                    var ctrl = subform.find('input[type="radio"][name="' + vM.name + '"]');
                                                    if (ctrl.length) {
                                                        attachHere = ctrl.last().parent(".form-group");
                                                    }
                                                    break;
                                                case 'textarea':
                                                    var ctrl = subform.find('textarea[name="' + vM.name + '"]');
                                                    if (ctrl.length) {
                                                        attachHere = ctrl.last().parent(".form-group");
                                                    }
                                                    break;
                                                case 'select':
                                                    var ctrl = subform.find('select[name="' + vM.name + '"]');
                                                    if (ctrl.length) {
                                                        attachHere = ctrl.last().parent(".form-group");
                                                    }
                                                    break;
                                                case 'date':
                                                    var ctrl = subform.find('input[type="date"][name="' + vM.name + '"]');
                                                    if (ctrl.length) {
                                                        attachHere = ctrl.last().parent(".form-group");
                                                    }
                                                    break;
                                                case 'time':
                                                    var ctrl = subform.find('input[type="time"][name="' + vM.name + '"]');
                                                    if (ctrl.length) {
                                                        attachHere = ctrl.last().parent(".form-group");
                                                    }
                                                    break;
                                            }
                                            var vmNodes = [];
                                            var errorCauses = [];
                                            for (var mI in vM) {
                                                if (vM.hasOwnProperty(mI) && mI !== "type" && mI !== "name") {
                                                    vmNodes.push('<div ng-message="' + mI + '">' + vM[mI] + '</div>');
                                                }
                                            }
                                            var vmNodes = '<div ng-messages="' + mainFormName + '.' + subFormName + '.' + vM["name"] + '.$error" style="color:maroon" role="alert">' +
                                                    vmNodes.join('') + '</div>';
                                            if (attachHere) {
                                                attachHere.attr("ng-class", '{\'has-error\':' + mainFormName + '.' + subFormName + '.' + vM["name"] + '.$invalid, \'has-success\':' + mainFormName + '.' + subFormName + '.' + vM["name"] + '.$valid}');
                                                
                                                attachHere.after($(vmNodes));
//                                                attachHere.replaceWith($compile(attachHere)(scope));
                                            }
                                        }

                                    }
                                }
                                subform.replaceWith($compile(subform)(scope));
                            });
                            if (!scope.driver.visibleFieldset || scope.driver.visibleFieldset === "") {
                                scope.driver.visibleFieldset = scope.formstep[0].legend;
                            }

                            scope.goToStep = function (x) {
                                if (x === -1) {
                                    scope.driver.visibleFieldset = "";
                                }
                                else if (!isNaN(x) && scope.formstep[parseInt(x)]) {
                                    scope.driver.visibleFieldset = scope.formstep[parseInt(x)].legend;
                                }
                                else if (angular.isString(x) && _.findIndex(scope.formstep, {legend: x}) > -1) {
                                    scope.driver.visibleFieldset = x;
                                }
                            };
                            scope.driver.goToStep = scope.goToStep;
                            scope.setInModel = function (key, value) {
                                scope.model[key] = value;
                                return true;
                            }
                            scope.driver.setInModel = scope.setInModel;
                            scope.setInDriver = function (key, value) {
                                scope.driver[key] = value;
                                return true;
                            }
                            scope.driver.setInDriver = scope.setInDriver;
                        }

                        iElem.append($compile(inner)(scope));


                        scope.do = function () {
                            if (!arguments || !arguments.length) {
                                return void(0);
                            }
                            var args = [];
                            for (var i in arguments) {
                                args.push(arguments[i]);
                            }

                            var first = args.shift();
                            first = Common.findInParentScope(scope, first, "function");

                            if (first) {
                                var fcn = first[1];
                                var out = fcn.apply(this, args);
                                return out;
                            }
                            return;
                        };
                        var formname = form.attr("name");
                        if (!formname) {
                            throw Error("formPropagate link fcn: missing form name!");
                            return void(0);
                        }
                        $timeout(function () {
                            scope.$apply(
                                    function () {
                                        if (scope[formname]) {
                                            scope.driver.formStatus = scope[formname];
                                            form.show();
                                        }
                                    }
                            );
                            scope.$watch("model", function () {
//                                scope.driver.formStatus = scope[formname];
                            }, true);
                        }, 1500);
                    }
                }
            }
        }

    }]);