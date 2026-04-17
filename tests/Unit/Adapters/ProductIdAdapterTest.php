<?php

namespace Jankx\Extensions\WooCommerce\Tests\Unit\Adapters;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\WooCommerce\Adapters\ProductIdAdapter;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Test case for ProductIdAdapter
 *
 * @package Jankx\Extensions\WooCommerce\Tests\Unit\Adapters
 */
class ProductIdAdapterTest extends TestCase
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
     * Test getId returns the product ID
     */
    public function testGetId(): void
    {
        $adapter = new ProductIdAdapter(123);
        $this->assertEquals(123, $adapter->getId());
    }

    /**
     * Test supports returns true for positive integers
     */
    public function testSupportsReturnsTrueForPositiveInteger(): void
    {
        $this->assertTrue(ProductIdAdapter::supports(1));
        $this->assertTrue(ProductIdAdapter::supports(999));
        $this->assertTrue(ProductIdAdapter::supports('123')); // String number
    }

    /**
     * Test supports returns false for invalid values
     */
    public function testSupportsReturnsFalseForInvalidValues(): void
    {
        $this->assertFalse(ProductIdAdapter::supports(0));
        $this->assertFalse(ProductIdAdapter::supports(-1));
        $this->assertFalse(ProductIdAdapter::supports('string'));
        $this->assertFalse(ProductIdAdapter::supports([]));
        $this->assertFalse(ProductIdAdapter::supports(new \stdClass()));
    }

    /**
     * Test implements correct interface
     */
    public function testImplementsCorrectInterface(): void
    {
        $adapter = new ProductIdAdapter(1);
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

        $adapter = new ProductIdAdapter(999);
        $this->assertFalse($adapter->isOnSale());
    }

    /**
     * Test getRegularPrice returns 0 when product not found
     */
    public function testGetRegularPriceReturnsZeroWhenProductNotFound(): void
    {
        Functions\when('wc_get_product')->justReturn(null);

        $adapter = new ProductIdAdapter(999);
        $this->assertEquals(0.0, $adapter->getRegularPrice());
    }

    /**
     * Test getSalePrice returns 0 when product not found
     */
    public function testGetSalePriceReturnsZeroWhenProductNotFound(): void
    {
        Functions\when('wc_get_product')->justReturn(null);

        $adapter = new ProductIdAdapter(999);
        $this->assertEquals(0.0, $adapter->getSalePrice());
    }
}
