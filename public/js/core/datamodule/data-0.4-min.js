var DATAMODULE=angular.module("DATAMODULE",[],["$interpolateProvider",function(a){a.startSymbol("--__");a.endSymbol("__--")}]);DATAMODULE.provider("DataService",function(){this.conf={};this.conf.loaded=false;this.conf.config=[];this.hash=[];var a=this;this.randomString=function(d){var e="";var b="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";for(var c=0;c<d;c++){e+=b.charAt(Math.floor(Math.random()*b.length))}return e};this.setTicket=function(){var b=true;while(b){var c=this.randomString(10);if(typeof(this.hash[c])==="undefined"){b=false}}this.hash[c]={status:"created",result:false,response:{},count:-1,downloaded:-1,request:{},isDeferred:false,cTimeStamp:new Date().getTime(),chunckLength:100};this.registeredServices=[];this.ping=[];this.pingRef=null;return[c,this.hash[c]]};this.$get=["$rootScope","$http","$interval","$timeout",function(b,f,e,c){var d=this;this.countRequest=function(h){var g=jQuery.extend(true,{},d.hash[h].request);g.headers["X-DeferredResponse-Count"]=true;f(g).success(function(k,i,l,j){if(typeof(k.length)!=="undefined"&&!(isNaN(k[0].count))){d.hash[h].count=Number(k[0].count);d.hash[h].downloaded=0;b.$emit("countPerformed "+h,{})}else{d.hash[h].status="internal_error: count is not a number";d.hash[h].result=false}}).error(function(k,i,l,j){d.hash[h].status=i;d.hash[h].result=false})};this.partialRequest=function(i,g){var h=jQuery.extend(true,{},d.hash[i].request);h.headers["X-DeferredResponse-Limit"]=g[0]+"|"+g[1];f(h).success(function(l,j,m,k){d.hash[i].lastResTimestamp=new Date().getTime();if(typeof(d.hash[i].response.data)==="undefined"){d.hash[i].response.data=[]}d.hash[i].response.data=d.hash[i].response.data.concat(l);d.hash[i].status=j;d.hash[i].downloaded+=l.length;b.$emit("partialRequestPerformed "+i,{limit:g})}).error(function(l,j,m,k){d.hash[i].status=j;d.hash[i].result=false})};return{find:function(g){if(typeof(d.hash[g])!=="undefined"){return[g,d.hash[g]]}else{return null}},makePing:function(){var g=document.URL;g=g.split("/public/");g=g[0]+"/public/ping";var i=new Date();var h=f.get(g).success(function(m,k,o,l){var j=new Date();var n=(j.getTime()-i.getTime())}).error(function(l,j,m,k){})},initialize:function(i,g,h){if(typeof(i)==="undefined"&&d.conf.loaded){return true}else{if(typeof(i)==="undefined"&&!d.conf.loaded){return false}else{var j=f.get(i).success(function(m,k,n,l){d.conf.config=m;d.conf.loaded=true;b.$emit("confLoaded",[]);if(typeof(g)==="function"){return g()}}).error(function(m,k,n,l){b.$emit("confFailure",[]);if(typeof(h)==="function"){return h()}});return true}}},getStaticResource:function(l,n,o,g,j,k){if(d.conf.loaded){var h=d.conf.config.services[l];if(!h||typeof(h)!=="string"){console.log("Errore - servizio "+l+" non disponibile");return}else{var m=d.setTicket();m[1].request={method:"GET",url:h,params:n,headers:{"Content-Type":"application/x-www-form-urlencoded"}};m[1].status="pending";m[1].reqTimestamp=new Date().getTime();var i=f(m[1].request).success(function(r,p,s,q){if(typeof(r.length)!=="undefined"){m[1].count=r.length}m[1].lastResTimestamp=new Date().getTime();m[1].response.data=r;m[1].status=p;m[1].result=true;m[1].message={message:o,messagePars:g};if(typeof(o)!=="undefined"){if(typeof(g)==="undefined"||!g){g={ticket:m[0]}}else{g.ticket=m[0]}b.$emit(o,g)}if(typeof(j)==="function"){j(m)}}).error(function(r,p,s,q){m[1].lastResTimestamp=new Date().getTime();m[1].response=r;m[1].status=p;m[1].result=false;if(typeof(o)!=="undefined"){if(g){g={result:"failure"}}b.$emit(o,g)}if(typeof(k)==="function"){k(m)}});return i}}else{console.log("Missing configuration!");return false}},getData:function(l,n,o,g,j,k){if(d.conf.loaded){var h=d.conf.config.services[l];if(!h||typeof(h)!=="string"){console.log("Errore - servizio "+l+" non disponibile");return}else{var m=d.setTicket();m[1].request={method:"GET",url:h,params:n,headers:{"Content-Type":"application/x-www-form-urlencoded"}};m[1].status="pending";m[1].reqTimestamp=new Date().getTime();var i=f(m[1].request).success(function(r,p,s,q){if(typeof(r.length)!=="undefined"){m[1].count=r.length}m[1].lastResTimestamp=new Date().getTime();m[1].response.data=r;m[1].status=p;m[1].result=true;m[1].message={message:o,messagePars:g};if(typeof(o)!=="undefined"){if(typeof(g)==="undefined"||!g){g={ticket:m[0]}}else{g.ticket=m[0]}b.$emit(o,g)}if(typeof(j)==="function"){j(m)}}).error(function(r,p,s,q){m[1].lastResTimestamp=new Date().getTime();m[1].response=r;m[1].status=p;m[1].result=false;if(typeof(o)!=="undefined"){if(g){g={result:"failure"}}b.$emit(o,g)}if(typeof(k)==="function"){k(m)}});return m}}else{console.log("Missing configuration!");return false}},getDeferredData:function(m,l,j,i,g){if(d.conf.loaded){var h=d.conf.config.services[m];if(!h||typeof(h)!=="string"){console.log("Errore - servizio "+m+" non disponibile");return}else{var k=d.setTicket();k[1].request={method:"GET",url:h,headers:{"Content-Type":"application/x-www-form-urlencoded","X-DeferredResponseReady":true}};k[1].isDeferred=true;k[1].status="pending";k[1].reqTimestamp=new Date().getTime();k[1].message={message:j,messagePars:i};d.countRequest(k[0]);b.$on("countPerformed "+k[0],function(p,o){k[1].chunks=[];var q=parseInt(k[1].count/k[1].chunckLength);if(q*k[1].chunckLength<k[1].count){q++}for(var n=0;n<q;n++){k[1].chunks[n]=n*k[1].chunckLength}d.partialRequest(k[0],[k[1].chunks.shift(),k[1].chunckLength]);d.partialRequest(k[0],[k[1].chunks.shift(),k[1].chunckLength]);d.partialRequest(k[0],[k[1].chunks.shift(),k[1].chunckLength])});b.$on("partialRequestPerformed "+k[0],function(p,o){var n=o.limit;if(k[1].downloaded<k[1].count){if(k[1].chunks.length){d.partialRequest(k[0],[k[1].chunks.shift(),k[1].chunckLength])}}else{if(k[1].downloaded>=k[1].count){}}});return k}}else{console.log("Missing configuration!");return false}},getDeferredData2:function(n,m,g,l,j,h){if(typeof(h)==="undefined"){h=false}g.result="pending";if(d.conf.loaded){var i=d.conf.config.services[n];if(!i||typeof(i)!=="string"){console.log("Errore - servizio "+n+" non disponibile");return}else{var k=f({method:"GET",url:i,headers:{"Content-Type":"application/x-www-form-urlencoded","X-DeferredResponseReady":true}}).success(function(r,o,s,p){if(h){var t=[];for(var q in r){t.push({index:q,value:r[q]})}g.arrayData=t}g.data=r;g.result=true;if(typeof(l)!=="undefined"){if(typeof(j)==="undefined"||!j){j={ticket:ticket[0]}}else{j.ticket=ticket[0]}b.$emit(l,j)}}).error(function(q,o,r,p){if(typeof(l)!=="undefined"){if(j){j={result:"failure"}}b.$emit(l,j)}})}}else{console.log("Missing configuration!");return false}},postData:function(m,h,o,g,k,l){if(d.conf.loaded){var i=d.conf.config.services[m];if(!i||typeof(i)!=="string"){console.log("Errore - servizio "+m+" non disponibile");return}else{var n=d.setTicket();n[1].request={method:"POST",url:i,data:$.param(h),headers:{"Content-Type":"application/x-www-form-urlencoded"}};n[1].status="pending";n[1].reqTimestamp=new Date().getTime();n[1].message={message:o,messagePars:g};var j=f(n[1].request).success(function(r,p,s,q){if(typeof(r.length)!=="undefined"){n[1].count=r.length}n[1].lastResTimestamp=new Date().getTime();n[1].response.data=r;n[1].status=p;n[1].result=true;if(typeof(o)!=="undefined"){if(typeof(g)==="undefined"||!g){g={ticket:n[0]}}else{g.ticket=n[0]}b.$emit(o,g)}if(typeof(k)==="function"){k(n)}}).error(function(r,p,s,q){n[1].lastResTimestamp=new Date().getTime();n[1].response=false;n[1].status=p;n[1].result=false;n[1].errorData=r;if(typeof(o)!=="undefined"){if(g){g={result:"failure"}}b.$emit(o,g)}if(typeof(l)==="function"){l(n)}});return n}}else{console.log("Missing configuration!");return false}}}}]});