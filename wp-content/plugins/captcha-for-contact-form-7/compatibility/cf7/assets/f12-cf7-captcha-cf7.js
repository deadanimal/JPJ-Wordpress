/**
 * Reload all Captchas
 * This will regenerate the Hash and the Captcha Value
 */
window.f12cf7captcha_cf7 = {
    /**
     * Reload Captchas
     */
    reloadCaptcha: function() {
        jQuery(document).find('.f12c').each(function(){
            var input_id = jQuery(this).attr('id');
            var hash_id = input_id+'_hash';
            var hash = jQuery('#'+hash_id);
            var label = jQuery('#'+input_id).parent().find('label');
            var method = jQuery(this).attr('data-method');

            jQuery.ajax({
                type: 'POST',
                url: f12_cf7_captcha.ajaxurl,
                data: {
                    action: 'f12_cf7_captcha_reload',
                    captchamethod: method
                },
                success: function(data, textStatus, XMLHttpRequest){
                    data = JSON.parse(data);
                    label.html(data.label);
                    hash.val(data.hash);
                },
                error:function (XMLHttpRequest, textstatus, errorThrown){
                    console.log(errorThrown);
                }
            });
        });
    },
    /**
     * Reload Timer
     */
    reloadTimer: function(){
        jQuery(document).find('.f12t').each(function(){
            var fieldname = 'f12_timer';
            var field = jQuery(this).find('.'+fieldname);

            jQuery.ajax({
                type: 'POST',
                url: f12_cf7_captcha.ajaxurl,
                data: {
                    action: 'f12_cf7_captcha_timer_reload'
                },
                success: function(data, textStatus, XMLHttpRequest){
                    data = JSON.parse(data);
                    field.val(data.hash);
                },
                error:function (XMLHttpRequest, textstatus, errorThrown){
                    console.log(errorThrown);
                }
            });
        });
    },
    /**
     * Init
     */
    init: function(){
        /**
         * Add Event Listener from Contact Form 7
         */
        var wpcf7Elm = document.querySelector('.wpcf7');

        if(typeof(wpcf7Elm) === 'undefined' || wpcf7Elm === null){
            return;
        }

        wpcf7Elm.addEventListener('wpcf7mailsent', function(event){
            window.f12cf7captcha_cf7.reloadCaptcha();
            window.f12cf7captcha_cf7.reloadTimer();
        }, false);

        wpcf7Elm.addEventListener('wpcf7submit', function(event){
            window.f12cf7captcha_cf7.reloadCaptcha();
            window.f12cf7captcha_cf7.reloadTimer();
        }, false);
    }
}

window.f12cf7captcha_cf7.init();