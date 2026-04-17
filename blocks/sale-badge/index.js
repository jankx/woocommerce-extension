/**
 * Sale Badge Block
 *
 * Gutenberg block for WooCommerce Sale Badge
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
    PanelBody,
    SelectControl,
    TextControl,
    RangeControl,
    ToggleControl,
    Notice,
    BaseControl,
    ColorPalette,
    __experimentalBoxControl as BoxControl,
} from '@wordpress/components';

import './editor.scss';

const Edit = ({ attributes, setAttributes }) => {
    const {
        badgeStyle,
        customText,
        position,
        backgroundColor,
        textColor,
        fontSize,
        borderRadius,
        padding,
        minimumDiscount,
    } = attributes;

    const blockProps = useBlockProps({
        className: `jankx-wc-sale-badge-edit jankx-wc-sale-badge--${position} jankx-wc-sale-badge--${badgeStyle}`,
        style: {
            backgroundColor: backgroundColor || '#ff5722',
            color: textColor || '#ffffff',
            fontSize: fontSize ? `${fontSize}px` : '14px',
            borderRadius: borderRadius ? `${borderRadius}px` : '4px',
            padding: padding
                ? `${padding.top} ${padding.right} ${padding.bottom} ${padding.left}`
                : '4px 8px',
        },
    });

    // Preview text based on style
    const getPreviewText = () => {
        switch (badgeStyle) {
            case 'percentage':
                return '-30%';
            case 'amount':
                return '-150,000₫';
            case 'text':
                return customText || __('Khuyến mãi', 'jankx');
            case 'hide':
                return '';
            default:
                return '-30%';
        }
    };

    // Check if feature is enabled
    const isEnabled = window.jankxWooCommerceBlocks?.saleBadgeEnabled !== false;

    const colorOptions = [
        { name: 'Red', color: '#ff5722' },
        { name: 'Green', color: '#4caf50' },
        { name: 'Blue', color: '#2196f3' },
        { name: 'Orange', color: '#ff9800' },
        { name: 'Purple', color: '#9c27b0' },
        { name: 'Black', color: '#333333' },
        { name: 'White', color: '#ffffff' },
    ];

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Badge Settings', 'jankx')} initialOpen={true}>
                    {!isEnabled && (
                        <Notice status="warning" isDismissible={false}>
                            {__('Sale Badge feature is disabled in theme options.', 'jankx')}
                        </Notice>
                    )}

                    <SelectControl
                        label={__('Badge Style', 'jankx')}
                        value={badgeStyle}
                        options={[
                            { label: __('Percentage (e.g., -30%)', 'jankx'), value: 'percentage' },
                            { label: __('Amount (e.g., -50,000đ)', 'jankx'), value: 'amount' },
                            { label: __('Custom Text', 'jankx'), value: 'text' },
                            { label: __('Hide Badge', 'jankx'), value: 'hide' },
                        ]}
                        onChange={(value) => setAttributes({ badgeStyle: value })}
                    />

                    {badgeStyle === 'text' && (
                        <TextControl
                            label={__('Custom Text', 'jankx')}
                            value={customText}
                            onChange={(value) => setAttributes({ customText: value })}
                            placeholder={__('Khuyến mãi', 'jankx')}
                            help={__('Use %percentage% and %amount% as placeholders', 'jankx')}
                        />
                    )}

                    <SelectControl
                        label={__('Position', 'jankx')}
                        value={position}
                        options={[
                            { label: __('Top Left', 'jankx'), value: 'top-left' },
                            { label: __('Top Right', 'jankx'), value: 'top-right' },
                            { label: __('Bottom Left', 'jankx'), value: 'bottom-left' },
                            { label: __('Bottom Right', 'jankx'), value: 'bottom-right' },
                        ]}
                        onChange={(value) => setAttributes({ position: value })}
                    />

                    <RangeControl
                        label={__('Minimum Discount to Show (%)', 'jankx')}
                        value={minimumDiscount}
                        onChange={(value) => setAttributes({ minimumDiscount: value })}
                        min={0}
                        max={100}
                        help={__('Only show badge when discount is at least this percentage', 'jankx')}
                    />
                </PanelBody>

                <PanelBody title={__('Appearance', 'jankx')} initialOpen={false}>
                    <BaseControl label={__('Background Color', 'jankx')}>
                        <ColorPalette
                            colors={colorOptions}
                            value={backgroundColor}
                            onChange={(value) => setAttributes({ backgroundColor: value })}
                        />
                    </BaseControl>

                    <BaseControl label={__('Text Color', 'jankx')}>
                        <ColorPalette
                            colors={colorOptions}
                            value={textColor}
                            onChange={(value) => setAttributes({ textColor: value })}
                        />
                    </BaseControl>

                    <RangeControl
                        label={__('Font Size', 'jankx')}
                        value={fontSize}
                        onChange={(value) => setAttributes({ fontSize: value })}
                        min={10}
                        max={32}
                    />

                    <RangeControl
                        label={__('Border Radius', 'jankx')}
                        value={borderRadius}
                        onChange={(value) => setAttributes({ borderRadius: value })}
                        min={0}
                        max={50}
                    />

                    {BoxControl && (
                        <BoxControl
                            label={__('Padding', 'jankx')}
                            values={padding}
                            onChange={(value) => setAttributes({ padding: value })}
                        />
                    )}
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                {getPreviewText() && (
                    <span className="jankx-wc-sale-badge__text">{getPreviewText()}</span>
                )}
                {badgeStyle === 'hide' && (
                    <span className="jankx-wc-sale-badge__placeholder">
                        {__('(Badge hidden)', 'jankx')}
                    </span>
                )}
            </div>
        </>
    );
};

registerBlockType('jankx-woocommerce/sale-badge', {
    edit: Edit,
    save: () => null, // Server-side render
});
