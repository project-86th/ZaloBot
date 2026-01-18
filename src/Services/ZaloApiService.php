<?php

namespace Inanh86\ZaloBot\Services;

/**
 * Class ZaloApiService
 * * Cung cấp các phương thức để tương tác trực tiếp với Zalo Bot API.
 * Chịu trách nhiệm thực hiện các yêu cầu HTTP (POST) và xử lý dữ liệu trả về.
 * @package Inanh86\ZaloBot\Services
 */
class ZaloApiService
{
    /**
     * URL gốc của Zalo Bot API.
     * @var string
     */
    protected $base_url = "https://bot-api.zaloplatforms.com";

    /**
     * Đăng ký URL Webhook với hệ thống Zalo.
     * * Khi cấu hình thành công, Zalo sẽ gửi mọi sự kiện (tin nhắn, quan tâm...) 
     * về địa chỉ URL này.
     * @param string $bot_token    Token định danh của Bot.
     * @param string $webhook_url  URL mà web của bạn dùng để nhận dữ liệu từ Zalo.
     * @param string $secret_token Khóa bí mật dùng để xác thực các yêu cầu gửi từ Zalo.
     * @return array Kết quả xử lý chứa 'success', 'message' và 'data'.
     */
    public function set_webhook($bot_token, $webhook_url, $secret_token): array
    {
        $api_url = "{$this->base_url}/bot{$bot_token}/setWebhook";

        $response = wp_remote_post($api_url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'url'          => $webhook_url,
                'secret_token' => $secret_token
            ]),
            'timeout' => 20, // Đợi tối đa 20 giây để tránh treo request
        ]);

        return $this->handle_response($response);
    }

    /**
     * Gửi tin nhắn văn bản tới một Chat ID cụ thể.
     * @param string $bot_token Token định danh của Bot.
     * @param string $chat_id   ID của người nhận hoặc nhóm nhận tin nhắn.
     * @param string $text      Nội dung tin nhắn (tối đa 2000 ký tự).
     * @return array Kết quả xử lý chứa 'success', 'message' và 'data'.
     */
    public function send_message($bot_token, $chat_id, $text): array
    {
        $api_url = "{$this->base_url}/bot{$bot_token}/sendMessage";

        $response = wp_remote_post($api_url, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'chat_id' => $chat_id,
                'text'    => $text
            ]),
            'timeout' => 20,
        ]);

        return $this->handle_response($response);
    }

    /**
     * Hàm dùng chung để xử lý và định dạng lại phản hồi từ WP_Error hoặc JSON của Zalo.
     * * Zalo Bot API luôn trả về key 'ok' kiểu boolean.
     * - Nếu 'ok' là true: Request thành công.
     * - Nếu 'ok' là false: Request thất bại, kèm thông tin lỗi trong 'description'.
     * @param array|\WP_Error $response Phản hồi từ hàm wp_remote_post.
     * @return array Mảng định dạng chuẩn:
     * - success (bool): Trạng thái thành công hay thất bại.
     * - message (string): Thông báo lỗi hoặc trạng thái.
     * - data (array): Dữ liệu chi tiết trả về từ 'result' của Zalo.
     */
    private function handle_response($response): array
    {
        // Kiểm tra lỗi kết nối hoặc lỗi server (ví dụ: mất mạng, domain không tồn tại)
        if (is_wp_error($response)) {
            return [
                'success' => false,
                'message' => 'Lỗi kết nối Server: ' . $response->get_error_message(),
                'data'    => []
            ];
        }

        // Giải mã nội dung JSON trả về từ Zalo
        $body = json_decode(wp_remote_retrieve_body($response), true);

        // Kiểm tra cấu trúc phản hồi theo chuẩn Bot API (phải có key 'ok')
        if (isset($body['ok']) && $body['ok'] === true) {
            return [
                'success' => true,
                'message' => 'Thành công',
                'data'    => $body['result'] ?? []
            ];
        }

        // Xử lý khi Zalo trả về lỗi (ok = false)
        // 'description' thường chứa mô tả chi tiết lỗi từ Zalo
        return [
            'success' => false,
            'message' => $body['description'] ?? ($body['message'] ?? 'Lỗi không xác định từ Zalo Bot API'),
            'data'    => $body
        ];
    }
}
