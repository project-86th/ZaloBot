<?php

namespace Inanh86\ZaloBot\Utils;

/**
 * Class Logger
 * Xử lý ghi log nội bộ cho Plugin vào thư mục /dist/logs/
 */
class Logger
{

    /**
     * Ghi nội dung vào file log
     * @param string $message Nội dung cần ghi
     * @param string $level Mức độ log (INFO, ERROR, DEBUG)
     * @return void 
     */
    public static function log($message, $level = 'INFO'): void
    {
        $log_dir = ZALO_BOT_DIR . 'dist/logs';
        $log_file = $log_dir . '/zalo-bot-' . date('Y-m-d') . '.log';

        // 1. Tạo thư mục nếu chưa tồn tại
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0755, true);
            // Tạo file index.php trống để bảo mật thư mục
            file_put_contents($log_dir . '/index.php', '');
            // Tạo .htaccess để chặn truy cập trực tiếp từ trình duyệt
            file_put_contents($log_dir . '/.htaccess', 'Deny from all');
        }

        // 2. Định dạng nội dung log
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }

        $entry = sprintf(
            "[%s] [%s]: %s" . PHP_EOL,
            date('H:i:s'),
            $level,
            $message
        );

        // 3. Ghi vào file (chế độ append)
        error_log($entry, 3, $log_file);
    }

    /**
     * Các phương thức tắt để ghi log nhanh
     */
    public static function info($msg): void
    {
        self::log($msg, 'INFO');
    }
    public static function error($msg): void
    {
        self::log($msg, 'ERROR');
    }
    public static function debug($msg): void
    {
        self::log($msg, 'DEBUG');
    }
}
