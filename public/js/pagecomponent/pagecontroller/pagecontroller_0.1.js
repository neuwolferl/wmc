/*
 * PAGE CONTROLLER 
 */

function wmvcPageController_ioService(pageComponentId){
    this.putData = function(){};    
    this.getData = function(){};    
}
function wmvcPageController_registerService(pageComponentId){
    var stor = localStorage.getItem("wmvc_pageComponent_"+pageComponentId);
        if (stor === null){
            //registro la componente
            stor = new Object();
            stor.id = pageComponentId;
            stor.dataVersion = 0;
            localStorage.setItem("wmvc_pageComponent_"+pageComponentId,JSON.stringify(stor));
        }
        else {
            
        }
    this.saveData = function(){
        
    };
    this.flushData = function(){};
}



$(document).ready(function(){
    
});