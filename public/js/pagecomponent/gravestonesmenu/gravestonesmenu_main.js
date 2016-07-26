$(document).ready(function() {
    $(".gravestonesMenuPage").hide();
    $(".gravestonesMenuPage").eq(0).show(100);
    $(".gravestonesMenu").attr("page", "0");
});

$(document).delegate(".gravestonesNavRight", "click", function() {

    var page = $(".gravestonesMenu").attr("page");
    page = Number(page);
    console.log("avanti " + page);
    var numpages = $(".gravestonesMenuPage").length;
    console.log("pagine " + numpages);
    if (page === numpages - 1)
        return;

    $(".gravestonesMenuPage").hide();
    $(".gravestonesMenuPage").eq(page + 1).show(100);
    $(".gravestonesMenu").attr("page", page + 1);
});
$(document).delegate(".gravestonesNavLeft", "click", function() {
    var page = $(".gravestonesMenu").attr("page");
    page = Number(page);
    console.log("indietro " + page);
    if (page === 0)
        return;
    $(".gravestonesMenuPage").hide();
    $(".gravestonesMenuPage").eq(page - 1).show(100);
    $(".gravestonesMenu").attr("page", page - 1);
});


$(document).delegate(".gravestone", "click", function() {
    if (clickUrl !== "") {
        var gravestone = $(this);
        var id = gravestone.attr("id").replace("gravestone_","");
        console.log(gravestone.attr("id"));
        var partUrl = clickUrl.replace("{{Pk project}}",id);
        window.location.href = partUrl;
    }
});

