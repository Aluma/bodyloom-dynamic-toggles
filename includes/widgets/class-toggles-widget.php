<?php
namespace Bodyloom\DynamicToggles\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Icons_Manager;
use Elementor\Utils;
use Bodyloom\DynamicToggles\Field_Discovery;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Toggles extends Widget_Base
{

    public function get_name()
    {
        return 'bodyloom-toggles';
    }

    public function get_title()
    {
        return esc_html__('Bodyloom Toggles', 'bodyloom-dynamic-toggles');
    }

    public function get_icon()
    {
        return 'eicon-toggle';
    }

    public function get_categories()
    {
        return ['general'];
    }

    public function get_script_depends()
    {
        return ['bodyloom-toggles'];
    }

    public function get_style_depends()
    {
        return ['bodyloom-toggles'];
    }

    protected function is_dynamic_content(): bool
    {
        return true;
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Toggles', 'bodyloom-dynamic-toggles'),
            ]
        );

        $this->add_control(
            'type',
            [
                'label' => esc_html__('Type', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'toggles',
                'options' => [
                    'toggles' => [
                        'title' => esc_html__('Toggles', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-toggle',
                    ],
                    'accordion' => [
                        'title' => esc_html__('Accordion', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-accordion',
                    ],
                ],
                'frontend_available' => true,
                'toggle' => false,
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'data_source',
            [
                'label' => esc_html__('Data Source', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::CHOOSE,
                'default' => 'static',
                'options' => [
                    'static' => [
                        'title' => esc_html__('Static', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-edit',
                    ],
                    'acf_repeater' => [
                        'title' => esc_html__('ACF Repeater', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-database',
                    ],
                    'pods_repeater' => [
                        'title' => esc_html__('Pods', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-database',
                    ],
                    'metabox_repeater' => [
                        'title' => esc_html__('Meta Box', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-database',
                    ],
                ],
                'toggle' => false,
            ]
        );

        // Static Repeater Controls
        $repeater = new Repeater();

        $repeater->add_control(
            'toggle_title',
            [
                'label' => esc_html__('Title', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('Toggle Title', 'bodyloom-dynamic-toggles'),
                'dynamic' => ['active' => true],
                'label_block' => true,
            ]
        );

        $repeater->add_control(
            'toggle_content',
            [
                'label' => esc_html__('Content', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::WYSIWYG,
                'default' => esc_html__('Toggle Content', 'bodyloom-dynamic-toggles'),
                'show_label' => false,
            ]
        );

        $repeater->add_control(
            'toggle_custom_id',
            [
                'label' => esc_html__('Custom ID', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::TEXT,
                'placeholder' => 'my-toggle-id',
                'description' => esc_html__('Add a custom ID for deep linking (e.g., #my-toggle-id).', 'bodyloom-dynamic-toggles'),
            ]
        );

        $this->add_control(
            'toggles',
            [
                'label' => esc_html__('Items', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'toggle_title' => esc_html__('Item #1', 'bodyloom-dynamic-toggles'),
                        'toggle_content' => esc_html__('Lorem ipsum dolor sit amet.', 'bodyloom-dynamic-toggles'),
                    ],
                    [
                        'toggle_title' => esc_html__('Item #2', 'bodyloom-dynamic-toggles'),
                        'toggle_content' => esc_html__('Lorem ipsum dolor sit amet.', 'bodyloom-dynamic-toggles'),
                    ],
                ],
                'title_field' => '{{{ toggle_title }}}',
                'condition' => [
                    'data_source' => 'static',
                ],
            ]
        );

        // ACF Controls
        $this->add_control(
            'acf_repeater_notice',
            [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => esc_html__('Choose a discovered field when available, or use the manual path fallback.', 'bodyloom-dynamic-toggles'),
                'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
                'condition' => ['data_source!' => 'static'],
            ]
        );

        $this->add_control(
            'acf_repeater_field_name',
            [
                'label' => esc_html__('Repeater Field Path', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::SELECT2,
                'options' => Field_Discovery::get_repeater_options(get_post_type() ?: 'post'),
                'description' => esc_html__('Supports nested paths such as parent_group/repeater_name.', 'bodyloom-dynamic-toggles'),
                'condition' => ['data_source!' => 'static'],
            ]
        );

        $this->add_control(
            'acf_repeater_field_name_manual',
            [
                'label' => esc_html__('Manual Repeater Field Path', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::TEXT,
                'condition' => ['data_source!' => 'static'],
            ]
        );

        $this->add_control(
            'acf_title_field',
            [
                'label' => esc_html__('Title Sub-Field', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::SELECT2,
                'options' => Field_Discovery::get_leaf_field_options(get_post_type() ?: 'post'),
                'condition' => ['data_source!' => 'static'],
            ]
        );

        $this->add_control(
            'acf_title_field_manual',
            [
                'label' => esc_html__('Manual Title Sub-Field', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::TEXT,
                'condition' => ['data_source!' => 'static'],
            ]
        );

        $this->add_control(
            'acf_content_field',
            [
                'label' => esc_html__('Content Sub-Field', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::SELECT2,
                'options' => Field_Discovery::get_leaf_field_options(get_post_type() ?: 'post'),
                'condition' => ['data_source!' => 'static'],
            ]
        );

        $this->add_control(
            'acf_content_field_manual',
            [
                'label' => esc_html__('Manual Content Sub-Field', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::TEXT,
                'condition' => ['data_source!' => 'static'],
            ]
        );

        $this->add_control(
            'title_html_tag',
            [
                'label' => esc_html__('Title HTML Tag', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                ],
                'default' => 'div',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'faq_schema',
            [
                'label' => esc_html__('FAQ Schema', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'default_toggle',
            [
                'label' => esc_html__('Active Toggle', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'default' => 0,
                'description' => esc_html__('Enter the number of the item to be active by default (e.g. 1). Set to 0 to keep all closed.', 'bodyloom-dynamic-toggles'),
                'render_type' => 'template',
            ]
        );

        $this->add_control(
            'trigger_icon',
            [
                'label' => esc_html__('Icon', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-plus',
                    'library' => 'fa-solid',
                ],
            ]
        );

        $this->add_control(
            'trigger_active_icon',
            [
                'label' => esc_html__('Active Icon', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-minus',
                    'library' => 'fa-solid',
                ],
                'condition' => [
                    'trigger_icon[value]!' => '',
                ],
            ]
        );

        $this->end_controls_section();

        // Style Section - Item
        $this->start_controls_section(
            'section_style_item',
            [
                'label' => esc_html__('Item', 'bodyloom-dynamic-toggles'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'item_space_between',
            [
                'label' => esc_html__('Space Between', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bodyloom-toggles__item:last-child' => 'margin-bottom: 0;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__item',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'item_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__item',
            ]
        );

        $this->end_controls_section();

        // Style Section - Title
        $this->start_controls_section(
            'section_style_title',
            [
                'label' => esc_html__('Title', 'bodyloom-dynamic-toggles'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__title',
            ]
        );

        $this->start_controls_tabs('title_colors_tabs');

        // Normal
        $this->start_controls_tab(
            'title_colors_normal',
            ['label' => esc_html__('Normal', 'bodyloom-dynamic-toggles')]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bodyloom-toggles__title-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bg_color',
            [
                'label' => esc_html__('Background', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bd_color',
            [
                'label' => esc_html__('Border Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title' => 'border-color: {{VALUE}};',
                ],
                'condition' => ['title_border_border!' => ''],
            ]
        );

        $this->add_responsive_control(
            'title_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'title_box_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__title',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__title-text',
            ]
        );

        $this->end_controls_tab();

        // Hover
        $this->start_controls_tab(
            'title_colors_hover',
            ['label' => esc_html__('Hover', 'bodyloom-dynamic-toggles')]
        );

        $this->add_control(
            'title_color_hover',
            [
                'label' => esc_html__('Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title:hover' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bodyloom-toggles__title:hover .bodyloom-toggles__title-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bg_color_hover',
            [
                'label' => esc_html__('Background', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bd_color_hover',
            [
                'label' => esc_html__('Border Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title:hover' => 'border-color: {{VALUE}};',
                ],
                'condition' => ['title_border_border!' => ''],
            ]
        );

        $this->add_responsive_control(
            'title_border_radius_hover',
            [
                'label' => esc_html__('Border Radius', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'title_box_shadow_hover',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__title:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow_hover',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__title:hover .bodyloom-toggles__title-text',
            ]
        );

        $this->end_controls_tab();

        // Active
        $this->start_controls_tab(
            'title_colors_active',
            ['label' => esc_html__('Active', 'bodyloom-dynamic-toggles')]
        );

        $this->add_control(
            'title_color_active',
            [
                'label' => esc_html__('Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title.active-toggle' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bodyloom-toggles__title.active-toggle .bodyloom-toggles__title-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bg_color_active',
            [
                'label' => esc_html__('Background', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title.active-toggle' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_bd_color_active',
            [
                'label' => esc_html__('Border Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title.active-toggle' => 'border-color: {{VALUE}};',
                ],
                'condition' => ['title_border_border!' => ''],
            ]
        );

        $this->add_responsive_control(
            'title_border_radius_active',
            [
                'label' => esc_html__('Border Radius', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title.active-toggle' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'title_box_shadow_active',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__title.active-toggle',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'title_text_shadow_active',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__title.active-toggle .bodyloom-toggles__title-text',
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'title_padding',
            [
                'label' => esc_html__('Padding', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'title_border',
                'label' => esc_html__('Border', 'bodyloom-dynamic-toggles'),
                'selector' => '{{WRAPPER}} .bodyloom-toggles__title',
            ]
        );

        $this->end_controls_section();

        // Style Section - Trigger
        $this->start_controls_section(
            'section_style_trigger',
            [
                'label' => esc_html__('Trigger', 'bodyloom-dynamic-toggles'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => ['trigger_icon[value]!' => ''],
            ]
        );

        $this->add_control(
            'trigger_icon_view',
            [
                'label' => __('View', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::CHOOSE, // Using CHOOSE as pseudo-buttons for Default/Stacked/Framed
                'options' => [
                    'default' => [
                        'title' => __('Default', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-ban', // No surrounding
                    ],
                    'stacked' => [
                        'title' => __('Stacked', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-circle',
                    ],
                    'framed' => [
                        'title' => __('Framed', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-square',
                    ],
                ],
                'default' => 'default',
                'prefix_class' => 'bodyloom-trigger-view-',
            ]
        );

        $this->add_responsive_control(
            'trigger_icon_size',
            [
                'label' => __('Size', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 80,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '--trigger-icon-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->start_controls_tabs('trigger_icon_colors_tabs');

        $this->start_controls_tab(
            'trigger_icon_colors_normal',
            ['label' => esc_html__('Normal', 'bodyloom-dynamic-toggles')]
        );

        $this->add_control(
            'trigger_icon_color',
            [
                'label' => esc_html__('Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__trigger' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'trigger_icon_bg_color',
            [
                'label' => esc_html__('Background', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__trigger' => 'background-color: {{VALUE}};',
                ],
                'condition' => ['trigger_icon_view!' => 'default'],
            ]
        );

        $this->add_control(
            'trigger_icon_bd_color',
            [
                'label' => esc_html__('Border Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__trigger' => 'border-color: {{VALUE}};',
                ],
                'condition' => ['trigger_icon_view' => 'framed'],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'trigger_icon_colors_hover',
            ['label' => esc_html__('Hover', 'bodyloom-dynamic-toggles')]
        );

        $this->add_control(
            'trigger_icon_color_hover',
            [
                'label' => esc_html__('Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title:hover .bodyloom-toggles__trigger' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'trigger_icon_bg_color_hover',
            [
                'label' => esc_html__('Background', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title:hover .bodyloom-toggles__trigger' => 'background-color: {{VALUE}};',
                ],
                'condition' => ['trigger_icon_view!' => 'default'],
            ]
        );

        $this->add_control(
            'trigger_icon_bd_color_hover',
            [
                'label' => esc_html__('Border Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title:hover .bodyloom-toggles__trigger' => 'border-color: {{VALUE}};',
                ],
                'condition' => ['trigger_icon_view' => 'framed'],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'trigger_icon_colors_active',
            ['label' => esc_html__('Active', 'bodyloom-dynamic-toggles')]
        );

        $this->add_control(
            'trigger_icon_color_active',
            [
                'label' => esc_html__('Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title.active-toggle .bodyloom-toggles__trigger' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'trigger_icon_bg_color_active',
            [
                'label' => esc_html__('Background', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title.active-toggle .bodyloom-toggles__trigger' => 'background-color: {{VALUE}};',
                ],
                'condition' => ['trigger_icon_view!' => 'default'],
            ]
        );

        $this->add_control(
            'trigger_icon_bd_color_active',
            [
                'label' => esc_html__('Border Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__title.active-toggle .bodyloom-toggles__trigger' => 'border-color: {{VALUE}};',
                ],
                'condition' => ['trigger_icon_view' => 'framed'],
            ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
            'trigger_icon_padding',
            [
                'label' => esc_html__('Padding', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__trigger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => ['trigger_icon_view!' => 'default'],
            ]
        );

        $this->add_responsive_control(
            'trigger_icon_bd_width',
            [
                'label' => esc_html__('Border Width', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__trigger' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; border-style: solid;',
                ],
                'condition' => ['trigger_icon_view' => 'framed'],
            ]
        );

        $this->add_responsive_control(
            'trigger_icon_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__trigger' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => ['trigger_icon_view!' => 'default'],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'trigger_icon_box_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__trigger',
                'condition' => ['trigger_icon_view!' => 'default'],
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'trigger_icon_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__trigger',
            ]
        );

        $this->end_controls_section();

        // Style Section - Content
        $this->start_controls_section(
            'section_style_content',
            [
                'label' => esc_html__('Content', 'bodyloom-dynamic-toggles'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'content_alignment',
            [
                'label' => esc_html__('Alignment', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bodyloom-dynamic-toggles'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'left',
                'prefix_class' => 'elementor-align-', // Changed from selectors to prefix_class or use selector on content
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__content',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label' => esc_html__('Color', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_link_hover',
            [
                'label' => esc_html__('Link Hover', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__content a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'content_background_color',
            [
                'label' => esc_html__('Background', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__content' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'toggles_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'toggles_box_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__item',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'content_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__content',
            ]
        );

        $this->add_responsive_control(
            'content_gap',
            [
                'label' => esc_html__('Gap', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__content' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label' => esc_html__('Padding', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_border',
                'label' => esc_html__('Border', 'bodyloom-dynamic-toggles'),
                'selector' => '{{WRAPPER}} .bodyloom-toggles__content',
            ]
        );

        $this->add_responsive_control(
            'content_border_radius',
            [
                'label' => esc_html__('Border Radius', 'bodyloom-dynamic-toggles'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bodyloom-toggles__content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'content_box_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__content',
            ]
        );

        $this->add_group_control(
            Group_Control_Text_Shadow::get_type(),
            [
                'name' => 'content_shadow',
                'selector' => '{{WRAPPER}} .bodyloom-toggles__content',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $toggles = [];

        if ('static' !== $settings['data_source']) {
            $toggles = $this->get_dynamic_toggles($settings);
        } else {
            $toggles = $settings['toggles'];
        }

        if (empty($toggles)) {
            return;
        }

        $id_int = substr($this->get_id_int(), 0, 3);
        $widget_class = 'bodyloom-toggles';

        echo '<div class="' . esc_attr($widget_class . '__list') . '" data-type="' . esc_attr($settings['type']) . '" data-default-toggle="' . esc_attr((string) $settings['default_toggle']) . '">';

        foreach ($toggles as $index => $item) {
            $tab_count = $index + 1;

            $toggle_title_setting_key = $this->get_repeater_setting_key('toggle_title', 'toggles', $index);
            $toggle_content_setting_key = $this->get_repeater_setting_key('toggle_content', 'toggles', $index);

            $is_active = ($tab_count == $settings['default_toggle']);
            $active_class = $is_active ? 'active-toggle' : '';
            $content_style = $is_active ? 'style="display: block;"' : '';

            $this->add_render_attribute($toggle_title_setting_key, [
                'id' => 'elementor-tab-title-' . $id_int . $tab_count,
                'class' => [$widget_class . '__title', $active_class],
                'data-tab' => $tab_count,
                'role' => 'button',
                'tabindex' => '0',
                'aria-expanded' => $is_active ? 'true' : 'false',
            ]);

            $this->add_render_attribute($toggle_content_setting_key, [
                'id' => $widget_class . '__content-' . $id_int . $tab_count,
                'class' => [$widget_class . '__content', $active_class],
                'data-tab' => $tab_count,
            ]);

            // Only add inline editing for static content
            if ($settings['data_source'] === 'static') {
                $this->add_inline_editing_attributes($toggle_content_setting_key, 'advanced');
            }

            $custom_id = '';
            if (!empty($item['toggle_custom_id'])) {
                $custom_id = ' data-toggle-custom-id="' . esc_attr(str_replace('#', '', $item['toggle_custom_id'])) . '"';
            }

            echo '<div class="' . esc_attr($widget_class . '__item') . '"' . $custom_id . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $custom_id is escaped above.

            // Title
            $title_tag = Utils::validate_html_tag($settings['title_html_tag']);
            echo '<' . tag_escape($title_tag) . ' ' . $this->get_render_attribute_string($toggle_title_setting_key) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor render attributes are escaped by get_render_attribute_string().

            // Link
            echo '<a class="' . esc_attr($widget_class . '__title-link') . '" href="#" tabindex="-1">';
            echo '<span class="' . esc_attr($widget_class . '__title-text') . '">' . wp_kses_post($item['toggle_title']) . '</span>';
            echo '</a>';

            // Trigger Icon
            if (!empty($settings['trigger_icon']['value'])) {
                echo '<span class="' . esc_attr($widget_class . '__trigger') . '">';

                echo '<span class="' . esc_attr($widget_class . '__trigger-closed') . '">';
                Icons_Manager::render_icon($settings['trigger_icon'], ['aria-hidden' => 'true']);
                echo '</span>';

                $active_icon = !empty($settings['trigger_active_icon']['value']) ? $settings['trigger_active_icon'] : $settings['trigger_icon'];
                echo '<span class="' . esc_attr($widget_class . '__trigger-opened') . '">';
                Icons_Manager::render_icon($active_icon, ['aria-hidden' => 'true']);
                echo '</span>';

                echo '</span>';
            }

            echo '</' . tag_escape($title_tag) . '>';

            // Content
            echo '<div ' . $this->get_render_attribute_string($toggle_content_setting_key) . ' ' . $content_style . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $this->parse_text_editor($item['toggle_content']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Elementor text editor parser returns sanitized editor content.
            echo '</div>';

            echo '</div>'; // End Item
        }

        echo '</div>'; // End List

        // FAQ Schema
        if (isset($settings['faq_schema']) && 'yes' === $settings['faq_schema']) {
            // Implementation for schema similar to reference...
            $json = [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => [],
            ];
            foreach ($toggles as $item) {
                if (empty($item['toggle_title']) || empty($item['toggle_content']))
                    continue;
                $json['mainEntity'][] = [
                    '@type' => 'Question',
                    'name' => wp_strip_all_tags($item['toggle_title']),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => wp_strip_all_tags($item['toggle_content']),
                    ],
                ];
            }
            $json_ld = wp_json_encode(
                $json,
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            );

            if (false !== $json_ld) {
                echo '<script type="application/ld+json">' . $json_ld . '</script>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }
    }

    /**
     * Return the first non-empty setting from a list of control names.
     *
     * The sub-field controls were renamed across releases
     * (acf_repeater_*_sub_field -> acf_*_field), so documents saved by an older
     * build still carry the previous names. Reading every known name keeps those
     * widgets rendering instead of silently emptying on upgrade.
     *
     * @param array $settings Widget settings.
     * @param array $keys     Control names, newest first.
     * @return string First non-empty value, or ''.
     */
    private function first_filled($settings, array $keys)
    {
        foreach ($keys as $key) {
            if (!empty($settings[$key]) && is_string($settings[$key])) {
                return $settings[$key];
            }
        }

        return '';
    }

    protected function get_dynamic_toggles($settings)
    {
        $repeater_name = $this->first_filled($settings, [
            'acf_repeater_field_name',
            'acf_repeater_field_name_manual',
        ]);
        $title_field = $this->first_filled($settings, [
            'acf_title_field',
            'acf_title_field_manual',
            'acf_repeater_title_sub_field',
        ]);
        $content_field = $this->first_filled($settings, [
            'acf_content_field',
            'acf_content_field_manual',
            'acf_repeater_content_sub_field',
        ]);

        if (empty($repeater_name) || empty($title_field) || empty($content_field)) {
            return [];
        }

        $post_id = get_the_ID();
        $source = '';

        if ('acf_repeater' === $settings['data_source']) {
            $source = 'acf';
        } elseif ('pods_repeater' === $settings['data_source']) {
            $source = 'pods';
        } elseif ('metabox_repeater' === $settings['data_source']) {
            $source = 'metabox';
        }

        $provider = \Bodyloom\DynamicToggles\Provider_Factory::get_provider($source, $repeater_name);

        if (!$provider) {
            return [];
        }

        return $provider->get_repeater_data($post_id, $repeater_name, $title_field, $content_field);
    }
}
