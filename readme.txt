=== Bodyloom Dynamic Toggles ===
Contributors: Jimmy Thanki
Tags: toggles, accordion, acf, metabox, pods
Requires at least: 5.0
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Create static and dynamic toggles or accordions for Elementor, the Block Editor, and shortcodes.

== Description ==

Bodyloom Dynamic Toggles displays static toggle items or dynamic accordion content from ACF Pro, Meta Box, and Pods repeater-style fields. It includes an Elementor widget, a dynamic block, and a shortcode so the same toggle content pattern can be used across page builders and native WordPress content.

Features include:

* Static toggles and accordions in Elementor.
* Dynamic repeater field output from ACF Pro, Meta Box, or Pods.
* Editor field pickers for discovered repeater and subfield paths.
* Manual field-path fallbacks for compatibility, nested paths, and undiscovered field layouts.
* Accordion and toggle interaction modes.
* Optional FAQ schema output with hardened JSON-LD encoding.
* Keyboard-accessible frontend controls.
* Gutenberg block and shortcode rendering for non-Elementor layouts.
* Frontend JavaScript that works without Elementor being loaded.
* Defensive provider handling so missing optional plugins do not fatal the site.

The plugin does not connect to external services or load remote JavaScript or CSS.

== Installation ==

1. Upload `bodyloom-dynamic-toggles` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the Plugins menu in WordPress.
3. Add the Bodyloom Toggles widget, the Dynamic Toggles block, or the `[bodyloom_toggles]` shortcode.

== Usage ==

= Elementor =

Use the "Bodyloom Toggles" widget. Choose Static for manually managed toggle items, or Dynamic to use repeater data from ACF Pro, Meta Box, or Pods.

= Block Editor =

Use the "Dynamic Toggles" block and configure the source, repeater field, title field, content field, and interaction mode in the block sidebar.

= Shortcode =

Use a shortcode such as:
`[bodyloom_toggles src="acf" repeater="my_repeater" title_field="question" content_field="answer" type="accordion"]`

== Frequently Asked Questions ==

= Does this require ACF Pro, Meta Box, or Pods? =

No. Static Elementor toggles work without those plugins. Dynamic repeater output requires the matching field plugin to be active and configured.

= What happens if a dynamic field plugin is missing? =

The widget, block, or shortcode returns an empty result instead of causing a fatal error.

= Can I still type field paths manually? =

Yes. Field pickers are provided where possible, but manual field-path fields remain available for compatibility, nested paths, and fallback use.

== Screenshots ==

1. Rendered accordion example: `assets/screenshots/bodyloom-dynamic-toggles-accordion-rendered.jpg`
2. Elementor elements panel: `assets/screenshots/elementor-elements-panel-bodyloom-icon-list-and-toggles-plugins.png`
3. Toggles content controls: `assets/screenshots/edit-bodyloom-toggles-content-panel-1.png`
4. Additional toggles content controls: `assets/screenshots/edit-bodyloom-toggles-content-panel-2.png`
5. ACF repeater field controls: `assets/screenshots/edit-bodyloom-toggles-content-panel-acf-repeater.png`
6. Pods field controls: `assets/screenshots/edit-bodyloom-toggles-content-panel-pods.png`
7. Meta Box field controls: `assets/screenshots/edit-bodyloom-toggles-content-panel-meta-box.png`
8. Toggles advanced controls: `assets/screenshots/edit-bodyloom-toggles-advanced-panel.png`

== Changelog ==

= 1.1.0 =
* Added editor field pickers with manual field-path fallbacks.
* Added dynamic source selection for ACF Pro, Meta Box, and Pods.
* Registered the Dynamic Toggles block from plugin initialization.
* Improved shortcode and block frontend behavior without requiring Elementor.
* Hardened rendering, JSON-LD output, and metadata for WordPress.org submission.
* Updated screenshot references and documentation for the v1.1.0 feature set.

= 1.0.0 =
* Initial release.
