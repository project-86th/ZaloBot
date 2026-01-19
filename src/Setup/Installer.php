<?php

namespace Inanh86\ZaloBot\Setup;

use Inanh86\ZaloBot\Utils\Logger;

defined('ABSPATH') || exit;

/**
 * Class Installer
 * * Chịu trách nhiệm thiết lập môi trường cho Plugin khi kích hoạt.
 * Bao gồm: Tạo bảng cơ sở dữ liệu, thiết lập tùy chọn mặc định và dọn dẹp cache.
 * * @package Inanh86\ZaloBot\Setup
 */
class Installer
{

    /**
     * Phương thức chính được gọi khi người dùng nhấn "Kích hoạt" Plugin.
     * @return void
     */
    public static function activate(): void
    {
        // Khởi chạy quy trình tạo bảng dữ liệu khách hàng
        self::create_table();

        // Cấu hình các thông số mặc định ban đầu cho Plugin
        self::add_default_options();

        // Thêm flag đánh dấu cần chạy Onboarding
        update_option('zalo_bot_needs_onboarding', true);

        // Làm mới quy tắc đường dẫn của WordPress (quan trọng nếu có Custom URL)
        flush_rewrite_rules();
    }

    /**
     * Tạo bảng lưu trữ định danh Zalo User ID trong Database.
     * Sử dụng hàm dbDelta để đảm bảo không làm mất dữ liệu khi cập nhật cấu trúc bảng.
     * @return void
     */
    public static function create_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'zalo_clients';
        $charset_collate = $wpdb->get_charset_collate();

        /**
         * LƯU Ý QUAN TRỌNG CHO dbDelta:
         * - Không đặt DEFAULT cho cột TEXT (last_message).
         * - Phải có 2 khoảng trắng giữa PRIMARY KEY và định nghĩa.
         * - Mỗi cột nên nằm trên 1 dòng.
         */
        $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        zalo_user_id varchar(100) NOT NULL,
        display_name varchar(255) DEFAULT '',
        last_message text,
        last_active datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY zalo_user_id (zalo_user_id)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Thiết lập các giá trị cấu hình mặc định trong bảng wp_options.
     *  @return void
     */
    private static function add_default_options(): void
    {
        $option_key = ZALO_BOT_SETTING_KEY;

        // Lấy dữ liệu hiện tại từ Database
        $existing_settings = get_option($option_key);

        $defaults = [
            'status'        => 'off',
            'webhook_token' => wp_generate_password(32, false),
            'version'       => '1.0.0',
            'access_token'  => '',
        ];

        // Nếu chưa từng có settings nào, lưu toàn bộ default
        if ($existing_settings === false) {
            update_option($option_key, $defaults);
        } else {
            // Nếu đã có, chỉ bổ sung những key còn thiếu (dành cho các bản update sau này)
            $new_settings = wp_parse_args($existing_settings, $defaults);
            update_option($option_key, $new_settings);
        }
    }
}
