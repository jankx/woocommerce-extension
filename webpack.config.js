/**
 * Webpack config for WooCommerce Extension Blocks
 */
const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = [
    // Buy Now Button Block
    {
        ...defaultConfig,
        name: 'buy-now-button',
        entry: './blocks/buy-now-button/index.js',
        output: {
            path: path.resolve(__dirname, 'blocks/buy-now-button/build'),
            filename: 'index.js',
        },
        module: {
            ...defaultConfig.module,
            rules: [
                ...defaultConfig.module.rules,
                {
                    test: /\.scss$/,
                    use: [
                        'style-loader',
                        'css-loader',
                        'sass-loader',
                    ],
                },
            ],
        },
    },
    // Sale Badge Block
    {
        ...defaultConfig,
        name: 'sale-badge',
        entry: './blocks/sale-badge/index.js',
        output: {
            path: path.resolve(__dirname, 'blocks/sale-badge/build'),
            filename: 'index.js',
        },
        module: {
            ...defaultConfig.module,
            rules: [
                ...defaultConfig.module.rules,
                {
                    test: /\.scss$/,
                    use: [
                        'style-loader',
                        'css-loader',
                        'sass-loader',
                    ],
                },
            ],
        },
    },
];
