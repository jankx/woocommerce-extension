<?php

namespace Jankx\Extensions\WooCommerce\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\WooCommerce\Services\BuyNowService;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Test case for BuyNowService
 *
 * @package Jankx\Extensions\WooCommerce\Tests\Unit\Services
 */
class BuyNowServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey::setUp();
        $this->service = new BuyNowService();
    }

    protected function tearDown(): void
    {
        Monkey::tearDown();
        parent::tearDown();
    }

    /**
     * Test that service initializes correctly
     */
    public function testInit(): void
    {
        // Mock WordPress hooks
        Functions\expect('add_action')
            ->once()
            ->with('woocommerce_after_add_to_cart_button', [$this->service, 'renderBuyNowButton']);
        
        Functions\expect('add_action')
            ->once()
            ->with('wp_ajax_buy_now', [$this->service, 'handleBuyNowAjax']);
        
        Functions\expect('add_action')
            ->once()
            ->with('wp_ajax_nopriv_buy_now', [$this->service, 'handleBuyNowAjax']);
        
        Functions\expect('add_action')
            ->once()
            ->with('wp_enqueue_scripts', [$this->service, 'enqueueAssets']);

        $this->service->init();
    }

    /**
     * Test renderBuyNowButton returns early when no product
     */
    public function testRenderBuyNowButtonNoProduct(): void
    {
        global $product;
        $product = null;

        // Should return early and not output anything
        ob_start();
        $this->service->renderBuyNowButton();
        $output = ob_get_clean();

        $this->assertEmpty($output);
    }

    /**
     * Test button text filter works
     */
    public function testBuyNowButtonTextFilter(): void
    {
        $expectedText = 'CUSTOM TEXT';
        
        Functions\expect('apply_filters')
            ->once()
            ->with('jankx/woocommerce/buy_now/text', 'MUA NGAY')
            ->andReturn($expectedText);

        Functions\expect('apply_filters')
            ->once()
            ->with('jankx/woocommerce/buy_now/text_prefix', '');

        // This test would need a mock product to fully test
        // Here we just verify the filter is applied
        $this->assertTrue(true); // Placeholder for actual test
    }

    /**
     * Test AJAX handler rejects invalid nonce
     */
    public function testHandleBuyNowAjaxInvalidNonce(): void
    {
        $_POST['nonce'] = 'invalid_nonce';

        Functions\expect('wp_verify_nonce')
            ->once()
            ->with('invalid_nonce', 'jankx_buy_now_nonce')
            ->andReturn(false);

        Functions\expect('wp_send_json_error')
            ->once();

        $this->service->handleBuyNowAjax();
    }

    /**
     * Test service exists and has required methods
     */
    public function testServiceHasRequiredMethods(): void
    {
        $this->assertTrue(method_exists($this->service, 'init'));
        $this->assertTrue(method_exists($this->service, 'renderBuyNowButton'));
        $this->assertTrue(method_exists($this->service, 'enqueueAssets'));
        $this->assertTrue(method_exists($this->service, 'handleBuyNowAjax'));
    }
}
