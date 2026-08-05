(function (blocks, element, components, blockEditor, i18n) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var TextControl = components.TextControl;
    var SelectControl = components.SelectControl;
    var ToggleControl = components.ToggleControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var ServerSideRender = components.ServerSideRender || wp.serverSideRender;
    var useEffect = element.useEffect;
    var useState = element.useState;
    var __ = i18n.__;

    registerBlockType('vybose/repeater-accordion', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var fieldState = useState({
                repeaters: [{ label: __('Manual entry / no discovered field', 'vybose-repeater-accordion'), value: '' }],
                leafFields: [{ label: __('Manual entry / no discovered field', 'vybose-repeater-accordion'), value: '' }]
            });
            var fieldOptions = fieldState[0];
            var setFieldOptions = fieldState[1];

            useEffect(function () {
                if (!window.wp || !window.wp.apiFetch) {
                    return;
                }

                window.wp.apiFetch({ path: '/vybose-repeater-accordion/v1/fields' }).then(function (response) {
                    var repeaters = [{ label: __('Manual entry / no discovered field', 'vybose-repeater-accordion'), value: '' }];
                    var leafFields = [{ label: __('Manual entry / no discovered field', 'vybose-repeater-accordion'), value: '' }];
                    var sources = response && response.sources ? response.sources : {};

                    Object.keys(sources).forEach(function (source) {
                        var sourceData = sources[source];
                        Object.keys(sourceData.repeaters || {}).forEach(function (path) {
                            repeaters.push({
                                label: sourceData.label + ': ' + sourceData.repeaters[path].label,
                                value: source + ':' + path
                            });
                        });
                        Object.keys(sourceData.leaf_fields || {}).forEach(function (path) {
                            leafFields.push({
                                label: sourceData.label + ': ' + sourceData.leaf_fields[path].label,
                                value: path
                            });
                        });
                    });

                    setFieldOptions({
                        repeaters: repeaters,
                        leafFields: leafFields
                    });
                }).catch(function () {
                    setFieldOptions({
                        repeaters: [{ label: __('Manual entry / no discovered field', 'vybose-repeater-accordion'), value: '' }],
                        leafFields: [{ label: __('Manual entry / no discovered field', 'vybose-repeater-accordion'), value: '' }]
                    });
                });
            }, []);

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Settings', 'vybose-repeater-accordion'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Source', 'vybose-repeater-accordion'),
                            value: attributes.source,
                            options: [
                                { label: __('First available provider', 'vybose-repeater-accordion'), value: '' },
                                { label: __('ACF', 'vybose-repeater-accordion'), value: 'acf' },
                                { label: __('Pods', 'vybose-repeater-accordion'), value: 'pods' },
                                { label: __('Meta Box', 'vybose-repeater-accordion'), value: 'metabox' }
                            ],
                            onChange: function (val) { setAttributes({ source: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Repeater Field', 'vybose-repeater-accordion'),
                            value: attributes.repeater_field,
                            options: fieldOptions.repeaters,
                            onChange: function (val) { setAttributes({ repeater_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Repeater Field Path', 'vybose-repeater-accordion'),
                            value: attributes.repeater_field_manual,
                            onChange: function (val) { setAttributes({ repeater_field_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Title Sub-Field', 'vybose-repeater-accordion'),
                            value: attributes.title_field,
                            options: fieldOptions.leafFields,
                            onChange: function (val) { setAttributes({ title_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Title Sub-Field', 'vybose-repeater-accordion'),
                            value: attributes.title_field_manual,
                            onChange: function (val) { setAttributes({ title_field_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Content Sub-Field', 'vybose-repeater-accordion'),
                            value: attributes.content_field,
                            options: fieldOptions.leafFields,
                            onChange: function (val) { setAttributes({ content_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Content Sub-Field', 'vybose-repeater-accordion'),
                            value: attributes.content_field_manual,
                            onChange: function (val) { setAttributes({ content_field_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Type', 'vybose-repeater-accordion'),
                            value: attributes.type,
                            options: [
                                { label: __('Toggles', 'vybose-repeater-accordion'), value: 'toggles' },
                                { label: __('Accordion', 'vybose-repeater-accordion'), value: 'accordion' }
                            ],
                            onChange: function (val) { setAttributes({ type: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Style', 'vybose-repeater-accordion'),
                            value: attributes.style,
                            options: [
                                { label: __('Default (Arrow)', 'vybose-repeater-accordion'), value: 'default' },
                                { label: __('Plus/Minus', 'vybose-repeater-accordion'), value: 'plus-minus' },
                                { label: __('Chevron', 'vybose-repeater-accordion'), value: 'chevron' }
                            ],
                            onChange: function (val) { setAttributes({ style: val }); }
                        }),
                        el(ToggleControl, {
                            label: __('Open First Item', 'vybose-repeater-accordion'),
                            checked: attributes.open_first,
                            onChange: function (val) { setAttributes({ open_first: val }); }
                        }),
                        el(ToggleControl, {
                            label: __('Enable FAQ Schema', 'vybose-repeater-accordion'),
                            checked: attributes.faq_schema,
                            onChange: function (val) { setAttributes({ faq_schema: val }); }
                        })
                    )
                ),
                el('div', { className: props.className },
                    el(ServerSideRender, {
                        block: 'vybose/repeater-accordion',
                        attributes: attributes
                    })
                )
            ];
        },
        save: function () {
            return null; // Rendered via PHP
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor,
    window.wp.i18n
);
