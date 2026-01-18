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
class WebHooksController extends BaseController
{
    protected $rest_base = 'webhook';
    protected $client_repo;

    public function __construct()
    {
        $this->client_repo = new ClientRepository();
    }

    public function register_routes()
    {
        $this->register_endpoint('/' . $this->rest_base, 'POST', 'handle_event');
    }

    /**
     * Kiểm tra token để đảm bảo yêu cầu đến từ Zalo hoặc có quyền
     */
    public function check_permission(\WP_REST_Request $request): bool|\WP_Error
    {
        // 1. Lấy cấu hình tập trung
        $settings = get_option(ZALO_BOT_SETTING_KEY, []);
        $saved_token = $settings['webhook_token'] ?? '';

        // 2. Kiểm tra nếu chưa cài đặt token trong admin
        if (empty($saved_token)) {
            Logger::error('Webhook chưa được cấu hình token trong Settings.');
            return new \WP_Error(
                'rest_forbidden',
                'Dịch vụ chưa sẵn sàng (Thiếu cấu hình).',
                ['status' => 403]
            );
        }

        // 3. Lấy token từ Zalo
        // Header x-bot-api-secret-token được Zalo gửi kèm mỗi event
        $token_from_header = $request->get_header('x-bot-api-secret-token');
        // Token từ query string dùng để ní test nhanh qua trình duyệt/Postman
        $token_from_query  = $request->get_param('token');

        // 4. So khớp an toàn với hash_equals
        $is_valid = false;

        if (!empty($token_from_header) && hash_equals($saved_token, $token_from_header)) {
            $is_valid = true;
        } elseif (!empty($token_from_query) && hash_equals($saved_token, $token_from_query)) {
            $is_valid = true;
        }

        if ($is_valid) {
            return true;
        }

        // 5. Log lỗi chi tiết để debug khi cần
        Logger::error([
            'message' => 'Xác thực Webhook thất bại. Token không khớp.',
            'received_header' => $token_from_header ? '***' : 'empty',
            'received_query'  => $token_from_query ? '***' : 'empty'
        ]);

        return new \WP_Error(
            'rest_forbidden',
            'Xác thực Webhook thất bại.',
            ['status' => 403]
        );
    }

    /**
     * Xử lý dữ liệu JSON từ Zalo
     */
    public function handle_event(\WP_REST_Request $request): \WP_REST_Response
    {
        // Log 1: Nhận request mới
        $data = $request->get_json_params();

        // Logger::debug("Nhận Webhook từ Zalo", $data);

        // 1. Kiểm tra trạng thái Bot
        $settings = get_option(ZALO_BOT_SETTING_KEY, []);
        $status = $settings['status'] ?? 'off';

        if ($status !== 'on') {
            Logger::debug("Bot đang tắt (status: {$status}). Dừng xử lý.");
            return new \WP_REST_Response(['ok' => false], 200);
        }

        $event_name = $data['event_name'] ?? '';

        // 2. Xử lý sự kiện tin nhắn
        if ($event_name === 'message.text.received') {
            $message_obj = $data['message'] ?? [];
            $from_obj    = $message_obj['from'] ?? [];

            $zalo_user_id = $from_obj['id'] ?? '';
            $display_name = $from_obj['display_name'] ?? 'Khách hàng';
            $message_text = $message_obj['text'] ?? '';

            // Logger::debug("Đang xử lý tin nhắn từ: {$display_name} ({$zalo_user_id})", ['nội dung' => $message_text]);

            if ($zalo_user_id) {
                // Lưu vào database
                $saved = $this->client_repo->save_client($zalo_user_id, $message_text, $display_name);
                Logger::debug($saved ? "Lưu Database thành công" : "Lưu Database thất bại");

                // 3. Tự động phản hồi
                $bot_token = $settings['access_token'] ?? '';
                if (!empty($bot_token)) {
                    $zalo_api = new ZaloApiService();
                    $reply = $zalo_api->send_message($bot_token, $zalo_user_id, "Bot đã nhận: " . $message_text);

                    // Logger::debug("Kết quả gửi tin nhắn phản hồi", $reply);
                }
            }
        } else {
            Logger::error("Sự kiện không được hỗ trợ: {$event_name}");
        }

        return new \WP_REST_Response(['ok' => true], 200);
    }
}
