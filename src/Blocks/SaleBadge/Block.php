<?php

namespace Jankx\Extensions\WooCommerce\Blocks\SaleBadge;

use Jankx\Extensions\WooCommerce\Admin\Options;
use Jankx\Extensions\WooCommerce\Services\SaleBadgeService;

/**
 * Sale Badge Block Render Class
 *
 * @package Jankx\Extensions\WooCommerce\Blocks\SaleBadge
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
     * Sale Badge Service
     *
     * @var SaleBadgeService
     */
    protected $service;

    public function __construct()
    {
        $this->service = new SaleBadgeService();
    }

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

        // Check if sale badge is enabled in settings
        if (!Options::isSaleBadgeEnabled()) {
            return '';
        }

        // Get the current product
        $product = $this->getProduct();

        // Check if product exists and is on sale
        if (!$product || !$product->is_on_sale()) {
            return '';
        }

        // Check minimum discount
        $discount = $this->calculateDiscount($product);
        if ($discount < $this->attributes['minimumDiscount']) {
            return '';
        }

        return $this->renderBadge($product, $discount);
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
            'badgeStyle' => Options::getOption('wc_sale_badge_format', 'percentage'),
            'customText' => '',
            'position' => 'top-left',
            'backgroundColor' => Options::getOption('wc_sale_badge_color', '#ff5722'),
            'textColor' => Options::getOption('wc_sale_badge_text_color', '#ffffff'),
            'fontSize' => 14,
            'borderRadius' => 4,
            'padding' => [
                'top' => '4px',
                'right' => '8px',
                'bottom' => '4px',
                'left' => '8px',
            ],
            'minimumDiscount' => Options::getOption('wc_sale_badge_min_discount', 1),
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

        // Try to get from current post
        if (is_singular('product')) {
            return wc_get_product(get_the_ID());
        }

        return null;
    }

    /**
     * Calculate discount percentage
     *
     * @param \WC_Product $product
     * @return int
     */
    protected function calculateDiscount(\WC_Product $product): int
    {
        $regular_price = (float) $product->get_regular_price();
        $sale_price = (float) $product->get_sale_price();

        if ($regular_price <= 0 || $sale_price <= 0 || $sale_price >= $regular_price) {
            return 0;
        }

        return round((($regular_price - $sale_price) / $regular_price) * 100);
    }

    /**
     * Get badge text based on style
     *
     * @param \WC_Product $product
     * @param int $discount
     * @return string
     */
    protected function getBadgeText(\WC_Product $product, int $discount): string
    {
        $style = $this->attributes['badgeStyle'];

        switch ($style) {
            case 'percentage':
                return "-{$discount}%";

            case 'amount':
                $regular_price = (float) $product->get_regular_price();
                $sale_price = (float) $product->get_sale_price();
                $amount = $regular_price - $sale_price;
                return '-' . wc_price($amount);

            case 'text':
                $custom_text = $this->attributes['customText'];
                if (empty($custom_text)) {
                    $custom_text = Options::getOption('wc_sale_badge_custom_text', 'Khuyến mãi');
                }
                $regular_price = (float) $product->get_regular_price();
                $sale_price = (float) $product->get_sale_price();
                $amount = $regular_price - $sale_price;
                return str_replace(
                    ['%percentage%', '%amount%'],
                    ["{$discount}%", wc_price($amount)],
                    $custom_text
                );

            case 'hide':
                return '';

            default:
                return "-{$discount}%";
        }
    }

    /**
     * Render the sale badge
     *
     * @param \WC_Product $product
     * @param int $discount
     * @return string
     */
    protected function renderBadge(\WC_Product $product, int $discount): string
    {
        $badge_text = $this->getBadgeText($product, $discount);

        if (empty($badge_text)) {
            return '';
        }

        // Build CSS classes
        $classes = ['jankx-wc-sale-badge'];
        $classes[] = 'jankx-wc-sale-badge--' . $this->attributes['position'];
        $classes[] = 'jankx-wc-sale-badge--' . $this->attributes['badgeStyle'];

        $class_string = implode(' ', $classes);

        // Build inline styles
        $styles = [];

        if ($this->attributes['backgroundColor']) {
            $styles[] = 'background-color: ' . esc_attr($this->attributes['backgroundColor']) . ';';
        }

        if ($this->attributes['textColor']) {
            $styles[] = 'color: ' . esc_attr($this->attributes['textColor']) . ';';
        }

        if ($this->attributes['fontSize']) {
            $styles[] = 'font-size: ' . intval($this->attributes['fontSize']) . 'px;';
        }

        if ($this->attributes['borderRadius']) {
            $styles[] = 'border-radius: ' . intval($this->attributes['borderRadius']) . 'px;';
        }

        // Add padding
        $padding = $this->attributes['padding'];
        if (is_array($padding)) {
            $styles[] = sprintf(
                'padding: %s %s %s %s;',
                esc_attr($padding['top'] ?? '4px'),
                esc_attr($padding['right'] ?? '8px'),
                esc_attr($padding['bottom'] ?? '4px'),
                esc_attr($padding['left'] ?? '8px')
            );
        }

        $style_string = implode(' ', $styles);

        // Generate badge HTML
        return sprintf(
            '<span class="%s" style="%s">%s</span>',
            esc_attr($class_string),
            esc_attr($style_string),
            $badge_text
        );
    }
}
