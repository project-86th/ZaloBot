import { StatusBadge } from "./Styled";

export const Header = ({ isEnabled }) => (
    <div style={{ marginBottom: "20px" }}>
        <h1 style={{ display: "inline-flex", alignItems: "center" }}>
            Zalo Bot Configuration
            <StatusBadge active={isEnabled}>
                {isEnabled ? "Đang hoạt động" : "Tạm dừng"}
            </StatusBadge>
        </h1>
        <p className="description">
            Cấu hình kết nối giữa Website của bạn và Zalo Official Account.
        </p>
        <hr />
    </div>
);
