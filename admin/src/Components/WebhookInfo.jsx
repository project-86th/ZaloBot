import { PanelBody } from "@wordpress/components";
import { CodeBlock } from "./Styled";

export const WebhookInfo = ({ webhookToken }) => {
    const webhookUrl = `${window.location.origin}/wp-json/zalo-bot/v1/webhook?token=${webhookToken}`;

    return (
        <PanelBody title="Thông tin Webhook" initialOpen={false}>
            <p>
                Sử dụng URL dưới đây để cấu hình trong{" "}
                <strong>Zalo Developer Portal</strong>:
            </p>
            <CodeBlock>{webhookUrl}</CodeBlock>
            <p style={{ fontSize: "11px", color: "#666", marginTop: "10px" }}>
                * Lưu ý: Không chia sẻ URL này cho người lạ.
            </p>
        </PanelBody>
    );
};
