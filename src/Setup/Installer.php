<?php

namespace Inanh86\ZaloBot\Setup;

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

        // Làm mới quy tắc đường dẫn của WordPress (quan trọng nếu có Custom URL)
        flush_rewrite_rules();
    }

    /**
     * Tạo bảng lưu trữ định danh Zalo User ID trong Database.
     * Sử dụng hàm dbDelta để đảm bảo không làm mất dữ liệu khi cập nhật cấu trúc bảng.
     * @return void
     */
    private static function create_table(): void
    {
        global $wpdb;

        // Định nghĩa tên bảng kèm tiền tố (ví dụ: wp_zalo_clients)
        $table_name = $wpdb->prefix . 'zalo_clients';
        $charset_collate = $wpdb->get_charset_collate();

        // Câu lệnh SQL tạo bảng theo chuẩn WordPress
        $sql = "CREATE TABLE $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            zalo_user_id varchar(100) NOT NULL,    /* ID định danh duy nhất từ Zalo */
            display_name varchar(255) DEFAULT '',  /* Tên hiển thị của khách hàng */
            last_message text DEFAULT '',          /* Nội dung tin nhắn cuối cùng */
            last_active datetime DEFAULT CURRENT_TIMESTAMP, /* Thời gian tương tác cuối */
            PRIMARY KEY  (id),
            UNIQUE KEY zalo_user_id (zalo_user_id) /* Ngăn chặn trùng lặp User ID */
        ) $charset_collate;";

        // Nạp thư viện cần thiết để chạy hàm dbDelta
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        // Thực thi tạo hoặc cập nhật bảng
        dbDelta($sql);
    }

    /**
     * Thiết lập các giá trị cấu hình mặc định trong bảng wp_options.
     *  @return void
     */
    private static function add_default_options(): void
    {
        $defaults = [
            'zalo_bot_status'        => 'off',                          // Trạng thái hoạt động (on/off)
            'zalo_bot_webhook_token' => wp_generate_password(32, false), // Mã bảo mật cho Webhook
            'zalo_bot_version'       => '1.0.0',                        // Phiên bản plugin
            'zalo_bot_access_token'  => '',                             // Token để gọi API Zalo
        ];

        foreach ($defaults as $option => $value) {
            if (get_option($option) === false) {
                update_option($option, $value);
            }
        }
    }
}
