import { PanelBody } from "@wordpress/components";
import { CodeBlock } from "./Styled";
import langZaloBot from "../lang/translations";

/**
 * Component hiển thị thông tin Webhook Endpoint.
 * * Component này cung cấp URL đầy đủ để người dùng copy và dán vào
 * cấu hình Webhook của Zalo OA tại trang Zalo Developer Portal.
 * @param {Object} props
 * @param {string} props.webhookToken - Token bảo mật dùng để định danh và xác thực yêu cầu từ Zalo.
 */
export const WebhookInfo = ({ webhookToken }) => {
    // Tạo URL Webhook đầy đủ dựa trên tên miền hiện tại của trang WordPress
    // Endpoint này sẽ được xử lý bởi WebhookController trong PHP
    const webhookUrl = `${window.location.origin}/wp-json/zalo-bot/v1/webhook?token=${webhookToken}`;

    return (
        <PanelBody title="Thông tin Webhook" initialOpen={true}>
            {/* Hiển thị URL trong CodeBlock để người dùng dễ dàng quan sát và sao chép */}
            {webhookToken ? (
                <CodeBlock>{webhookUrl}</CodeBlock>
            ) : (
                <p style={{ color: "red" }}>{langZaloBot[10]}</p>
            )}

            <p style={{ fontSize: "11px", color: "#666", marginTop: "10px" }}>
                {langZaloBot[9]}
            </p>
        </PanelBody>
    );
};
