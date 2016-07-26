
var app = angular.module("Vincenzo", ['TSNWCLIENT'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});

app.controller("PutController", function (ClientTSNW, $rootScope) {
    this.so = [];
    this.resource = "/accounts/1233";
    this.vm = [];
    this.newvm = {};
    this.fields = [];
    this.ord = 'editable';
    var pippo = this;
    this.getVM = function () {
        var req = ClientTSNW.get(pippo.resource);
        console.log(req);
        $rootScope.$on("getPerformed", function (event, mass) {
            if (req === mass[0]) {
                this.vm = [];
                this.newvm = {};
                this.fields = [];
                pippo.vm = ClientTSNW.show(mass[0]);
                console.log(pippo.vm);
                if (typeof (pippo.vm["result"][0]) !== "undefined") {
                    for (var i in pippo.vm["result"][0]) {
                        pippo.fields.push({name: i, value: pippo.vm["result"][0][i]});
                        pippo.newvm[i] = pippo.vm["result"][0][i];
                    }
                }
                else {
                    for (var i in pippo.vm["result"]) {
                        pippo.fields.push({name: i, value: pippo.vm["result"][i]});
                        pippo.newvm[i] = pippo.vm["result"][i];
                    }
                }

            }
        });
    };
    this.putVM = function () {

        var req = ClientTSNW.put(pippo.resource, pippo.newvm);
        console.log(req);
        $rootScope.$on("putPerformed", function (event, mass) {
            if (req === mass[0]) {
                console.log(ClientTSNW.show(mass[0]));
            }
        });
    };
});

app.controller("PostController", function (ClientTSNW, $rootScope) {
    this.so = [];
    this.resource = "/tmpmilestones";
    this.vm = [];
    this.newvm = {};
    this.fields = [];
    this.ord = 'editable';
    var pippo = this;
    this.getVM = function () {
        var req = ClientTSNW.get(pippo.resource);
        console.log(req);
        $rootScope.$on("getPerformed", function (event, mass) {
            if (req === mass[0]) {
                this.vm = [];
                this.newvm = {};
                this.fields = [];
                console.log(ClientTSNW.show(mass[0]));
                pippo.vm = ClientTSNW.show(mass[0]);
                for (var i in pippo.vm["result"]["fields"]) {
                    if (pippo.vm["result"]["fields"][i])
                        pippo.fields.push(pippo.vm["result"]["fields"][i]);
//                    if (pippo.vm["data"]["result"]["fields"][i]["editable"])
                    pippo.newvm[pippo.vm["result"]["fields"][i]["name"]] = "";
                }
            }
        });
    };
    this.postVM = function () {

        var req = ClientTSNW.post(pippo.resource, pippo.newvm);
        console.log(req);
        $rootScope.$on("postPerformed", function (event, mass) {
            if (req === mass[0]) {
                console.log(ClientTSNW.show(mass[0]));
            }
        });
    };
});
app.controller("MainController", function (ClientTSNW, $rootScope) {
    this.so = [];
    this.resource = "/accounts";
    this.resourceid = "1233";
    this.payload = [];
    this.payload[0] = {"chiave": "", "valore": ""};

    this.adjustPayload = function () {
        if (this.payload.length === 1) {
            if (this.payload[0].chiave !== "" || this.payload[0].valore !== "")
                this.payload[1] = {"chiave": "", "valore": ""};
        }
        else {
            var le = this.payload.length;
            if (this.payload[le - 1].chiave !== "" || this.payload[le - 1].valore !== "")
                this.payload[le] = {"chiave": "", "valore": ""};
        }
    }
    var pippo = this;
    this.sendRequest = function () {
        var payload = {};
        for (var i in pippo.payload) {
            if (pippo.payload[i].chiave === "" && pippo.payload[i].valore === "") {
                pippo.payload.splice(i, 1);
            }
            else {
                payload[pippo.payload[i].chiave] = pippo.payload[i].valore;
            }
        }
        if (pippo.payload.length === 0) {
            pippo.payload[0] = {"chiave": "", "valore": ""};
        }
        var req = ClientTSNW.get(pippo.resource, pippo.resourceid, payload);
        $rootScope.$on("getPerformed", function (event, mass) {
            if (req === mass[0]) {
                pippo.so = ClientTSNW.show(mass[0]);
            }
        });

    };

}


);