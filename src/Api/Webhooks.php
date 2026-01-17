<?php

namespace Inanh86\ZaloBot\Api;

use Inanh86\ZaloBot\Database\ClientRepository;
use Inanh86\ZaloBot\Services\ZaloApiService;
use Inanh86\ZaloBot\Utils\Logger;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;


defined('ABSPATH') || exit;

/**
 * Đăng ký một Endpoint REST API để nhận dữ liệu từ Zalo
 * URL sẽ có dạng: yourdomain.com/wp-json/zalo-bot/v1/webhook
 */
class WebHooks
{

    protected $namespace = 'zalo-bot/v1';
    protected $rest_base = 'webhook';
    protected $client_repo;

    public function __construct()
    {
        $this->client_repo = new ClientRepository();
    }

    public function register_routes()
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::CREATABLE, // POST
                'callback'            => [$this, 'handle_event'],
                'permission_callback' => [$this, 'validate_token'],
            ],
        ]);
    }

    /**
     * Kiểm tra token để đảm bảo yêu cầu đến từ Zalo hoặc có quyền
     */
    public function validate_token(\WP_REST_Request $request): bool|\WP_Error
    {
        // 1. Lấy mảng cấu hình tập trung
        $settings = get_option(ZALO_BOT_SETTING_KEY, []);

        // 2. Trích xuất webhook_token từ mảng
        $saved_token = isset($settings['webhook_token']) ? $settings['webhook_token'] : '';

        if (empty($saved_token)) {
            Logger::error('Webhook chưa được cấu hình token');
            return new \WP_Error('rest_forbidden', 'Webhook chưa được cấu hình token.', ['status' => 403]);
        }

        // 3. Lấy token gửi từ Zalo (Ưu tiên Header theo docs, dự phòng Query String)
        $token_from_header = $request->get_header('x-bot-api-secret-token');
        $token_from_query  = $request->get_param('token');

        // 4. So khớp
        if (
            (!empty($token_from_header) && hash_equals($saved_token, $token_from_header)) ||
            (!empty($token_from_query) && hash_equals($saved_token, $token_from_query))
        ) {
            return true;
        }
        Logger::error('Xác thực Webhook thất bại');
        return new \WP_Error('rest_forbidden', 'Xác thực Webhook thất bại.', ['status' => 403]);
    }

    /**
     * Xử lý dữ liệu JSON từ Zalo
     */
    public function handle_event(WP_REST_Request $request): WP_REST_Response
    {
        // Kiểm tra xem Bot có đang được bật không
        $status = get_option('zalo_bot_status', 'off');

        if ($status !== 'on') {
            return new \WP_REST_Response([
                'message' => 'Bot hiện đang tạm dừng hoạt động.'
            ], 503); // Trả về mã lỗi 503 (Service Unavailable)
        }

        $data = $request->get_json_params();

        // Lấy chat_id (ID của client đang chat với bot)
        $zalo_user_id = isset($data['sender']['id']) ? sanitize_text_field($data['sender']['id']) : '';

        // Có chat_id thì tiến hành ghi vào database
        if ($zalo_user_id) {
            // 1. Lưu vào database (như bài trước)
            $this->client_repo->save_client($zalo_user_id, $data['message']['text'] ?? '');

            // 2. Tự động nhắn lại (Auto-reply)
            $bot_token = get_option('zalo_bot_access_token');
            if (!empty($bot_token)) {
                $zalo_api = new ZaloApiService();
                $zalo_api->send_message(
                    $bot_token,
                    $zalo_user_id,
                    "Bot đã nhận được tin nhắn: " . ($data['message']['text'] ?? '')
                );
            }
        }

        // trả kết quả
        return new \WP_REST_Response(['status' => 'success'], 200);
    }
}
