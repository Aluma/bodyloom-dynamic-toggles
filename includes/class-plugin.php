<?php
namespace Vybose\RepeaterAccordion;

use Vybose\RepeaterAccordion\Widgets\Toggles;
use Vybose\RepeaterAccordion\Provider_Factory;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Main Plugin Class
 */
class Plugin
{

    /**
     * Instance
     *
     * @var Plugin The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @return Plugin An instance of the class.
     */
    public static function get_instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required files
     */
    private function includes()
    {
        // require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/widgets/class-toggles-widget.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks()
    {
        add_action('init', [$this, 'register_assets_and_blocks']);

        // Register Elementor Widget
        add_action('elementor/widgets/register', [$this, 'register_widgets']);

        // Register Shortcode
        add_shortcode('vybose_repeater_accordion', [$this, 'render_shortcode']);

        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    /**
     * Register Elementor Widgets
     *
     * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
     */
    public function register_widgets($widgets_manager)
    {
        require_once VYBOSE_REPEATER_ACCORDION_PATH . 'includes/widgets/class-toggles-widget.php';
        $widgets_manager->register(new Widgets\Toggles());

        // Resolves documents saved before the 2.0.0 rename. Absent from the
        // WordPress.org package, where no legacy data exists.
        $legacy_alias = VYBOSE_REPEATER_ACCORDION_PATH . 'includes/compat/class-legacy-widget-alias.php';

        if (file_exists($legacy_alias)) {
            require_once $legacy_alias;
            $widgets_manager->register(new Compat\Legacy_Widget_Alias());
        }
    }

    /**
     * Enqueue Scripts and Styles
     */
    public function register_assets_and_blocks()
    {
        wp_register_style(
            'vybose-repeater-accordion',
            VYBOSE_REPEATER_ACCORDION_URL . 'assets/css/vybose-repeater-accordion.css',
            [],
            VYBOSE_REPEATER_ACCORDION_VERSION
        );

        wp_register_script(
            'vybose-repeater-accordion',
            VYBOSE_REPEATER_ACCORDION_URL . 'assets/js/vybose-repeater-accordion.js',
            ['jquery'],
            VYBOSE_REPEATER_ACCORDION_VERSION,
            true
        );

        register_block_type(VYBOSE_REPEATER_ACCORDION_PATH . 'blocks/toggles');
    }

    public function register_rest_routes()
    {
        register_rest_route(
            'vybose-repeater-accordion/v1',
            '/fields',
            [
                'methods' => 'GET',
                'callback' => [$this, 'get_field_discovery'],
                'permission_callback' => function (\WP_REST_Request $request) {
                    // Field schemas are only exposed to users who can edit the
                    // post type actually being asked about. A blanket edit_posts
                    // check would let any contributor enumerate every post type.
                    $post_type = sanitize_key((string) $request->get_param('post_type')) ?: 'post';
                    $post_type_object = get_post_type_object($post_type);

                    if (!$post_type_object || empty($post_type_object->cap->edit_posts)) {
                        return false;
                    }

                    return current_user_can($post_type_object->cap->edit_posts);
                },
                'args' => [
                    'post_type' => [
                        'type' => 'string',
                        'sanitize_callback' => 'sanitize_key',
                    ],
                    'refresh' => [
                        'type' => 'boolean',
                        'default' => false,
                    ],
                ],
            ]
        );
    }

    public function get_field_discovery($request)
    {
        $post_type = $request->get_param('post_type') ?: 'post';
        $refresh = (bool) $request->get_param('refresh');

        return rest_ensure_response(Field_Discovery::get_rest_data($post_type, $refresh));
    }

    /**
     * Render Shortcode
     *
     * @param array $atts Shortcode attributes.
     * @return string Shortcode output.
     */
    public function render_shortcode($atts)
    {
        $atts = shortcode_atts([
            'id' => '', // Post ID to pull ACF data from (optional)
            'source' => '', // acf, pods, metabox, or empty for first active provider
            'repeater' => '', // ACF Repeater field name
            'title_field' => '', // Sub-field for title
            'content_field' => '', // Sub-field for content
            'type' => 'toggles', // toggles or accordion
            'title_tag' => 'div', // HTML tag for title
            'faq_schema' => 'no', // yes or no
        ], $atts, 'vybose_repeater_accordion');

        // Enqueue assets
        wp_enqueue_style('vybose-repeater-accordion');
        wp_enqueue_script('vybose-repeater-accordion');

        if (empty($atts['repeater']) || empty($atts['title_field']) || empty($atts['content_field'])) {
            return '';
        }

        $post_id = !empty($atts['id']) ? intval($atts['id']) : get_the_ID();
        $toggles = $this->get_dynamic_data($post_id, $atts['repeater'], $atts['title_field'], $atts['content_field'], $atts['source']);

        if (empty($toggles)) {
            return '';
        }

        // Prepare view data
        $vybose_view_data = [
            'id' => 'sc-' . uniqid(),
            'settings' => [
                'type' => $atts['type'],
                'title_html_tag' => $atts['title_tag'],
                'faq_schema' => $atts['faq_schema'],
                'style' => $atts['style'] ?? 'default',
                'default_toggle' => (isset($atts['open_first']) && 'yes' === $atts['open_first']) ? 1 : 0,
            ],
            'toggles' => $toggles,
        ];

        ob_start();
        include VYBOSE_REPEATER_ACCORDION_PATH . 'templates/toggles-view.php';
        return ob_get_clean();
    }

    /**
     * Get Dynamic Data
     *
     * @param int $post_id Post ID.
     * @param string $repeater_name Repeater name.
     * @param string $title_field Title field name.
     * @param string $content_field Content field name.
     * @return array Toggles data.
     */
    public function get_dynamic_data($post_id, $repeater_name, $title_field, $content_field, $source = '')
    {
        $provider = Provider_Factory::get_provider($source, $repeater_name);

        if (!$provider) {
            return [];
        }

        return $provider->get_repeater_data($post_id, $repeater_name, $title_field, $content_field);
    }
}
