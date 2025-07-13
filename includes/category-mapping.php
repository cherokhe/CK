<?php
if (!defined('ABSPATH')) exit;

class TW_CategoryMapping {
    public function get_mappings() {
        return get_option('tw_category_mappings', []);
    }
    public function save_mapping($woo_cat_id, $trendyol_cat_id) {
        $mappings = $this->get_mappings();
        $mappings[$woo_cat_id] = $trendyol_cat_id;
        update_option('tw_category_mappings', $mappings);
    }
    public function get_trendyol_category($woo_cat_id) {
        $mappings = $this->get_mappings();
        return isset($mappings[$woo_cat_id]) ? $mappings[$woo_cat_id] : null;
    }
}
