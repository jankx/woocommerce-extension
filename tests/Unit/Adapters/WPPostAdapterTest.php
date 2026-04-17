<?php

namespace Jankx\Extensions\WooCommerce\Tests\Unit\Adapters;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\WooCommerce\Adapters\WPPostAdapter;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Test case for WPPostAdapter
 *
 * @package Jankx\Extensions\WooCommerce\Tests\Unit\Adapters
 */
class WPPostAdapterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey::setUp();
    }

    protected function tearDown(): void
    {
        Monkey::tearDown();
        parent::tearDown();
    }

    /**
     * Test getId returns the post ID
     */
    public function testGetId(): void
    {
        $mockPost = $this->createMockPost(456);
        $adapter = new WPPostAdapter($mockPost);
        
        $this->assertEquals(456, $adapter->getId());
    }

    /**
     * Test supports returns true for WP_Post instance
     */
    public function testSupportsReturnsTrueForWPPost(): void
    {
        $mockPost = $this->createMockPost(1);
        $this->assertTrue(WPPostAdapter::supports($mockPost));
    }

    /**
     * Test supports returns false for non-WP_Post
     */
    public function testSupportsReturnsFalseForNonWPPost(): void
    {
        $this->assertFalse(WPPostAdapter::supports('string'));
        $this->assertFalse(WPPostAdapter::supports(123));
        $this->assertFalse(WPPostAdapter::supports([]));
        $this->assertFalse(WPPostAdapter::supports(new \stdClass()));
    }

    /**
     * Test implements correct interface
     */
    public function testImplementsCorrectInterface(): void
    {
        $mockPost = $this->createMockPost(1);
        $adapter = new WPPostAdapter($mockPost);
        
        $this->assertInstanceOf(
            \Jankx\Extensions\WooCommerce\Adapters\Contracts\ProductAdapterInterface::class,
            $adapter
        );
    }

    /**
     * Test isOnSale returns false when product not found
     */
    public function testIsOnSaleReturnsFalseWhenProductNotFound(): void
    {
        Functions\when('wc_get_product')->justReturn(null);

        $mockPost = $this->createMockPost(999);
        $adapter = new WPPostAdapter($mockPost);
        
        $this->assertFalse($adapter->isOnSale());
    }

    /**
     * Helper to create mock WP_Post
     */
    private function createMockPost(int $id): \WP_Post
    {
        $post = new \WP_Post(new \stdClass());
        $post->ID = $id;
        return $post;
    }
}
