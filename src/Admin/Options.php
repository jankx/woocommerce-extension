<?php

namespace Jankx\Extensions\WooCommerce\Admin;

use Jankx\Dashboard\Elements\Page;
use Jankx\Dashboard\Elements\Section;
use Jankx\Dashboard\Factories\FieldFactory;

/**
 * WooCommerce Theme Options
 *
 * Adds WooCommerce settings to Jankx Theme Options page
 *
 * @package Jankx\Extensions\WooCommerce\Admin
 */
class Options
{
    /**
     * Initialize options
     */
    public static function init()
    {
        add_filter('jankx/option/pages', [__CLASS__, 'addWooCommercePage']);
    }

    /**
     * Get WooCommerce options page
     *
     * @return Page
     */
    public function getWooCommercePage(): Page
    {
        $wcPage = new Page(__('WooCommerce', 'jankx'), [], 'dashicons-cart');
        $wcPage->setId('woocommerce');
        $wcPage->setPriority(8);

        // General Settings Section
        $generalSection = new Section(__('General Settings', 'jankx'), []);
        $generalSection->setId('wc_general');

        $generalSection->addField(FieldFactory::create(
            'wc_enable_enhancements',
            __('Enable WooCommerce Enhancements', 'jankx'),
            'switch',
            [
                'default' => 1,
                'description' => __('Enable all Jankx WooCommerce enhancements', 'jankx'),
            ]
        ));

        $wcPage->addSection($generalSection);

        // Buy Now Section
        $buyNowSection = new Section(__('Buy Now Button', 'jankx'), []);
        $buyNowSection->setId('wc_buy_now');

        $buyNowSection->addField(FieldFactory::create(
            'wc_buy_now_enabled',
            __('Enable Buy Now Button', 'jankx'),
            'switch',
            [
                'default' => 1,
                'description' => __('Add a "Buy Now" button to product pages that redirects directly to checkout', 'jankx'),
            ]
        ));

        $buyNowSection->addField(FieldFactory::create(
            'wc_buy_now_text',
            __('Button Text', 'jankx'),
            'text',
            [
                'default' => __('MUA NGAY', 'jankx'),
                'description' => __('Text displayed on the Buy Now button', 'jankx'),
            ]
        ));

        $buyNowSection->addField(FieldFactory::create(
            'wc_buy_now_position',
            __('Button Position', 'jankx'),
            'select',
            [
                'default' => 'after_add_to_cart',
                'options' => [
                    'after_add_to_cart' => __('After Add to Cart Button', 'jankx'),
                    'before_add_to_cart' => __('Before Add to Cart Button', 'jankx'),
                    'replace_add_to_cart' => __('Replace Add to Cart Button', 'jankx'),
                ],
                'description' => __('Where to display the Buy Now button', 'jankx'),
            ]
        ));

        $buyNowSection->addField(FieldFactory::create(
            'wc_buy_now_clear_cart',
            __('Clear Cart Before Adding', 'jankx'),
            'switch',
            [
                'default' => 1,
                'description' => __('Clear the cart before adding the Buy Now product (single product checkout)', 'jankx'),
            ]
        ));

        $wcPage->addSection($buyNowSection);

        // Sale Badge Section
        $saleBadgeSection = new Section(__('Sale Badge', 'jankx'), []);
        $saleBadgeSection->setId('wc_sale_badge');

        $saleBadgeSection->addField(FieldFactory::create(
            'wc_sale_badge_enabled',
            __('Enable Percentage Sale Badge', 'jankx'),
            'switch',
            [
                'default' => 1,
                'description' => __('Convert "Sale!" text to discount percentage (e.g., "-30%")', 'jankx'),
            ]
        ));

        $saleBadgeSection->addField(FieldFactory::create(
            'wc_sale_badge_format',
            __('Badge Format', 'jankx'),
            'select',
            [
                'default' => 'percentage',
                'options' => [
                    'percentage' => __('Percentage (e.g., -30%)', 'jankx'),
                    'amount' => __('Amount (e.g., -50,000đ)', 'jankx'),
                    'both' => __('Both (e.g., -30% (-50,000đ))', 'jankx'),
                    'text' => __('Custom Text', 'jankx'),
                ],
                'description' => __('Format of the sale badge display', 'jankx'),
            ]
        ));

        $saleBadgeSection->addField(FieldFactory::create(
            'wc_sale_badge_custom_text',
            __('Custom Badge Text', 'jankx'),
            'text',
            [
                'default' => __('Khuyến mãi', 'jankx'),
                'description' => __('Custom text to display when "Custom Text" format is selected. Use %percentage% and %amount% as placeholders', 'jankx'),
                'condition' => [
                    'wc_sale_badge_format' => 'text',
                ],
            ]
        ));

        $saleBadgeSection->addField(FieldFactory::create(
            'wc_sale_badge_min_discount',
            __('Minimum Discount to Show', 'jankx'),
            'slider',
            [
                'default' => 1,
                'min' => 0,
                'max' => 50,
                'step' => 1,
                'unit' => '%',
                'description' => __('Only show badge when discount is at least this percentage', 'jankx'),
            ]
        ));

        $saleBadgeSection->addField(FieldFactory::create(
            'wc_sale_badge_color',
            __('Badge Background Color', 'jankx'),
            'color',
            [
                'default' => '#ff5722',
                'description' => __('Background color of the sale badge', 'jankx'),
            ]
        ));

        $saleBadgeSection->addField(FieldFactory::create(
            'wc_sale_badge_text_color',
            __('Badge Text Color', 'jankx'),
            'color',
            [
                'default' => '#ffffff',
                'description' => __('Text color of the sale badge', 'jankx'),
            ]
        ));

        $wcPage->addSection($saleBadgeSection);

        // Product Page Section
        $productPageSection = new Section(__('Product Page', 'jankx'), []);
        $productPageSection->setId('wc_product_page');

        $productPageSection->addField(FieldFactory::create(
            'wc_product_sticky_add_to_cart',
            __('Sticky Add to Cart', 'jankx'),
            'switch',
            [
                'default' => 0,
                'description' => __('Show sticky add to cart bar on product pages', 'jankx'),
            ]
        ));

        $productPageSection->addField(FieldFactory::create(
            'wc_product_hide_tabs',
            __('Hide Product Tabs', 'jankx'),
            'switch',
            [
                'default' => 0,
                'description' => __('Hide the Description/Reviews tabs and show all content', 'jankx'),
            ]
        ));

        $wcPage->addSection($productPageSection);

        // Shop Page Section
        $shopPageSection = new Section(__('Shop & Archive', 'jankx'), []);
        $shopPageSection->setId('wc_shop_page');

        $shopPageSection->addField(FieldFactory::create(
            'wc_shop_ajax_add_to_cart',
            __('AJAX Add to Cart', 'jankx'),
            'switch',
            [
                'default' => 1,
                'description' => __('Add products to cart without page reload on shop pages', 'jankx'),
            ]
        ));

        $shopPageSection->addField(FieldFactory::create(
            'wc_shop_quick_view',
            __('Enable Quick View', 'jankx'),
            'switch',
            [
                'default' => 0,
                'description' => __('Add quick view button to product cards', 'jankx'),
            ]
        ));

        $wcPage->addSection($shopPageSection);

        return $wcPage;
    }

    /**
     * Add WooCommerce page to options array
     *
     * @param array $pages
     * @return array
     */
    public static function addWooCommercePage($pages): array
    {
        $instance = new self();
        $pages['WooCommerce'] = $instance->getWooCommercePage();
        return $pages;
    }

    /**
     * Get option value with WooCommerce defaults
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getOption(string $key, $default = null)
    {
        return jankx_get_theme_option($key, $default);
    }

    /**
     * Check if Buy Now is enabled
     *
     * @return bool
     */
    public static function isBuyNowEnabled(): bool
    {
        return (bool) self::getOption('wc_buy_now_enabled', true);
    }

    /**
     * Check if Sale Badge is enabled
     *
     * @return bool
     */
    public static function isSaleBadgeEnabled(): bool
    {
        return (bool) self::getOption('wc_sale_badge_enabled', true);
    }
}
