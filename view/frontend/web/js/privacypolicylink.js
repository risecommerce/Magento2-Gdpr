define(
    [
        'jquery',
        'mage/cookies'
    ], function ($) {
        'use strict';

        $.widget(
            'mage.privacypolicyLink', {
                _create: function () {
                    $(this.options.privacyLink).attr("href",this.options.dataUrl);
                }
            }
        );

        jQuery(".footer .risecommerce_consent_checkbox").insertAfter('#newsletter-validate-detail .newsletter');
        jQuery(".footer .risecommerce_consent_checkbox div").removeClass('control');
        jQuery(".footer .risecommerce_consent_checkbox label").removeClass('label');
        return $.mage.privacypolicyLink;
    }
);