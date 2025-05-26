<?php
/**
 * Plugin Name: WooCommerce Trendyol Entegrasyonu
 * Description: WooCommerce ürünlerinizi Trendyol mağazanıza profesyonel şekilde entegre edin
 * Version: 3.0.0
 * Author: Coşkun KOÇ CK - 2025
 * Requires PHP: 7.4
 * Requires at least: 5.8
 * Tested up to: 6.7
 * WC requires at least: 6.0
 * WC tested up to: 8.0
 */

defined('ABSPATH') || exit;

// HPOS Uyumsuzluk Bildirimi
add_action('before_woocommerce_init', function() {
    if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, false);
    }
});

// Sabitler
define('WC_TRENDYOL_VERSION', '3.0.0');
define('WC_TRENDYOL_PLUGIN_FILE', __FILE__);
define('WC_TRENDYOL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WC_TRENDYOL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WC_TRENDYOL_ASSETS_URL', WC_TRENDYOL_PLUGIN_URL . 'assets/');

// Temel Dosyalar
require_once WC_TRENDYOL_PLUGIN_PATH . 'includes/class-api-handler.php';
require_once WC_TRENDYOL_PLUGIN_PATH . 'includes/class-admin-menu.php';
require_once WC_TRENDYOL_PLUGIN_PATH . 'includes/class-settings.php';
require_once WC_TRENDYOL_PLUGIN_PATH . 'includes/class-product-sync.php';
require_once WC_TRENDYOL_PLUGIN_PATH . 'includes/class-category-mapping.php';
require_once WC_TRENDYOL_PLUGIN_PATH . 'includes/class-product-mapping.php';

// Eklenti Başlatma
add_action('plugins_loaded', function() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'wc_trendyol_missing_woocommerce_notice');
        return;
    }

    load_plugin_textdomain('wc-trendyol', false, dirname(plugin_basename(__FILE__)) . '/languages');

    new WC_Trendyol_Admin_Menu();
    new WC_Trendyol_Settings();
    new WC_Trendyol_Category_Mapping();
    new WC_Trendyol_Product_Mapping();
}, 20);

function wc_trendyol_missing_woocommerce_notice() {
    echo '<div class="notice notice-error"><p>';
    echo sprintf(
        __('%sWooCommerce%s kurulu değil! Trendyol entegrasyonu çalışması için zorunludur.', 'wc-trendyol'),
        '<strong>',
        '</strong>'
    );
    echo '</p></div>';
}

// Aktivasyon Hook'ları
register_activation_hook(__FILE__, function() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('WooCommerce kurulu değil!', 'wc-trendyol'));
    }

    $defaults = [
        'api_key' => '',
        'api_secret' => '',
        'supplier_id' => '',
        'auto_sync' => 'no',
        'default_category' => 0
    ];
    add_option('wc_trendyol_options', $defaults);
});