var multiPageModule = angular.module("MULTIPAGE", ['ngAnimate'], ['$interpolateProvider',
    function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);


multiPageModule.directive("multiPage", ['mPage', function (mPage) {
        var version = "0.1";
        return {
//            templateUrl: function () {
//                var baseurl = document.URL;
//                baseurl = baseurl.split("public");
//                return baseurl[0] + "public/js/component/multiPage/multiPage-" + version + ".html";
//            },
            restrict: 'A',
//            replace: true,
            scope: {
            },
            link: function (scope, element, attrs) {
//                console.log(scope);
//                console.log(element);
//                console.log(attrs);
                mPage.newFragment(attrs.multiPage);
            },
            controller: ['$scope', '$element', '$attrs', function ($scope, $element, $attrs) {

                }]
        };
    }]);

multiPageModule.provider("mPage", [function () {
        var that = this;
        this.registerFragment = function (hash) {
            var flag = true;
            if (typeof (this.fragments[hash]) === "undefined") {
                this.fragments[hash] = {
                    addClass: function () {

                    },
                    removeClass: function () {

                    },
                    sets: []
                };
                return [hash, this.fragments[hash]];
            }
            else
                return false;
        };
        this.registerSet = function (setHash) {
            var flag = true;
            if (typeof (this.sets[setHash]) === "undefined") {
                this.sets[setHash] = {
                    addClass: function () {

                    },
                    removeClass: function () {

                    },
                    fragments: []
                };
                return [setHash, this.sets[setHash]];
            }
            else
                return false;
        }
        this.fragments = [];
        this.sets = [];
        this.$get = [function giovanni() {
                var pippo = this;
                return {
                    debugListAll: function () {
                        console.log("fragments");
                        console.log(that.fragments);
                        console.log("sets");
                        console.log(that.sets);
                    },
                    show: function (hash) {

                    },
                    getFragment: function (hash) {
                        var fragment = that.fragments[hash];
                        if (typeof fragment === undefined)
                            return false;
                        return {
                            getSets: function () {
                                return fragment.sets;
                            },
                            addToSet: function (setHash) {
                                var set = that.sets[setHash];
                                if (typeof set === undefined) {
                                    var couple = that.registerSet(setHash);
                                    set = couple[1];
                                }
                                set.fragments.push(hash);
                                fragment.sets.push(setHash);
                                return giovanni().getFragment(hash);
                            },
                            removeFromSet: function (setHash) {
                                var set = that.sets[setHash];
                                if (typeof set === undefined) {
                                    return giovanni().getFragment(hash);
                                }
                                var index1 = set.fragments.indexOf(hash);
                                if (index1 > -1)
                                    set.fragments.slice(index1, 1);
                                var index2 = fragment.sets.indexOf(setHash);
                                if (index2 > -1)
                                    fragment.sets.slice(index2, 1);
                                return giovanni().getFragment(hash);
                            },
                        }
                    },
                    getSet: function (setHash) {
                        var set = that.sets[setHash];
                        if (typeof set === undefined)
                            return false;
                        return {
                            getFragments: function () {
                                return set.fragments;
                            },
                            addFragment: function (hash) {
                                var fragment = that.fragments[hash];
                                if (typeof fragment === undefined) {
                                    return giovanni().getSet(setHash);
                                }
                                var index2 = set.fragments.indexOf(hash);
                                if (index2 === -1) {
                                    set.fragments.push(hash);
                                }
                                var index3 = fragment.sets.indexOf(setHash);
                                if (index3 === -1) {
                                    fragment.sets.push(setHash);
                                }
                                return giovanni().getSet(setHash);
                            },
                            removeFragment: function (hash) {
                                var index2 = set.fragments.indexOf(hash);
                                if (index2 > -1) {
                                    set.fragments.slice(index2, 1);
                                }
                                var fragment = that.fragments[hash];
                                if (typeof fragment === undefined) {
                                    return giovanni().getSet(setHash);
                                }
                                var index3 = fragment.sets.indexOf(setHash);
                                if (index3 > -1) {
                                    fragment.set.slice(index3, 1);
                                }
                                return giovanni().getSet(setHash);
                            }
                        }
                    },
                    newFragment: function (hash) {
                        var couple = that.registerFragment(hash);
                        return giovanni().getFragment(couple[0]);
                    },
                    newSet: function (setHash) {
                        var couple = that.registerSet(setHash);
                        return giovanni().getSet(couple[0]);

                    }
                };
            }];
    }]);