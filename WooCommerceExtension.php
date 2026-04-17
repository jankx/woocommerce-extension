<?php
namespace Jankx\Extensions\WooCommerce;

use Jankx\Extensions\AbstractExtension;
use Jankx\Extensions\WooCommerce\Admin\Options;
use Jankx\Extensions\WooCommerce\Blocks\BlocksManager;

/**
 * WooCommerce Extension for Jankx
 *
 * Provides enhanced WooCommerce functionality including:
 * - Buy Now button
 * - Sale badge percentage display
 *
 * @package Jankx\Extensions\WooCommerce
 */
class WooCommerceExtension extends AbstractExtension
{
    protected static $instance;

    /**
     * Service instances
     */
    protected $buyNowService;
    protected $saleBadgeService;

    public function __construct()
    {
        $this->define_constants();
        $this->register_autoloader();
        parent::__construct();
    }

    protected function define_constants()
    {
        if (!defined('JANKX_WOOCOMMERCE_VERSION')) {
            define('JANKX_WOOCOMMERCE_VERSION', $this->get_version());
        }
        if (!defined('JANKX_WOOCOMMERCE_PATH')) {
            define('JANKX_WOOCOMMERCE_PATH', trailingslashit($this->get_extension_path()));
        }
        if (!defined('JANKX_WOOCOMMERCE_URL')) {
            define('JANKX_WOOCOMMERCE_URL', trailingslashit($this->get_extension_url()));
        }
    }

    protected function register_autoloader()
    {
        spl_autoload_register(function ($class) {
            $prefix = 'Jankx\\Extensions\\WooCommerce\\';
            $base_dir = __DIR__ . '/src/';

            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }

            $relative_class = substr($class, $len);
            $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

            if (file_exists($file)) {
                require $file;
            }
        });
    }

    public function init(): void
    {
        self::$instance = $this;

        // Initialize admin options
        Options::init();

        // Initialize Gutenberg blocks
        BlocksManager::init();

        // Inject options page into theme options
        add_action('jankx/option/adapter/initialized', function($adapter) {
            if (method_exists($adapter, 'getFramework')) {
                $framework = $adapter->getFramework();
                if ($framework) {
                    $options = new Options();
                    $wcPage = $options->getWooCommercePage();
                    $framework->addPage($wcPage);
                }
            }
        });

        // Fallback: if already initialized
        try {
            $themeOptions = \Jankx\Facades\App::get('theme-options');
            $adapter = $themeOptions->getAdapter();
            if ($adapter && method_exists($adapter, 'getFramework')) {
                $framework = $adapter->getFramework();
                if ($framework) {
                    $options = new Options();
                    $framework->addPage($options->getWooCommercePage());
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        // Only initialize frontend features if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return;
        }

        $this->init_services();
    }

    protected function init_services(): void
    {
        // Check if enhancements are globally enabled
        if (!Options::getOption('wc_enable_enhancements', true)) {
            return;
        }

        // Initialize Buy Now Service
        if (apply_filters('jankx/woocommerce/buy_now/enabled', Options::isBuyNowEnabled())) {
            $this->buyNowService = new Services\BuyNowService();
            $this->buyNowService->init();
        }

        // Initialize Sale Badge Service
        if (apply_filters('jankx/woocommerce/sale_badge/enabled', Options::isSaleBadgeEnabled())) {
            $this->saleBadgeService = new Services\SaleBadgeService();
            $this->init_sale_badge_hooks();
        }
    }

    protected function init_sale_badge_hooks(): void
    {
        // Traditional WooCommerce sale flash
        add_filter('woocommerce_sale_flash', [$this->saleBadgeService, 'filterSaleBadgeHtml'], 10, 2);
        add_filter('woocommerce_get_sale_flash', [$this->saleBadgeService, 'filterSaleBadgeHtml'], 10, 2);

        // Product price display
        add_filter('woocommerce_product_get_sale_flash', [$this->saleBadgeService, 'filterSaleBadgeText'], 10, 2);
        add_filter('woocommerce_variation_get_sale_flash', [$this->saleBadgeService, 'filterSaleBadgeText'], 10, 2);

        // WooCommerce Blocks support
        add_filter('woocommerce_blocks_product_grid_item_html', [$this->saleBadgeService, 'filterProductGridItemHtml'], 10, 3);
        add_filter('woocommerce_blocks_sale_badge_html', [$this->saleBadgeService, 'filterWooCommerceBlocksSaleBadgeHtml'], 10, 2);
    }

    public function register_hooks(): void
    {
        // Hooks are registered in init_services() for better control
    }

    public static function get_instance(): ?self
    {
        return self::$instance;
    }

    /**
     * Get Buy Now Service instance
     *
     * @return Services\BuyNowService|null
     */
    public function get_buy_now_service(): ?Services\BuyNowService
    {
        return $this->buyNowService;
    }

    /**
     * Get Sale Badge Service instance
     *
     * @return Services\SaleBadgeService|null
     */
    public function get_sale_badge_service(): ?Services\SaleBadgeService
    {
        return $this->saleBadgeService;
    }
}
