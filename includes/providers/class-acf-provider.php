<?php
namespace Bodyloom\DynamicToggles\Providers;

use Bodyloom\DynamicToggles\Interfaces\Field_Provider;

if (!defined('ABSPATH')) {
    exit;
}

class ACF_Provider implements Field_Provider
{

    public function is_active()
    {
        return function_exists('get_field');
    }

    public function get_repeater_data($post_id, $field_name, $title_field, $content_field)
    {
        $data = [];
        $field_name = \Bodyloom\DynamicToggles\Provider_Factory::parse_source_path($field_name)['path'];

        if (!$this->is_active() || '' === $field_name) {
            return $data;
        }

        $field_name = $this->normalize_path($field_name);
        $title_field = $this->normalize_path($title_field);
        $content_field = $this->normalize_path($content_field);

        foreach ($this->resolve_rows($post_id, $field_name) as $row) {
            $data[] = [
                'toggle_title' => \Bodyloom\DynamicToggles\Provider_Factory::get_nested_value($row, $title_field, $field_name),
                'toggle_content' => \Bodyloom\DynamicToggles\Provider_Factory::get_nested_value($row, $content_field, $field_name),
                'toggle_custom_id' => '',
            ];
        }

        return $data;
    }

    /**
     * Resolve a slash-delimited field path to a flat list of repeater rows.
     *
     * Rows are always sourced from get_field(), which returns *formatted* values
     * keyed by sub-field NAME. The loop helpers (have_rows()/get_row()) expose the
     * *unformatted* value instead, whose rows are keyed by field KEY
     * ('field_abc123') -- see ACF Pro pro/fields/class-acf-field-repeater.php,
     * load_value(). Looking up a sub-field by name in that array always misses,
     * which silently renders every row blank.
     *
     * An intermediate segment may be either a group (a single associative array)
     * or a repeater (a list of rows); rows of a repeater ancestor are flattened.
     *
     * @param int    $post_id Post ID.
     * @param string $path    Field path, e.g. 'faqs' or 'hub_variant_cards/proxy_faqs'.
     * @return array List of rows keyed by sub-field name.
     */
    private function resolve_rows($post_id, $path)
    {
        $rows = $this->walk_rows($post_id, $path);

        // A seamless ACF clone shows up in a saved path but stores no level of
        // its own, so the full path resolves to nothing. The trailing segment is
        // the repeater's real name -- retry on that.
        if (empty($rows) && false !== strpos($path, '/')) {
            $segments = explode('/', $path);
            $rows = $this->walk_rows($post_id, end($segments));
        }

        return $rows;
    }

    /**
     * Translate ACF field-key segments ('field_abc123') into field names.
     *
     * Settings saved against earlier builds stored field keys, while get_field()
     * returns rows keyed by field name. Without this, key-based settings resolve
     * to nothing.
     *
     * @param string $path Field path.
     * @return string Path with every key segment replaced by its field name.
     */
    private function normalize_path($path)
    {
        $path = \Bodyloom\DynamicToggles\Provider_Factory::parse_source_path(is_string($path) ? $path : '')['path'];

        if ('' === $path) {
            return '';
        }

        $segments = [];

        foreach (explode('/', $path) as $segment) {
            if (0 === strpos($segment, 'field_') && function_exists('acf_get_field')) {
                $field = acf_get_field($segment);

                if (!empty($field['name'])) {
                    $segment = $field['name'];
                }
            }

            $segments[] = $segment;
        }

        return implode('/', $segments);
    }

    private function walk_rows($post_id, $path)
    {
        $segments = explode('/', $path);
        $current = get_field(array_shift($segments), $post_id);

        foreach ($segments as $segment) {
            // A repeater ancestor yields a list of rows; a group yields a single array.
            $parents = (is_array($current) && isset($current[0])) ? $current : [$current];
            $next = [];

            foreach ($parents as $parent) {
                if (is_array($parent) && isset($parent[$segment]) && is_array($parent[$segment])) {
                    $next = array_merge($next, array_values($parent[$segment]));
                }
            }

            $current = $next;
        }

        if (!is_array($current)) {
            return [];
        }

        return array_values(array_filter($current, 'is_array'));
    }
}
