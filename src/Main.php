<?php

namespace Inanh86\ZaloBot;

use Inanh86\ZaloBot\Admin\SettingsPage;
use Inanh86\ZaloBot\Database\ClientRepository;

defined('ABSPATH') || exit;

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
        $this->init_admin();
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
     * Nạp các hook quan trọng
     */
    private function init_hooks()
    {
        // Khai báo Rest API 
        add_action('rest_api_init', [$this, 'init_rest_api']);
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
     * Đăng ký các Hook dùng chung cho cả Frontend và Backend
     */
    public function init_rest_api()
    {
        $dir = ZALO_BOT_DIR . 'src/Api';
        $files = glob($dir . '/*.php');

        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            $class_name = pathinfo($file, PATHINFO_FILENAME);
            $full_class_name = "\\Inanh86\\ZaloBot\\Api\\" . $class_name;

            // Kiểm tra class tồn tại trước khi dùng Reflection
            if (class_exists($full_class_name)) {
                try {
                    $reflection = new \ReflectionClass($full_class_name);

                    // CHỈ KHỞI TẠO NẾU:
                    // 1. Class không phải là abstract (bỏ qua BaseController và Interface)
                    // 2. Class có thực thi RouteInterface
                    if (!$reflection->isAbstract() && $reflection->implementsInterface("\\Inanh86\\ZaloBot\\Api\\RouteInterface")) {
                        $controller = $reflection->newInstance();
                        $controller->register_routes();
                    }
                } catch (\ReflectionException $e) {
                    // Nếu có lỗi trong quá trình quét, log lại để debug
                    if (class_exists('Inanh86\\ZaloBot\\Utils\\Logger')) {
                        \Inanh86\ZaloBot\Utils\Logger::debug("Lỗi quét API: " . $e->getMessage());
                    }
                    continue;
                }
            }
        }
    }
}
