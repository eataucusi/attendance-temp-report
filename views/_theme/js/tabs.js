/** 
 * Archivo views/_theme/js/tabs.js
 * 
 * Sistema de tabs
 * 
 * @license https://creativecommons.org/licenses/by-nc-sa/4.0/deed.es Atribución-NoComercial-CompartirIgual 4.0 Internacional (CC BY-NC-SA 4.0)
 * @author ITEC Andahuaylas. <itec.andahuaylas@gmail.com>
 * @version 1.0 11/02/2020 02:38:53
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