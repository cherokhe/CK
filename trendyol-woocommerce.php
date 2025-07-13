<?php
/*
Plugin Name: Trendyol WooCommerce Entegrasyonu
Description: WooCommerce ürünlerinizi Trendyol mağazanıza kolayca aktarın, kategori ve marka eşleştirin.
Version: 1.0
Author: Senin İsmin
*/

if (!defined('ABSPATH')) exit;

define('TW_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TW_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once TW_PLUGIN_DIR . 'includes/api-client.php';
require_once TW_PLUGIN_DIR . 'includes/category-mapping.php';
require_once TW_PLUGIN_DIR . 'includes/logger.php';
require_once TW_PLUGIN_DIR . 'includes/product-sync.php'; // Varsa ürün sync class'ını da çağır
// Ek dosyaları buraya ekle

// === ADMIN CSS, JS, Select2 ENTEGRASYONU ===
add_action('admin_enqueue_scripts', function(){
    wp_enqueue_style('tw-admin-style', TW_PLUGIN_URL . 'assets/css/admin-style.css', [], '1.0');
    wp_enqueue_script('tw-admin-js', TW_PLUGIN_URL . 'assets/js/admin.js', ['jquery'], '1.0', true);
    wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js', ['jquery'], '4.1.0', true);
    wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css', [], '4.1.0');
    wp_localize_script('tw-admin-js', 'twAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('tw_nonce')
    ]);
});

// === ADMIN MENÜ ===
add_action('admin_menu', function(){
    add_menu_page(
        'Trendyol Entegrasyonu', 'Trendyol', 'manage_options',
        'trendyol-woocommerce', 'tw_settings_page', 'dashicons-cart'
    );
    add_submenu_page('trendyol-woocommerce', 'Ayarlar', 'Ayarlar', 'manage_options', 'trendyol-woocommerce', 'tw_settings_page');
    add_submenu_page('trendyol-woocommerce', 'Kategoriler', 'Kategoriler', 'manage_options', 'trendyol-woocommerce-category-mapping', 'tw_category_mapping_page');
    add_submenu_page('trendyol-woocommerce', 'Ürünler', 'Ürünler', 'manage_options', 'trendyol-woocommerce-product-sync', 'tw_product_sync_page');
    add_submenu_page('trendyol-woocommerce', 'Loglar', 'Loglar', 'manage_options', 'trendyol-woocommerce-log', 'tw_log_page');
});

// === SAYFA CALLBACKLER ===
function tw_settings_page() {
    include TW_PLUGIN_DIR . 'templates/settings-page.php';
}
function tw_category_mapping_page() {
    include TW_PLUGIN_DIR . 'templates/category-mapping-page.php';
}
function tw_product_sync_page() {
    include TW_PLUGIN_DIR . 'templates/product-sync-page.php';
}
function tw_log_page() {
    include TW_PLUGIN_DIR . 'templates/log-page.php';
}

// === AJAX HANDLERLAR ===
add_action('wp_ajax_tw_save_api_settings', function(){
    check_ajax_referer('tw_nonce');
    update_option('tw_api_key', sanitize_text_field($_POST['api_key']));
    update_option('tw_api_secret', sanitize_text_field($_POST['api_secret']));
    update_option('tw_supplier_id', sanitize_text_field($_POST['supplier_id']));
    update_option('tw_brand_id', intval($_POST['tw_brand_select'] ?? 0));
    wp_send_json_success(['message' => 'API bilgileri kaydedildi.']);
});

// AJAX ile markaları dinamik arama
add_action('wp_ajax_tw_fetch_brands', function(){
    check_ajax_referer('tw_nonce');
    $api = new TW_ApiClient();
    $name = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
    $brands = $api->get_brands($name);
    $results = [];
    if (isset($brands['brands'])) {
        foreach ($brands['brands'] as $brand) {
            if (!empty($brand['id']) && !empty($brand['name'])) {
                $results[] = ['id' => $brand['id'], 'name' => $brand['name']];
            }
        }
    }
    wp_send_json($results);
});

// Ürün gönderimi
add_action('wp_ajax_tw_send_products', function(){
    check_ajax_referer('tw_nonce');
    $product_ids = array_map('intval', $_POST['product_ids'] ?? []);
    $sync = new TW_ProductSync();
    $result = $sync->sync($product_ids);
    wp_send_json_success(['message' => 'Gönderildi.', 'result' => $result]);
});

// Log temizle
add_action('wp_ajax_tw_clear_logs', function(){
    (new TW_Logger())->clear();
    wp_send_json_success(['message' => 'Loglar temizlendi.']);
});


add_action('admin_enqueue_scripts', function(){
    // Select2 JS
    wp_enqueue_script(
        'select2',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js',
        ['jquery'],
        '4.1.0',
        true
    );
    // Select2 CSS
    wp_enqueue_style(
        'select2',
        'https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css',
        [],
        '4.1.0'
    );
});
