# Bodyloom Dynamic Toggles

**Contributors:** Jimmy Thanki  
**Tags:** toggles, accordion, acf, metabox, pods  
**Requires at least:** 5.0  
**Tested up to:** 7.0  
**Requires PHP:** 7.4  
**Stable tag:** 1.1.0  
**License:** GPLv2 or later  

Bodyloom Dynamic Toggles creates static and dynamic toggles or accordions from Elementor, blocks, shortcodes, and custom field repeaters.

## Features

- Static toggles and accordions in Elementor.
- Dynamic repeater output from ACF Pro, Meta Box, or Pods.
- Editor field pickers with manual field-path fallbacks.
- Optional FAQ schema output.
- Keyboard-accessible toggle controls.
- Gutenberg block and shortcode rendering for non-Elementor layouts.

## Installation

1. Upload `bodyloom-dynamic-toggles` to `/wp-content/plugins/`.
2. Activate the plugin in WordPress.
3. Add the Bodyloom Toggles widget, block, or shortcode.

## Usage

Elementor: use the "Bodyloom Toggles" widget.

Block Editor: use the "Dynamic Toggles" block.

Shortcode:

```text
[bodyloom_toggles source="acf" repeater="my_repeater" title_field="question" content_field="answer" type="accordion"]
```

## Changelog

### 1.1.0

- Added editor field pickers with manual field-path fallbacks.
- Added dynamic source selection for ACF Pro, Meta Box, and Pods.
- Registered the Dynamic Toggles block from plugin initialization.
- Improved shortcode and block frontend behavior without requiring Elementor.
- Hardened rendering and metadata for WordPress.org submission.

### 1.0.0

- Initial release.
