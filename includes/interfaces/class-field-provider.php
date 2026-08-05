<?php
namespace Vybose\RepeaterAccordion\Interfaces;

if (!defined('ABSPATH')) {
    exit;
}

interface Field_Provider
{
    /**
     * Check if the provider's plugin is active.
     *
     * @return bool
     */
    public function is_active();

    /**
     * Get repeater data from the provider.
     *
     * @param int    $post_id       Post ID.
     * @param string $field_name    Repeater field name.
     * @param string $title_field   Sub-field for title.
     * @param string $content_field Sub-field for content.
     * @return array Array of items with 'toggle_title' and 'toggle_content'.
     */
    public function get_repeater_data($post_id, $field_name, $title_field, $content_field);
}
