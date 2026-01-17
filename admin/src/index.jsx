import { useState } from "@wordpress/element";
import { Panel, SnackbarList, Button } from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import { AdminContainer } from "./Components/Styled";
import { Header } from "./Components/Header";
import { StatusPanel } from "./components/StatusPanel";
import { WebhookInfo } from "./Components/WebhookInfo";

/**
 * Khai báo ZaloBotApp
 */
const Inanh86ZaloBotApp = () => {
    const [status, setStatus] = useState(window.zaloBotData.status === "on");
    const [apiKey, setApiKey] = useState(window.zaloBotData.api_key || "");
    const [isSaving, setIsSaving] = useState(false);
    const [notices, setNotices] = useState([]);

    const handleSave = async () => {
        setIsSaving(true);
        try {
            await apiFetch({
                path: "/zalo-bot/v1/settings",
                method: "POST",
                data: { status: status ? "on" : "off", api_key: apiKey },
            });
            setNotices([
                {
                    id: Date.now(),
                    content: "Lưu thành công!",
                    status: "success",
                },
            ]);
        } catch (err) {
            setNotices([
                { id: Date.now(), content: "Lỗi API", status: "error" },
            ]);
        } finally {
            setIsSaving(false);
        }
    };

    return (
        <AdminContainer>
            <Header isEnabled={status} />
            <SnackbarList notices={notices} onRemove={(id) => setNotices([])} />

            <Panel>
                <StatusPanel
                    status={status}
                    setStatus={setStatus}
                    apiKey={apiKey}
                    setApiKey={setApiKey}
                />
                <WebhookInfo webhookToken={window.zaloBotData.webhook_token} />
            </Panel>

            <div style={{ marginTop: "20px" }}>
                <Button
                    variant="primary"
                    isBusy={isSaving}
                    onClick={handleSave}
                >
                    Cập nhật thay đổi
                </Button>
            </div>
        </AdminContainer>
    );
};

// Render App vào div có id đã tạo ở PHP
render(<Inanh86ZaloBotApp />, document.getElementById("zalo-bot-admin-app"));
