<?php
namespace Vybose\RepeaterAccordion\Providers;

use Vybose\RepeaterAccordion\Interfaces\Field_Provider;

if (!defined('ABSPATH')) {
    exit;
}

class Metabox_Provider implements Field_Provider
{

    public function is_active()
    {
        return function_exists('rwmb_meta');
    }

    public function get_repeater_data($post_id, $field_name, $title_field, $content_field)
    {
        $data = [];
        $field_name = \Vybose\RepeaterAccordion\Provider_Factory::parse_source_path($field_name)['path'];

        if (!$this->is_active()) {
            return $data;
        }

        // Meta Box stores groups/repeaters as arrays
        $meta = rwmb_meta($field_name, [], $post_id);

        if (!empty($meta) && is_array($meta)) {
            foreach ($meta as $item) {
                $data[] = [
                    'toggle_title' => \Vybose\RepeaterAccordion\Provider_Factory::get_nested_value($item, $title_field, $field_name),
                    'toggle_content' => \Vybose\RepeaterAccordion\Provider_Factory::get_nested_value($item, $content_field, $field_name),
                    'toggle_custom_id' => '',
                ];
            }
        }

        return $data;
    }
}
