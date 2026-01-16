<?php

namespace Inanh86\ZaloBot\Setup;

/**
 * Class Installer
 * Xử lý các tác vụ khi cài đặt hoặc kích hoạt plugin
 */
class Installer
{

    /**
     * Phương thức chạy khi kích hoạt
     */
    public static function activate()
    {
        // 1. Cài đặt các tùy chọn mặc định nếu chưa có
        self::add_default_options();

        // 2. Làm mới Permalink (nếu plugin của bạn có Custom Post Type)
        flush_rewrite_rules();
    }

    /**
     * Thêm các cấu hình mặc định vào bảng wp_options
     */
    private static function add_default_options()
    {
        $defaults = [
            'zalo_bot_api_key'      => '',
            'zalo_bot_status'       => 'off',
            'zalo_bot_webhook_token' => wp_generate_password(32, false), // Tạo token 32 ký tự
            'zalo_bot_version'      => '1.0.0',
        ];

        foreach ($defaults as $option => $value) {
            if (get_option($option) === false) {
                update_option($option, $value);
            }
        }
    }
}
