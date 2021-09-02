/**
 * Archivo views/_theme/js/tabs.js
 * 
 * Sistema de tabs
 * 
 * @copyright (c) 2020
 * @author Edison Ataucusi Romero <eataucusis@gmail.com>
 * @license http://creativecommons.org/licenses/by-sa/4.0/ Licencia Creative Commons Atribución-CompartirIgual 4.0 Internacional
 */


$(function () {
    $('.ear-tab .tabs .col').on('click', 'a', function (evt) {
        if ($(this).parent('.disabled').length) {
            evt.preventDefault();
        } else {
            var current = $(this).parent().index();
            $('.ear-tab .tabs .current').removeClass('current');
            $(this).parent().addClass('current');
            $('.ear-tab .tab-content').removeClass('current').hide();
            $('.ear-tab .tab-content').eq(current).fadeIn();
        }
    });
    var tab = window.location.hash;
    if (tab != '') {
        n = $('.ear-tab .tabs a[href$=\\' +tab+']').parent().addClass('current').index();
        $('.ear-tab .tab-content').eq(n).fadeIn();
    } else {
        $('.ear-tab .tabs .col:first-child').addClass('current');
        $('.ear-tab .tab-content:first-child').fadeIn();
    }

});