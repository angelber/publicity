(function ($, Drupal, drupalSettings) {

    'use strict';

    Drupal.behaviors.example = {
        attach: function (context, settings) {

            var dataEntity = drupalSettings.publicity.publicity_var.entityad;
            var idEntity = drupalSettings.publicity.publicity_var.entityad['ad_id'];
            var url = drupalSettings.publicity.publicity_var.entityad['url'];
            var breakpoints = drupalSettings.publicity.publicity_var.data_breakpoints['form'];

            $('.renderAd', context).once('example').each(function () {

                $(this).append('<div id="div-container" class=' + idEntity + '></div>');
                $(' div[class="' + idEntity + '"] ', context).append('<iframe scrolling="no" frameborder="0" src="' + url + '"></iframe>');

                $.each(breakpoints, function (index) {

                    if ($(window).width() <= 420) {
                        if (breakpoints[index]['width'] <= 420) {
                            $(' div[class="' + idEntity + '"] ', context).css({
                                'width': breakpoints[index]['width'],
                            });
                        }
                    }

                    if ($(window).width() > 420 && $(window).width() <= 770) {
                        if (breakpoints[index]['width'] > 420 && breakpoints[index]['width'] <= 770) {
                            $(' div[class="' + idEntity + '"] ', context).css({
                                'width': breakpoints[index]['width'],
                            });
                        }
                    }

                    if ($(window).width() > 770) {
                        if (breakpoints[index]['width'] > 770) {
                            $(' div[class="' + idEntity + '"] ', context).css({
                                'maxWidth': breakpoints[index]['width'],
                            });
                        }
                    }

                });


            });
        }
    };

})(jQuery, Drupal, drupalSettings);