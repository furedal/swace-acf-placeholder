;(function ($) {
    $(function () {
        $('.swace-acf-placeholder-button').click(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: wpjd.ajaxurl,
                data: {
                    action: 'wp_swace_placeholder_manual_trigger',
                    security: wpjd.placeholder_button_nonce,
                },
                dataType: 'json',
                success: function(res) {
                    console.log('Acf placeholder sync success', res);
                    alert("Acf placeholder sync success");
                },
            });
        });

    });
})(jQuery, window, document);
