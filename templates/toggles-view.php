<?php
/**
 * Toggles View Template
 *
 * @var array $bodyloom_view_data
 */

if (!defined('ABSPATH')) {
	exit;
}

if (empty($bodyloom_view_data['toggles'])) {
	return;
}

$bodyloom_id = $bodyloom_view_data['id'];
$bodyloom_settings = $bodyloom_view_data['settings'];
$bodyloom_toggles_items = $bodyloom_view_data['toggles'];
$bodyloom_title_tag_val = $bodyloom_settings['title_html_tag'] ?? 'div';
$bodyloom_allowed_tags = ['div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'span', 'p'];
$bodyloom_title_tag = (in_array($bodyloom_title_tag_val, $bodyloom_allowed_tags)) ? $bodyloom_title_tag_val : 'div';

$bodyloom_widget_class = 'bodyloom-toggles';
?>
<div class="<?php echo esc_attr($bodyloom_widget_class); ?>__list" id="bodyloom-toggles-<?php echo esc_attr($bodyloom_id); ?>"
	data-type="<?php echo esc_attr($bodyloom_settings['type'] ?? 'toggles'); ?>"
	data-default-toggle="<?php echo esc_attr((string) ($bodyloom_settings['default_toggle'] ?? 0)); ?>">
	<?php foreach ($bodyloom_toggles_items as $bodyloom_index => $bodyloom_item):
		$bodyloom_tab_count = $bodyloom_index + 1;
		$bodyloom_custom_id_attr = '';
		if (!empty($bodyloom_item['toggle_custom_id'])) {
			$bodyloom_custom_id_attr = ' data-toggle-custom-id="' . esc_attr(str_replace('#', '', $bodyloom_item['toggle_custom_id'])) . '"';
		}
		$bodyloom_is_active = (int) ($bodyloom_settings['default_toggle'] ?? 0) === $bodyloom_tab_count;
		$bodyloom_active_class = $bodyloom_is_active ? ' active-toggle' : '';
		$bodyloom_content_style = $bodyloom_is_active ? ' style="display: block;"' : '';

		?>
		<div class="<?php echo esc_attr($bodyloom_widget_class); ?>__item"<?php echo $bodyloom_custom_id_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<<?php echo tag_escape($bodyloom_title_tag); ?> class="<?php echo esc_attr($bodyloom_widget_class . '__title' . $bodyloom_active_class); ?>"
				data-tab="<?php echo esc_attr($bodyloom_tab_count); ?>"
				role="button" tabindex="0" aria-expanded="<?php echo $bodyloom_is_active ? 'true' : 'false'; ?>">

				<a class="<?php echo esc_attr($bodyloom_widget_class); ?>__title-link" href="#" tabindex="-1">
					<span
						class="<?php echo esc_attr($bodyloom_widget_class); ?>__title-text"><?php echo wp_kses_post($bodyloom_item['toggle_title']); ?></span>
				</a>

				<?php if (!empty($bodyloom_settings['trigger_icon']['value']) && class_exists('\Elementor\Icons_Manager')): ?>
					<span class="<?php echo esc_attr($bodyloom_widget_class); ?>__trigger">
						<span class="<?php echo esc_attr($bodyloom_widget_class); ?>__trigger-closed">
							<?php \Elementor\Icons_Manager::render_icon($bodyloom_settings['trigger_icon'], ['aria-hidden' => 'true']); ?>
						</span>
						<?php
						$bodyloom_active_icon = !empty($bodyloom_settings['trigger_active_icon']['value']) ? $bodyloom_settings['trigger_active_icon'] : $bodyloom_settings['trigger_icon'];
						?>
						<span class="<?php echo esc_attr($bodyloom_widget_class); ?>__trigger-opened">
							<?php \Elementor\Icons_Manager::render_icon($bodyloom_active_icon, ['aria-hidden' => 'true']); ?>
						</span>
					</span>
				<?php endif; ?>

			</<?php echo tag_escape($bodyloom_title_tag); ?>>

			<div class="<?php echo esc_attr($bodyloom_widget_class . '__content' . $bodyloom_active_class); ?>"
				data-tab="<?php echo esc_attr($bodyloom_tab_count); ?>"<?php echo $bodyloom_content_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo wp_kses_post(do_shortcode($bodyloom_item['toggle_content'])); ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<?php
// FAQ Schema
if (!empty($bodyloom_settings['faq_schema']) && 'yes' === $bodyloom_settings['faq_schema']) {
	$bodyloom_schema = [
		'@context' => 'https://schema.org',
		'@type' => 'FAQPage',
		'mainEntity' => [],
	];

	foreach ($bodyloom_toggles_items as $bodyloom_item) {
		$bodyloom_schema['mainEntity'][] = [
			'@type' => 'Question',
			'name' => wp_strip_all_tags($bodyloom_item['toggle_title']),
			'acceptedAnswer' => [
				'@type' => 'Answer',
				'text' => wp_strip_all_tags($bodyloom_item['toggle_content']),
			],
		];
	}
	$bodyloom_schema_json = wp_json_encode(
		$bodyloom_schema,
		JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	);

	if (false !== $bodyloom_schema_json) {
	?>
	<script type="application/ld+json"><?php echo $bodyloom_schema_json; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
	<?php
	}
}
?>
