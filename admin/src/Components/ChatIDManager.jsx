import { useState, useEffect } from "@wordpress/element";
import {
    Card,
    CardBody,
    CardHeader,
    Button,
    Tooltip,
    Notice,
    TextControl,
    Modal,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const ChatIDManager = () => {
    const [clients, setClients] = useState([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isSending, setIsSending] = useState(false);
    const [notice, setNotice] = useState(null);
    // Thêm 1 state để lưu từ khóa tìm kiếm
    const [searchTerm, setSearchTerm] = useState("");

    // Trạng thái cho Modal gửi tin nhắn nhanh
    const [selectedClient, setSelectedClient] = useState(null);
    const [demoMsg, setDemoMsg] = useState(
        "Chào bạn, đây là tin nhắn thử từ hệ thống!",
    );

    // 1. Lấy danh sách khách hàng
    const fetchClients = async () => {
        setIsLoading(true);
        try {
            const response = await apiFetch({ path: "zalo-bot/v1/clients" });

            // KIỂM TRA Ở ĐÂY: response bây giờ là một Object { success, data, total, page }
            if (response.success && Array.isArray(response.data)) {
                setClients(response.data); // Gán mảng nằm trong key 'data'
            } else {
                setClients([]);
            }
        } catch (error) {
            setNotice({
                type: "error",
                message: "Không thể tải danh sách khách hàng.",
            });
            setClients([]);
        }
        setIsLoading(false);
    };

    useEffect(() => {
        fetchClients();
        console.log(clients);
    }, []);

    // 2. Hàm gửi tin nhắn demo
    const sendDemo = async () => {
        if (!selectedClient) return;
        setIsSending(true);
        try {
            await apiFetch({
                path: "zalo-bot/v1/clients/send-demo", // Endpoint ní sẽ tạo trong SettingsController
                method: "POST",
                data: {
                    chat_id: selectedClient.zalo_user_id,
                    message: demoMsg,
                },
            });
            setNotice({
                type: "success",
                message: `Đã gửi tin nhắn tới ${selectedClient.display_name}`,
            });
            setSelectedClient(null); // Đóng modal
        } catch (error) {
            setNotice({ type: "error", message: "Gửi tin nhắn thất bại." });
        }
        setIsSending(false);
    };

    // Lọc danh sách dựa trên từ khóa (DisplayName hoặc ZaloID)
    const filteredClients = clients.filter(
        (client) =>
            client.display_name
                ?.toLowerCase()
                .includes(searchTerm.toLowerCase()) ||
            client.zalo_user_id?.includes(searchTerm),
    );

    return (
        <div className="zalo-bot-chat-manager">
            {notice && (
                <Notice status={notice.type} onDismiss={() => setNotice(null)}>
                    {notice.message}
                </Notice>
            )}

            <Card>
                <CardHeader>
                    <h2 style={{ margin: 0 }}>
                        Danh sách khách hàng (Chat ID)
                    </h2>

                    {/* Thanh tìm kiếm nhanh */}
                    <div style={{ width: "300px" }}>
                        <TextControl
                            placeholder="Tìm tên hoặc Zalo ID..."
                            value={searchTerm}
                            onChange={(val) => setSearchTerm(val)}
                            style={{ marginBottom: 0 }}
                        />
                    </div>

                    {/* Nút làm mới bản */}
                    <Button
                        variant="secondary"
                        onClick={fetchClients}
                        isBusy={isLoading}
                    >
                        Làm mới
                    </Button>
                </CardHeader>
                <CardBody>
                    <table className="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Tên khách hàng</th>
                                <th>Zalo ID</th>
                                <th>Tin nhắn cuối</th>
                                <th>Hoạt động</th>
                                <th style={{ width: "80px" }}>Gửi thử</th>
                            </tr>
                        </thead>
                        <tbody>
                            {filteredClients.length > 0 ? (
                                filteredClients.map((item) => (
                                    <tr key={item.id}>
                                        <td>
                                            <strong>{item.display_name}</strong>
                                        </td>
                                        <td>
                                            <code>{item.zalo_user_id}</code>
                                        </td>
                                        <td>{item.last_message}</td>
                                        <td>{item.last_active}</td>
                                        <td>
                                            <Button
                                                icon="paper-plane"
                                                onClick={() =>
                                                    setSelectedClient(item)
                                                }
                                            />
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td
                                        colSpan="5"
                                        style={{
                                            textAlign: "center",
                                            padding: "20px",
                                        }}
                                    >
                                        {searchTerm
                                            ? "Không tìm thấy kết quả nào!"
                                            : "Đang chờ khách hàng nhắn tin..."}
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </CardBody>
            </Card>

            {/* Modal Gửi tin nhắn nhanh */}
            {selectedClient && (
                <Modal
                    title={`Gửi tin nhắn tới: ${selectedClient.display_name}`}
                    onRequestClose={() => setSelectedClient(null)}
                >
                    <TextControl
                        label="Nội dung tin nhắn"
                        value={demoMsg}
                        onChange={(value) => setDemoMsg(value)}
                    />
                    <div
                        style={{
                            display: "flex",
                            justifyContent: "flex-end",
                            gap: "10px",
                        }}
                    >
                        <Button
                            variant="tertiary"
                            onClick={() => setSelectedClient(null)}
                        >
                            Hủy
                        </Button>
                        <Button
                            variant="primary"
                            onClick={sendDemo}
                            isBusy={isSending}
                        >
                            Gửi ngay
                        </Button>
                    </div>
                </Modal>
            )}
        </div>
    );
};

export default ChatIDManager;
