<?php
/**
 * WooCommerce Extension Test Bootstrap
 *
 * @package Jankx\Extensions\WooCommerce\Tests
 */

// Load WordPress test environment if available
if (getenv('WP_TESTS_DIR')) {
    require_once getenv('WP_TESTS_DIR') . '/includes/functions.php';
    require_once getenv('WP_TESTS_DIR') . '/includes/bootstrap.php';
}

// Mock WooCommerce functions if not available
if (!function_exists('wc_get_product')) {
    function wc_get_product($product_id) {
        return null;
    }
}

if (!function_exists('wc_get_checkout_url')) {
    function wc_get_checkout_url() {
        return 'https://example.com/checkout/';
    }
}

if (!function_exists('WC')) {
    function WC() {
        return null;
    }
}
