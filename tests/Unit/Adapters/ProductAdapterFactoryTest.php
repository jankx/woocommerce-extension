<?php

namespace Jankx\Extensions\WooCommerce\Tests\Unit\Adapters;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\WooCommerce\Adapters\ProductAdapterFactory;
use Jankx\Extensions\WooCommerce\Adapters\WCProductAdapter;
use Jankx\Extensions\WooCommerce\Adapters\WPPostAdapter;
use Jankx\Extensions\WooCommerce\Adapters\ProductIdAdapter;

/**
 * Test case for ProductAdapterFactory
 *
 * @package Jankx\Extensions\WooCommerce\Tests\Unit\Adapters
 */
class ProductAdapterFactoryTest extends TestCase
{
    /**
     * Test factory can create WCProductAdapter for WC_Product objects
     */
    public function testCreateReturnsWCProductAdapterForWCProduct(): void
    {
        // Create a mock WC_Product
        $mockProduct = $this->getMockBuilder('WC_Product')
            ->disableOriginalConstructor()
            ->getMock();

        $adapter = ProductAdapterFactory::create($mockProduct);

        $this->assertInstanceOf(WCProductAdapter::class, $adapter);
    }

    /**
     * Test factory can create WPPostAdapter for WP_Post objects
     */
    public function testCreateReturnsWPPostAdapterForWPPost(): void
    {
        // Create a mock WP_Post
        $mockPost = $this->getMockBuilder('WP_Post')
            ->disableOriginalConstructor()
            ->getMock();
        $mockPost->ID = 1;

        $adapter = ProductAdapterFactory::create($mockPost);

        $this->assertInstanceOf(WPPostAdapter::class, $adapter);
    }

    /**
     * Test factory can create ProductIdAdapter for integer IDs
     */
    public function testCreateReturnsProductIdAdapterForInteger(): void
    {
        $adapter = ProductAdapterFactory::create(123);

        $this->assertInstanceOf(ProductIdAdapter::class, $adapter);
    }

    /**
     * Test factory returns null for unsupported types
     */
    public function testCreateReturnsNullForUnsupportedType(): void
    {
        $adapter = ProductAdapterFactory::create('invalid_string');

        $this->assertNull($adapter);
    }

    /**
     * Test factory returns null for zero ID
     */
    public function testCreateReturnsNullForZeroId(): void
    {
        $adapter = ProductAdapterFactory::create(0);

        $this->assertNull($adapter);
    }

    /**
     * Test factory returns null for negative ID
     */
    public function testCreateReturnsNullForNegativeId(): void
    {
        $adapter = ProductAdapterFactory::create(-1);

        $this->assertNull($adapter);
    }

    /**
     * Test canAdapt returns true for supported types
     */
    public function testCanAdaptReturnsTrueForSupportedTypes(): void
    {
        $this->assertTrue(ProductAdapterFactory::canAdapt(123));
    }

    /**
     * Test canAdapt returns false for unsupported types
     */
    public function testCanAdaptReturnsFalseForUnsupportedTypes(): void
    {
        $this->assertFalse(ProductAdapterFactory::canAdapt('string'));
        $this->assertFalse(ProductAdapterFactory::canAdapt([]));
        $this->assertFalse(ProductFactory::canAdapt(new \stdClass()));
    }

    /**
     * Test register adds custom adapter
     */
    public function testRegisterAddsCustomAdapter(): void
    {
        // Create a custom adapter class
        $customAdapter = new class implements \Jankx\Extensions\WooCommerce\Adapters\Contracts\ProductAdapterInterface {
            public function getId(): ?int { return 999; }
            public function isOnSale(): bool { return false; }
            public function getRegularPrice(): float { return 0.0; }
            public function getSalePrice(): float { return 0.0; }
            public static function supports($product): bool { return $product === 'custom'; }
        };

        ProductAdapterFactory::register(get_class($customAdapter));

        // Should now be able to adapt 'custom' string
        $this->assertTrue(ProductAdapterFactory::canAdapt('custom'));
    }
}
