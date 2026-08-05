<?php
/**
 * Plugin Name: Vybose Repeater Accordion
 * Plugin URI: https://github.com/Vybose/vybose-repeater-accordion
 * Description: Create accessible toggles and accordions from static content or dynamic repeater fields in ACF Pro, Meta Box, or Pods.
 * Version: 2.0.0
 * Requires at least: 6.3
 * Tested up to: 7.0
 * Requires PHP: 7.4
 * Author: Jimmy Thanki
 * Author URI: https://vybose.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: vybose-repeater-accordion
 */

if (!defined('ABSPATH')) {
    exit;
}

define('VYBOSE_REPEATER_ACCORDION_VERSION', '2.0.0');
define('VYBOSE_REPEATER_ACCORDION_PATH', plugin_dir_path(__FILE__));
define('VYBOSE_REPEATER_ACCORDION_URL', plugin_dir_url(__FILE__));

// Include Interfaces
require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/interfaces/class-field-provider.php';

// Include Providers
require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/providers/class-acf-provider.php';
require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/providers/class-metabox-provider.php';
require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/providers/class-pods-provider.php';

// Include Factory
require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/class-provider-factory.php';

// Include Field Discovery
require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/class-field-discovery.php';

// Include Main Class
require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/class-plugin.php';

// Initialize Plugin
function vybose_repeater_accordion_init()
{
    \Vybose\RepeaterAccordion\Plugin::get_instance();
}
add_action('plugins_loaded', 'vybose_repeater_accordion_init');
