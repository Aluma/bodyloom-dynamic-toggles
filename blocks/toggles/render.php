<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Render callback for the Dynamic Toggles block.
 *
 * @param array $attributes Block attributes.
 * @return string Block output.
 */

$vybose_repeater_field = !empty($attributes['repeater_field']) ? $attributes['repeater_field'] : ($attributes['repeater_field_manual'] ?? '');
$vybose_title_field = !empty($attributes['title_field']) ? $attributes['title_field'] : ($attributes['title_field_manual'] ?? '');
$vybose_content_field = !empty($attributes['content_field']) ? $attributes['content_field'] : ($attributes['content_field_manual'] ?? '');
$vybose_source = $attributes['source'] ?? '';

if (empty($vybose_repeater_field) || empty($vybose_title_field) || empty($vybose_content_field)) {
    return '<div class="vybose-repeater-accordion-placeholder">' . esc_html__('Please configure the Dynamic Toggles block.', 'vybose-repeater-accordion') . '</div>';
}

$plugin = \Vybose\RepeaterAccordion\Plugin::get_instance();
$post_id = get_the_ID();
$vybose_repeater_accordion = $plugin->get_dynamic_data($post_id, $vybose_repeater_field, $vybose_title_field, $vybose_content_field, $vybose_source);

if (empty($vybose_repeater_accordion)) {
    return '<div class="vybose-repeater-accordion-empty">' . esc_html__('No data found for the specified repeater field.', 'vybose-repeater-accordion') . '</div>';
}

// Prepare view data
$vybose_view_data = [
    'id' => 'block-' . uniqid(),
    'settings' => [
        'type' => $attributes['type'],
        'title_html_tag' => 'div', // Default for block
        'faq_schema' => $attributes['faq_schema'] ? 'yes' : 'no',
        'style' => $attributes['style'],
        'default_toggle' => $attributes['open_first'] ? 1 : 0,
    ],
    'toggles' => $vybose_repeater_accordion,
];

// Enqueue assets
wp_enqueue_style('vybose-repeater-accordion');
wp_enqueue_script('vybose-repeater-accordion');

ob_start();
include VYBOSE_REPEATER_ACCORDION_PATH . 'templates/toggles-view.php';
return ob_get_clean();
