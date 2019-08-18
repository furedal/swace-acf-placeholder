<?php

namespace Swace\AcfPlaceholder;

class Settings
{
    /**
     * Setup required hooks for the Settings
     *
     * @return void
     */
    public static function init()
    {
        add_action('admin_init', [__CLASS__, 'register']);
    }

    /**
     * Register settings & fields
     *
     * @return void
     */
    public static function register()
    {
        $key = SWACE_ACF_PLACEHOLDER_OPTIONS_KEY;

        register_setting($key, $key, [__CLASS__, 'sanitize']);
        add_settings_section('general', 'General', '__return_empty_string', $key);
        
        // ...

        $option = swace_placeholder_get_options();

        add_settings_field('excluded_post_types', 'Excluded Post Types', ['Swace\AcfPlaceholder\Field', 'checkboxes'], $key, 'general', [
            'name' => "{$key}[excluded_post_types]",
            'value' => isset($option['excluded_post_types']) ? $option['excluded_post_types'] : [],
            'choices' => self::getPostTypes(),
            'description' => 'Select post types that does not require placeholders',
            'legend' => 'Excluded Post Types'
        ]);
    }

    /**
     * Get an array of post types in name > label format
     *
     * @return array
     */
    protected static function getPostTypes()
    {
        $return = [];

        $filterDefaultChoices = ['page', 'attachment'];

        $defaultChoices = get_post_types(['show_in_rest' => true, '_builtin' => true], 'objects');
        foreach ($defaultChoices as $choice) {
            if (in_array($choice->name, $filterDefaultChoices)) {
                $return[$choice->name] = $choice->labels->name;
            }
        }

        $choices = get_post_types(['show_in_rest' => true, '_builtin' => false], 'objects');
        foreach ($choices as $choice) {
            $return[$choice->name] = $choice->labels->name;
        }

        return $return;
    }

    /**
     * Sanitize user input
     *
     * @var array $input
     * @return array
     */
    public static function sanitize($input)
    {
        if (!isset($input['excluded_post_types']) || !is_array($input['excluded_post_types'])) {
            $input['excluded_post_types'] = [];
        }

        return $input;
    }
}
