<?php
namespace Bodyloom\DynamicToggles\Providers;

use Bodyloom\DynamicToggles\Interfaces\Field_Provider;

if (!defined('ABSPATH')) {
    exit;
}

class Pods_Provider implements Field_Provider
{

    public function is_active()
    {
        return function_exists('pods');
    }

    public function get_repeater_data($post_id, $field_name, $title_field, $content_field)
    {
        $data = [];
        $field_name = \Bodyloom\DynamicToggles\Provider_Factory::parse_source_path($field_name)['path'];

        if (!$this->is_active()) {
            return $data;
        }

        $pod = pods(get_post_type($post_id), $post_id);

        if ($pod && $pod->exists()) {
            $repeater_data = $pod->field($field_name);

            if (!empty($repeater_data) && is_array($repeater_data)) {
                foreach ($repeater_data as $item) {
                    $data[] = [
                        'toggle_title' => \Bodyloom\DynamicToggles\Provider_Factory::get_nested_value($item, $title_field, $field_name),
                        'toggle_content' => \Bodyloom\DynamicToggles\Provider_Factory::get_nested_value($item, $content_field, $field_name),
                        'toggle_custom_id' => '',
                    ];
                }
            }
        }

        return $data;
    }
}
