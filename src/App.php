<?php

namespace Swace\AcfPlaceholder;

use Swace\AcfPlaceholder\UI\SettingsScreen;
use Swace\AcfPlaceholder\PlaceholderTrigger;
use Swace\AcfPlaceholder\Settings;

class App
{
    /**
     * Singleton instance
     * 
     * @var null|App
     */
    protected static $instance = null;

    /**
     * Create a new singleton instance
     * 
     * @return App
     */
    public static function instance()
    {
        if (!is_a(App::$instance, App::class)) {
            App::$instance = new App;
        }

        return App::$instance;
    }

    /**
     * Bootstrap the plugin
     * 
     * @return void
     */
    protected function __construct()
    {
        $this->constants();
        $this->includes();
        $this->hooks();
    }

    /**
     * Register constants
     *
     * @return void
     */
    protected function constants()
    {
        define('SWACE_ACF_PLACEHOLDER_OPTIONS_KEY', 'wp_swace_placeholder');
    }

    /**
     * Include/require files
     *
     * @return void
     */
    protected function includes()
    {
        require_once (SWACE_ACF_PLACEHOLDER_PATH.'/src/UI/SettingsScreen.php');

        require_once (SWACE_ACF_PLACEHOLDER_PATH.'/src/Settings.php');
        require_once (SWACE_ACF_PLACEHOLDER_PATH.'/src/Generator.php');
        require_once (SWACE_ACF_PLACEHOLDER_PATH.'/src/PlaceholderTrigger.php');
        require_once (SWACE_ACF_PLACEHOLDER_PATH.'/src/Field.php');

        require_once (SWACE_ACF_PLACEHOLDER_PATH.'/src/functions.php');
    }

    /**
     * Register actions & filters
     *
     * @return void
     */
    protected function hooks()
    {
        register_activation_hook(SWACE_ACF_PLACEHOLDER_FILE, [$this, 'activation']);
        register_deactivation_hook(SWACE_ACF_PLACEHOLDER_FILE, [$this, 'deactivation']);

        SettingsScreen::init();
        Settings::init();
        PlaceholderTrigger::init();
    }

    /**
     * Fires on plugin activation
     *
     * @return void
     */
    public function activation()
    {
        
    }

    /**
     * Fires on plugin deactivation
     *
     * @return void
     */
    public function deactivation()
    {

    }
}
