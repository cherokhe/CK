jQuery(document).ready(function ($) {
    // Trendyol kategori eşleştirme için Select2 (arama özellikli)
    if ($.fn.select2) {
        $('.tw-trendyol-cat-select').select2({
            width: '250px',
            placeholder: 'Kategori ara...',
            allowClear: true,
            language: "tr" // Türkçe destek için, istersen kaldırabilirsin
        });
    }

    // Kategori eşleştirme kaydet butonu (AJAX)
    $('.tw-map-cat-btn').on('click', function (e) {
        e.preventDefault();
        var btn = $(this);
        var woo_cat_id = btn.data('woo');
        var trendyol_cat_id = $('#trendyol_cat_' + woo_cat_id).val();
        if (!trendyol_cat_id) {
            alert('Lütfen bir Trendyol kategorisi seçiniz!');
            return;
        }
        $.post(twAjax.ajaxurl, {
            action: 'tw_map_category',
            _ajax_nonce: twAjax.nonce,
            woo_cat_id: woo_cat_id,
            trendyol_cat_id: trendyol_cat_id
        }, function (res) {
            if (res.success) {
                btn.after('<span class="tw-cat-saved-msg" style="color:green;margin-left:8px;">Kaydedildi!</span>');
                setTimeout(function () {
                    btn.parent().find('.tw-cat-saved-msg').fadeOut(500, function () { $(this).remove(); });
                }, 1400);
            }
        });
    });
});
