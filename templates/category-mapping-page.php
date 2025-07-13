<?php
if (!defined('ABSPATH')) exit;

// WooCommerce kategorilerini çek
$categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]);
$mapping = new TW_CategoryMapping();
$api = new TW_ApiClient();
$trendyol_cats = $api->get_categories();

// --- Yapı kontrolü ---
if (is_array($trendyol_cats) && isset($trendyol_cats['categories']) && is_array($trendyol_cats['categories'])) {
    $trendyol_cats = $trendyol_cats['categories'];
} else {
    echo '<div style="color:red;">Trendyol kategorileri yüklenemedi. API veya bağlantı sorunu olabilir.</div>';
    $trendyol_cats = [];
}

// Alt kategorisi olmayanları (leaf) getir
function tw_render_trendyol_leaf_categories($categories, $selected = '', $prefix = '') {
    foreach ($categories as $cat) {
        if (!isset($cat['id'], $cat['name'])) continue;
        $is_leaf = (empty($cat['subCategories']) || count($cat['subCategories']) == 0);
        if ($is_leaf) {
            $is_selected = ($selected == $cat['id']) ? 'selected' : '';
            echo '<option value="' . esc_attr($cat['id']) . '" ' . $is_selected . '>' . $prefix . esc_html($cat['name']) . '</option>';
        }
        if (isset($cat['subCategories']) && is_array($cat['subCategories']) && count($cat['subCategories'])) {
            tw_render_trendyol_leaf_categories($cat['subCategories'], $selected, $prefix . '— ');
        }
    }
}
?>
<div class="tw-admin-wrapper">
    <h1>
        <span class="dashicons dashicons-networking"></span> Kategori Eşleştirme
    </h1>
    <table class="widefat">
        <thead>
            <tr>
                <th>WooCommerce Kategorisi</th>
                <th>Trendyol Kategorisi</th>
                <th>Eşleştir</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?php echo esc_html($cat->name); ?></td>
                <td>
                    <select class="tw-trendyol-cat-select" id="trendyol_cat_<?php echo $cat->term_id; ?>">
                        <option value="">Seçiniz</option>
                        <?php tw_render_trendyol_leaf_categories($trendyol_cats, $mapping->get_trendyol_category($cat->term_id)); ?>
                    </select>
                </td>
                <td>
                    <button class="button tw-map-cat-btn" data-woo="<?php echo esc_attr($cat->term_id); ?>">
                        <span class="dashicons dashicons-yes"></span> Kaydet
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
