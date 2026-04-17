<?php

namespace Jankx\Extensions\WooCommerce\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\WooCommerce\Services\SaleBadgeService;
use Jankx\Extensions\WooCommerce\Adapters\Contracts\ProductAdapterInterface;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Test case for SaleBadgeService
 *
 * @package Jankx\Extensions\WooCommerce\Tests\Unit\Services
 */
class SaleBadgeServiceTest extends TestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        Monkey::setUp();
        $this->service = new SaleBadgeService();
    }

    protected function tearDown(): void
    {
        Monkey::tearDown();
        parent::tearDown();
    }

    /**
     * Test convertToPercentage returns original if already contains %
     */
    public function testConvertToPercentageReturnsOriginalIfHasPercent(): void
    {
        $input = 'Already -50%';
        $result = $this->service->convertToPercentage($input, null);
        
        $this->assertEquals($input, $result);
    }

    /**
     * Test service exists and has required methods
     */
    public function testServiceHasRequiredMethods(): void
    {
        $this->assertTrue(method_exists($this->service, 'convertToPercentage'));
        $this->assertTrue(method_exists($this->service, 'filterSaleBadgeText'));
        $this->assertTrue(method_exists($this->service, 'filterSaleBadgeHtml'));
        $this->assertTrue(method_exists($this->service, 'getSaleBadgeText'));
        $this->assertTrue(method_exists($this->service, 'hasValidDiscount'));
        $this->assertTrue(method_exists($this->service, 'filterProductGridItemHtml'));
        $this->assertTrue(method_exists($this->service, 'filterWooCommerceBlocksSaleBadgeHtml'));
    }

    /**
     * Test filterSaleBadgeText returns percentage when product is on sale
     */
    public function testFilterSaleBadgeTextReturnsPercentage(): void
    {
        // Create mock adapter
        $adapter = $this->createMock(ProductAdapterInterface::class);
        $adapter->method('isOnSale')->willReturn(true);
        $adapter->method('getRegularPrice')->willReturn(100.0);
        $adapter->method('getSalePrice')->willReturn(70.0);
        $adapter->method('getId')->willReturn(1);

        // Mock the factory to return our adapter
        Functions\when('wc_get_product')->justReturn(null);

        $result = $this->service->filterSaleBadgeText('Sale!', $adapter);
        
        // Should return -30% (30% discount)
        $this->assertStringContainsString('%', $result);
    }

    /**
     * Test filterSaleBadgeText returns original text when no sale
     */
    public function testFilterSaleBadgeTextReturnsOriginalWhenNoSale(): void
    {
        $originalText = 'Sale!';
        
        // Mock adapter that doesn't support the product
        Functions\when('wc_get_product')->justReturn(null);

        $result = $this->service->filterSaleBadgeText($originalText, null);
        
        $this->assertEquals($originalText, $result);
    }

    /**
     * Test filterSaleBadgeHtml replaces Sale! text
     */
    public function testFilterSaleBadgeHtmlReplacesSaleText(): void
    {
        $html = '<span class="onsale">Sale!</span>';
        
        // With null product, should return original
        $result = $this->service->filterSaleBadgeHtml($html, null);
        
        $this->assertEquals($html, $result);
    }

    /**
     * Test hasValidDiscount returns false when no product
     */
    public function testHasValidDiscountReturnsFalseWhenNoProduct(): void
    {
        Functions\when('wc_get_product')->justReturn(null);
        
        $result = $this->service->hasValidDiscount(999);
        
        $this->assertFalse($result);
    }

    /**
     * Test getSaleBadgeText returns empty string when not on sale
     */
    public function testGetSaleBadgeTextReturnsEmptyWhenNotOnSale(): void
    {
        Functions\when('wc_get_product')->justReturn(null);
        
        $result = $this->service->getSaleBadgeText(999);
        
        $this->assertEmpty($result);
    }
}
