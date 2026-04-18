# WooCommerce Extension for Jankx

Enhanced WooCommerce integration for Jankx theme with Buy Now button, sale badge percentage display, and Gutenberg blocks.

## Features

### 🛒 Buy Now Button
- One-click checkout functionality
- Clears cart before adding product (optional)
- AJAX-powered for smooth experience
- Fully customizable button text and styling
- Can be enabled/disabled via theme options

### 🏷️ Sale Badge
- Converts "Sale!" to percentage discount (e.g., "-30%")
- Multiple badge formats: percentage, amount, custom text
- Configurable minimum discount threshold
- Color customization options
- Works with WooCommerce blocks

### 🧩 Gutenberg Blocks
- **Buy Now Button Block**: Add buy now buttons anywhere in your product templates
- **Sale Badge Block**: Display sale badges with flexible positioning and styling

### ⚙️ Theme Options Integration
- Dedicated WooCommerce settings page in Jankx Theme Options
- Enable/disable features globally
- Configure button text, badge format, colors, and more
- Product page settings (sticky add to cart, hide tabs)
- Shop page settings (AJAX add to cart, quick view)

## Installation

This extension is bundled with Jankx theme and will be automatically loaded when WooCommerce is active.

## Usage

### Enabling Features

1. Go to **Jankx > WooCommerce** in the WordPress admin
2. Enable/disable features as needed
3. Configure settings for each feature
4. Save changes

### Using Gutenberg Blocks

1. Edit a page or template in the block editor
2. Look for blocks in the "WooCommerce" category
3. Add "Buy Now Button" or "Sale Badge" blocks
4. Customize via block settings panel

### Theme Options Location

The WooCommerce options page is automatically injected into:
`wp-admin/admin.php?page=jankx-theme-options`

## File Structure

```
extensions/woocommerce/
├── WooCommerceExtension.php    # Main extension class
├── manifest.json               # Extension manifest
├── package.json                # NPM dependencies for block building
├── webpack.config.js           # Webpack configuration for blocks
├── src/
│   ├── Admin/
│   │   └── Options.php         # Theme options integration
│   ├── Services/
│   │   ├── BuyNowService.php   # Buy now functionality
│   │   └── SaleBadgeService.php # Sale badge logic
│   ├── Adapters/
│   │   ├── ProductAdapterFactory.php
│   │   ├── WCProductAdapter.php
│   │   ├── WPPostAdapter.php
│   │   ├── ProductIdAdapter.php
│   │   └── Contracts/
│   │       └── ProductAdapterInterface.php
│   └── Blocks/
│       ├── BlocksManager.php   # Block registration
│       ├── BuyNowButton/
│       │   └── Block.php       # Buy now block render
│       └── SaleBadge/
│           └── Block.php       # Sale badge block render
├── blocks/                     # Gutenberg block source files
│   ├── buy-now-button/
│   │   ├── block.json          # Block metadata
│   │   ├── index.js            # Block registration & edit component
│   │   ├── editor.scss         # Editor-only styles
│   │   ├── style.scss          # Frontend & editor styles
│   │   └── build/              # Compiled block assets
│   │       ├── index.js
│   │       ├── index.asset.php
│   │       ├── editor.css
│   │       └── style.css
│   └── sale-badge/
│       ├── block.json          # Block metadata
│       ├── index.js            # Block registration & edit component
│       ├── editor.scss         # Editor-only styles
│       ├── style.scss          # Frontend & editor styles
│       └── build/              # Compiled block assets
│           ├── index.js
│           ├── index.asset.php
│           ├── editor.css
│           └── style.css
├── assets/
│   └── css/
│       └── blocks-editor.css   # Editor styles
├── tests/                      # Unit & integration tests
│   ├── bootstrap.php
│   ├── phpunit.xml             # PHPUnit configuration
│   ├── Unit/
│   │   ├── Services/
│   │   │   ├── BuyNowServiceTest.php
│   │   │   └── SaleBadgeServiceTest.php
│   │   ├── Adapters/
│   │   │   ├── ProductAdapterFactoryTest.php
│   │   │   ├── ProductIdAdapterTest.php
│   │   │   ├── WCProductAdapterTest.php
│   │   │   └── WPPostAdapterTest.php
│   │   └── Admin/
│   │       └── OptionsTest.php
│   └── Integration/
│       └── WooCommerceExtensionTest.php
└── langs/                      # Translation files
```

## Development

### Running Tests

Tests use PHPUnit with configuration at `tests/phpunit.xml`:

```bash
cd extensions/woocommerce
phpunit --configuration tests/phpunit.xml
```

### Adding New Features

1. Create service class in `src/Services/`
2. Add settings in `src/Admin/Options.php`
3. Initialize service in `WooCommerceExtension::init_services()`
4. Write tests in `tests/`

## Filters & Actions

### Buy Now
- `jankx/woocommerce/buy_now/enabled` - Enable/disable buy now feature
- `jankx/woocommerce/buy_now/text` - Modify button text
- `jankx/woocommerce/buy_now/text_prefix` - Add prefix to button text
- `jankx/woocommerce/buy_now/css/enabled` - Enable custom CSS

### Sale Badge
- `jankx/woocommerce/sale_badge/enabled` - Enable/disable sale badge

## Requirements

- WordPress 6.0+
- WooCommerce 7.0+
- Jankx Theme 2.0+

## Changelog

### 1.0.0
- Initial release
- Buy Now button feature
- Sale badge percentage display
- Gutenberg blocks
- Theme options integration
- Full unit test coverage

## License

GPL-2.0-or-later
