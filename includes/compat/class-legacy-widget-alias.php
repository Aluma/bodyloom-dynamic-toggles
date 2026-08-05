<?php
/**
 * Legacy widget alias — NOT distributed on WordPress.org.
 *
 * Documents saved before the 2.0.0 rename store the widget under its previous
 * name. Elementor resolves a saved element by that stored string, so without an
 * alias every pre-2.0.0 instance would render as "widget not found" while its
 * settings sat intact but unreachable in the database.
 *
 * This registers the old name as a hidden subclass of the current widget: saved
 * documents resolve and render exactly as before, but the legacy entry never
 * appears in the Elementor panel, so nothing new can be created against it.
 *
 * Excluded from the WordPress.org package by build-wordpress-org-packages.sh,
 * since a fresh install has no legacy data for it to rescue.
 *
 * @package Vybose\RepeaterAccordion
 */

namespace Vybose\RepeaterAccordion\Compat;

use Vybose\RepeaterAccordion\Widgets\Toggles;

if (!defined('ABSPATH')) {
    exit;
}

class Legacy_Widget_Alias extends Toggles
{
    /**
     * The pre-2.0.0 widget name as stored in _elementor_data.
     */
    public function get_name()
    {
        return 'bodyloom-toggles';
    }

    /**
     * Keep the alias out of the widget panel so it cannot be inserted anew.
     */
    public function show_in_panel()
    {
        return false;
    }
}
