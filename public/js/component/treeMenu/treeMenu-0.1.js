var TREEMENU = angular.module("TREEMENULIB", ['ngAnimate', 'ngDragDrop'], ['$interpolateProvider', function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);
TREEMENU.provider('treeMenuUtilities', [function () {
        var pippo = this;
        this.matchInitialPath = function (path1, path2, sep1, sep2) {

            if (typeof (sep1) === "undefined") {
                sep1 = "/";
            }
            if (typeof (sep2) === "undefined") {
                sep2 = "/";
            }
            if (typeof (path1) === "string") {
                path1 = path1.split(sep1);
            }
            if (typeof (path2) === "string") {
                path2 = path2.split(sep2);
            }
            if (path1[0] === "") {
                path1.splice(0, 1);
            }
            if (path2[0] === "") {
                path2.splice(0, 1);
            }
            if (path1[path1.length - 1] === "") {
                path1.splice(path1.length - 1, 1);
            }
            if (path2[path2.length - 1] === "") {
                path2.splice(path2.length - 1, 1);
            }
            var len1 = path1.length;
            var len2 = path2.length;
            if (len1 > len2) {
                return false;
            }
            for (var i = 0; i < len1; i++) {
                if (path1[i] !== path2[i]) {
                    return false;
                }
            }
            var out = [];
            for (var i = len1; i < len2; i++) {
                out.push(path2[i]);
            }
            return out;
        }
        this.addToTree = function (treeNav, node, sep, startNode) {
            var path = node.path;
            var searchNode = startNode;
            do {
                var root = searchNode;
                var match = null;
                if (root.path) {
                    match = pippo.matchInitialPath(root.path, path, "/", sep);
                    if (match !== false) {
                        if (match.length === 0) {
                            var nodeData = {
                                label: node.label,
                                data: node.data,
                            };
                            if (node.id)
                                nodeData.id = node.id;
                            treeNav.addNode(root, nodeData, true);
                            return;
                        }
                        else {
                            var flag = false;
                            var childrenNodes = treeNav.getChildrenNodes(root);
                            for (var k in childrenNodes) {
                                if (childrenNodes[k].label === match[0]
                                        && childrenNodes[k].path === (root.path + "/" + match[0]).replace("//", "/")) {
                                    flag = true;
                                    break;
                                }
                            }
                            if (!flag) {
                                var nodeData = {
                                    label: match[0],
                                    path: (root.path + "/" + match[0]).replace("//", "/"),
                                    children: []
                                };
                                treeNav.addNode(root, nodeData, true);
                            }


                            var childrenNodes = treeNav.getChildrenNodes(root);
                            pippo.addToTree(treeNav, node, sep, childrenNodes[0]);
                        }
                    }
                    else {
                        continue;
                    }

                }
                else {
                    throw new Error("addToTree error: a previously inserted node doesn't have a regular path");
                }

            } while (searchNode = treeNav.getNextSiblingNode(searchNode));
            return;
        };
        this.trees = [];
        this.$get = ['$timeout', function giovanni($timeout) {
                return {
                    newTree: function (tree) {
                        pippo.trees.push({
                            connector: {label: null},
                            nav: {},
                            tree: tree
                        });
                        return (pippo.trees.length - 1);
                    },
                    getTree: function (index) {
                        return pippo.trees[index];
                    },
                    deleteTree: function (index) {
                        if (pippo.trees[index])
                            pippo.trees.slice(index, 1);
                    },
                    inflateMenuData: function (input, treeNav, descriptor, roots) {
                        if (!angular.isArray(input)) {
                            throw new Error("inflateMenuData error: input must be an array of pre-nodes");
                        }
                        if (roots && !angular.isArray(roots)) {
                            throw new Error("inflateMenuData error: input must be an array of pre-nodes");
                        }
                        if (!angular.isObject(descriptor)) {
                            throw new Error("inflateMenuData error: descriptor must be an object");
                        }
                        var path = descriptor.path;
                        var id = descriptor.id;
                        var data = descriptor.data;
                        var pathSep = descriptor.pathSep;
                        if (typeof (pathSep) === "undefined") {
                            pathSep = "/";
                        }
                        if (typeof (path) === "undefined") {
                            throw new Error("inflateMenuData error: path must be valid to perform this translation");
                        }
                        var r = treeNav.getFirstNode();
                        for (var i in roots) {
                            var flag = false;
                            do {
                                if (r.label === roots[i].label && r.path === roots[i].path) {
                                    flag = true;
                                    break;
                                }
                            } while (r = treeNav.getNextSiblingNode(r))
                            if (!flag) {
                                var nodeData = {
                                    label: roots[i].label,
                                    path: roots[i].path,
                                    children: []
                                };
                                treeNav.addNode(null, nodeData);
                                treeNav.selectNode(null);
//                                $timeout(function () {
//                                giovanni.inflateMenuData(input, treeNav, descriptor, roots);
//                                }, 300);
//                                return;
                            }
                        }
                        for (var i in input) {
                            var node = input[i];
                            pippo.addToTree(treeNav, node, pathSep, treeNav.getFirstNode());
                            treeNav.selectNode(null);
                        }
                    }
                }
            }];
    }]);
TREEMENU.directive('treeMenu', ['$timeout', function ($timeout) {
//        
        var version = "0.1";
        return {
            restrict: "E",
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/treeMenu/treeMenu-" + version + ".html";
            },
            replace: true,
            scope: {
                treeMenuData: '=data',
                globalSelectionCallback: '&',
                initialSelection: '@',
                navigator: '=',
                selectedNodeConnector: '=',
                selectorLocker: "="
            },
            link: function (scope, element, attrs) {
                if (attrs.working)
                    return;
                attrs.working = true;
                scope.randomString = function (le)
                {
                    var text = "";
                    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
                    for (var i = 0; i < le; i++)
                        text += possible.charAt(Math.floor(Math.random() * possible.length));
                    return text;
                };
                /*
                 * D&D
                 */
                scope.dropMan = function (a, b, c) {
                    console.log("D&D", a, b, c);
                    if (!c || c === 0) {
                        scope.localNav.moveNodeUnder(b);
                    }
                    else {
                        scope.localNav.moveNodeUnder(b, c);
                    }

                }

                scope.ods = function () {
                    element.find(".tree-menu-row-placeholder").show();
                }
                scope.ode = function () {
                    element.find(".tree-menu-row-placeholder").hide();
                }

                var initialExpand, nav;
                var selectedNode = null;
                var log = function (v, isErr) {
                    if (isErr) {
                        console.log("TREE MENU, ERROR: ", v);
                        return void(0);
                    }
                    else {
                        console.log("TREE MENU: ", v);
                    }
                };
                if (!attrs.iconExpand) {
                    attrs.iconExpand = 'icon-plus glyphicon glyphicon-plus fa fa-plus';
                }
                if (!attrs.iconCollapse) {
                    attrs.iconCollapse = 'icon-minus glyphicon glyphicon-minus fa fa-minus';
                }
                if (!attrs.iconLeaf) {
                    attrs.iconLeaf = 'icon-file glyphicon glyphicon-file fa fa-file';
                }
                if (!attrs.iconError) {
                    attrs.iconError = 'icon-warning glyphicon glyphicon-warning-sign fa fa-warning';
                }
                if (!attrs.initialExpand) {
                    attrs.initialExpand = '3';
                    var initialExpand = '3';
                }
                if (!attrs.folderqualificator) {
                    attrs.folderQualificator = null;
                    scope.folderQualificator = null;
                }
                else {
                    attrs.folderQualificator = attrs.folderqualificator;
                    scope.folderQualificator = attrs.folderqualificator;
                }
                if (!attrs.foldermixture) {
                    attrs.folderQualificator = null;
                    scope.folderQualificator = null;
                }
                else {
                    attrs.folderMixture = attrs.foldermixture;
                    scope.folderMixture = attrs.foldermixture;
                }
                if (scope.folderMixture === "true") {
                    scope.folderMixture = true;
                }
                if (scope.folderMixture === "false") {
                    scope.folderMixture = false;
                }
                initialExpand = parseInt(attrs.initialExpand, 1);
                if (!scope.treeMenuData) {
                    log('Albero vuoto');
                    return;
                }
                if (scope.treeMenuData.length === null) {
                    if (scope.treeMenuData.label !== null) {
                        scope.treeMenuData = [scope.treeMenuData];
                    }
                    else {
                        log("I dati che alimentano l'albero non sono scritti nel modo corretto");
                    }
                }
                var applyToAllNodes = function (fcn, root) {
                    var applyFcn = function (node, level) {
                        var outcome = [];
                        outcome.push(fcn(node, level));
                        if (!node.children)
                            node.children = [];
                        if (node.children !== null && node.children.length) {
                            for (var i = 0; i < node.children.length; i++) {
                                var child = node.children[i];
                                var tmp = applyFcn(child, level + 1);
                                outcome.push(tmp); // ricorsione
                            }
                        }
                        return outcome;
                    };
                    var outcome = [];
                    if (typeof (root) === "undefined") {
                        for (var i = 0; i < scope.treeMenuData.length; i++) {
                            outcome.push(applyFcn(scope.treeMenuData[i], 1));
                        }
                    }
                    else if (root && root.children) {
                        for (var i = 0; i < root.children.length; i++) {
                            outcome.push(applyFcn(root.children[i], root.level + 1));
                        }
                    }
                    return outcome;
                };
                scope.globalSelectionCallback = function (selection) {
                    if (!angular.isObject(selection)) {
                        log("Questa non è una selezione valida", true);
                        return;
                    }
                    if (typeof (selection.node) !== "undefined") {
                        var node = selection.node;
                    }
                };
                var selectNode = function (node) {
                    var can = scope.selectorLocker();
                    if (can) {
                        return;
                    }
                    if (!node) {
                        if (selectedNode !== null) {
                            selectedNode.selected = false;
                        }
                        selectedNode = null;
                        scope.selectedNodeConnector = {label: null};
                        return;
                    }
                    if (node !== selectedNode) {
                        if (selectedNode !== null) {
                            selectedNode.selected = false;
                        }
                        node.selected = true;
                        scope.selectedNodeConnector = node;
                        selectedNode = node;
                        showAllAncestors(node);
                        if (node.selectionCallback) {
                            return $timeout(function () {
                                return node.selectionCallback(node);
                            }, 100);
                        } else {
                            if (scope.globalSelectionCallback === "true") {
                                return $timeout(function () {
                                    return scope.globalSelectionCallback({
                                        node: node
                                    });
                                });
                            }
                        }
                    }
                    else {
                        selectedNode.selected = false;
                        scope.selectedNodeConnector = {label: null};
                        selectedNode = null;
                    }
                };
                scope.clickOnNode = function (node) {
                    return selectNode(node);
                };
                var getParentNode = function (node) {
                    var parent;
                    parent = null;
                    if (node.parentId) {
                        applyToAllNodes(function (nn) {
                            if (nn.id === node.parentId) {
                                return parent = nn;
                            }
                        });
                    }
                    return parent;
                };
                var applyToAllAncestors = function (node, fcn) {
                    var parent;
                    parent = getParentNode(node);
                    if (parent !== null) {
                        fcn(parent);
                        return applyToAllAncestors(parent, fcn);
                    }
                };
                var showAllAncestors = function (node) {
                    return applyToAllAncestors(node, function (n) {
                        try {
                            return scope.expandNode(n, true);
                        }
                        catch (e) {
                            log(e, true);
                            log(n, true);
                        }
                    });
                };
                scope.treeRows = [];
                var globalDataChangeHandler = function (update) {
                    if (update) {
                        scope.treeRows = [];
                    }
                    var pippo = applyToAllNodes(function (n, l) {
                        var rS = scope.randomString(10);
                        if (typeof (n) === "string") {
                            log("TROVATO NODO STRINGA -- qualcosa è andato storto", true);
                            return n;
                        }
                        if (!n.id) {
                            n.id = rS;
                            if (n.children && n.children.length) {
                                for (var i in n.children) {
                                    if (typeof (n.children[i]) === "string") {
                                        n.children[i] = {label: n.children[i]};
                                    }
                                }
                            }
                            return n; // <<<< salvare in hash
                        }

                    });
                    applyToAllNodes(function (n) {
                        if (angular.isArray(n.children)) {
                            var outcome = [];
                            for (var i in n.children) {
                                var child = n.children[i];
                                outcome.push(child.parentId = n.id);
                            }
                            return outcome;
                        }
                    });
                    scope.treeRows = [];
                    applyToAllNodes(function (node) {
                        if (node.children && node.children.length > 0) {
                            var fcn = function (e) {
                                if (typeof (e) === "string") {
                                    return {
                                        label: e,
                                        children: []
                                    };
                                } else {
                                    return e;
                                }
                            };
                            return node.children = (function () {
                                var outcome = [];
                                for (var i in node.children) {
                                    var child = node.children[i];
                                    outcome.push(fcn(child));
                                }
                                return outcome;
                            })();
                        } else {
                            return node.children = [];
                        }
                    });
                    applyToAllNodes(function (n, l) {
                        try {
                            n.level = l;
                        }
                        catch (e) {
                            log(e, true);
                            log(n, true);
                            log(l, true);
                        }
                        return n.expanded = n.level < initialExpand;
                    });
                    if (scope.initialSelectionId !== null) {
                        applyToAllNodes(function (n) {
                            if (n.id === scope.initialSelectionId) {
                                $timeout(function () {
                                    showAllAncestors(n);
                                    return selectNode(n);
                                }, 100);
                                return true;
                            }
                            else {
                                return false;
                            }
                        });
                    }
                    else if (attrs.initialSelection !== null) {
                        applyToAllNodes(function (n) {
                            if (n.label === attrs.initialSelection) {
                                $timeout(function () {
                                    showAllAncestors(n);
                                    return selectNode(n);
                                }, 100);
                                return true;
                            }
                            return false;
                        });
                    }
                    var nodeToList = function (level, node, visible) {
                        var tree_icon;
                        if (node.expanded === null) {
                            node.expanded = false;
                        }
                        if (!node.children || !node.children.length) {
                            tree_icon = attrs.iconLeaf;
                        }
                        else {
                            if (node.expanded) {
                                tree_icon = attrs.iconCollapse;
                            } else {
                                tree_icon = attrs.iconExpand;
                            }
                        }
                        if (node.error || (node.data && node.data.error)) {
                            tree_icon = attrs.iconError;
                        }
                        scope.treeRows.push({
                            level: level,
                            node: node,
                            label: node.label,
                            tree_icon: tree_icon,
                            visible: visible
                        });
                        if (node.children !== null) {
                            var outcome = [];
                            for (var i in node.children) {
                                var child = node.children[i];
                                var child_visible = visible && node.expanded;
                                outcome.push(nodeToList(level + 1, child, child_visible));
                            }
                            return outcome;
                        }
                    };
                    var outcome = [];
                    for (var i in scope.treeMenuData) {
                        var root = scope.treeMenuData[i];
                        outcome.push(nodeToList(1, root, true));
                    }
                    return outcome;
                };
                scope.$watch('treeMenuData', globalDataChangeHandler);
                scope.treeRowsSearchNode = function (node) {
                    for (var i in scope.treeRows) {
                        if (node.id === scope.treeRows[i].node.id)
                            return i;
                    }
                    return -1;
                };
                scope.expandNode = function (node, force) {
                    if (force === false || (typeof (force) === "undefined" && node.expanded)) {
                        node.expanded = false;
                        applyToAllNodes(function (n, l) {
                            var index = scope.treeRowsSearchNode(n);
                            if (index !== -1) {
                                scope.treeRows[index].visible = false;
                                scope.treeRows[index].tree_icon = (n.children.length ? attrs.iconExpand : attrs.iconLeaf);
                            }
                            n.expanded = false;
                        }, node);
                    }
                    else if (force === true || (typeof (force) === "undefined" && !node.expanded)) {
                        node.expanded = true;
                        for (var i in node.children) {
                            var index = scope.treeRowsSearchNode(node.children[i]);
                            if (index !== -1) {
                                scope.treeRows[index].visible = true;
                            }
                        }
                    }
                    var index = scope.treeRowsSearchNode(node);
                    if (index > -1) {
                        if (node.expanded) {
                            scope.treeRows[index].visible = true;
                            scope.treeRows[index].tree_icon = (node.children.length ? attrs.iconCollapse : attrs.iconLeaf);
                        } else {
                            scope.treeRows[index].tree_icon = (node.children.length ? attrs.iconExpand : attrs.iconLeaf);
                        }
                    }

                };
                scope.formatNode = function (n) {
                    var out = {};
                    if (n && n.renderStyle && angular.isObject(n.renderStyle)) {
                        if (n.renderStyle.color) {
                            out.color = n.renderStyle.color;
                        }
                    }
                    return out;
                };
                scope.rowClass = function (row) {
                    if (scope.folderQualificator && row.node[scope.folderQualificator]) {
                        return (row.node.children.length ? attrs.iconCollapse : attrs.iconExpand);
                    }
                    else {
                        return row.tree_icon;
                    }
                }
                if (scope.navigator && (angular.isObject(scope.navigator) || angular.isFunction(scope.navigator))) {
                    var nav;
                    if (angular.isObject(scope.navigator)) {
                        nav = scope.navigator;
                    }
                    else if (angular.isFunction(scope.navigator))
                        nav = {};
                    nav.getTreeRows = function (compact) {
                        if (compact) {
                            var out = [];
                            for (var i in scope.treeRows) {
                                out.push({
                                    level: scope.treeRows[i].level,
                                    node: {
                                        id: scope.treeRows[i].node.id,
                                        parentId: scope.treeRows[i].node.parentId,
                                        childrenLe: scope.treeRows[i].node.children.length,
                                        folder: (scope.folderQualificator ? scope.treeRows[i].node[scope.folderQualificator] : "ND")
                                    },
                                    label: scope.treeRows[i].label,
                                    tree_icon: scope.treeRows[i].tree_icon,
                                    visible: scope.treeRows[i].visible,
                                });
                            }
                            return out;
                        }
                        return scope.treeRows;
                    }
                    nav.expandAll = function () {
                        applyToAllNodes(function (n, l) {
                            scope.expandNode(n, true);
                        });
                    };
                    nav.collapseAll = function () {
                        return applyToAllNodes(function (n, l) {
                            scope.expandNode(n, false);
                        });
                    };
                    nav.getFirstCommonAncestor = function (n1, n2) {
                        if (!n1 || !n2) {
                            return null;
                        }
                        var p1, p2;
                        p1 = n1;
                        do {
                            p2 = n2;
                            do {
                                if (p1.id === p2.id) {
                                    return p1;
                                }
                                p2 = nav.getParentNode(p2);
                            } while (p2);
                            p1 = nav.getParentNode(p1);
                        } while (p1);
                        return null;
                    }
                    nav.getFirstNode = function () {
                        var n = scope.treeMenuData.length;
                        if (n > 0) {
                            return scope.treeMenuData[0];
                        }
                    };
                    nav.selectFirstNode = function () {
                        var n = nav.getFirstNode();
                        return nav.selectNode(n);
                    };
                    nav.getRoot = function (n) {
                        var node = n;
                        var parent = n;
                        do {
                            node = parent;
                            parent = getParentNode(node);
                        } while (parent !== null);
                        return node;
                    }
                    nav.getSelectedNode = function () {
                        return selectedNode;
                    };
                    nav.getParentNode = function (n) {
                        return getParentNode(n);
                    };
                    nav.selectNode = function (n) {
                        selectNode(n);
                        return n;
                    };
                    nav.getChildrenNodes = function (n) {
                        return n.children;
                    };
                    nav.selectParentNode = function (n) {
                        var p;
                        if (n === null) {
                            n = nav.getSelectedNode();
                        }
                        if (n !== null) {
                            p = nav.getParentNode(n);
                            if (p !== null) {
                                nav.selectNode(p);
                                return p;
                            }
                        }
                    };
                    nav.addNode = function (parentNode, newNode, confirmed, expand, select) {
                        if (!newNode) {
                            newNode = {label: "nuovo nodo"};
                        }
                        if (scope.folderQualificator) {
                            newNode[scope.folderQualificator] = false;
                        }
                        else if (attrs.folderQualificator) {
                            newNode[attrs.folderQualificator] = false;
                        }
                        var rS = scope.randomString(10);
                        newNode.id = rS;
                        newNode.unconfirmed = !(confirmed);
                        if (parentNode !== null) {
                            if (parentNode.level >= 10) {
                                log("Questa componente supporta al massimo 10 livelli.", true);
                                return;
                            }
                            newNode.parentId = parentNode.id;
                            parentNode.children.push(newNode);
                            if (scope.folderQualificator) {
                                parentNode[scope.folderQualificator] = true;
                            }
                            else if (attrs.folderQualificator) {
                                parentNode[attrs.folderQualificator] = true;
                            }

                            if (expand) {
                                scope.expandNode(parentNode, true);
                            }
                            if (selectedNode) {
                                selectedNode.selected = false;
                                selectedNode = null;
                            }
                        } else {
                            scope.treeMenuData.push(newNode);
                        }
                        var newData = angular.copy(scope.treeMenuData);
                        if (select) {
                            scope.initialSelectionId = rS;
                        }
                        nav.forceUpdate();
                    };
                    nav.removeNode = function (node) {
                        if (!node) {
                            node = selectedNode;
                        }
                        if (!node)
                            return;
                        var parentNode = getParentNode(node);
                        if (parentNode !== null) {

                            for (var i in parentNode.children) {
                                if (parentNode.children[i].id === node.id) {
                                    parentNode.children.splice(i, 1);
                                }
                            }
                            node.selected = false;
                            selectedNode = null;
                            node = null;
                        }
                        else {
                            for (var i in scope.treeMenuData) {
                                if (scope.treeMenuData[i].id === node.id) {
                                    scope.treeMenuData.splice(i, 1);
                                }
                            }
                            selectedNode.selected = false;
                            selectedNode = null;
                            node = null;
                        }
                        var newData = angular.copy(scope.treeMenuData);
                        scope.treeMenuData = [];
                        $timeout(function () {
                            scope.treeMenuData = newData;
                        }, 100);
                    };
                    nav.toggleExpandNode = function (n) {
                        if (n === null) {
                            n = nav.getSelectedNode();
                        }
                        if (n !== null) {
                            scope.expandNode(n);
                            return n;
                        }
                    };
                    nav.getSiblingNodes = function (n) {

                        if (n === null) {
                            n = selectedNode;
                        }
                        if (n !== null) {
                            var p = nav.getParentNode(n);
                            var siblings;
                            if (p) {
                                siblings = p.children;
                            } else {
                                siblings = scope.treeMenuData;
                            }
                            return siblings;
                        }
                    };
                    nav.getNextSiblingNode = function (n) {
                        if (n === null) {
                            n = selectedNode;
                        }
                        if (n !== null) {
                            var siblings = nav.getSiblingNodes(n);
                            var l = siblings.length;
                            var i = siblings.indexOf(n);
                            if (i < l - 1) {
                                return siblings[i + 1];
                            }
                            else
                                return null;
                        }
                    };
                    nav.getPreviousSiblingNode = function (n) {
                        var i, siblings;
                        if (n === null) {
                            n = selectedNode;
                        }
                        var siblings = nav.getSiblingNodes(n);
                        var i = siblings.indexOf(n);
                        if (i > 0) {
                            return siblings[i - 1];
                        }
                        else
                            return null;
                    };
                    nav.selectNextSiblingNode = function (n) {
                        var next;
                        if (n === null) {
                            n = selectedNode;
                        }
                        if (n !== null) {
                            next = nav.getNextSiblingNode(n);
                            if (next !== null) {
                                return nav.selectNode(next);
                            }
                        }
                    };
                    nav.selectPreviousSiblingNode = function (n) {
                        var prev;
                        if (n === null) {
                            n = selectedNode;
                        }
                        if (n !== null) {
                            prev = nav.getPreviousSiblingNode(n);
                            if (prev !== null) {
                                return nav.selectNode(prev);
                            }
                        }
                    };
                    nav.moveNodeUnder = function (node, under) {

                        if (!node) {
                            node = selectedNode;
                        }
                        if (!node) {
                            return;
                        }
                        console.log("spostamento di", node, "sotto", under);
                        console.log("nelle righe si trova a :", scope.treeRowsSearchNode(node));
                        console.log("LUNGH TREErOWS :", scope.treeRows.length);
                        var parent = getParentNode(node);
                        if (!parent) {
                            console.log("attuale parent nessuno: stai muovendo una radice");
                        }
                        else {
                            console.log("attuale parent ", parent.label, parent.id);
                        }

                        if (typeof (under) === "undefined" || !under) {
                            console.log("spostamento a radice --- poi lo vediamo");
                            console.log("situazione attuale:");
                            var logNode = angular.copy(node);
                            delete(logNode.data);
                            logNode.children = (logNode.children ? logNode.children.length : 0);
                            console.log("nodo da spostare:", JSON.stringify(logNode));
                            if (parent) {
                                var logParent = angular.copy(parent);
                                delete(logParent.data);
                                logParent.children = (logParent.children ? logParent.children.length : 0);
                                console.log("attuale nodo padre:", JSON.stringify(logParent));
                            }

                            if (!parent) {
                                console.log("spostamento da radice a radice, inutile");
                                return;
                            }
                            console.log("STACCO DAL NODO PADRE");
                            var index = _.findIndex(parent.children, node);
                            if (index > -1) {
                                parent.children.splice(index, 1);
                            }
                            else {
                                console.log("NON RIESCO A STACCARLO PERCHé NON LO TROVO");
                                console.log(parent.children);
                                return;
                            }
                            var logParent = angular.copy(parent);
                            delete(logParent.data);
                            logParent.children = (logParent.children ? logParent.children.length : 0);
                            console.log("attuale nodo padre:", JSON.stringify(logParent));
                            console.log("ATTACCO COME NUOVA RADICE");
                            delete(node.parentId);
                            node.level = 1;
                            scope.treeMenuData.push(node);
                            var logNode = angular.copy(node);
                            delete(logNode.data);
                            logNode.children = (logNode.children ? logNode.children.length : 0);
                            console.log("nodo spostato:", JSON.stringify(logNode));
                            console.log("aggiorno vista");
                            nav.updateNodeView(node);
                            var tmptreeMenuData = scope.treeMenuData;
                            scope.treeMenuData = [];
                            $timeout(function () {
                                scope.treeMenuData = tmptreeMenuData;
                                nav.forceUpdate(true);
                                scope.expandNode(node);

                                console.log(scope.treeRows);
                                scope.$digest();
                            }, 100);

                        }
                        else if (under.id && angular.isArray(under.children)) {
                            console.log("spostamento sotto nuovo nodo");
                            console.log("situazione attuale:");
                            var logNode = angular.copy(node);
                            delete(logNode.data);
                            logNode.children = (logNode.children ? logNode.children.length : 0);
                            console.log("nodo da spostare:", JSON.stringify(logNode));
                            if (parent) {
                                var logParent = angular.copy(parent);
                                delete(logParent.data);
                                logParent.children = (logParent.children ? logParent.children.length : 0);
                                console.log("attuale nodo padre:", JSON.stringify(logParent));
                            }
                            var logUnder = angular.copy(under);
                            delete(logUnder.data);
                            logUnder.children = (logUnder.children ? logUnder.children.length : 0);
                            console.log("nuovo nodo padre:", JSON.stringify(logUnder));

                            //controllo di poter spostare
                            var cA = nav.getFirstCommonAncestor(node, under);
                            if (cA && cA.id === node.id) {
                                console.log("Stai cercando di spostare un nodo dentro se stesso.");
                                return;
                            }
                            if (scope.folderQualificator && scope.folderMixture === false) { //questo albero gestisce le cartelle separatamente
                                if (!under[scope.folderQualificator]) {
                                    console.log("La destinazione non é una cartella!");
                                    return;
                                }
                            }

                            if (!parent) {
                                console.log("STACCO LA RADICE");
                                var index = _.findIndex(scope.treeMenuData, node);
                                if (index > -1) {
                                    scope.treeMenuData.splice(index, 1);
                                    node.parentId = null;
                                }
                                else {
                                    console.log("NON RIESCO A STACCARLO PERCHé NON LO TROVO");
                                    console.log(scope.treeMenuData);
                                    return;
                                }
                            }
                            else {
                                if (parent.id === under.id) {
                                    console.log("spostamento vuoto, stai spostando un nodo nella stessa posizione");
                                    return;
                                }



                                console.log("STACCO DAL NODO PADRE");
                                var index = _.findIndex(parent.children, node);
                                if (index > -1) {
                                    parent.children.splice(index, 1);
                                    node.parentId = null;
                                }
                                else {
                                    console.log("NON RIESCO A STACCARLO PERCHé NON LO TROVO");
                                    console.log(parent.children);
                                    return;
                                }
                                var logParent = angular.copy(parent);
                                delete(logParent.data);
                                logParent.children = (logParent.children ? logParent.children.length : 0);
                                console.log("attuale nodo padre:", JSON.stringify(logParent));
                            }
                            console.log("ATTACCO AL NUOVO PADRE");
                            node.parentId = under.id;
                            under.children.push(node);
                            var logNode = angular.copy(node);
                            delete(logNode.data);
                            logNode.children = (logNode.children ? logNode.children.length : 0);
                            console.log("nodo spostato:", JSON.stringify(logNode));
                            var logUnder = angular.copy(under);
                            delete(logUnder.data);
                            logUnder.children = (logUnder.children ? logUnder.children.length : 0);
                            console.log("nuovo nodo padre:", JSON.stringify(logUnder));
                            console.log("aggiorno vista");


                            nav.forceUpdate(true);
                            scope.expandNode(under);
                            console.log(scope.treeMenuData);

                        }
                        else {
                            log("Impossibile realizzare lo spostamento, under non è un nodo valido", true);
                        }
                        $timeout(function () {
                            nav.forceUpdate(true);
                        }, 100);
                        return node;
                    };
                    nav.showThisNode = function (node) {
                        if (!node) {
                            node = selectedNode;
                        }
                        if (!node) {
                            return;
                        }
                        showAllAncestors(node);
                        if (node !== selectedNode)
                            selectNode(node);
                    };
                    nav.updateNodeView = function (node) {
                        var index = -1;
                        index = scope.treeRowsSearchNode(node);
                        if (index === -1)
                            return;
                        log(scope.treeRows[index].node);
                        scope.treeRows[index].label = node.label;
                    };
                    nav.forceUpdate = function (withExpand) {
                        globalDataChangeHandler(true);
                        if (withExpand) {
                            nav.expandAll();
                        }
                    };
                    nav.searchNodeByLabel = function (l, start) {
                        var node;
                        if (!start) {
                            node = nav.getFirstNode();
                        }
                        else {
                            node = start;
                        }
                        var flag = false;
                        do {
                            var children = nav.getChildrenNodes(node);
                            if (node.label && node.label === l) {
                                flag = true;
                            }
                            else if (children && children.length) { // ricorsione per profondità
                                var down = nav.searchNodeByLabel(l, children[0]);
                                if (down !== null)
                                    return down;
                            }
                        }
                        while (!flag && (node = nav.getNextSiblingNode(node)));
                        if (node && node.label && node.label === l) {
                            return node;
                        }
                        else {
                            return null;
                        }
                    }

                    if (angular.isFunction(scope.navigator)) {
                        scope.localNav = scope.navigator(nav);
                    }
                    else if (angular.isObject(scope.navigator)) {
                        scope.localNav = scope.navigator;
                    }
                }
                else {
                    log("tree navigator non è un oggetto nè una funzione", true);
                }
                attrs.working = false;
            }
        };
    }
]);