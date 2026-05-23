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

    registerBlockType('bodyloom/dynamic-toggles', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var fieldState = useState({
                repeaters: [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-toggles'), value: '' }],
                leafFields: [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-toggles'), value: '' }]
            });
            var fieldOptions = fieldState[0];
            var setFieldOptions = fieldState[1];

            useEffect(function () {
                if (!window.wp || !window.wp.apiFetch) {
                    return;
                }

                window.wp.apiFetch({ path: '/bodyloom-dynamic-toggles/v1/fields' }).then(function (response) {
                    var repeaters = [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-toggles'), value: '' }];
                    var leafFields = [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-toggles'), value: '' }];
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
                        repeaters: [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-toggles'), value: '' }],
                        leafFields: [{ label: __('Manual entry / no discovered field', 'bodyloom-dynamic-toggles'), value: '' }]
                    });
                });
            }, []);

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Settings', 'bodyloom-dynamic-toggles'), initialOpen: true },
                        el(SelectControl, {
                            label: __('Source', 'bodyloom-dynamic-toggles'),
                            value: attributes.source,
                            options: [
                                { label: __('First available provider', 'bodyloom-dynamic-toggles'), value: '' },
                                { label: __('ACF', 'bodyloom-dynamic-toggles'), value: 'acf' },
                                { label: __('Pods', 'bodyloom-dynamic-toggles'), value: 'pods' },
                                { label: __('Meta Box', 'bodyloom-dynamic-toggles'), value: 'metabox' }
                            ],
                            onChange: function (val) { setAttributes({ source: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Repeater Field', 'bodyloom-dynamic-toggles'),
                            value: attributes.repeater_field,
                            options: fieldOptions.repeaters,
                            onChange: function (val) { setAttributes({ repeater_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Repeater Field Path', 'bodyloom-dynamic-toggles'),
                            value: attributes.repeater_field_manual,
                            onChange: function (val) { setAttributes({ repeater_field_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Title Sub-Field', 'bodyloom-dynamic-toggles'),
                            value: attributes.title_field,
                            options: fieldOptions.leafFields,
                            onChange: function (val) { setAttributes({ title_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Title Sub-Field', 'bodyloom-dynamic-toggles'),
                            value: attributes.title_field_manual,
                            onChange: function (val) { setAttributes({ title_field_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Discovered Content Sub-Field', 'bodyloom-dynamic-toggles'),
                            value: attributes.content_field,
                            options: fieldOptions.leafFields,
                            onChange: function (val) { setAttributes({ content_field: val }); }
                        }),
                        el(TextControl, {
                            label: __('Manual Content Sub-Field', 'bodyloom-dynamic-toggles'),
                            value: attributes.content_field_manual,
                            onChange: function (val) { setAttributes({ content_field_manual: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Type', 'bodyloom-dynamic-toggles'),
                            value: attributes.type,
                            options: [
                                { label: __('Toggles', 'bodyloom-dynamic-toggles'), value: 'toggles' },
                                { label: __('Accordion', 'bodyloom-dynamic-toggles'), value: 'accordion' }
                            ],
                            onChange: function (val) { setAttributes({ type: val }); }
                        }),
                        el(SelectControl, {
                            label: __('Style', 'bodyloom-dynamic-toggles'),
                            value: attributes.style,
                            options: [
                                { label: __('Default (Arrow)', 'bodyloom-dynamic-toggles'), value: 'default' },
                                { label: __('Plus/Minus', 'bodyloom-dynamic-toggles'), value: 'plus-minus' },
                                { label: __('Chevron', 'bodyloom-dynamic-toggles'), value: 'chevron' }
                            ],
                            onChange: function (val) { setAttributes({ style: val }); }
                        }),
                        el(ToggleControl, {
                            label: __('Open First Item', 'bodyloom-dynamic-toggles'),
                            checked: attributes.open_first,
                            onChange: function (val) { setAttributes({ open_first: val }); }
                        }),
                        el(ToggleControl, {
                            label: __('Enable FAQ Schema', 'bodyloom-dynamic-toggles'),
                            checked: attributes.faq_schema,
                            onChange: function (val) { setAttributes({ faq_schema: val }); }
                        })
                    )
                ),
                el('div', { className: props.className },
                    el(ServerSideRender, {
                        block: 'bodyloom/dynamic-toggles',
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
