<?php

namespace Swace\AcfPlaceholder\UI;

class SettingsScreen
{
    /**
     * Register the requred hooks for the admin screen
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'addMenu']);
    }

    /**
     * Register an tools/management menu for the admin area
     *
     * @return void
     */
    public static function addMenu()
    {
        add_options_page(
            'Acf Placeholder (Settings)',
            'Acf Placeholder',
            'manage_options',
            'swace-acf-placeholder-settings',
            [__CLASS__, 'renderPage']
        );
    }

    /**
     * Render the management/tools page
     *
     * @return void
     */
    public static function renderPage()
    {
        ?><div class="wrap">

            <h2><?= get_admin_page_title(); ?></h2>
            
            <form method="post" action="<?= esc_url(admin_url('options.php')); ?>">
                <?php

                settings_fields(SWACE_ACF_PLACEHOLDER_OPTIONS_KEY);
                do_settings_sections(SWACE_ACF_PLACEHOLDER_OPTIONS_KEY);

                submit_button('Save Settings', 'primary', 'submit', false);

                $uri = wp_nonce_url(
                    admin_url('admin.php?page=swace-acf-placeholder-settings&action=acf-placeholder-trigger'),
                    'swace_acf_placeholder_trigger',
                    'swace_acf_placeholder_trigger'
                );

                ?>
            </form>

        </div><?php
    }
}
