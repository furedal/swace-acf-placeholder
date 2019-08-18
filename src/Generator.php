<?php

namespace Swace\AcfPlaceholder;

class Generator
{

    public function __construct()
    {
        $this->dummy_title = 'SWACE_PLACEHOLDER';
        $this->dummy_cache = [];
        $this->wpml_languages = [];

        global $sitepress;
        $languages = apply_filters('wpml_active_languages', null);
        if (!empty($languages) && !empty($sitepress)) {
            $default_lang = $sitepress->get_default_language();
            foreach ($languages as $lang => $val) {
                if ($lang !== $default_lang) {
                    $this->wpml_languages[] = $lang;
                }
            }
        }
    }

    private function getDummyID($type)
    {
        if (empty($type)) {
            return 0;
        }
        if (empty($this->dummy_cache[$type])) {
            $this->dummy_cache[$type] = get_page_by_title($this->dummy_title, OBJECT, $type);
        }
        return $this->dummy_cache[$type] !== null ? $this->dummy_cache[$type]->ID : 0;
    }

    private function getPostTypes()
    {
        if (empty($this->types)) {
            $this->types = get_post_types(['show_in_rest' => true, '_builtin' => false]);
            $this->types['page'] = 'page';
            $this->types['attachment'] = 'attachment';

            $excluded_post_types = swace_placeholder_get_options()['excluded_post_types'] ?: [];
            $this->types = array_diff($this->types, $excluded_post_types);
        }

        return $this->types;
    }

    public function get_or_create_dummy($type, $title)
    {
        $dummy_exists = get_page_by_title($title, OBJECT, $type);
        if ($dummy_exists) {
            return $dummy_exists->ID;
        }

        $dummy = array(
            'post_title' => $title,
            'post_type' => $type,
            'post_status' => 'publish',
        );
        return wp_insert_post($dummy);
    }

    public function get_fields_for_type($type)
    {
        $fields = [];
        $field_groups = acf_get_field_groups(array('post_type' => $type));

        foreach ($field_groups as $field_group) {
            foreach (acf_get_fields($field_group['key']) as $field) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function get_field_value($field)
    {
        $value = $field['name'];
        switch ($field['type']) {
            case 'flexible_content':
                $value = array_map(function ($layout) {
                    $sub_fields = array_reduce($layout['sub_fields'], function ($result, $sub_field) {
                        $result[$sub_field['key']] = $this->get_field_value($sub_field);
                        return $result;
                    }, []);

                    return array_merge(["acf_fc_layout" => $layout["name"]], $sub_fields);
                }, $field["layouts"]);

                break;

            case 'group':
                $value = array_reduce($field['sub_fields'], function ($result, $sub_field) {
                    $result[$sub_field['key']] = $this->get_field_value($sub_field);
                    return $result;
                }, []);
                break;

            case 'repeater':
                $value = array_reduce($field['sub_fields'], function ($result, $sub_field) {
                    $result[$sub_field['key']] = $this->get_field_value($sub_field);
                    return $result;
                }, []);

                $value = [$value];
                break;

            case 'post_object':
                $value = $this->getDummyID(reset($field['post_type']));
                break;

            case 'image':
                $value = $this->getDummyID('attachment');
                break;

            case 'file':
                // TODO: Handle getting dummy depending on mimetypes
                $value = 0;
                break;

            case 'gallery':
                $value = [$this->getDummyID('attachment')];
                break;

            case 'select':
            case 'checkbox':
                $value = reset($field['choices']) || '';
                break;
        }

        return $value;
    }

    public function fields_to_params($fields)
    {
        return array_reduce($fields, function ($result, $field) {
            $key = $field['key'];
            $value = $this->get_field_value($field);
            $result[$key] = $value;

            return $result;
        }, []);
    }

    public function duplicate_for_wpml($post_id)
    {
        global $iclTranslationManagement;
        if (!empty($iclTranslationManagement) && !empty($this->wpml_languages)) {
            foreach ($this->wpml_languages as $lang) {
                $iclTranslationManagement->make_duplicate($post_id, $lang);
            }
        }
    }

    public function create_or_update_dummy($type)
    {
        $fields = $this->get_fields_for_type($type);
        $params = $this->fields_to_params($fields);
        $pid = $this->get_or_create_dummy($type, $this->dummy_title);

        foreach ($params as $key => $value) {
            update_field($key, $value, $pid);
        }

        $this->duplicate_for_wpml($pid);

        $seoImages = ["_yoast_wpseo_twitter-image", "_yoast_wpseo_opengraph-image"];
        $attachment_url = wp_get_attachment_url($this->getDummyID('attachment'));
        foreach ($seoImages as $seoImage) {
            update_post_meta($pid, $seoImage, $attachment_url);
        }

        $wpseo_options = get_option('wpseo_titles');
        if (empty($wpseo_options['company_logo'])) {
            update_option('wpseo_titles', ['company_logo' => $attachment_url, 'company_logo_id' => $this->getDummyID('attachment')]);
        }
    }

    public function init()
    {
        foreach ($this->getPostTypes() as $type) {
            $this->create_or_update_dummy($type);
        }
    }

    public function get_all_placeholder_ids_for_type($type)
    {
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_title = %s AND post_type = %s", $this->dummy_title, $type));

        return array_map(function ($item) {
            return $item->ID;
        }, $result);
    }

    public function hide_dummy_from_query($query)
    {
        global $pagenow, $post_type;
        if (is_admin()) {
            $ignore_ids = [];

            foreach ($this->getPostTypes() as $type) {
                $ignore_ids = array_merge($ignore_ids, $this->get_all_placeholder_ids_for_type($type));
            }

            $query->query_vars['post__not_in'] = $ignore_ids;
        }
    }
}
