function Simplemenu(pageComponentId){
    this.pageComponentId = pageComponentId;
    this.data = {};
    this.render = function(){
        //identifico il container
        var container = $("#wmvc_pageComponent_"+pageComponentId+"_container");
        if (container.length !== 1){
            console.log("An error occurred: Page Component #"+pageComponentId+" was not found in the DOM");
        }
        container.html(pageComponentId);
    }
}