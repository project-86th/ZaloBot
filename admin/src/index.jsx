import { useState, createRoot } from "@wordpress/element";
import { Panel, SnackbarList, Button } from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";
import { AdminContainer, ContentArea, LayoutBody } from "./Components/Styled";
import { Header } from "./Components/Header";
import { StatusPanel } from "./components/StatusPanel";
import { WebhookInfo } from "./Components/WebhookInfo";
import Wizard from "./Components/Wizard";
import { SidebarBar } from "./Components/Sidebar";
import langZaloBot from "./lang/translations";
import ChatIDManager from "./Components/ChatIDManager";

/**
 * Khai báo ZaloBotApp
 */
const Inanh86ZaloBotApp = () => {
    // Quản lý trạng thái Tab
    const [activeTab, setActiveTab] = useState("general");

    // Nếu chưa có API Key thì mặc định coi là lần đầu chạy
    const [showWizard, setShowWizard] = useState(!window.zaloBotData.api_key);
    const [status, setStatus] = useState(window.zaloBotData.status === "on");
    const [apiKey, setApiKey] = useState(window.zaloBotData.api_key || "");
    const [isSaving, setIsSaving] = useState(false);
    const [notices, setNotices] = useState([]);

    // Gọi API lưu cài đặt
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
                    content: langZaloBot[13],
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

    // Hàm gọi khi nhấn nút kết thúc ở Wizard
    const completeOnboarding = async () => {
        // Loại bỏ khoảng trắng thừa trước khi xử lý
        const cleanApiKey = apiKey.trim();

        // Lưu lại token ngay khi hoàn thành wizard
        await apiFetch({
            path: "/zalo-bot/v1/settings",
            method: "POST",
            data: { status: "on", api_key: cleanApiKey },
        });
        setStatus(true);
        setShowWizard(false);
    };

    if (showWizard) {
        return (
            <Wizard
                apiKey={apiKey}
                setApiKey={setApiKey}
                onComplete={completeOnboarding}
            />
        );
    }

    // --- Giao diện Dashboard chính với Sidebar ---
    return (
        <AdminContainer>
            <Header isEnabled={status} />
            {/* Thông báo */}
            <SnackbarList notices={notices} onRemove={() => setNotices([])} />

            <LayoutBody>
                {/* 2. Sidebar bên trái */}
                <SidebarBar
                    activeTab={activeTab}
                    onTabChange={(id) => setActiveTab(id)}
                />

                {/* 3. Vùng nội dung bên phải thay đổi theo Tab */}
                <ContentArea>
                    {activeTab === "general" && (
                        <div className="tab-general">
                            <h2 style={{ marginTop: 0 }}>{langZaloBot[12]}</h2>
                            <Panel>
                                <StatusPanel
                                    status={status}
                                    setStatus={setStatus}
                                    apiKey={apiKey}
                                    setApiKey={setApiKey}
                                />
                                <WebhookInfo
                                    webhookToken={
                                        window.zaloBotData.webhook_token
                                    }
                                />
                            </Panel>

                            <div style={{ marginTop: "20px" }}>
                                <Button
                                    variant="primary"
                                    isBusy={isSaving}
                                    onClick={handleSave}
                                    __next40pxDefaultSize
                                >
                                    {langZaloBot[11]}
                                </Button>
                            </div>
                        </div>
                    )}

                    {activeTab === "clients" && (
                        <div className="tab-clients">
                            <h2 style={{ marginTop: 0 }}>
                                Quản lý Danh sách Chat ID
                            </h2>
                            {/* Lấy danh sách Chat_ID(Đã follow bot của ta) */}
                            <ChatIDManager />
                        </div>
                    )}

                    {activeTab === "dev-log" && (
                        <div className="tab-dev-log">
                            <h2 style={{ marginTop: 0 }}>Nhật ký phát triển</h2>
                            <p>Theo dõi quá trình cập nhật Plugin của bạn.</p>
                            <div
                                style={{
                                    padding: "15px",
                                    background: "#f0f0f1",
                                }}
                            >
                                <strong>v1.0.2:</strong> Đã tích hợp Sidebar và
                                Layout mới.
                            </div>
                        </div>
                    )}
                </ContentArea>
            </LayoutBody>
        </AdminContainer>
    );
};

// Render App vào div có id đã tạo ở PHP
const container = document.getElementById("zalo-bot-admin-app");
if (container) {
    const root = createRoot(container);
    root.render(<Inanh86ZaloBotApp />);
}
