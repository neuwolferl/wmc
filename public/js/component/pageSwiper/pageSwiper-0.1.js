var pageSwiperModule = angular.module("PAGESWIPER", ['ngAnimate'], ['$interpolateProvider',
    function ($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    }]);


pageSwiperModule.directive("pageSwiper", [function () {
        var version = "0.1";
        return {
            templateUrl: function () {
                var baseurl = document.URL;
                baseurl = baseurl.split("public");
                return baseurl[0] + "public/js/component/pageSwiper/pageSwiper-" + version + ".html";
            },
            restrict: 'E',
//            replace: true,
            scope: {
                conf: "="
            },
            transclude: true,
            link: function (scope, element, attrs) {
                console.log("LINK");
                console.log(attrs);
                var swiper = element.find(".pageSwiper");
                
               
                if (!scope.conf)
                    return;
                if (scope.conf.width){
                    swiper.css("width",scope.conf.width);
                }
                if (scope.conf.height){
                    if (scope.conf.height === "visible"){
                        var winh = $(window).height();
                        var top = swiper.offset().top;
                        console.log(winh,top);
                        swiper.css("height", (winh-top)+"px");
                    }
                    else
                        swiper.css("height",scope.conf.height);
                }
                swiper.children().each(function(){
                   if (!$(this).hasClass("swipe-page")){
                       $(this).remove();
                   }
                   $(this).css("width","100%");
                   $(this).css("height","100%");
                   
                });
            },
            controller: ['$scope', '$element', '$attrs', function ($scope, $element, $attrs) {

                }]
        };
    }]);

