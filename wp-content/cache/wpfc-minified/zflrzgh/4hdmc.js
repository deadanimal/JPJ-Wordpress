// source --> http://10.180.0.219/wp-content/plugins/ht-mega-for-elementor/assets/extensions/ht-menu/js/htmegamenu-main.js?ver=6.1.1 
(function ($) {
    "use strict";

    $(document).ready(function () {
        headermobileAside();
    });

    /*====== mobile off canvas active ======*/
    function headermobileAside() {
        var navbarTrigger = $('.htmobile-aside-button'),
            endTrigger = $('.htmobile-aside-close'),
            container = $('.htmobile-menu-wrap'),
            wrapper = $('#page');
        
        wrapper.prepend('<div class="htbody-overlay"></div>');
        
        navbarTrigger.on('click', function(e) {
            e.preventDefault();
            container.addClass('inside');
            wrapper.addClass('htoverlay-active');
        });
        
        endTrigger.on('click', function() {
            container.removeClass('inside');
            wrapper.removeClass('htoverlay-active');
        });
        
        $('.htbody-overlay').on('click', function() {
            container.removeClass('inside');
            wrapper.removeClass('htoverlay-active');
        });


        var $offCanvasNav = $('.htmobile-navigation'),
        $offCanvasNavSubMenu = $offCanvasNav.find('.htmegamenu-content-wrapper,.sub-menu');
    
        /*Add Toggle Button With Off Canvas Sub Menu*/
        $offCanvasNavSubMenu.parent().prepend('<span class="menu-expand"><i class="fa fa-plus"></i></span>');
        
        /*Close Off Canvas Sub Menu*/
        $offCanvasNavSubMenu.slideUp();
        
        /*Category Sub Menu Toggle*/
        $offCanvasNav.on('click', 'li a, li .menu-expand', function(e) {
            var $this = $(this);

            if ( ($this.parent().attr('class').match(/\b(htmega_mega_menu)\b/)) && ($this.attr('href') === '#' || $this.hasClass('menu-expand')) ) {
                e.preventDefault();
                if ($this.siblings('div:visible').length){
                    $this.parent('li').removeClass('active');
                    $this.siblings('div').slideUp();
                } else {
                    $this.parent('li').addClass('active');
                    $this.closest('li').siblings('li').removeClass('active').find('li').removeClass('active');
                    $this.closest('li').siblings('li').find('div:visible').slideUp();
                    $this.closest('li').siblings('li').find('ul:visible').slideUp();
                    $this.siblings('div').slideDown();
                }
            }else{
                if ( ($this.parent().attr('class').match(/\b(menu-item-has-children|has-children|has-sub-menu)\b/)) && ($this.attr('href') === '#' || $this.hasClass('menu-expand')) ) {
                    e.preventDefault();
                    if ($this.siblings('ul:visible').length){
                        $this.parent('li').removeClass('active');
                        $this.siblings('ul').slideUp();
                    } else {
                        $this.parent('li').addClass('active');
                        $this.closest('li').siblings('li').removeClass('active').find('li').removeClass('active');
                        $this.closest('li').siblings('li').find('ul:visible').slideUp();
                        $this.closest('li').siblings('li').find('div:visible').slideUp();
                        $this.siblings('ul').slideDown();
                    }
                }
            }

        });


    };
    


})(jQuery);