<?php
if (!defined('ABSPATH')) exit;

class TW_BrandMapping {
    public function get_brand() {
        return get_option('tw_selected_brand', '');
    }
    public function set_brand($brand_id) {
        update_option('tw_selected_brand', $brand_id);
    }
}
