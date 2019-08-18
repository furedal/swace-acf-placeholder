<?php

/**
 * Plugin Name: Swace Acf Placeholder
 * Description: A WordPress plugin for populating placeholders for each post-type making integration with frameworks such as Gatsby easier.
 * Author: Furedal
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('SWACE_ACF_PLACEHOLDER_FILE', __FILE__);
define('SWACE_ACF_PLACEHOLDER_PATH', untrailingslashit(plugin_dir_path(__FILE__)));
define('SWACE_ACF_PLACEHOLDER_URL', untrailingslashit(plugin_dir_url(__FILE__)));

require_once (SWACE_ACF_PLACEHOLDER_PATH.'/src/App.php');

Swace\AcfPlaceholder\App::instance();
