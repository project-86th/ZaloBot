/*
 *
 *  This file contains all WordPress i18n calls in the plugin javaScript.
 *  It exists solely so WordPress translation tools can detect strings.
 */
import { __, _n, sprintf } from "@wordpress/i18n";

const langZaloBot = [
    __("Cấu hình ZaloBot", "inanh86-zalo-bot"), // <-- [0]
    __(
        "Cấu hình kết nối giữa Wordpress của bạn và Zalo Official Account.",
        "inanh86-zalo-bot",
    ), // <--[1]
    __("Đang hoạt động", "inanh86-zalo-bot"), // <-- [2]
    __("Tạm dừng", "inanh86-zalo-bot"), // <-- [3]
    __("Sử dụng URL dưới đây để cấu hình trong", "inanh86-zalo-bot"), // <-- [4]
    __("Zalo Developer Portal", "inanh86-zalo-bot"), // <-- [5]
    __("Quản lý chung", "inanh86-zalo-bot"), // <-- [6]
    __("Danh sách chat_id", "inanh86-zalo-bot"), // <-- [7]
    __("Log phát triển", "inanh86-zalo-bot"), // <-- [8]
    __(
        "* Lưu ý: Token đính kèm trên URL giúp Bot nhận diện yêu cầu hợp lệ. Không chia sẻ URL này cho người lạ.",
        "inanh86-zalo-bot",
    ), // <-- [9]
    __("Vui lòng cấu hình Webhook Token trước.", "inanh86-zalo-bot"), // <-- [10]
    __("Cập nhật cài đặt", "inanh86-zalo-bot"), // <-- [11]
    __("Cấu hình hệ thống", "inanh86-zalo-bot"), // <-- [12]
    __("Lưu thành công!", "inanh86-zalo-bot"), // <-- [13]
    __("Chào mừng bạn đến với Zalo Bot", "inanh86-zalo-bot"), // <-- [14]
    __(
        " Hãy cùng thiết lập kết nối với ZaloBot của bạn chỉ trong vài phút.",
        "inanh86-zalo-bot",
    ), // <-- [15]
    __("Tiếp theo", "inanh86-zalo-bot"), // <-- [16]
    __("Zalo Access Token", "inanh86-zalo-bot"), // <-- [17]
    __("Nhập token tại đây...", "inanh86-zalo-bot"), // <-- [18]
];
export default langZaloBot;
