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
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
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
     * Nhúng file JS đã build từ React
     */
    public function enqueue_assets(string $hook): void
    {
        if ($hook !== 'toplevel_page_zalo-bot-settings') {
            return;
        }

        $asset_file = ZALO_BOT_DIR . 'admin/build/index.asset.php';
        if (!file_exists($asset_file)) {
            return;
        }

        $assets = require $asset_file;

        wp_enqueue_script(
            'zalo-bot-admin',
            ZALO_BOT_URL . 'admin/build/index.js',
            $assets['dependencies'],
            $assets['version'],
            true
        );

        // Truyền dữ liệu sang React
        wp_localize_script('zalo-bot-admin', 'zaloBotData', [
            'status' => get_option('zalo_bot_status', 'off'),
            'api_key' => get_option('zalo_bot_access_token', ''),
            'nonce'  => wp_create_nonce('wp_rest')
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
