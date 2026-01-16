<?php

/**
 * Plugin Name: Zalo Bot Workstation
 * Description: Tích hợp giải pháp Zalo Bot cho WordPress.
 * Version: 1.0.0
 * Author: inanh86.com
 * Text Domain: zalo-bot
 */

// Ngăn chặn truy cập trực tiếp
if (!defined('ABSPATH')) {
    exit;
}

// Định nghĩa các hằng số
define('ZALO_BOT_DIR', plugin_dir_path(__FILE__));
define('ZALO_BOT_URL', plugin_dir_url(__FILE__));

// Nạp Autoloader từ Composer
if (file_exists(ZALO_BOT_DIR . 'vendor/autoload.php')) {
    require_once ZALO_BOT_DIR . 'vendor/autoload.php';
}

/**
 * Hàm kích hoạt Plugin
 */
function activate_zalo_bot_plugin()
{
    // Gọi class chuyên xử lý cài đặt
    if (class_exists('Inanh86\ZaloBot\Setup\Installer')) {
        Inanh86\ZaloBot\Setup\Installer::activate();
    }
}
// Đăng ký hook khi nhấn "Kích hoạt"
register_activation_hook(__FILE__, 'activate_zalo_bot_plugin');

/**
 * Khởi tạo Plugin
 */
function run_zalo_bot_plugin()
{
    if (class_exists('Inanh86\ZaloBot\Main')) {
        return new Inanh86\ZaloBot\Main();
    }
}
add_action('plugins_loaded', 'run_zalo_bot_plugin');
