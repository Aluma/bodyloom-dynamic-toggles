<?php
/**
 * Toggles View Template
 *
 * @var array $vybose_view_data
 */

if (!defined('ABSPATH')) {
	exit;
}

if (empty($vybose_view_data['toggles'])) {
	return;
}

$vybose_id = $vybose_view_data['id'];
$vybose_settings = $vybose_view_data['settings'];
$vybose_repeater_accordion_items = $vybose_view_data['toggles'];
$vybose_title_tag_val = $vybose_settings['title_html_tag'] ?? 'div';
$vybose_allowed_tags = ['div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'p'];
$vybose_title_tag = (in_array($vybose_title_tag_val, $vybose_allowed_tags)) ? $vybose_title_tag_val : 'div';

$vybose_widget_class = 'vybose-repeater-accordion';
?>
<div class="<?php echo esc_attr($vybose_widget_class); ?>__list" id="vybose-repeater-accordion-<?php echo esc_attr($vybose_id); ?>"
	data-type="<?php echo esc_attr($vybose_settings['type'] ?? 'toggles'); ?>"
	data-default-toggle="<?php echo esc_attr((string) ($vybose_settings['default_toggle'] ?? 0)); ?>">
	<?php foreach ($vybose_repeater_accordion_items as $vybose_index => $vybose_item):
		$vybose_tab_count = $vybose_index + 1;
		$vybose_custom_id_attr = '';
		if (!empty($vybose_item['toggle_custom_id'])) {
			$vybose_custom_id_attr = ' data-toggle-custom-id="' . esc_attr(str_replace('#', '', $vybose_item['toggle_custom_id'])) . '"';
		}
		$vybose_is_active = (int) ($vybose_settings['default_toggle'] ?? 0) === $vybose_tab_count;
		$vybose_active_class = $vybose_is_active ? ' active-toggle' : '';
		$vybose_content_style = $vybose_is_active ? ' style="display: block;"' : '';

		?>
		<div class="<?php echo esc_attr($vybose_widget_class); ?>__item"<?php echo $vybose_custom_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<<?php echo tag_escape($vybose_title_tag); ?> class="<?php echo esc_attr($vybose_widget_class . '__title' . $vybose_active_class); ?>"
				data-tab="<?php echo esc_attr($vybose_tab_count); ?>"
				role="button" tabindex="0" aria-expanded="<?php echo $vybose_is_active ? 'true' : 'false'; ?>">

				<a class="<?php echo esc_attr($vybose_widget_class); ?>__title-link" href="#" tabindex="-1">
					<span
						class="<?php echo esc_attr($vybose_widget_class); ?>__title-text"><?php echo wp_kses_post($vybose_item['toggle_title']); ?></span>
				</a>

				<?php if (!empty($vybose_settings['trigger_icon']['value']) && class_exists('\Elementor\Icons_Manager')): ?>
					<span class="<?php echo esc_attr($vybose_widget_class); ?>__trigger">
						<span class="<?php echo esc_attr($vybose_widget_class); ?>__trigger-closed">
							<?php \Elementor\Icons_Manager::render_icon($vybose_settings['trigger_icon'], ['aria-hidden' => 'true']); ?>
						</span>
						<?php
						$vybose_active_icon = !empty($vybose_settings['trigger_active_icon']['value']) ? $vybose_settings['trigger_active_icon'] : $vybose_settings['trigger_icon'];
						?>
						<span class="<?php echo esc_attr($vybose_widget_class); ?>__trigger-opened">
							<?php \Elementor\Icons_Manager::render_icon($vybose_active_icon, ['aria-hidden' => 'true']); ?>
						</span>
					</span>
				<?php endif; ?>

			</<?php echo tag_escape($vybose_title_tag); ?>>

			<div class="<?php echo esc_attr($vybose_widget_class . '__content' . $vybose_active_class); ?>"
				data-tab="<?php echo esc_attr($vybose_tab_count); ?>"<?php echo $vybose_content_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo wp_kses_post(do_shortcode($vybose_item['toggle_content'])); ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?php
// FAQ Schema
if (!empty($vybose_settings['faq_schema']) && 'yes' === $vybose_settings['faq_schema']) {
	$vybose_schema = [
		'@context' => 'https://schema.org',
		'@type' => 'FAQPage',
		'mainEntity' => [],
	];

	foreach ($vybose_repeater_accordion_items as $vybose_item) {
		$vybose_schema['mainEntity'][] = [
			'@type' => 'Question',
			'name' => wp_strip_all_tags($vybose_item['toggle_title']),
			'acceptedAnswer' => [
				'@type' => 'Answer',
				'text' => wp_strip_all_tags($vybose_item['toggle_content']),
			],
		];
	}
	$vybose_schema_json = wp_json_encode(
		$vybose_schema,
		JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	);

	if (false !== $vybose_schema_json) {
	?>
	<script type="application/ld+json"><?php echo $vybose_schema_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
	<?php
	}
}
?>
