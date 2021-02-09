define([
    'jquery',
    'owlcarouselslider'
], function ($) {
    'use strict';
   
    return function(config) {
     

        $('.owl-carousel').owlCarousel({
            loop: true,
            margin: 10,
            dots:true,
            autoplay              :true ,
            autoplayTimeout       :4000,
            autoplayHoverPause    :true,
            responsive: {
                0: {
                    items:1
                },
                570: {
                    items:config.count<2?config.count:2,
                    nav: config.count<2?true:false, 
                    loop:config.count<2?false:true,
                },
                846: {
                    items:config.count<3?config.count:3,
                    nav: config.count<3?true:false, 
                    loop:config.count<3?false:true,
                },
                1122: { 
                    items:config.count<4?config.count:4,
                    nav: config.count<4?true:false, 
                    loop:config.count<4?false:true,
                
                }
            },
        
         
        }); 
    }
});