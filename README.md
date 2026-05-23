# Bodyloom Dynamic Toggles

**Contributors:** Jimmy Thanki  
**Tags:** toggles, accordion, acf, metabox, pods  
**Requires at least:** 5.0  
**Tested up to:** 7.0  
**Requires PHP:** 7.4  
**Stable tag:** 1.1.0  
**License:** GPLv2 or later  

Bodyloom Dynamic Toggles creates static and dynamic toggles or accordions for Elementor, the Block Editor, and shortcodes.

## Features

- Elementor widget for manually managed static toggles and accordions.
- Dynamic repeater output from ACF Pro, Meta Box, or Pods.
- Editor field pickers for discovered repeater and subfield paths.
- Manual field-path fallbacks when discovery is unavailable or custom paths are preferred.
- Accordion and toggle interaction modes.
- Optional FAQ schema output with hardened JSON-LD encoding.
- Keyboard-accessible frontend controls.
- Gutenberg block and shortcode rendering for non-Elementor layouts.
- Frontend JavaScript that works without Elementor being loaded.
- Defensive provider handling so missing ACF, Meta Box, Pods, or Elementor dependencies do not fatal the site.

## Installation

1. Upload `bodyloom-dynamic-toggles` to `/wp-content/plugins/`.
2. Activate the plugin in WordPress.
3. Add the Bodyloom Toggles widget, Dynamic Toggles block, or shortcode.

## Usage

Elementor: use the "Bodyloom Toggles" widget. Choose Static for hand-authored items or Dynamic for ACF Pro, Meta Box, or Pods repeater data.

Block Editor: use the "Dynamic Toggles" block and configure the source, repeater field, title field, content field, and interaction mode.

Shortcode:

```text
[bodyloom_toggles src="acf" repeater="my_repeater" title_field="question" content_field="answer" type="accordion"]
```

Field pickers are convenience controls. Manual fields remain available for compatibility, nested field paths, and providers or field layouts that cannot be discovered safely.

## Screenshots

*Elementor Elements Panel*
![Elementor Elements Panel](assets/screenshots/elementor-elements-panel-bodyloom-icon-list-and-toggles-plugins.png)

*Content Edit Panel*
![Content Edit Panel](assets/screenshots/edit-bodyloom-toggles-content-panel.png)

*Style Edit Panel*
![Style Edit Panel](assets/screenshots/edit-bodyloom-toggles-style-panel.png)

*Advanced Edit Panel*
![Advanced Edit Panel](assets/screenshots/edit-bodyloom-toggles-advanced-panel.png)

*Rendered Accordion Example*
![Rendered Accordion Example](assets/screenshots/bodyloom-dynamic-toggles-accordion-rendered.jpg)

## Changelog

### 1.1.0

- Added editor field pickers with manual field-path fallbacks.
- Added dynamic source selection for ACF Pro, Meta Box, and Pods.
- Registered the Dynamic Toggles block from plugin initialization.
- Improved shortcode and block frontend behavior without requiring Elementor.
- Hardened rendering, JSON-LD output, and metadata for WordPress.org submission.
- Updated screenshot references and documentation for the v1.1.0 feature set.

### 1.0.0

- Initial release.
