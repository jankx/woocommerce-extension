<?php

namespace Jankx\Extensions\WooCommerce\Blocks;

use Jankx\Extensions\WooCommerce\Blocks\BuyNowButton\Block as BuyNowButtonBlock;
use Jankx\Extensions\WooCommerce\Blocks\SaleBadge\Block as SaleBadgeBlock;

/**
 * Blocks Manager
 *
 * Registers and manages Gutenberg blocks for WooCommerce extension
 *
 * @package Jankx\Extensions\WooCommerce\Blocks
 */
class BlocksManager
{
    /**
     * Initialize blocks
     */
    public static function init(): void
    {
        add_action('init', [__CLASS__, 'registerBlocks']);
        add_action('enqueue_block_editor_assets', [__CLASS__, 'enqueueEditorAssets']);
        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueueFrontendAssets']);
    }

    /**
     * Register blocks
     */
    public static function registerBlocks(): void
    {
        // Register Buy Now Button block
        self::registerBlock('buy-now-button', [
            'render_callback' => [new BuyNowButtonBlock(), 'render'],
        ]);

        // Register Sale Badge block
        self::registerBlock('sale-badge', [
            'render_callback' => [new SaleBadgeBlock(), 'render'],
        ]);
    }

    /**
     * Register a single block
     *
     * @param string $block_name
     * @param array $args
     */
    protected static function registerBlock(string $block_name, array $args = []): void
    {
        $block_path = JANKX_WOOCOMMERCE_PATH . "blocks/{$block_name}";

        if (!file_exists("{$block_path}/block.json")) {
            return;
        }

        // Register block type from metadata
        register_block_type("{$block_path}/block.json", $args);
    }

    /**
     * Enqueue block editor assets
     */
    public static function enqueueEditorAssets(): void
    {
        // Enqueue shared editor styles
        wp_enqueue_style(
            'jankx-wc-blocks-editor',
            JANKX_WOOCOMMERCE_URL . 'assets/css/blocks-editor.css',
            [],
            JANKX_WOOCOMMERCE_VERSION
        );

        // Register Buy Now Button block assets
        self::registerBlockAssets('buy-now-button', [
            'editor' => 'jankx-wc-buy-now-button-editor',
            'style' => 'jankx-wc-buy-now-button',
        ]);

        // Register Sale Badge block assets
        self::registerBlockAssets('sale-badge', [
            'editor' => 'jankx-wc-sale-badge-editor',
            'style' => 'jankx-wc-sale-badge',
        ]);

        // Pass settings to JavaScript
        wp_localize_script(
            'wp-blocks',
            'jankxWooCommerceBlocks',
            [
                'buyNowEnabled' => \Jankx\Extensions\WooCommerce\Admin\Options::isBuyNowEnabled(),
                'saleBadgeEnabled' => \Jankx\Extensions\WooCommerce\Admin\Options::isSaleBadgeEnabled(),
                'settingsUrl' => admin_url('admin.php?page=jankx-theme-options'),
            ]
        );
    }

    /**
     * Register block assets
     *
     * @param string $block_name
     * @param array $handles
     */
    protected static function registerBlockAssets(string $block_name, array $handles): void
    {
        $block_path = JANKX_WOOCOMMERCE_PATH . "blocks/{$block_name}";
        $block_url = JANKX_WOOCOMMERCE_URL . "blocks/{$block_name}";

        // Register editor script
        if (file_exists("{$block_path}/build/index.js")) {
            $asset_file = "{$block_path}/build/index.asset.php";
            $asset = file_exists($asset_file) ? require $asset_file : ['dependencies' => [], 'version' => '1.0.0'];

            wp_register_script(
                $handles['editor'],
                "{$block_url}/build/index.js",
                array_merge(['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'], $asset['dependencies']),
                $asset['version'],
                true
            );
        }

        // Register editor style
        if (file_exists("{$block_path}/build/editor.css")) {
            wp_register_style(
                $handles['editor'],
                "{$block_url}/build/editor.css",
                [],
                JANKX_WOOCOMMERCE_VERSION
            );
        }

        // Register frontend style
        if (file_exists("{$block_path}/build/style.css")) {
            wp_register_style(
                $handles['style'],
                "{$block_url}/build/style.css",
                [],
                JANKX_WOOCOMMERCE_VERSION
            );
        }
    }

    /**
     * Enqueue frontend assets
     */
    public static function enqueueFrontendAssets(): void
    {
        if (!is_singular('product') && !is_archive()) {
            return;
        }

        // Enqueue block frontend styles if blocks are used
        if (has_block('jankx-woocommerce/buy-now-button') || is_singular('product')) {
            wp_enqueue_style('jankx-wc-buy-now-button');
        }

        if (has_block('jankx-woocommerce/sale-badge') || is_singular('product')) {
            wp_enqueue_style('jankx-wc-sale-badge');
        }
    }

    /**
     * Check if a block is registered
     *
     * @param string $block_name
     * @return bool
     */
    public static function isBlockRegistered(string $block_name): bool
    {
        $block_type = \WP_Block_Type_Registry::get_instance()->get_registered("jankx-woocommerce/{$block_name}");
        return $block_type !== null;
    }
}
