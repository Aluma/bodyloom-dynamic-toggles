<?php

if (!defined('ABSPATH')) {
    exit;
}

return [
    'dependencies' => [
        'wp-api-fetch',
        'wp-block-editor',
        'wp-blocks',
        'wp-components',
        'wp-element',
        'wp-i18n',
        'wp-server-side-render',
    ],
    'version' => defined('BODYLOOM_TOGGLES_VERSION') ? BODYLOOM_TOGGLES_VERSION : '1.1.0',
];
