<?php

namespace Inanh86\ZaloBot\Database;

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
     * Lưu hoặc cập nhật thông tin khách hàng
     * Sử dụng kỹ thuật ON DUPLICATE KEY UPDATE để tối ưu hiệu suất
     */
    public function save_client($user_id, $message, $display_name = '')
    {
        global $wpdb;
        $table = self::get_table_name();

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
            current_time('mysql')
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
