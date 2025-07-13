<?php
if (!defined('ABSPATH')) exit;
$logger = new TW_Logger();
$logs = $logger->get_logs();
?>
<div class="tw-admin-wrapper">
    <h1>
        <span class="dashicons dashicons-clipboard"></span> Gönderim ve Hata Logları
    </h1>
    <button class="button tw-clear-log-btn" style="margin-bottom:15px;">
        <span class="dashicons dashicons-trash"></span> Tüm Logları Temizle
    </button>
    <table class="widefat">
        <thead>
            <tr>
                <th>Tarih</th>
                <th>Tip</th>
                <th>Mesaj</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr><td colspan="3" style="text-align:center;color:#888;">Kayıt bulunamadı.</td></tr>
            <?php else: foreach ($logs as $l): ?>
                <tr>
                    <td><?php echo esc_html($l['time']); ?></td>
                    <td><?php echo esc_html($l['level']); ?></td>
                    <td><?php echo esc_html($l['msg']); ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>
