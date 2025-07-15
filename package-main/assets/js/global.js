/**
 * Project pack javascript
 *
 * @package mealprep
 */

;( function( w, $ ) {
    'use strict';

    var searchNav = function(){
      var btnSearch = $('.header-search');
      var btnBackHome = $('.top-search');

      btnSearch.on('click',function(){
        $.magnificPopup.open({
          items: {
            src: '#ins_form_search'
          },
          type: 'inline',
          closeBtnInside: true
        });
      });

      btnBackHome.on('click',function(){
        $('.ins-search-form-default .mfp-close').trigger('click')
      });
    }


    var megaMenu = function(){
      var megamenu = $('.wpmm_mega_menu');
      var screenWidth = $(window).width();
      $('.style-megamenu').remove();
      megamenu.each(function( index ) {
        var x = $(this).offset();
        var id = $(this).attr('id');
        var megamenu = $(this).find('.wp-megamenu-sub-menu');
        var spacingWhite = (screenWidth - megamenu.width()) / 2;
        var leftArrow = (x.left - (spacingWhite - 100));
        $('body').append('<div class="style-megamenu"><style>#'+id+'>.wp-megamenu-sub-menu::after{left:'+leftArrow+'px !important}</style></div>');
      });
    }

    $(window).on('load',function(){
      searchNav();
      megaMenu();
    });

    $(window).on('resize',function(){
      megaMenu();
    });


} )( window, jQuery )
