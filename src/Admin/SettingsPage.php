<?php

namespace Inanh86\ZaloBot\Admin;

/**
 * Class SettingsPage
 * Khởi tạo trang cấu hình và nhúng React app
 */
class SettingsPage
{

    /**
     * Hàm khởi tạo - KHÔNG khai báo kiểu trả về ở đây
     */
    public function __construct()
    {
        add_action('admin_init', [$this, 'redirect_to_onboarding']);
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Chuyển hướng tới phần cài đặt (Wizard) plugins ngay khi actvie plugins 
     */
    public function redirect_to_onboarding(): void
    {
        if (get_option('zalo_bot_needs_onboarding')) {
            delete_option('zalo_bot_needs_onboarding'); // Chỉ chạy 1 lần
            wp_safe_redirect(admin_url('admin.php?page=zalo-bot-settings'));
            exit;
        }
    }

    /**
     * Thêm menu vào WordPress Admin
     */
    public function add_menu(): void
    {
        add_menu_page(
            'Zalo Bot Settings',
            'Zalo Bot',
            'manage_options',
            'zalo-bot-settings',
            [$this, 'render_page'],
            'dashicons-format-chat'
        );
    }


    /**
     * Nhúng file JS đã build từ React vào trang Admin
     */
    public function enqueue_assets(string $hook): void
    {
        // Kiểm tra đúng trang cấu hình của plugin mới nạp script
        if ($hook !== 'toplevel_page_zalo-bot-settings') {
            return;
        }

        // Đường dẫn vật lý và URL tới thư mục dist/build
        $build_dir = ZALO_BOT_DIR . 'dist/build/';
        $build_url = ZALO_BOT_URL . 'dist/build/';

        // File asset.php do wp-scripts tự sinh ra để quản lý dependencies (wp-element, wp-components,...)
        $asset_file = $build_dir . 'index.asset.php';

        if (!file_exists($asset_file)) {
            // Gợi ý: Nếu không thấy file, hãy kiểm tra xem đã chạy `npm run build` chưa
            return;
        }

        $assets = require $asset_file;

        // Nạp file JS chính
        wp_enqueue_script(
            'zalo-bot-admin',
            $build_url . 'index.js',
            $assets['dependencies'],
            $assets['version'],
            true
        );

        // Nạp CSS của WordPress Components (bắt buộc để React UI hiển thị đúng)
        wp_enqueue_style('wp-components');

        // Truyền dữ liệu từ Server (PHP) sang Client (React)
        wp_localize_script('zalo-bot-admin', 'zaloBotData', [
            'status'         => get_option('zalo_bot_status', 'off'),
            'api_key'        => get_option('zalo_bot_access_token', ''),
            'webhook_token'  => get_option('zalo_bot_webhook_token', ''),
            'nonce'          => wp_create_nonce('wp_rest') // Cần thiết để React gọi API bảo mật
        ]);
    }
    /**
     * Render khung chứa cho React
     */
    public function render_page(): void
    {
        echo '<div id="zalo-bot-admin-app"></div>';
    }
}
