<?php

namespace Swace\AcfPlaceholder;

class PlaceholderTrigger
{
    /**
     * Setup hooks for triggering the webhook
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_bar_menu', [__CLASS__, 'adminBarTriggerButton']);

        add_action('admin_footer', [__CLASS__, 'adminBarCssAndJs']);
        add_action('wp_footer', [__CLASS__, 'adminBarCssAndJs']);
        
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueScripts']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueueScripts']);

        add_action('wp_ajax_wp_swace_placeholder_manual_trigger', [__CLASS__, 'ajaxTrigger']);
    }

    /**
     * Show the admin bar css & js
     * 
     * @todo move this somewhere else
     * @return void
     */
    public static function adminBarCssAndJs()
    {
        if (!is_admin_bar_showing()) {
            return;
        }

        ?><style>

        #wpadminbar .swace-acf-placeholder-button > a {
            background-color: rgba(255, 255, 255, .2) !important;
            color: #FFFFFF !important;
        }
        #wpadminbar .swace-acf-placeholder-button > a:hover,
        #wpadminbar .swace-acf-placeholder-button > a:focus {
            background-color: rgba(255, 255, 255, .25) !important;
        }

        #wpadminbar .swace-acf-placeholder-button svg {
            width: 12px;
            height: 12px;
            margin-left: 5px;
        }

        #wpadminbar .swace-acf-placeholder-badge > .ab-item {
            display: flex;
            align-items: center;
        }

        </style><?php
    }

    /**
     * Enqueue js to the admin & frontend
     * 
     * @return void
     */
    public static function enqueueScripts()
    {
        wp_enqueue_script(
            'swace-acf-placeholder-adminbar',
            SWACE_ACF_PLACEHOLDER_URL.'/assets/admin.js',
            ['jquery'],
            filemtime(SWACE_ACF_PLACEHOLDER_PATH.'/assets/admin.js')
        );

        $button_nonce = wp_create_nonce('swace-acf-placeholder-button-nonce');

        wp_localize_script('swace-acf-placeholder-adminbar', 'swace_acf_placeholder_nonce', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'placeholder_button_nonce' => $button_nonce,
        ]);
    }

    /**
     * Add a "trigger webhook" button to the admin bar
     *
     * @param object $bar
     * @return void
     */
    public static function adminBarTriggerButton($bar)
    {
        $bar->add_node([
            'id' => 'swace-acf-placeholder',
            'title' => 'Sync ACF Placeholders',
            'parent' => 'top-secondary',
            'href' => 'javascript:void(0)',
            'meta' => [
                'class' => 'swace-acf-placeholder-button'
            ]
        ]);
    }

    /**
     * Trigger a request manually from the admin settings
     *
     * @return void
     */
    public static function ajaxTrigger()
    {
        check_ajax_referer('swace-acf-placeholder-button-nonce', 'security');
        self::triggerPlaceholder();
        echo 1;
        exit;
    }

    public static function triggerPlaceholder() {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        if (is_plugin_active('advanced-custom-fields-pro/acf.php')) {
            $dummy = new \Swace\AcfPlaceholder\Generator();
            $dummy->init();
        }
    }
}
