/**
 * Buy Now Button Block - Built File
 */
(function() {
    'use strict';

    var __ = wp.i18n.__;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps = wp.blockEditor.useBlockProps;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var RangeControl = wp.components.RangeControl;
    var Notice = wp.components.Notice;

    var Edit = function(props) {
        var attributes = props.attributes;
        var setAttributes = props.setAttributes;

        var buttonText = attributes.buttonText;
        var buttonStyle = attributes.buttonStyle;
        var buttonSize = attributes.buttonSize;
        var fullWidth = attributes.fullWidth;
        var clearCart = attributes.clearCart;
        var backgroundColor = attributes.backgroundColor;
        var textColor = attributes.textColor;
        var borderRadius = attributes.borderRadius;

        var blockProps = useBlockProps({
            className: 'jankx-wc-buy-now-button-edit button button--' + buttonStyle + ' button--' + buttonSize,
            style: {
                backgroundColor: backgroundColor || undefined,
                color: textColor || undefined,
                borderRadius: borderRadius ? borderRadius + 'px' : undefined,
                width: fullWidth ? '100%' : 'auto'
            }
        });

        var isEnabled = window.jankxWooCommerceBlocks && window.jankxWooCommerceBlocks.buyNowEnabled !== false;

        return wp.element.createElement(
            wp.element.Fragment,
            null,
            wp.element.createElement(
                InspectorControls,
                null,
                wp.element.createElement(
                    PanelBody,
                    { title: __('Button Settings', 'jankx'), initialOpen: true },
                    !isEnabled && wp.element.createElement(
                        Notice,
                        { status: 'warning', isDismissible: false },
                        __('Buy Now feature is disabled in theme options.', 'jankx')
                    ),
                    wp.element.createElement(TextControl, {
                        label: __('Button Text', 'jankx'),
                        value: buttonText,
                        onChange: function(value) { setAttributes({ buttonText: value }); },
                        placeholder: __('MUA NGAY', 'jankx')
                    }),
                    wp.element.createElement(SelectControl, {
                        label: __('Button Style', 'jankx'),
                        value: buttonStyle,
                        options: [
                            { label: __('Primary', 'jankx'), value: 'primary' },
                            { label: __('Secondary', 'jankx'), value: 'secondary' },
                            { label: __('Outline', 'jankx'), value: 'outline' }
                        ],
                        onChange: function(value) { setAttributes({ buttonStyle: value }); }
                    }),
                    wp.element.createElement(SelectControl, {
                        label: __('Button Size', 'jankx'),
                        value: buttonSize,
                        options: [
                            { label: __('Small', 'jankx'), value: 'small' },
                            { label: __('Medium', 'jankx'), value: 'medium' },
                            { label: __('Large', 'jankx'), value: 'large' }
                        ],
                        onChange: function(value) { setAttributes({ buttonSize: value }); }
                    }),
                    wp.element.createElement(ToggleControl, {
                        label: __('Full Width', 'jankx'),
                        checked: fullWidth,
                        onChange: function(value) { setAttributes({ fullWidth: value }); }
                    }),
                    wp.element.createElement(ToggleControl, {
                        label: __('Clear Cart Before Adding', 'jankx'),
                        checked: clearCart,
                        onChange: function(value) { setAttributes({ clearCart: value }); },
                        help: __('Enable for single product checkout', 'jankx')
                    })
                ),
                wp.element.createElement(
                    PanelBody,
                    { title: __('Appearance', 'jankx'), initialOpen: false },
                    wp.element.createElement(TextControl, {
                        label: __('Background Color', 'jankx'),
                        value: backgroundColor,
                        onChange: function(value) { setAttributes({ backgroundColor: value }); },
                        placeholder: '#ff5722',
                        help: __('Hex color or CSS color value', 'jankx')
                    }),
                    wp.element.createElement(TextControl, {
                        label: __('Text Color', 'jankx'),
                        value: textColor,
                        onChange: function(value) { setAttributes({ textColor: value }); },
                        placeholder: '#ffffff'
                    }),
                    wp.element.createElement(RangeControl, {
                        label: __('Border Radius', 'jankx'),
                        value: borderRadius,
                        onChange: function(value) { setAttributes({ borderRadius: value }); },
                        min: 0,
                        max: 50
                    })
                )
            ),
            wp.element.createElement(
                'div',
                blockProps,
                wp.element.createElement('span', { className: 'button-text' }, buttonText || __('MUA NGAY', 'jankx'))
            )
        );
    };

    registerBlockType('jankx-woocommerce/buy-now-button', {
        edit: Edit,
        save: function() { return null; }
    });
})();
