<?php

namespace Jankx\Extensions\WooCommerce\Blocks\BuyNowButton;

use Jankx\Extensions\WooCommerce\Admin\Options;

/**
 * Buy Now Button Block Render Class
 *
 * @package Jankx\Extensions\WooCommerce\Blocks\BuyNowButton
 */
class Block
{
    /**
     * Block attributes
     *
     * @var array
     */
    protected $attributes;

    /**
     * Block content
     *
     * @var string
     */
    protected $content;

    /**
     * WP_Block instance
     *
     * @var \WP_Block
     */
    protected $block;

    /**
     * Render the block
     *
     * @param array $attributes
     * @param string $content
     * @param \WP_Block $block
     * @return string
     */
    public function render($attributes, $content, $block): string
    {
        $this->attributes = $this->parseAttributes($attributes);
        $this->content = $content;
        $this->block = $block;

        // Get the current product
        $product = $this->getProduct();

        // Check if product is valid and purchasable
        if (!$product || !$product->is_purchasable() || !$product->is_in_stock()) {
            return '';
        }

        // Check if buy now is enabled in settings
        if (!Options::isBuyNowEnabled()) {
            return '';
        }

        return $this->renderButton($product);
    }

    /**
     * Parse and set default attributes
     *
     * @param array $attributes
     * @return array
     */
    protected function parseAttributes($attributes): array
    {
        $defaults = [
            'buttonText' => Options::getOption('wc_buy_now_text', 'MUA NGAY'),
            'buttonStyle' => 'primary',
            'buttonSize' => 'medium',
            'fullWidth' => false,
            'clearCart' => true,
            'backgroundColor' => '',
            'textColor' => '',
            'borderRadius' => 4,
        ];

        return wp_parse_args($attributes, $defaults);
    }

    /**
     * Get the current product
     *
     * @return \WC_Product|null
     */
    protected function getProduct(): ?\WC_Product
    {
        // Try to get product from block context
        if (isset($this->block->context['postId'])) {
            $product = wc_get_product($this->block->context['postId']);
            if ($product) {
                return $product;
            }
        }

        // Fallback to global product
        global $product;
        if ($product instanceof \WC_Product) {
            return $product;
        }

        // Try to get from query loop
        if (is_singular('product')) {
            return wc_get_product(get_the_ID());
        }

        return null;
    }

    /**
     * Render the buy now button
     *
     * @param \WC_Product $product
     * @return string
     */
    protected function renderButton(\WC_Product $product): string
    {
        $product_id = $product->get_id();
        $button_text = esc_html($this->attributes['buttonText']);

        // Build CSS classes
        $classes = ['jankx-wc-buy-now-button', 'button'];
        $classes[] = 'button--' . $this->attributes['buttonStyle'];
        $classes[] = 'button--' . $this->attributes['buttonSize'];

        if ($this->attributes['fullWidth']) {
            $classes[] = 'button--full-width';
        }

        $class_string = implode(' ', $classes);

        // Build inline styles
        $styles = [];
        if ($this->attributes['backgroundColor']) {
            $styles[] = 'background-color: ' . esc_attr($this->attributes['backgroundColor']) . ';';
        }
        if ($this->attributes['textColor']) {
            $styles[] = 'color: ' . esc_attr($this->attributes['textColor']) . ';';
        }
        if ($this->attributes['borderRadius']) {
            $styles[] = 'border-radius: ' . intval($this->attributes['borderRadius']) . 'px;';
        }

        $style_string = implode(' ', $styles);

        // Build data attributes
        $data_attrs = [
            'data-product-id' => $product_id,
            'data-action' => 'buy-now',
        ];

        if ($this->attributes['clearCart']) {
            $data_attrs['data-clear-cart'] = 'true';
        }

        $data_string = '';
        foreach ($data_attrs as $key => $value) {
            $data_string .= sprintf(' %s="%s"', esc_attr($key), esc_attr($value));
        }

        // Generate button HTML
        $button = sprintf(
            '<button type="button" class="%s" style="%s"%s>%s</button>',
            esc_attr($class_string),
            esc_attr($style_string),
            $data_string,
            $button_text
        );

        // Wrap in container
        $wrapper_classes = ['jankx-wc-buy-now-wrapper'];
        if ($this->attributes['fullWidth']) {
            $wrapper_classes[] = 'jankx-wc-buy-now-wrapper--full-width';
        }

        return sprintf(
            '<div class="%s">%s</div>',
            esc_attr(implode(' ', $wrapper_classes)),
            $button
        );
    }
}
