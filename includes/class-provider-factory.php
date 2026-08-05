<?php
namespace Vybose\RepeaterAccordion;

use Vybose\RepeaterAccordion\Providers\ACF_Provider;
use Vybose\RepeaterAccordion\Providers\Metabox_Provider;
use Vybose\RepeaterAccordion\Providers\Pods_Provider;

if (!defined('ABSPATH')) {
    exit;
}

class Provider_Factory
{

    /**
     * Get the first active provider.
     *
     * @return \Vybose\RepeaterAccordion\Interfaces\Field_Provider|null
     */
    public static function get_provider($source = '', $field_path = '')
    {
        $parsed = self::parse_source_path($field_path);
        $source = $parsed['source'] ?: $source;
        $providers = [
            'acf' => new ACF_Provider(),
            'metabox' => new Metabox_Provider(),
            'pods' => new Pods_Provider(),
        ];

        if ($source && isset($providers[$source])) {
            return $providers[$source]->is_active() ? $providers[$source] : null;
        }

        foreach ($providers as $provider) {
            if ($provider->is_active()) {
                return $provider;
            }
        }

        return null;
    }

    public static function parse_source_path($path)
    {
        $path = is_string($path) ? trim($path) : '';

        if (preg_match('/^(acf|pods|metabox):(.+)$/', $path, $matches)) {
            return [
                'source' => $matches[1],
                'path' => $matches[2],
            ];
        }

        return [
            'source' => '',
            'path' => $path,
        ];
    }

    public static function get_nested_value($data, $path, $root_path = '')
    {
        $path = is_string($path) ? trim($path) : '';
        $root_path = self::parse_source_path($root_path)['path'];

        if ($root_path && 0 === strpos($path, $root_path . '/')) {
            $path = substr($path, strlen($root_path) + 1);
        }

        if ('' === $path) {
            return '';
        }

        $value = self::walk_path($data, $path);

        // Settings saved by earlier builds stored absolute paths that included
        // ancestors which are not present in a row (field-group keys, or a
        // seamless ACF clone that contributes no stored level). The trailing
        // segment is the sub-field's own name, so retry on that alone.
        if ('' === $value && false !== strpos($path, '/')) {
            $segments = explode('/', $path);
            $value = self::walk_path($data, end($segments));
        }

        return $value;
    }

    private static function walk_path($data, $path)
    {
        $value = $data;

        foreach (explode('/', $path) as $part) {
            if (is_array($value) && array_key_exists($part, $value)) {
                $value = $value[$part];
            } else {
                return '';
            }
        }

        return $value;
    }
}
