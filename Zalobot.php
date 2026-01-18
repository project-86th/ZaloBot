<?php

/**
 * Plugin Name: Zalo Bot Workstation
 * Description: Tích hợp giải pháp Zalo Bot cho WordPress.
 * Version: 1.0.0
 * Author: inanh86.com
 * Text Domain: zalo-bot
 */

defined('ABSPATH') || exit;

// 1. Nạp Autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// 2. Định nghĩa hằng số
define('ZALO_BOT_DIR', plugin_dir_path(__FILE__));
define('ZALO_BOT_URL', plugin_dir_url(__FILE__));
define('ZALO_BOT_SETTING_KEY', 'zalo_bot_settings');

/**
 * 3. Đăng ký Hook kích hoạt
 */
register_activation_hook(__FILE__, 'activate_zalo_bot_plugin');

function activate_zalo_bot_plugin()
{
    // Gọi Installer để tạo bảng và Set flag onboarding
    if (class_exists(\Inanh86\ZaloBot\Setup\Installer::class)) {
        \Inanh86\ZaloBot\Setup\Installer::activate();
        // Chắc chắn rằng option này được set tại đây
        update_option('zalo_bot_needs_onboarding', true);
    }
}

/**
 * 4. Khởi chạy Plugin
 */
add_action('plugins_loaded', function () {
    if (class_exists(\Inanh86\ZaloBot\Main::class)) {
        new \Inanh86\ZaloBot\Main();
    }
});

/**
 * 5. Xử lý chuyển hướng Onboarding
 * Dùng hook 'admin_init' là chuẩn nhất
 */
add_action('admin_init', function () {
    // CHỈ chạy nếu có flag và là yêu cầu từ trang admin
    if (!get_option('zalo_bot_needs_onboarding')) {
        return;
    }

    // Nếu đang ở đúng trang cấu hình thì xóa flag để kết thúc chu kỳ redirect
    if (isset($_GET['page']) && $_GET['page'] === 'zalo-bot-settings') {
        delete_option('zalo_bot_needs_onboarding');
        return;
    }

    global $pagenow;
    // Chỉ nhảy trang khi người dùng đang ở trang plugins hoặc trang chủ admin
    if ($pagenow === 'plugins.php' || $pagenow === 'index.php') {
        // Kiểm tra xem trang Menu đã được đăng ký chưa (tránh redirect vào trang 404)
        $url = admin_url('admin.php?page=zalo-bot-settings');

        // Quan trọng: Xóa flag TRƯỚC khi redirect để tránh loop nếu có lỗi
        delete_option('zalo_bot_needs_onboarding');

        wp_safe_redirect($url);
        exit;
    }
});
