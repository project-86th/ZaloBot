import {
    PanelBody,
    PanelRow,
    ToggleControl,
    TextControl,
} from "@wordpress/components";

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
            />
        </PanelRow>

        <TextControl
            label="Zalo Access Token"
            value={apiKey}
            onChange={(value) => setApiKey(value)}
            placeholder="Nhập Access Token từ Zalo OA..."
        />
    </PanelBody>
);
