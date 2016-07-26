(function () {
    var app = angular.module("Setup", ['DATAMODULE', 'COMMON', 'restangular', 'TREEMENULIB', 'MULTISELECT', 'ngDragDrop'], ['$interpolateProvider',
        function ($interpolateProvider) {
            $interpolateProvider.startSymbol("--__");
            $interpolateProvider.endSymbol("__--");
        }]);
    app.config(['RestangularProvider', function (RestangularProvider) {
            RestangularProvider.setBaseUrl('https://ec2-54-149-19-183.us-west-2.compute.amazonaws.com/tsnwtest/public/publicapi/json');
            RestangularProvider.setParentless(true);
            RestangularProvider.setRequestSuffix(".ws");
            RestangularProvider.setRestangularFields({
                id: 'id_cliente.$oid'
            });
            RestangularProvider.setRequestInterceptor(function (elem, operation, what) {

                if (operation === 'put') {
                    elem._id = undefined;
                    return elem;
                }
                return elem;
            })
        }]);
    app.controller("SetupController", ['$q', 'fieldNodeCTN', 'DataService', '$scope', '$rootScope', '$timeout', '$interval', 'Common', 'Restangular', 'treeMenuUtilities',
        function ($q, fieldNodeCTN, DataService, $scope, $rootScope, $timeout, $interval, Common, Restangular, treeMenuUtilities) {
            var that = this;
            $scope.load = {
                services: false,
                activerecord: false,
                preLoadMainTree: false,
                mainTree: false,
                resourceSelected: false,
                resourceForm: false,
                organizedFields: false,
            };
            $scope.flow = null;
            $scope.modelEdit = {};
            $scope.Common = Common;
            this.treeMenuUtilities = treeMenuUtilities;
            this.destroyPage = function () {
                this.errorMask = true;
                $("error-mask").siblings().each(function () {
                    $(this).hide();
                });
            };
            /*
             * STATIC
             */
            $scope.opPar = {};
            $scope.modelTypes = ["activerecord", "VTWS"];
            $scope.models = {};
            $scope.services = [];
            //menu ad albero
            this.preloadMainTree = function () {
                var deferred = $q.defer();
                $timeout(function () {
                    $scope.albero = [
                        {
                            label: 'topsource',
                            path: '/var/www/tsnwtest/application/services/',
                            children: []
                        }
                    ];
                    $scope.mainTreeIndex = treeMenuUtilities.newTree($scope.albero);
                    $scope.mainTree = treeMenuUtilities.getTree($scope.mainTreeIndex);
                    $scope.treeNav = $scope.mainTree.nav;
                    $scope.albero = $scope.mainTree.tree;
                    if (angular.isObject($scope.treeNav)) {
                        $scope.load.preLoadMainTree = true;
                        deferred.resolve(true);
                    }
                    else {
                        that.preloadMainTree();
                    }


                }, 500);
                deferred.promise.then(function () {
                    $scope.flow = that.loadMainTree()
                });
                return deferred.promise;
            }
            this.loadMainTree = function () {
                if (!that.canLoadMainTree())
                    return false;
                $scope.remoteServices = angular.copy($scope.services);
                var deferred = $q.defer();
                $timeout(function () {
                    treeMenuUtilities.inflateMenuData($scope.services, $scope.treeNav, {
                        path: "path",
                        id: "id",
                        data: "data",
                        pathSep: "/"
                    },
                    [{label: "topsource", path: "/var/www/tsnwtest/application/services/"}]);
                    $scope.load.mainTree = true;
                    if (angular.isFunction($scope.treeNav.expandAll)) {
                        deferred.resolve(true);
                    }

                }, 500);

                deferred.promise.then(function () {
                    var accountNode = $scope.treeNav.searchNodeByLabel("Account");
                    $scope.treeNav.selectNode(null);
                    $scope.treeNav.expandAll();
                    $scope.treeNav.selectNode(accountNode);
                    $scope.load.resourceSelected = true;
                }
                );
                return deferred.promise;
            };

            this.resourceStructure = function () {

                that.organizeFields();
            }

            this.resourceForm = function () {
                $scope.load.organizedFields = false;
                $scope.load.resourceForm = false;
                if ($scope.resourceSubTreeIndex)
                    treeMenuUtilities.deleteTree($scope.resourceSubTreeIndex);
            };

            $scope.$watch("mainTree.connector.data.static_props._mainmodel", function (newValue, oldValue) {
                if (!newValue || !oldValue || !$scope.load.organizedFields) {
                    return;
                }
                $scope.load.organizedFields = false;
                if ($scope.resourceSubTreeIndex)
                    treeMenuUtilities.deleteTree($scope.resourceSubTreeIndex);
                $timeout(function () {
                    that.organizeFields();
                }, 500);
            }, true);

            this.checkModelsSelectionUpdate = function (n, o) {
                var deferred = $q.defer();

                $timeout(function () {
                    //controllo che il dato sia valido e aggiornato, altrimenti butto via
                    if (!n || !o || (!n.length && !o.length)) {
                        deferred.reject(null);
                    }
                    if (n.length !== $scope.mainTree.connector.data.modelsSelection.length) {
                        deferred.reject(null);
                    }
                    var flag = false;
                    if (n.length && n.length !== o.length) {
                        flag = true;
                    }
                    else {
                        for (var i in n) {
                            if (n[i].selected !== o[i].selected) {
                                flag = true;
                                break;
                            }
                        }
                    }
                    if (flag) {
                        deferred.resolve(true);
                    }
                }, 500);
                return deferred.promise;
            }

            this.organizeFields = function () {
                $scope.load.organizedFields = false;
                $scope.load.resourceSelected = true;

                var modelType = $scope.mainTree.connector.data.static_props._modeltype;
                if ($scope.modelTypes.indexOf(modelType) === -1)
                    return;
                $scope.modelEdit.modelType = modelType;
//                var invMods = $scope.mainTree.connector.data.static_props._modelsinvolved;
                var invMods = $scope.mainTree.connector.data.static_props._modelsinvolved;
                if (!invMods || !invMods.length)
                    return;
                $scope.modelEdit.invMods = invMods;

                var mainMod = $scope.mainTree.connector.data.static_props._mainmodel;
                console.log("---", invMods, mainMod);
                if (invMods.indexOf(mainMod) === -1) {
                    mainMod = null;
                }
                $scope.modelEdit.mainMod = mainMod;
                $scope.modelEdit.modelDets = {};
                for (var i in invMods) {
                    if (invMods[i] !== mainMod) {
                        $scope.modelEdit.modelDets[invMods[i]] = _.find($scope.models[modelType], {class: invMods[i]});
                    }
                }
                if (mainMod) {
                    var mainModelDets = _.find($scope.models[modelType], {class: mainMod});

                    $scope.modelEdit.modelDets[mainMod] = mainModelDets;
                    if (!mainModelDets || !angular.isObject(mainModelDets)) {
                        alert("C'è un problema nella definizione del modello " + mainMod + ". Controllare.");
                        return;
                    }

                    //cerco id che è scrittto in 2 modi:
                    var mainId1 = mainModelDets.primary_key;

                    if (angular.isArray(mainId1)) {
                        if (mainId1[0] && mainId1[0] !== "") {
                            mainId1 = mainId1[0];
                        }
                        else if (!mainId1[1] || mainId1[1] === "") {
                            alert("C'è un problema nella definizione del modello " + mainMod + ". Non è definita una chiave primaria. Controllare.");
                            return;
                        }
                        else {
                            mainId1 = mainId1[1];
                        }
                    }

                    var mainId2 = null;
                    var mainIdAlias2 = null;
                    console.log("analisi mainModel", mainModelDets);
                    for (var kk in mainModelDets.fields) {
                        if (mainModelDets.fields[kk].pk) {
                            mainId2 = mainModelDets.fields[kk].fieldname;
                            for (var s in mainModelDets.alias_attribute) {
                                if (mainModelDets.alias_attribute.hasOwnProperty(s) && mainModelDets.alias_attribute[s] === mainId2) {
                                    mainIdAlias2 = s;
                                    break;
                                }
                            }
                            break;
                        }
                    }

                    if (!mainId2) {
                        alert("C'è un problema nella definizione del modello " + mainMod + ". Non ci sono campi chiave primaria. Controllare.");
                    }
                    if (mainId1 !== mainId2) {
                        alert("C'è un problema nella definizione del modello " + mainMod + ". Sono definite due chiavi primarie diverse " + mainId1 + " e " + mainId2 + ".");
                        var mainId = null
                        $scope.modelEdit.mainId = null;
                    }
                    else {
                        var mainId = mainId1;
                        $scope.modelEdit.mainId = mainId;
                        $scope.modelEdit.mainIdAlias = mainIdAlias2;
                    }
                }
                else {
                    var mainId = null;
                }

                $scope.load.resourceForm = true;
                var vm = $scope.mainTree.connector.data.static_props._vm;
                $timeout(function () {
                    that.makeResourceSubTree(vm)
                            .then(function (e) {
                                //analisi fields
                            }, function (e) {
                            }, function (e) {
                            });
                }, 500);
            };

            this.makeResourceSubTree = function (vm) {
                var albero = that._vmToVm(vm);

                $scope.resourceSubTreeIndex = treeMenuUtilities.newTree(albero);
                $scope.resourceSubTree = treeMenuUtilities.getTree($scope.resourceSubTreeIndex);
                $scope.load.organizedFields = true;
                var deferred = $q.defer();
                var check = function (i) {
                    setTimeout(function () {
                        if ($scope.resourceSubTree.nav.selectFirstNode) {
                            deferred.resolve(i);
                        } else {
                            if (i < 10) {
                                deferred.notify("creazione subtree " + $scope.resourceSubTreeIndex + " " + i + " fail" + " " + JSON.stringify($scope.resourceSubTree.nav));
                                check(i + 1);
                            }
                            else {
                                deferred.reject(i);
                            }
                        }
                    }, 500);
                }
                check(0);
                return deferred.promise;
            }

            this.fieldToModel = function (content) {
                var mod;
                var modSel;
                if (!content.from || content.from === "main") { //dovrei essere nel modello principale
                    mod = $scope.modelEdit.mainMod;
                    var modSel = _.find($scope.mainTree.connector.data.modelsSelection, {value: mod});
                    return modSel;
                }
                else { //cerco il modello giusto
                    var mainMod = $scope.modelEdit.mainMod;
                    if (!mainMod)
                        return null;
                    var rels = $scope.modelEdit.modelDets[mainMod].relations;
                    var inOne = _.find(rels.has_one, {"0": content.from});

                    var inMany = _.find(rels.has_many, {"0": content.from});
                    if (inOne !== "undefined") {
                        for (var i in $scope.modelEdit.modelDets) {
                            if ($scope.modelEdit.modelDets[i].class === inOne.class_name ||
                                    $scope.modelEdit.modelDets[i].pre + $scope.modelEdit.modelDets[i].class === inOne.class_name) {
                                modSel = _.find($scope.mainTree.connector.data.modelsSelection, {value: $scope.modelEdit.modelDets[i].class});
                                return modSel;
                            }
                        }
                    }
                    else if (inMany !== "undefined") {
                        for (var i in $scope.modelEdit.modelDets) {
                            if ($scope.modelEdit.modelDets[i].class === inMany.class_name ||
                                    $scope.modelEdit.modelDets[i].pre + $scope.modelEdit.modelDets[i].class === inMany.class_name) {
                                modSel = _.find($scope.mainTree.connector.data.modelsSelection, {value: $scope.modelEdit.modelDets[i].class});
                                return modSel;
                            }
                        }
                    }
                    else
                        return null;
                }
            }

            this._vmToVm = function (vm) {
                var out = [];
                for (var i in vm) {
                    if (!vm.hasOwnProperty(i))
                        continue;
                    if (!vm[i].content) {
                        console.log("_vmToVm error: field has no content", vm[i], "ignoring...");
                    }
                    if (typeof (vm[i].content) === "string") {
                        var linkToMods = that.fieldToModel(vm[i]);
                        if (!linkToMods) {
                            console.log("errore linkToMods", i, vm[i], $scope.modelEdit);
                        }
                        var data = {
                            type: vm[i].content,
                            from: (vm[i].from ? vm[i].from : null),
                            inmodel: (vm[i].inmodel ? vm[i].inmodel : null),
                            error: (linkToMods && linkToMods.selected ? false : true)
                        };
                        var badge = null;
                        if (!linkToMods) {
                            badge = "err";
                        }
                        else if (!linkToMods.selected) {
                            badge = "Model?";
                        }
                        else {
                            if ($scope.modelEdit.mainMod === linkToMods.value &&
                                    ($scope.modelEdit.mainId === i || $scope.modelEdit.mainIdAlias === i)) {
                                badge = "PK";
                            }
                        }

                        out.push({
                            label: i,
                            data: data,
                            badge: badge,
                            renderStyle: {
                                color: (linkToMods && linkToMods.selected ? linkToMods.color : "grey")
                            },
                            children: []
                        });
                    }
                    else { //array
                        out.push({
                            label: i,
                            children: that._vmToVm(vm[i].content),
                            renderStyle: {
                                color: "#000000"
                            },
                        });
                    }
                }
                return out;
            }

            this.dynamicFieldFormName = function (connector, i) {
                switch (i) {
                    case 0:
                        return connector.label + "_model";
                        break;
                    default:
                        return " ";
                }
            }

            this.percorsoPubblico = function (node) {
                var root = $scope.treeNav.getRoot(node);
                if (!root || !root.path)
                    return "ND";
                if (!node.path) {
                    var path = $scope.treeNav.getParentNode(node).path;
                }
                else {
                    var path = node.path;
                }

                return Restangular.configuration.baseUrl + "/" + (((path.replace(root.path, "") + "/" + (node.label).toLowerCase()).replace(/\/\//g, "/")).replace(/\//g, "_")) + "/__DESCRIBE__.ws";
            };

            

            this.canPreloadMainTree = function () {
                return ($scope.load.services && $scope.load.activerecord && !$scope.load.preLoadMainTree);
            }
            this.canLoadMainTree = function () {
                return ($scope.load.services && $scope.load.activerecord && $scope.load.preLoadMainTree);
            }
            this.canShowResourceForm = function () {
                return ($scope.load.services && $scope.load.activerecord && $scope.load.preLoadMainTree && $scope.load.resourceSelected);
            }
            this.canLoadResourceSubTree = function () {
                return ($scope.load.services && $scope.load.activerecord && $scope.load.preLoadMainTree
                        && $scope.load.resourceSelected && $scope.load.resourceForm && !$scope.load.organizedFields);
            }
            this.canShowResourceSubTree = function () {
                return ($scope.load.services && $scope.load.activerecord && $scope.load.preLoadMainTree
                        && $scope.load.resourceSelected && $scope.load.resourceForm && $scope.load.organizedFields);
            }
            this.canShowResourceSubTreeDetails = function () {
                return ($scope.load.services && $scope.load.activerecord && $scope.load.preLoadMainTree
                        && $scope.load.resourceSelected && $scope.load.resourceForm
                        && $scope.load.organizedFields && $scope.resourceSubTree.connector.id
                        );
            }
            this.canShowResourceFields = function () {
                return ($scope.load.services && $scope.load.activerecord && $scope.load.preLoadMainTree
                        && $scope.load.resourceSelected && $scope.load.resourceForm
                        && $scope.load.organizedFields && !$scope.resourceSubTree.connector.id
                        );
            }
            
            
            this.retrieveActiveRecord = function () {
                DataService.getData("describe_active_record", {}, "describe_active_record_performed", {},
                        function (t) {
                            $scope.models["activerecord"] = [];
                            for (var i in t[1].response.data) {
                                var datum = t[1].response.data[i];
                                $scope.models["activerecord"].push({
                                    class: datum.class,
                                    fields: datum.fields,
                                    relations: {
                                        has_many: datum.static_props.has_many,
                                        has_one: datum.static_props.has_one,
                                    },
                                    alias_attribute: datum.static_props.alias_attribute,
                                    primary_key: datum.static_props.primary_key,
                                    details: {
                                        path: datum.path,
                                        table_name: datum.static_props.table_name,
                                        db: datum.static_props.db,
                                        connection: datum.static_props.connection,
                                    },
                                    pre: datum.pre,
                                    data: datum.data
                                });
                            }
                            $scope.models["activerecord"].sort();
                            $scope.load.activerecord = true;
                            if (that.canPreloadMainTree()) {
                                $scope.flow = that.preloadMainTree();
                            }
                        });
            };
            
            this.refreshData = function () {
                this.retrieveActiveRecord();
                DataService.getData("describe_services", {}, "describe_servicesPerformed", {},
                        function (t) {
                            $scope.services = [];
                            for (var i in t[1].response.data) {
                                var node = {
                                    label: t[1].response.data[i].class,
                                    path: t[1].response.data[i].path,
                                    data: {
                                        file: t[1].response.data[i].file,
                                        static_props: t[1].response.data[i].static_props
                                    }
                                };
                                $scope.services.push(node);
                            }
                            DataService.getData("describe_services_in_db", {}, "describe_services_in_dbPerformed", {},
                                    function (t) {
                                        for (var i in t[1].response.data) {
                                            var index = _.findIndex($scope.services, {
                                                label: t[1].response.data[i].nome_risorsa,
                                                path: t[1].response.data[i].path
                                            });
                                            if (index > -1) {
                                                $scope.services[i].data.dbid = t[1].response.data[i].id;
                                            }
                                        }

                                        $scope.load.services = true;
                                        if (that.canPreloadMainTree()) {
                                            $scope.flow = that.preloadMainTree();
                                        }
                                    });
                        });
            };

            DataService.initializeOnCurrentPage(
                    function () {
                        that.refreshData();
                    },
                    function () {
                        that.destroyPage();
                    }
            );
            console.log(this);
        }]);
    app.factory('fieldNodeCTN', [function () {
            var fieldNodeConnector = {};
            var fieldAlbero = [];
            var fieldTreeNav = {};
            return function () {
                return {
                    getConnector: function () {
                        return fieldNodeConnector;
                    },
                    getTree: function () {
                        return fieldAlbero;
                    },
                    getNav: function () {
                        return fieldTreeNav;
                    },
                    setNav: function (nav) {
                        fieldTreeNav = nav;
                    },
                    set: function (which, what) {
                        switch (which) {
                            case "connector":
                                fieldNodeConnector = what;
                                break;
                            case "tree":
                                fieldAlbero = what;
                                break;
                            case "nav":
                                fieldTreeNav = what;
                                break;
                        }
                    },
                }
            };
        }]);
    app.filter("selectedF", [function () {
            return function (input) {
                var out = [];
                for (var i in input) {
                    if (typeof (input[i]) === "object" && input[i].selected) {
                        out.push(input[i]);
                    }
                }
                return out;
            }
        }]);
    app.filter("fAlias", [function () {
            return function (input, modDets) {
                if (modDets && modDets.fields) {
                    if (!modDets.alias_attribute) {
                        return input;
                    }
                    if (_.findKey(modDets.fields, {fieldname: input}) > -1) {
                        var key = null;
                        for (var i in modDets.alias_attribute) {
                            if (modDets.alias_attribute.hasOwnProperty(i) && modDets.alias_attribute[i] === input) {
                                key = i;
                                break;
                            }
                        }
                        if (key)
                            return key + " (" + input + ")";
                        else
                            return input;
                    }
                    else {
                        return "ND";
                    }
                }
                else {
                    return "ND";
                }
            }
        }]);
})();





