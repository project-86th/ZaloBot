<?php

namespace Inanh86\ZaloBot\Api;

use Inanh86\ZaloBot\Services\ZaloApiService;
use Inanh86\ZaloBot\Utils\Logger;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

defined('ABSPATH') || exit;

class Settings
{

    protected $namespace = 'zalo-bot/v1';
    protected $rest_base = 'settings';

    /**
     * Đăng ký các route
     */
    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::CREATABLE, // Phương thức POST
                'callback'            => [$this, 'update_settings'],
                'permission_callback' => [$this, 'get_item_permissions_check'],
            ],
        ]);
    }

    /**
     * Logic xử lý lưu dữ liệu
     */
    public function update_settings(\WP_REST_Request $request): WP_REST_Response
    {
        $params  = $request->get_json_params();
        $option_key = ZALO_BOT_SETTING_KEY;

        // 1. Lấy mảng cài đặt hiện tại từ Database
        // Nếu chưa có thì lấy mảng trống
        $settings = get_option($option_key, []);

        // 2. Cập nhật các giá trị mới từ React gửi lên
        // Dùng trim() để loại bỏ khoảng trắng cho API Key
        if (isset($params['api_key'])) {
            $settings['access_token'] = trim($params['api_key']);
        }

        if (isset($params['status'])) {
            $settings['status'] = sanitize_text_field($params['status']);
        }

        // 3. Lưu mảng đã cập nhật ngược lại vào Database
        update_option($option_key, $settings);

        // 4. Logic đồng bộ Webhook lên Zalo
        $zalo_msg = '';
        // Sử dụng giá trị vừa cập nhật trong mảng $settings
        if (!empty($settings['access_token'])) {
            $webhook_url = get_rest_url(null, 'zalo-bot/v1/webhook');

            $zalo_api = new ZaloApiService();
            // Truyền token bảo mật là webhook_token trong mảng
            $sync = $zalo_api->set_webhook(
                $settings['access_token'],
                $webhook_url,
                $settings['webhook_token'] ?? ''
            );

            if ($sync['success']) {
                $zalo_msg = ' và đã đồng bộ Webhook lên Zalo!';
            } else {
                $zalo_msg = ' nhưng lỗi đồng bộ: ' . $sync['message'];
                Logger::error(['error' => $sync['message'], 'message' => 'Lỗi Đồng bộ']);
            }
        }

        return new \WP_REST_Response([
            'success' => true,
            'message' => 'Cấu hình đã được lưu' . $zalo_msg
        ], 200);
    }

    /**
     * Kiểm tra quyền truy cập: Chỉ admin mới được lưu
     */
    public function get_item_permissions_check($request)
    {
        return current_user_can('manage_options');
    }
}
