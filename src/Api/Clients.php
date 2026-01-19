<?php

namespace Inanh86\ZaloBot\Api;

use Inanh86\ZaloBot\Database\ClientRepository;
use Inanh86\ZaloBot\Services\ZaloApiService;
use Inanh86\ZaloBot\Utils\Logger;

/**
 * Quản lý danh sách khách hàng và các thao tác liên quan tới Chat ID.
 */
class Clients extends BaseController
{

    protected $rest_base = 'clients';
    protected $client_repo;

    public function __construct()
    {
        $this->client_repo = new ClientRepository();
    }

    /**
     * Đăng ký các Route
     */
    public function register_routes()
    {
        // Lấy danh sách khách hàng: GET wp-json/zalo-bot/v1/clients
        $this->register_endpoint('/' . $this->rest_base, 'GET', 'get_clients');

        // Gửi tin nhắn demo: POST wp-json/zalo-bot/v1/clients/send-demo
        $this->register_endpoint('/' . $this->rest_base . '/send-demo', 'POST', 'send_demo_message');
    }

    /**
     * Lấy danh sách khách hàng có bắt lỗi
     */
    public function get_clients($request)
    {
        try {
            $limit  = (int) $request->get_param('per_page') ?: 20;
            $page   = (int) $request->get_param('page') ?: 1;
            $offset = ($page - 1) * $limit;

            // Gọi Repo lấy dữ liệu
            $data = $this->client_repo->get_all_clients($limit, $offset);

            // Kiểm tra nếu dữ liệu trả về bị lỗi hoặc rỗng không đúng cấu trúc
            if (!isset($data['items'])) {
                throw new \Exception('Dữ liệu khách hàng không hợp lệ.');
            }

            return new \WP_REST_Response([
                'success' => true,
                'data'    => $data['items'],
                'total'   => $data['total'],
                'page'    => $page
            ], 200);
        } catch (\Exception $e) {
            Logger::debug("Lỗi lấy danh sách Client: " . $e->getMessage());

            return new \WP_Error(
                'internal_error',
                'Không thể tải danh sách khách hàng. Vui lòng thử lại sau.',
                ['status' => 500]
            );
        }
    }

    /**
     * Gửi demo với bắt lỗi chặt chẽ
     */
    public function send_demo_message($request)
    {
        try {
            $params = $request->get_json_params();
            $chat_id = $params['chat_id'] ?? '';
            $message = $params['message'] ?? '';

            // 1. Validate đầu vào
            if (empty($chat_id) || empty($message)) {
                return new \WP_Error('missing_params', 'Thiếu Chat ID hoặc nội dung tin nhắn.', ['status' => 400]);
            }

            // 2. Lấy token
            $settings = get_option(ZALO_BOT_SETTING_KEY, []);
            $bot_token = $settings['access_token'] ?? '';

            if (empty($bot_token)) {
                return new \WP_Error('missing_token', 'Bot chưa được cấu hình Access Token.', ['status' => 400]);
            }

            // 3. Gọi API Zalo
            $zalo_api = new ZaloApiService();
            $result = $zalo_api->send_message($bot_token, $chat_id, $message);

            // 4. Kiểm tra lỗi trả về từ Zalo (Zalo thường trả 200 kèm error code)
            if (isset($result['error']) && $result['error'] !== 0) {
                $error_msg = $result['message'] ?? 'Lỗi không xác định từ Zalo';
                Logger::debug("Zalo API Error: $error_msg", $result);

                return new \WP_Error('zalo_api_error', "Zalo báo lỗi: $error_msg", ['status' => 400]);
            }

            return new \WP_REST_Response([
                'success' => true,
                'message' => 'Gửi tin nhắn thành công!'
            ], 200);
        } catch (\Exception $e) {
            Logger::debug("Exception khi gửi demo: " . $e->getMessage());
            return new \WP_Error('server_error', 'Có lỗi xảy ra trong quá trình xử lý.', ['status' => 500]);
        }
    }
}
