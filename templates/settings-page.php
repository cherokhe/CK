<?php
if (!defined('ABSPATH')) exit;
$api_key = get_option('tw_api_key', '');
$api_secret = get_option('tw_api_secret', '');
$supplier_id = get_option('tw_supplier_id', '');
$brand_id = get_option('tw_brand_id', '');
$brand_name = '';
if ($brand_id) {
    // Son seçilen marka adı için küçük bir sorgu, API veya DB'ye göre burası değişebilir
    // Kolaylık için $brand_name = ... doldurulabilir
}
?>
<div class="tw-admin-wrapper">
    <h1>
        <span class="dashicons dashicons-admin-settings"></span> Trendyol Ayarları
    </h1>
    <form id="tw_settings_form">
        <table class="form-table">
            <tr>
                <th>API Key:</th>
                <td><input type="text" name="api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th>API Secret:</th>
                <td><input type="text" name="api_secret" value="<?php echo esc_attr($api_secret); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th>Supplier ID:</th>
                <td><input type="text" name="supplier_id" value="<?php echo esc_attr($supplier_id); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th>Marka Seçimi:</th>
                <td>
                    <select id="tw_brand_select" name="tw_brand_select" style="width:250px;">
                        <?php if ($brand_id): ?>
                            <option value="<?php echo esc_attr($brand_id); ?>" selected>
                                <?php echo esc_html($brand_name ?: "Seçili Marka ID: $brand_id"); ?>
                            </option>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
        </table>
        <p>
            <button type="submit" class="button button-primary">
                <span class="dashicons dashicons-yes"></span> Kaydet
            </button>
        </p>
        <div id="tw_settings_save_msg"></div>
    </form>
</div>
<script>
jQuery(document).ready(function($){
    $('#tw_brand_select').select2({
        placeholder: 'Marka ara...',
        minimumInputLength: 2,
        allowClear: true,
        width: '250px',
        ajax: {
            url: twAjax.ajaxurl,
            dataType: 'json',
            delay: 350,
            data: function (params) {
                return {
                    action: 'tw_fetch_brands',
                    _ajax_nonce: twAjax.nonce,
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function(brand){
                        return {
                            id: brand.id,
                            text: brand.name
                        }
                    })
                };
            }
        }
    });

    $('#tw_settings_form').on('submit', function(e){
        e.preventDefault();
        var form = $(this);
        $.post(twAjax.ajaxurl, form.serialize() + '&action=tw_save_api_settings&_ajax_nonce=' + twAjax.nonce, function(res){
            $('#tw_settings_save_msg').html('<span style="color:green">'+res.data.message+'</span>');
        });
    });
});
</script>
