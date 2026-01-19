<?php

namespace Inanh86\ZaloBot\Api;

/**
 * Base Controller cho hệ thống REST API của Zalo Bot.
 * Cung cấp cơ chế kiểm tra quyền hạn và đăng ký route dùng chung.
 */
abstract class BaseController extends \WP_REST_Controller implements RouteInterface
{

    protected $namespace = 'zalo-bot/v1';

    /**
     * Mặc định mọi API của Plugin chỉ dành cho Admin.
     * Các lớp con có thể override hàm này nếu cần quyền khác (vd: Webhook).
     */
    /**
     * Kiểm tra quyền truy cập mặc định cho các API.
     * * @param \WP_REST_Request $request
     * @return bool|\WP_Error Trả về true nếu có quyền, hoặc WP_Error nếu bị từ chối.
     */
    public function check_permission(\WP_REST_Request $request): bool|\WP_Error
    {
        // Kiểm tra quyền quản trị viên cao nhất
        if (current_user_can('manage_options')) {
            return true;
        }

        // Trả về lỗi chi tiết thay vì chỉ false
        return new \WP_Error(
            'rest_forbidden',
            __('Bạn không có quyền thực hiện hành động này.', 'zalo-bot'),
            ['status' => 403]
        );
    }

    /**
     * Hàm hỗ trợ đăng ký nhanh một Endpoint.
     */
    protected function register_endpoint($route, $method, $callback, $args = [])
    {
        register_rest_route($this->namespace, $route, [
            array_merge([
                'methods'             => $method,
                'callback'            => [$this, $callback],
                'permission_callback' => [$this, 'check_permission'],
            ], $args)
        ]);
    }

    /**
     * Bắt buộc các lớp con phải tự định nghĩa các Route của riêng mình.
     */
    // abstract public function register_routes();
}
