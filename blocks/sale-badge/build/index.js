/**
 * Sale Badge Block - Built File
 */
(function() {
    'use strict';

    var __ = wp.i18n.__;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var useBlockProps = wp.blockEditor.useBlockProps;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var TextControl = wp.components.TextControl;
    var RangeControl = wp.components.RangeControl;
    var Notice = wp.components.Notice;

    var Edit = function(props) {
        var attributes = props.attributes;
        var setAttributes = props.setAttributes;

        var badgeStyle = attributes.badgeStyle;
        var customText = attributes.customText;
        var position = attributes.position;
        var backgroundColor = attributes.backgroundColor;
        var textColor = attributes.textColor;
        var fontSize = attributes.fontSize;
        var borderRadius = attributes.borderRadius;
        var padding = attributes.padding;
        var minimumDiscount = attributes.minimumDiscount;

        var blockProps = useBlockProps({
            className: 'jankx-wc-sale-badge-edit jankx-wc-sale-badge--' + position + ' jankx-wc-sale-badge--' + badgeStyle,
            style: {
                backgroundColor: backgroundColor || '#ff5722',
                color: textColor || '#ffffff',
                fontSize: fontSize ? fontSize + 'px' : '14px',
                borderRadius: borderRadius ? borderRadius + 'px' : '4px',
                padding: padding ? padding.top + ' ' + padding.right + ' ' + padding.bottom + ' ' + padding.left : '4px 8px'
            }
        });

        var getPreviewText = function() {
            switch (badgeStyle) {
                case 'percentage': return '-30%';
                case 'amount': return '-150,000₫';
                case 'text': return customText || __('Khuyến mãi', 'jankx');
                case 'hide': return '';
                default: return '-30%';
            }
        };

        var isEnabled = window.jankxWooCommerceBlocks && window.jankxWooCommerceBlocks.saleBadgeEnabled !== false;

        return wp.element.createElement(
            wp.element.Fragment,
            null,
            wp.element.createElement(
                InspectorControls,
                null,
                wp.element.createElement(
                    PanelBody,
                    { title: __('Badge Settings', 'jankx'), initialOpen: true },
                    !isEnabled && wp.element.createElement(
                        Notice,
                        { status: 'warning', isDismissible: false },
                        __('Sale Badge feature is disabled in theme options.', 'jankx')
                    ),
                    wp.element.createElement(SelectControl, {
                        label: __('Badge Style', 'jankx'),
                        value: badgeStyle,
                        options: [
                            { label: __('Percentage (e.g., -30%)', 'jankx'), value: 'percentage' },
                            { label: __('Amount (e.g., -50,000đ)', 'jankx'), value: 'amount' },
                            { label: __('Custom Text', 'jankx'), value: 'text' },
                            { label: __('Hide Badge', 'jankx'), value: 'hide' }
                        ],
                        onChange: function(value) { setAttributes({ badgeStyle: value }); }
                    }),
                    badgeStyle === 'text' && wp.element.createElement(TextControl, {
                        label: __('Custom Text', 'jankx'),
                        value: customText,
                        onChange: function(value) { setAttributes({ customText: value }); },
                        placeholder: __('Khuyến mãi', 'jankx'),
                        help: __('Use %percentage% and %amount% as placeholders', 'jankx')
                    }),
                    wp.element.createElement(SelectControl, {
                        label: __('Position', 'jankx'),
                        value: position,
                        options: [
                            { label: __('Top Left', 'jankx'), value: 'top-left' },
                            { label: __('Top Right', 'jankx'), value: 'top-right' },
                            { label: __('Bottom Left', 'jankx'), value: 'bottom-left' },
                            { label: __('Bottom Right', 'jankx'), value: 'bottom-right' }
                        ],
                        onChange: function(value) { setAttributes({ position: value }); }
                    }),
                    wp.element.createElement(RangeControl, {
                        label: __('Minimum Discount to Show (%)', 'jankx'),
                        value: minimumDiscount,
                        onChange: function(value) { setAttributes({ minimumDiscount: value }); },
                        min: 0,
                        max: 100,
                        help: __('Only show badge when discount is at least this percentage', 'jankx')
                    })
                ),
                wp.element.createElement(
                    PanelBody,
                    { title: __('Appearance', 'jankx'), initialOpen: false },
                    wp.element.createElement(TextControl, {
                        label: __('Background Color', 'jankx'),
                        value: backgroundColor,
                        onChange: function(value) { setAttributes({ backgroundColor: value }); },
                        placeholder: '#ff5722'
                    }),
                    wp.element.createElement(TextControl, {
                        label: __('Text Color', 'jankx'),
                        value: textColor,
                        onChange: function(value) { setAttributes({ textColor: value }); },
                        placeholder: '#ffffff'
                    }),
                    wp.element.createElement(RangeControl, {
                        label: __('Font Size', 'jankx'),
                        value: fontSize,
                        onChange: function(value) { setAttributes({ fontSize: value }); },
                        min: 10,
                        max: 32
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
                getPreviewText() && wp.element.createElement('span', { className: 'jankx-wc-sale-badge__text' }, getPreviewText()),
                badgeStyle === 'hide' && wp.element.createElement('span', { className: 'jankx-wc-sale-badge__placeholder' }, __('(Badge hidden)', 'jankx'))
            )
        );
    };

    registerBlockType('jankx-woocommerce/sale-badge', {
        edit: Edit,
        save: function() { return null; }
    });
})();
