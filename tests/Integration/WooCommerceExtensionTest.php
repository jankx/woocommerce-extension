<?php

namespace Jankx\Extensions\WooCommerce\Tests\Integration;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\WooCommerce\WooCommerceExtension;

/**
 * Integration test for WooCommerceExtension
 *
 * @package Jankx\Extensions\WooCommerce\Tests\Integration
 */
class WooCommerceExtensionTest extends TestCase
{
    private $extension;

    protected function setUp(): void
    {
        parent::setUp();
        
        if (!class_exists('WooCommerce')) {
            $this->markTestSkipped('WooCommerce is not available');
        }

        $this->extension = new WooCommerceExtension();
    }

    /**
     * Test extension is created correctly
     */
    public function testExtensionCreated(): void
    {
        $this->assertInstanceOf(WooCommerceExtension::class, $this->extension);
    }

    /**
     * Test extension defines constants
     */
    public function testExtensionDefinesConstants(): void
    {
        $this->extension->init();

        $this->assertTrue(defined('JANKX_WOOCOMMERCE_VERSION'));
        $this->assertTrue(defined('JANKX_WOOCOMMERCE_PATH'));
        $this->assertTrue(defined('JANKX_WOOCOMMERCE_URL'));
    }

    /**
     * Test extension instance is retrievable
     */
    public function testExtensionInstance(): void
    {
        $this->extension->init();
        
        $instance = WooCommerceExtension::get_instance();
        $this->assertInstanceOf(WooCommerceExtension::class, $instance);
    }

    /**
     * Test services are accessible
     */
    public function testServicesAccessible(): void
    {
        // Before init, services should be null
        $this->assertNull($this->extension->get_buy_now_service());
        $this->assertNull($this->extension->get_sale_badge_service());
    }
}
