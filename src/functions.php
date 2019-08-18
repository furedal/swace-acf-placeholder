<?php

if (!function_exists('swace_placeholder_get_options')) {
    /**
     * Return the plugin settings/options
     *
     * @return array
     */
    function swace_placeholder_get_options()
    {
        return get_option(SWACE_ACF_PLACEHOLDER_OPTIONS_KEY);
    }
}

include_once ABSPATH . 'wp-admin/includes/plugin.php';
if (is_plugin_active('advanced-custom-fields-pro/acf.php')) {
    $options = swace_placeholder_get_options();
    if (!empty($options['display_placeholders']) && $options['display_placeholders'] !== 'true' && !function_exists('hide_acf_dummy')) {
        function hide_acf_dummy($query)
        {
            $dummy = new \Swace\AcfPlaceholder\Generator();
            $dummy->hide_dummy_from_query($query);
        }
        add_filter('parse_query', 'hide_acf_dummy');
    }
}