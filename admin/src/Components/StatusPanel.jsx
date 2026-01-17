import {
    PanelBody,
    PanelRow,
    ToggleControl,
    TextControl,
} from "@wordpress/components";

/**
 * Component hiển thị bảng điều khiển trạng thái hoạt động và nhập Token.
 * @param {Object} props
 * @param {boolean} props.status - Trạng thái bật/tắt của Bot.
 * @param {Function} props.setStatus - Hàm cập nhật trạng thái Bot.
 * @param {string} props.apiKey - Giá trị Access Token hiện tại.
 * @param {Function} props.setApiKey - Hàm cập nhật Access Token.
 */
export const StatusPanel = ({ status, setStatus, apiKey, setApiKey }) => (
    <PanelBody title="Trạng thái & Bảo mật" initialOpen={true}>
        <PanelRow>
            <ToggleControl
                label="Kích hoạt Bot"
                help={
                    status
                        ? "Bot đang sẵn sàng nhận tin."
                        : "Bot đang tạm dừng."
                }
                checked={status}
                onChange={() => setStatus(!status)}
                // Bật chế độ không margin-bottom để tuân thủ chuẩn WP 7.0+
                __nextHasNoMarginBottom
            />
        </PanelRow>

        <div style={{ marginTop: "10px" }}></div>

        <TextControl
            label="Zalo Access Token"
            value={apiKey}
            onChange={(value) => setApiKey(value)}
            placeholder="Nhập Access Token từ Zalo OA..."
            // Sử dụng kích thước chuẩn mới (40px) của Gutenberg
            __next40pxDefaultSize
            // Xóa margin mặc định để dễ dàng kiểm soát layout bằng CSS/Wrapper
            __nextHasNoMarginBottom
        />
    </PanelBody>
);
