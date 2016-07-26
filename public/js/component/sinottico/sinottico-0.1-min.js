var version="0.1";var Sinottico=angular.module("SINOTTICO",["SMARTBUTTON"],["$interpolateProvider",function(a){a.startSymbol("--__");a.endSymbol("__--")}]);Sinottico.directive("calendarioSinottico",["Sin",function(a){return{templateUrl:function(){var b=document.URL;b=b.split("public");return b[0]+"public/js/component/sinottico/sinottico-"+version+".html"},restrict:"E",scope:{calId:"=calid",title:"@title"},controller:["$scope","$element","$attrs","$rootScope","$timeout","SmartButton",function(j,k,i,h,e,c){var g=this;j.btn=c.newBtn();c.getBtn(j.btn[0]).setWhatToShow({enabled:"",disabled:"wait"});c.getBtn(j.btn[0]).setClickFcn(function(){j.btn[1].disable();j.changeLabels()});var f=new Date();j.dateObj={date:f,dateEngString:f.getFullYear()+"-"+(f.getMonth()<9?"0":"")+(f.getMonth()+1)+"-"+(f.getDate()<10?"0":"")+f.getDate(),dateItaString:(f.getDate()<10?"0":"")+f.getDate()+"-"+(f.getMonth()<9?"0":"")+(f.getMonth()+1)+"-"+f.getFullYear()};j.$watch("dateObj.dateEngString",function(p,m){var n=new Date(m);var l=new Date(p);var o=(l.getTime()-n.getTime());j.dateObj.date=l;j.dateObj.dateItaString=(l.getDate()<10?"0":"")+l.getDate()+"-"+(l.getMonth()<9?"0":"")+(l.getMonth()+1)+"-"+l.getFullYear();j.calObj.renderPars.startTime.setTime(j.calObj.renderPars.startTime.getTime()+o);j.calObj.renderPars.endTime.setTime(j.calObj.renderPars.endTime.getTime()+o)});j.calObj=a.getCal(j.calId).getObj();k.find("canvas#calId").attr("id",j.calId);k.find("input#calDate").attr("id",j.calId+"calDate");k.find("div#calInfo").attr("id",j.calId+"calInfo");j.info="";j.$watch("info",function(l,m){k.find("#"+j.calId+"calInfo").html(l)});j.containerStyle={"text-align":"center","vertical-align":"middle",position:"absolute",border:"1px solid purple",top:"10%",left:"10%",width:"80%",heigth:"80%","background-color":"white","z-index":2000};j.labelTicket={};j.dataTicket={};j.labelSignal="";j.getLabels=function(){j.labelSignal=a.getCal(j.calId).getObj().labelGetter(j.labelTicket);h.$on(j.labelSignal,function(n,m){j.calObj.labels=a.getCal(j.calId).getObj().labelRetriever(m.ticket);j.calObj.renderPars.ylabels=[];for(var l in j.calObj.labels){j.calObj.labels[l]={label:j.calObj.labels[l],active:true};j.calObj.renderPars.ylabels.push(j.calObj.labels[l].label)}j.calObj.renderPars.ylabels.sort(function(p,o){return d(p).localeCompare(d(o))});j.calObj.loaded=true})};j.getData=function(){var l=j.calObj.renderPars.ylabels;j.dataSignal=a.getCal(j.calId).getObj().dataGetter({ticket:j.dataTicket,labels:l,date:j.dateObj.dateEngString});h.$on(j.dataSignal,function(n,m){j.calObj.data=a.getCal(j.calId).getObj().dataRetriever(m.ticket);j.calObj.shapeIndexedData=[];b();j.btn[1].enable()})};j.deactivateLabel=function(l){if(j.calObj.labels[l]){j.calObj.labels[l].active=false}};j.activateLabel=function(l){if(j.calObj.labels[l]){j.calObj.labels[l].active=true}};j.changeLabels=function(){j.calObj.renderPars.ylabels=[];for(var l in j.calObj.labels){if(j.calObj.labels[l].active){j.calObj.renderPars.ylabels.push(j.calObj.labels[l].label)}}j.calObj.renderPars.ylabels.sort(function(n,m){return d(n).localeCompare(d(m))});j.getData()};a.getCal(j.calId).setOpener(function(){if(!j.calObj.loaded){j.getLabels()}k.show(500)});j.$watch("calObj.labels.length",function(l,m){if(m===0&&l!=0){j.getData()}});j.$watch("dateObj.date",function(l,m){j.getData()});a.getCal(j.calId).setCloser(function(){k.hide(500)});function d(l){l=l.split(" ");l=l[1].toUpperCase();return l}function b(){var B=[];j.calObj.shapeIndexedData=[];j.calObj.renderPars.startTime.setMinutes(0);j.calObj.renderPars.startTime.setSeconds(0);j.calObj.renderPars.endTime.setMinutes(0);j.calObj.renderPars.endTime.setSeconds(0);var n=j.calObj.renderPars.startTime.getTime();var A=j.calObj.renderPars.endTime.getTime();var q=(j.calObj.renderPars.xmax/(A-n+3600000));for(var y in j.calObj.renderPars.ylabels){var v=j.calObj.renderPars.ylabels[y];var t=d(v);var r=[];for(var x in j.calObj.data){if(j.calObj.data[x].label===v){var p=new Date(j.calObj.data[x].date_start+" "+j.calObj.data[x].time_start);p=p.getTime();var o=new Date(j.calObj.data[x].due_date+" "+j.calObj.data[x].time_end);o=o.getTime();j.calObj.data[x].renderDuration=Math.floor((o-p)*q);j.calObj.data[x].renderStart=Math.floor((p-n)*q);var w;if(j.calObj.renderPars.colorer!==""&&typeof(j.calObj.data[x][j.calObj.renderPars.colorer])!=="undefined"&&typeof(j.calObj.renderPars.colors[j.calObj.data[x][j.calObj.renderPars.colorer]])!=="undefined"){w=j.calObj.renderPars.colors[j.calObj.data[x][j.calObj.renderPars.colorer]]}else{w="white"}j.calObj.shapeIndexedData.push(j.calObj.data[x]);r.push([j.calObj.data[x].renderStart,j.calObj.data[x].renderDuration,null,t,w])}}if(!r.length){j.calObj.shapeIndexedData.push({});r.push([,,null,t,"red"])}B.push(r)}var z=j.calObj.renderPars.endTime.getTime()-j.calObj.renderPars.startTime.getTime();z=1+Math.floor(z/(60*60*1000));var u=Math.floor(j.calObj.renderPars.xmax/z);var l=[];for(var y=0;y<z;y++){var m=j.calObj.renderPars.startTime.getHours()+y;l.push((m<10?"0":"")+m)}var s=[];for(var y=0;y<z;y++){s.push([y*u,u,"rgba(192,255,192,0.5)"]);y++}var C="";if(typeof(j.calObj.renderPars.title.title)==="function"){C=j.calObj.renderPars.title.title(j.calObj,j.dateObj)}else{C=j.calObj.renderPars.title.title}if(j.calObj.drawn){RGraph.Clear(document.getElementById(j.calId));j.gantt.data=[];j.gantt.data=B;j.gantt.Set("title",C);j.gantt.Draw()}else{j.gantt=new RGraph.Gantt(j.calId,B).Set("xmax",j.calObj.renderPars.xmax).Set("gutter.right",j.calObj.renderPars.gutter.right).Set("gutter.left",j.calObj.renderPars.gutter.left).Set("labels",l).Set("title",C).Set("defaultcolor",j.calObj.renderPars.defaultcolor).Set("background.grid",j.calObj.renderPars.background.grid).Set("text.size",j.calObj.renderPars.text.size).Set("vbars",s).Set("chart.borders",j.calObj.renderPars.chart.borders).Set("borders",j.calObj.renderPars.borders).Set("title.vpos",j.calObj.renderPars.title.vpos).Set("labels.align",j.calObj.renderPars.labels.align).Draw();if(j.gantt.reDraw){j.gantt.reDraw()}j.calObj.drawn=true;j.gantt.onmousemove=function(F,D){var E={e:F,shape:D};j.info=j.calObj.shapeInfoDisplayer(j.calObj.getShapeUnderlyingData(D.index));j.$digest()};j.gantt.onclick=function(F,D){var E={e:F,shape:D};j.calObj.onClick(E)}}}}]}}]);Sinottico.provider("Sin",function(){this.randomString=function(c){var d="";var a="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";for(var b=0;b<c;b++){d+=a.charAt(Math.floor(Math.random()*a.length))}return d};this.setTicket=function(){var a=true;while(a){var b=this.randomString(10);if(typeof(this.hash[b])==="undefined"){a=false}}this.hash[b]={open:function(){},close:function(){},labels:[],data:{},shapeIndexedData:[],drawn:false,loaded:false,labelGetter:function(){},labelRetriever:function(){},dataGetter:function(){},dataRetriever:function(){},onMouseMove:function(){},onClick:function(){},shapeInfoDisplayer:function(){},getShapeUnderlyingData:function(c){return this.shapeIndexedData[c]},renderPars:{xmax:900,gutter:{right:35,left:120},defaultcolor:"#faa",background:{grid:false},text:{size:12},vbars:[],chart:{borders:true},borders:false,title:{title:"",vpos:0.6},labels:{labels:[],align:"bottom"},startTime:{},endTime:{},ylabels:[],colors:{},colorer:""}};return[b,this.hash[b]]};this.lock=false;this.hash=[];this.$get=function(){var a=this;return{getCal:function(b){return{setOpener:function(c){a.hash[b].open=function(){c()}},setCloser:function(c){a.hash[b].close=function(){c()}},show:function(){a.hash[b].open()},hide:function(){a.hash[b].close()},canShow:function(){},setLabelGetter:function(c){a.hash[b].labelGetter=function(d){return c(d)}},setLabelRetriever:function(c){a.hash[b].labelRetriever=function(d){return c(d)}},setDataGetter:function(c){a.hash[b].dataGetter=function(d){return c(d)}},setDataRetriever:function(c){a.hash[b].dataRetriever=function(d){return c(d)}},setOnClick:function(c){a.hash[b].onClick=function(d){return c(d)}},setOnMouseMove:function(c){a.hash[b].onMouseMove=function(d){return c(d)}},setData:function(c){},getObj:function(){return a.hash[b]},setShapeInfoDisplayer:function(c){a.hash[b].shapeInfoDisplayer=function(d){return c(d)}}}},getAll:function(){return a.hash},isDefined:function(b){return(typeof(a.hash[b])!=="undefined")},newCal:function(){return a.setTicket()}}}});