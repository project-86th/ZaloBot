<?php

namespace Inanh86\ZaloBot\Services;

defined('ABSPATH') || exit;

class ZaloApiService
{

    protected $base_url = "https://bot-api.zaloplatforms.com";

    /**
     * Đồng bộ Webhook lên Zalo
     */
    public function set_webhook($bot_token, $webhook_url, $secret_token)
    {
        $api_url = "{$this->base_url}/bot{$bot_token}/setWebhook";

        $response = wp_remote_post($api_url, [
            'headers' => ['Content-Type' => 'application/json'],
            'body'    => json_encode([
                'url'          => $webhook_url,
                'secret_token' => $secret_token
            ]),
            'timeout' => 20,
        ]);

        return $this->handle_response($response);
    }

    /**
     * Gửi tin nhắn văn bản tới người dùng qua Zalo Bot API
     * * @param string $bot_token Token của Bot (lấy từ cài đặt)
     * @param string $chat_id   ID người nhận (lấy từ database hoặc webhook)
     * @param string $text      Nội dung tin nhắn (tối đa 2000 ký tự)
     */
    public function send_message($bot_token, $chat_id, $text)
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

    private function handle_response($response)
    {
        if (is_wp_error($response)) {
            return ['success' => false, 'message' => $response->get_error_message()];
        }
        $body = json_decode(wp_remote_retrieve_body($response), true);
        return [
            'success' => (isset($body['status']) && $body['status'] === 200),
            'data'    => $body
        ];
    }
}
