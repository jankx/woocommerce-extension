/**
 * Buy Now Button Block
 *
 * Gutenberg block for WooCommerce Buy Now functionality
 */

import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import {
    PanelBody,
    TextControl,
    SelectControl,
    ToggleControl,
    RangeControl,
    Notice,
} from '@wordpress/components';

import './editor.scss';

const Edit = ({ attributes, setAttributes }) => {
    const {
        buttonText,
        buttonStyle,
        buttonSize,
        fullWidth,
        clearCart,
        backgroundColor,
        textColor,
        borderRadius,
    } = attributes;

    const blockProps = useBlockProps({
        className: `jankx-wc-buy-now-button-edit button button--${buttonStyle} button--${buttonSize}`,
        style: {
            backgroundColor: backgroundColor || undefined,
            color: textColor || undefined,
            borderRadius: borderRadius ? `${borderRadius}px` : undefined,
            width: fullWidth ? '100%' : 'auto',
        },
    });

    // Check if feature is enabled
    const isEnabled = window.jankxWooCommerceBlocks?.buyNowEnabled !== false;

    return (
        <>
            <InspectorControls>
                <PanelBody title={__('Button Settings', 'jankx')} initialOpen={true}>
                    {!isEnabled && (
                        <Notice status="warning" isDismissible={false}>
                            {__('Buy Now feature is disabled in theme options.', 'jankx')}
                        </Notice>
                    )}

                    <TextControl
                        label={__('Button Text', 'jankx')}
                        value={buttonText}
                        onChange={(value) => setAttributes({ buttonText: value })}
                        placeholder={__('MUA NGAY', 'jankx')}
                    />

                    <SelectControl
                        label={__('Button Style', 'jankx')}
                        value={buttonStyle}
                        options={[
                            { label: __('Primary', 'jankx'), value: 'primary' },
                            { label: __('Secondary', 'jankx'), value: 'secondary' },
                            { label: __('Outline', 'jankx'), value: 'outline' },
                        ]}
                        onChange={(value) => setAttributes({ buttonStyle: value })}
                    />

                    <SelectControl
                        label={__('Button Size', 'jankx')}
                        value={buttonSize}
                        options={[
                            { label: __('Small', 'jankx'), value: 'small' },
                            { label: __('Medium', 'jankx'), value: 'medium' },
                            { label: __('Large', 'jankx'), value: 'large' },
                        ]}
                        onChange={(value) => setAttributes({ buttonSize: value })}
                    />

                    <ToggleControl
                        label={__('Full Width', 'jankx')}
                        checked={fullWidth}
                        onChange={(value) => setAttributes({ fullWidth: value })}
                    />

                    <ToggleControl
                        label={__('Clear Cart Before Adding', 'jankx')}
                        checked={clearCart}
                        onChange={(value) => setAttributes({ clearCart: value })}
                        help={__('Enable for single product checkout', 'jankx')}
                    />
                </PanelBody>

                <PanelBody title={__('Appearance', 'jankx')} initialOpen={false}>
                    <TextControl
                        label={__('Background Color', 'jankx')}
                        value={backgroundColor}
                        onChange={(value) => setAttributes({ backgroundColor: value })}
                        placeholder="#ff5722"
                        help={__('Hex color or CSS color value', 'jankx')}
                    />

                    <TextControl
                        label={__('Text Color', 'jankx')}
                        value={textColor}
                        onChange={(value) => setAttributes({ textColor: value })}
                        placeholder="#ffffff"
                    />

                    <RangeControl
                        label={__('Border Radius', 'jankx')}
                        value={borderRadius}
                        onChange={(value) => setAttributes({ borderRadius: value })}
                        min={0}
                        max={50}
                    />
                </PanelBody>
            </InspectorControls>

            <div {...blockProps}>
                <span className="button-text">{buttonText || __('MUA NGAY', 'jankx')}</span>
            </div>
        </>
    );
};

registerBlockType('jankx-woocommerce/buy-now-button', {
    edit: Edit,
    save: () => null, // Server-side render
});
