<?php
if (!defined('ABSPATH')) exit;
$args = [
    'post_type' => 'product',
    'posts_per_page' => 50,
    'post_status' => 'publish'
];
$products = get_posts($args);
?>
<div class="tw-admin-wrapper">
    <h1>
        <span class="dashicons dashicons-products"></span> Ürün Gönderimi
    </h1>
    <form id="tw-product-sync-form">
        <table class="widefat">
            <thead>
                <tr>
                    <th><input type="checkbox" id="tw_checkall"></th>
                    <th>Ürün</th>
                    <th>Kategori</th>
                    <th>SKU / Barkod</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $post):
                $p = wc_get_product($post->ID);
                $cats = wp_get_post_terms($post->ID, 'product_cat');
                ?>
                <tr>
                    <td><input type="checkbox" class="tw-product-checkbox" value="<?php echo $post->ID; ?>"></td>
                    <td>
                        <?php echo esc_html($p->get_name()); ?>
                    </td>
                    <td>
                        <?php
                        echo esc_html(
                            count($cats) ? $cats[0]->name : '-'
                        );
                        ?>
                    </td>
                    <td><?php echo esc_html($p->get_sku()); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button type="button" class="button tw-sync-btn" style="margin-top: 18px;">
            <span class="dashicons dashicons-migrate"></span> Seçili Ürünleri Trendyol'a Gönder
        </button>
        <img src="<?php echo includes_url('images/spinner.gif'); ?>" class="tw-loader" style="display:none;">
    </form>
    <script>
    jQuery(document).ready(function($){
        $('#tw_checkall').on('change', function(){
            $('.tw-product-checkbox').prop('checked', $(this).is(':checked'));
        });
    });
    </script>
</div>
