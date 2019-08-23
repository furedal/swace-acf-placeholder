;(function ($) {
    $(function () {
        $('.swace-acf-placeholder-button').click(function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: swace_acf_placeholder_nonce.ajaxurl,
                data: {
                    action: 'wp_swace_placeholder_manual_trigger',
                    security: swace_acf_placeholder_nonce.placeholder_button_nonce,
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
