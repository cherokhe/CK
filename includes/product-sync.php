<?php
if (!defined('ABSPATH')) exit;

class TW_ProductSync {
    private $api, $category, $brand, $logger;
    public function __construct() {
        $this->api     = new TW_ApiClient();
        $this->category= new TW_CategoryMapping();
        $this->brand   = new TW_BrandMapping();
        $this->logger  = new TW_Logger();
    }

    public function send_product($product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            $this->logger->error("Ürün bulunamadı: $product_id");
            return false;
        }
        $woo_cat_ids = $product->get_category_ids();
        $woo_cat = isset($woo_cat_ids[0]) ? $woo_cat_ids[0] : null;
        $trendyol_cat = $this->category->get_trendyol_category($woo_cat);
        if (!$trendyol_cat) {
            $this->logger->error("Eşleşmemiş kategori: WooCat $woo_cat");
            return false;
        }

        $images = [];
        foreach ($product->get_gallery_image_ids() as $img_id) {
            $img_url = wp_get_attachment_url($img_id);
            if ($img_url) $images[] = ['url' => $img_url];
        }
        // Ana görsel
        $main_image_id = $product->get_image_id();
        if ($main_image_id && (!isset($images[0]) || $images[0]['url'] != wp_get_attachment_url($main_image_id))) {
            array_unshift($images, ['url' => wp_get_attachment_url($main_image_id)]);
        }

        $payload = [
            [
                "barcode" => $product->get_sku() ?: 'NO-BARCODE-' . $product_id,
                "title" => $product->get_name(),
                "brandId" => $this->brand->get_brand(),
                "categoryId" => $trendyol_cat,
                "quantity" => $product->get_stock_quantity() ?: 1,
                "stockCode" => $product->get_sku() ?: '',
                "dimensionalWeight" => 1,
                "description" => $product->get_description(),
                "images" => $images,
                "attributes" => [], // Kategori özellikleri burada doldurulabilir!
                // Gerekirse ek alanlar!
            ]
        ];

        $result = $this->api->send_products($payload);
        if (!$result) {
            $this->logger->error("Ürün gönderim hatası: $product_id");
        } else {
            $this->logger->info("Ürün gönderildi: $product_id");
        }
        return $result;
    }
}
