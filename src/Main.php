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
        // Đường dẫn tới thư mục Rest
        $dir = ZALO_BOT_DIR . 'src/Api';

        // Quét tất cả các file .php trong thư mục
        $files = glob($dir . '/*.php');

        if (empty($files)) {
            return;
        }

        foreach ($files as $file) {
            // Lấy tên class từ tên file (ví dụ: WebhookController)
            $class_name = pathinfo($file, PATHINFO_FILENAME);

            // Ghép namespace đầy đủ
            $full_class_name = "\\Inanh86\\ZaloBot\\Api\\" . $class_name;

            // Kiểm tra nếu class tồn tại và có phương thức register_routes
            if (class_exists($full_class_name)) {
                $controller = new $full_class_name();

                if (method_exists($controller, 'register_routes')) {
                    $controller->register_routes();
                }
            }
        }
    }
}
