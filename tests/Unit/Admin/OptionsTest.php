<?php

namespace Jankx\Extensions\WooCommerce\Tests\Unit\Admin;

use PHPUnit\Framework\TestCase;
use Jankx\Extensions\WooCommerce\Admin\Options;
use Brain\Monkey;
use Brain\Monkey\Functions;

/**
 * Test case for Admin Options
 *
 * @package Jankx\Extensions\WooCommerce\Tests\Unit\Admin
 */
class OptionsTest extends TestCase
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
     * Test init adds filter
     */
    public function testInitAddsFilter(): void
    {
        Functions\expect('add_filter')
            ->once()
            ->with('jankx/option/pages', [Options::class, 'addWooCommercePage']);

        Options::init();
    }

    /**
     * Test getWooCommercePage returns Page instance
     */
    public function testGetWooCommercePageReturnsPage(): void
    {
        $options = new Options();
        $page = $options->getWooCommercePage();

        $this->assertInstanceOf(\Jankx\Dashboard\Elements\Page::class, $page);
    }

    /**
     * Test page has correct ID
     */
    public function testPageHasCorrectId(): void
    {
        $options = new Options();
        $page = $options->getWooCommercePage();

        $this->assertEquals('woocommerce', $page->getId());
    }

    /**
     * Test addWooCommercePage adds page to array
     */
    public function testAddWooCommercePageAddsPage(): void
    {
        $pages = [];
        $result = Options::addWooCommercePage($pages);

        $this->assertArrayHasKey('WooCommerce', $result);
        $this->assertInstanceOf(\Jankx\Dashboard\Elements\Page::class, $result['WooCommerce']);
    }

    /**
     * Test getOption calls jankx_get_theme_option
     */
    public function testGetOptionCallsThemeOption(): void
    {
        Functions\expect('jankx_get_theme_option')
            ->once()
            ->with('test_key', 'default')
            ->andReturn('test_value');

        $result = Options::getOption('test_key', 'default');

        $this->assertEquals('test_value', $result);
    }

    /**
     * Test isBuyNowEnabled returns true when enabled
     */
    public function testIsBuyNowEnabledReturnsTrue(): void
    {
        Functions\expect('jankx_get_theme_option')
            ->once()
            ->with('wc_buy_now_enabled', true)
            ->andReturn(true);

        $this->assertTrue(Options::isBuyNowEnabled());
    }

    /**
     * Test isBuyNowEnabled returns false when disabled
     */
    public function testIsBuyNowEnabledReturnsFalse(): void
    {
        Functions\expect('jankx_get_theme_option')
            ->once()
            ->with('wc_buy_now_enabled', true)
            ->andReturn(false);

        $this->assertFalse(Options::isBuyNowEnabled());
    }

    /**
     * Test isSaleBadgeEnabled returns true when enabled
     */
    public function testIsSaleBadgeEnabledReturnsTrue(): void
    {
        Functions\expect('jankx_get_theme_option')
            ->once()
            ->with('wc_sale_badge_enabled', true)
            ->andReturn(true);

        $this->assertTrue(Options::isSaleBadgeEnabled());
    }

    /**
     * Test isSaleBadgeEnabled returns false when disabled
     */
    public function testIsSaleBadgeEnabledReturnsFalse(): void
    {
        Functions\expect('jankx_get_theme_option')
            ->once()
            ->with('wc_sale_badge_enabled', true)
            ->andReturn(false);

        $this->assertFalse(Options::isSaleBadgeEnabled());
    }

    /**
     * Test page has all required sections
     */
    public function testPageHasRequiredSections(): void
    {
        $options = new Options();
        $page = $options->getWooCommercePage();
        $sections = $page->getSections();

        $sectionIds = array_map(function($section) {
            return $section->getId();
        }, $sections);

        $this->assertContains('wc_general', $sectionIds);
        $this->assertContains('wc_buy_now', $sectionIds);
        $this->assertContains('wc_sale_badge', $sectionIds);
        $this->assertContains('wc_product_page', $sectionIds);
        $this->assertContains('wc_shop_page', $sectionIds);
    }
}
