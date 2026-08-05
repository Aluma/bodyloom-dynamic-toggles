<?php

namespace Vybose\RepeaterAccordion;

if (!defined('ABSPATH')) {
    exit;
}

class Field_Discovery
{
    private const CACHE_GROUP = 'vybose_repeater_accordion';
    private const CACHE_TTL = 300;

    public static function get_repeater_options($post_type = '')
    {
        $options = [
            '' => __('Manual entry / no discovered field', 'vybose-repeater-accordion'),
        ];

        foreach (self::get_sources($post_type) as $source => $data) {
            foreach ($data['repeaters'] as $path => $field) {
                $options[$source . ':' . $path] = sprintf(
                    '%s: %s',
                    $data['label'],
                    $field['label']
                );
            }
        }

        return $options;
    }

    public static function get_leaf_field_options($post_type = '')
    {
        $options = [
            '' => __('Manual entry / no discovered field', 'vybose-repeater-accordion'),
        ];

        foreach (self::get_sources($post_type) as $source => $data) {
            foreach ($data['leaf_fields'] as $path => $field) {
                $options[$path] = sprintf(
                    '%s: %s',
                    $data['label'],
                    $field['label']
                );
            }
        }

        return $options;
    }

    public static function get_rest_data($post_type = '', $refresh = false)
    {
        if ($refresh) {
            self::delete_cache($post_type);
        }

        return [
            'sources' => self::sanitize_sources(self::get_sources($post_type)),
        ];
    }

    public static function get_sources($post_type = '')
    {
        $post_type = sanitize_key($post_type ?: 'post');
        $cache_key = 'field_sources_' . $post_type;
        $cached = wp_cache_get($cache_key, self::CACHE_GROUP);

        if (false !== $cached) {
            return $cached;
        }

        $sources = [
            'acf' => [
                'label' => 'ACF',
                'active' => function_exists('acf_get_field_groups') && function_exists('acf_get_fields'),
                'repeaters' => self::get_acf_repeaters(),
                'leaf_fields' => self::get_acf_leaf_fields(),
            ],
            'pods' => [
                'label' => 'Pods',
                'active' => function_exists('pods_api'),
                'repeaters' => self::get_pods_repeaters($post_type),
                'leaf_fields' => self::get_pods_leaf_fields($post_type),
            ],
            'metabox' => [
                'label' => 'Meta Box',
                'active' => function_exists('rwmb_get_registry'),
                'repeaters' => self::get_metabox_repeaters($post_type),
                'leaf_fields' => self::get_metabox_leaf_fields($post_type),
            ],
        ];

        wp_cache_set($cache_key, $sources, self::CACHE_GROUP, self::CACHE_TTL);

        return $sources;
    }

    private static function delete_cache($post_type)
    {
        $post_type = sanitize_key($post_type ?: 'post');
        wp_cache_delete('field_sources_' . $post_type, self::CACHE_GROUP);
    }

    private static function sanitize_sources($sources)
    {
        $sanitized = [];

        foreach ((array) $sources as $source_key => $source) {
            $source_id = sanitize_key($source_key);
            $sanitized[$source_id] = [
                'label' => sanitize_text_field($source['label'] ?? ''),
                'active' => !empty($source['active']),
                'repeaters' => self::sanitize_field_map($source['repeaters'] ?? []),
                'leaf_fields' => self::sanitize_field_map($source['leaf_fields'] ?? []),
            ];
        }

        return $sanitized;
    }

    private static function sanitize_field_map($fields)
    {
        $sanitized = [];

        foreach ((array) $fields as $path => $field) {
            $field_path = sanitize_text_field((string) $path);
            $sanitized[$field_path] = [
                'label' => sanitize_text_field($field['label'] ?? $field_path),
                'type' => sanitize_key($field['type'] ?? ''),
            ];
        }

        return $sanitized;
    }

    private static function get_acf_repeaters()
    {
        return self::acf_collect_fields('repeaters');
    }

    private static function get_acf_leaf_fields()
    {
        return self::acf_collect_fields('leaf_fields');
    }

    private static function acf_collect_fields($target)
    {
        if (!function_exists('acf_get_field_groups') || !function_exists('acf_get_fields')) {
            return [];
        }

        $collected = [
            'repeaters' => [],
            'leaf_fields' => [],
        ];

        foreach ((array) acf_get_field_groups() as $group) {
            $fields = acf_get_fields($group);

            if (is_array($fields)) {
                self::walk_acf_fields($fields, '', $collected, $group['title'] ?? 'ACF');
            }
        }

        return $collected[$target];
    }

    private static function walk_acf_fields($fields, $prefix, array &$collected, $group_label)
    {
        foreach ($fields as $field) {
            if (empty($field['name'])) {
                continue;
            }

            $path = $prefix ? $prefix . '/' . $field['name'] : $field['name'];
            $label = sprintf('%s / %s', $group_label, $field['label'] ?? $field['name']);
            $type = $field['type'] ?? '';

            if ('repeater' === $type) {
                $collected['repeaters'][$path] = [
                    'label' => $label,
                    'type' => $type,
                ];
            } elseif (!in_array($type, ['group', 'tab', 'accordion', 'message'], true)) {
                $collected['leaf_fields'][$path] = [
                    'label' => $label,
                    'type' => $type,
                ];
            }

            if (!empty($field['sub_fields']) && is_array($field['sub_fields'])) {
                self::walk_acf_fields($field['sub_fields'], $path, $collected, $label);
            }
        }
    }

    private static function get_pods_repeaters($post_type)
    {
        return self::pods_collect_fields($post_type, 'repeaters');
    }

    private static function get_pods_leaf_fields($post_type)
    {
        return self::pods_collect_fields($post_type, 'leaf_fields');
    }

    private static function pods_collect_fields($post_type, $target)
    {
        if (!function_exists('pods_api')) {
            return [];
        }

        $pod = self::get_pods_definition($post_type);
        $fields = isset($pod['fields']) && is_array($pod['fields']) ? $pod['fields'] : [];
        $collected = [
            'repeaters' => [],
            'leaf_fields' => [],
        ];

        foreach ($fields as $name => $field) {
            $field_name = is_string($name) ? $name : ($field['name'] ?? '');

            if (!$field_name) {
                continue;
            }

            $label = $field['label'] ?? $field_name;
            $type = $field['type'] ?? '';
            $is_repeatable = !empty($field['repeatable']) || !empty($field['options']['repeatable']);

            if ($is_repeatable || in_array($type, ['pick', 'file'], true)) {
                $collected['repeaters'][$field_name] = [
                    'label' => $label,
                    'type' => $type,
                ];
            } else {
                $collected['leaf_fields'][$field_name] = [
                    'label' => $label,
                    'type' => $type,
                ];
            }
        }

        return $collected[$target];
    }

    private static function get_pods_definition($post_type)
    {
        $api = pods_api();

        if (!is_object($api) || !method_exists($api, 'load_pod')) {
            return [];
        }

        $pod = $api->load_pod(['name' => $post_type], false);

        return is_array($pod) ? $pod : [];
    }

    private static function get_metabox_repeaters($post_type)
    {
        return self::metabox_collect_fields($post_type, 'repeaters');
    }

    private static function get_metabox_leaf_fields($post_type)
    {
        return self::metabox_collect_fields($post_type, 'leaf_fields');
    }

    private static function metabox_collect_fields($post_type, $target)
    {
        if (!function_exists('rwmb_get_registry')) {
            return [];
        }

        $registry = rwmb_get_registry('field');

        if (!is_object($registry) || !method_exists($registry, 'get_by_object_type')) {
            return [];
        }

        $all_fields = $registry->get_by_object_type('post');
        $fields = $all_fields[$post_type] ?? [];
        $collected = [
            'repeaters' => [],
            'leaf_fields' => [],
        ];

        self::walk_metabox_fields($fields, '', $collected, 'Meta Box');

        return $collected[$target];
    }

    private static function walk_metabox_fields($fields, $prefix, array &$collected, $group_label)
    {
        foreach ((array) $fields as $field) {
            if (empty($field['id'])) {
                continue;
            }

            $path = $prefix ? $prefix . '/' . $field['id'] : $field['id'];
            $label = sprintf('%s / %s', $group_label, $field['name'] ?? $field['id']);
            $type = $field['type'] ?? '';
            $is_group = 'group' === $type;
            $is_repeatable = !empty($field['clone']) || !empty($field['multiple']);

            if ($is_group && $is_repeatable) {
                $collected['repeaters'][$path] = [
                    'label' => $label,
                    'type' => $type,
                ];
            } elseif (!$is_group) {
                $collected['leaf_fields'][$path] = [
                    'label' => $label,
                    'type' => $type,
                ];
            }

            if (!empty($field['fields']) && is_array($field['fields'])) {
                self::walk_metabox_fields($field['fields'], $path, $collected, $label);
            }
        }
    }
}
