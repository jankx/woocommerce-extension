<?php

namespace Jankx\Extensions\WooCommerce\Tests\Unit\Adapters;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\WooCommerce\Adapters\WCProductAdapter;

/**
 * Test case for WCProductAdapter
 *
 * @package Jankx\Extensions\WooCommerce\Tests\Unit\Adapters
 */
class WCProductAdapterTest extends TestCase
{
    private $mockProduct;
    private $adapter;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create mock WC_Product
        $this->mockProduct = $this->getMockBuilder('WC_Product')
            ->disableOriginalConstructor()
            ->onlyMethods(['get_id', 'is_on_sale', 'get_regular_price', 'get_sale_price'])
            ->getMock();

        $this->adapter = new WCProductAdapter($this->mockProduct);
    }

    /**
     * Test getId returns product ID
     */
    public function testGetId(): void
    {
        $this->mockProduct->method('get_id')->willReturn(123);

        $this->assertEquals(123, $this->adapter->getId());
    }

    /**
     * Test isOnSale returns true when product is on sale
     */
    public function testIsOnSaleReturnsTrue(): void
    {
        $this->mockProduct->method('is_on_sale')->willReturn(true);

        $this->assertTrue($this->adapter->isOnSale());
    }

    /**
     * Test isOnSale returns false when product is not on sale
     */
    public function testIsOnSaleReturnsFalse(): void
    {
        $this->mockProduct->method('is_on_sale')->willReturn(false);

        $this->assertFalse($this->adapter->isOnSale());
    }

    /**
     * Test getRegularPrice returns correct price
     */
    public function testGetRegularPrice(): void
    {
        $this->mockProduct->method('get_regular_price')->willReturn('99.99');

        $this->assertEquals(99.99, $this->adapter->getRegularPrice());
    }

    /**
     * Test getSalePrice returns correct price
     */
    public function testGetSalePrice(): void
    {
        $this->mockProduct->method('get_sale_price')->willReturn('79.99');

        $this->assertEquals(79.99, $this->adapter->getSalePrice());
    }

    /**
     * Test getRegularPrice returns 0 for empty price
     */
    public function testGetRegularPriceReturnsZeroForEmpty(): void
    {
        $this->mockProduct->method('get_regular_price')->willReturn('');

        $this->assertEquals(0.0, $this->adapter->getRegularPrice());
    }

    /**
     * Test getSalePrice returns 0 for non-numeric price
     */
    public function testGetSalePriceReturnsZeroForNonNumeric(): void
    {
        $this->mockProduct->method('get_sale_price')->willReturn('not_a_price');

        $this->assertEquals(0.0, $this->adapter->getSalePrice());
    }

    /**
     * Test supports returns true for WC_Product instance
     */
    public function testSupportsReturnsTrueForWCProduct(): void
    {
        $this->assertTrue(WCProductAdapter::supports($this->mockProduct));
    }

    /**
     * Test supports returns false for non-WC_Product
     */
    public function testSupportsReturnsFalseForNonWCProduct(): void
    {
        $this->assertFalse(WCProductAdapter::supports('string'));
        $this->assertFalse(WCProductAdapter::supports(123));
        $this->assertFalse(WCProductAdapter::supports(new \stdClass()));
    }

    /**
     * Test implements correct interface
     */
    public function testImplementsCorrectInterface(): void
    {
        $this->assertInstanceOf(
            \Jankx\Extensions\WooCommerce\Adapters\Contracts\ProductAdapterInterface::class,
            $this->adapter
        );
    }
}
