<?php
if (!defined('ABSPATH')) exit;
$brand_mapping = new TW_BrandMapping();
$current_brand = $brand_mapping->get_brand();
?>
<div class="tw-admin-wrapper">
    <h1>
        <span class="dashicons dashicons-tag"></span> Marka Eşleştirme
    </h1>
    <form id="tw_brand_form" method="post">
        <label for="tw_brand_select"><strong>Trendyol Markası:</strong></label>
        <select id="tw_brand_select" name="tw_brand_select" style="width:250px;">
            <?php if ($current_brand): ?>
                <option value="<?php echo esc_attr($current_brand['id']); ?>" selected>
                    <?php echo esc_html($current_brand['name']); ?>
                </option>
            <?php endif; ?>
        </select>
        <button type="submit" class="button button-primary">
            <span class="dashicons dashicons-yes"></span> Kaydet
        </button>
    </form>
    <div id="tw_brand_save_msg"></div>
</div>
<script>
jQuery(document).ready(function($){
    // Marka eşleştir
    $('#tw_brand_form').on('submit', function(e){
        e.preventDefault();
        var brand_id = $('#tw_brand_select').val();
        $.post(twAjax.ajaxurl, {
            action: 'tw_save_brand',
            _ajax_nonce: twAjax.nonce,
            brand_id: brand_id
        }, function(res){
            $('#tw_brand_save_msg').html('<span style="color:green">'+res.data.message+'</span>');
        });
    });
    // Select2 dinamik marka arama (zaten admin.js'de de var, burada da ek güvenceyle!)
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
});
</script>
