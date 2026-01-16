<?php

namespace Inanh86\ZaloBot;

use Inanh86\ZaloBot\Admin\SettingsPage;
use Inanh86\ZaloBot\Database\ClientRepository;
use WP_Error;

/**
 * Class Main
 * Điều phối chính các hoạt động của Plugin Zalo Bot
 */
class Main
{
    protected $client_repo;

    /**
     * Khởi tạo các thành phần của Plugin
     */
    public function __construct()
    {
        $this->load_dependencies();

        // Khởi tạo Repository để dùng chung cho cả class
        $this->client_repo = new ClientRepository();

        // tại các Hook Quang Trọng
        $this->init_hooks();

        // Nếu ở trong trang quản trị
        if (is_admin()) {
            $this->init_admin();
        }
    }

    /**
     * Nạp các class thành phần (nếu cần thủ công)
     * Thường Composer đã lo phần này, nhưng đây là nơi để khởi tạo các Class con.
     */
    private function load_dependencies()
    {
        // Ví dụ: $this->api_client = new Api\Client();
    }

    /**
     * Đăng ký các Hook dùng chung cho cả Frontend và Backend
     */
    private function init_hooks()
    {
        // Ví dụ: Lắng nghe Webhook từ Zalo gửi về
        add_action('rest_api_init', array($this, 'register_zalo_webhook_route'));
    }

    /**
     * Khởi tạo các tính năng trong Admin
     */
    private function init_admin(): void
    {
        // Khởi tạo trang cài đặt ReactJS
        new SettingsPage();
    }

    /**
     * Đăng ký một Endpoint REST API để nhận dữ liệu từ Zalo
     * URL sẽ có dạng: yourdomain.com/wp-json/zalo-bot/v1/webhook
     */
    public function register_zalo_webhook_route(): void
    {
        register_rest_route('zalo-bot/v1', '/webhook', array(
            'methods'  => 'POST',
            'callback' => array($this, 'handle_zalo_webhook'),
            'permission_callback' => array($this, 'validate_webhook_token'),
        ));
    }

    /**
     * Kiểm tra xem request gửi đến có kèm mã Token đúng không
     */
    public function validate_webhook_token($request): bool|WP_Error
    {
        $client_token = $request->get_param('token'); // Lấy từ URL: ?token=abc...
        $server_token = get_option('zalo_bot_webhook_token');

        if (!empty($server_token) && $client_token === $server_token) {
            return true;
        }

        return new \WP_Error('rest_forbidden', 'Mã xác thực không hợp lệ.', ['status' => 401]);
    }

    /**
     * Xử lý dữ liệu từ Zalo gửi đến
     */
    public function handle_zalo_webhook($request): \WP_REST_Response
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
        // Nội dung đoạn chat ( Có thể sử dụng để viết thêm /comm )
        $message_text = isset($data['message']['text']) ? sanitize_textarea_field($data['message']['text']) : '';

        // Có chat_id thì tiến hành ghi vào database
        if ($zalo_user_id) {
            // Sử dụng Repository để lưu dữ liệu
            $this->client_repo->save_client($zalo_user_id, $message_text);
        }

        // trả kết quả
        return new \WP_REST_Response(['status' => 'success'], 200);
    }
}
