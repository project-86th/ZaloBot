<?php

namespace Inanh86\ZaloBot\Database;

defined('ABSPATH') || exit;

/**
 * Class ClientRepository
 * Quản lý mọi thao tác CRUD với bảng dữ liệu khách hàng Zalo
 */
class ClientRepository
{

    /**
     * Lấy tên bảng có kèm tiền tố (prefix) của WordPress
     */
    private static function get_table_name()
    {
        global $wpdb;
        return $wpdb->prefix . 'zalo_clients';
    }

    /**
     * Lưu hoặc cập nhật thông tin khách hàng từ Zalo vào Database.
     * * Hàm này sử dụng cơ chế "Upsert" (Update if Insert fails):
     * - Nếu Chat ID chưa tồn tại: Tạo dòng mới.
     * - Nếu Chat ID đã có: Cập nhật tin nhắn cuối, thời gian hoạt động và tên hiển thị.
     * @param string $user_id      ID duy nhất của người dùng Zalo (chat_id).
     * @param string $message      Nội dung tin nhắn cuối cùng khách gửi.
     * @param string $display_name Tên hiển thị của khách (Ví dụ: "Ted").
     * @return int|false Số dòng bị ảnh hưởng hoặc false nếu truy vấn lỗi.
     */
    public function save_client($user_id, $message, $display_name = ''): int|bool
    {
        global $wpdb;
        $table = self::get_table_name();

        // 1. Làm sạch dữ liệu đầu vào để chống SQL Injection và XSS
        $user_id      = sanitize_text_field($user_id);
        $message      = sanitize_textarea_field($message); // Giữ lại xuống dòng nếu có
        $display_name = sanitize_text_field($display_name);

        /**
         * Sử dụng prepare() để truyền tham số an toàn.
         * Logic ON DUPLICATE KEY UPDATE:
         * - last_message: Luôn cập nhật tin nhắn mới nhất.
         * - last_active: Ghi nhận thời điểm tương tác cuối cùng.
         * - display_name: Chỉ cập nhật nếu Zalo gửi sang tên không rỗng (tránh ghi đè tên cũ bằng rỗng).
         */
        return $wpdb->query($wpdb->prepare(
            "INSERT INTO $table (zalo_user_id, last_message, display_name, last_active) 
         VALUES (%s, %s, %s, %s) 
         ON DUPLICATE KEY UPDATE 
         last_message = VALUES(last_message), 
         last_active = VALUES(last_active),
         display_name = IF(VALUES(display_name) != '', VALUES(display_name), display_name)",
            $user_id,
            $message,
            $display_name,
            current_time('mysql') // Lấy thời gian hiện tại theo múi giờ của WordPress
        ));
    }

    /**
     * Lấy danh sách tất cả khách hàng đã từng nhắn tin
     */
    public function get_all_clients($limit = 20, $offset = 0)
    {
        global $wpdb;
        $table = self::get_table_name();

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table ORDER BY last_active DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        ));
    }

    /**
     * Tìm kiếm một khách hàng theo Zalo User ID
     */
    public function get_client_by_zalo_id($user_id)
    {
        global $wpdb;
        $table = self::get_table_name();

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table WHERE zalo_user_id = %s",
            $user_id
        ));
    }
}
