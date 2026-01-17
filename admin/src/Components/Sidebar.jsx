import langZaloBot from "../lang/translations";
import { SidebarNav, NavItem } from "./Styled";
/**
 * Component điều hướng Sidebar
 * @param {string} activeTab - Tab hiện tại đang được chọn
 * @param {Function} onTabChange - Hàm callback khi người dùng nhấn chuyển Tab
 */
export const SidebarBar = ({ activeTab, onTabChange }) => {
    // danh sách menu
    const menuItems = [
        { id: "general", label: langZaloBot[6], icon: "admin-generic" },
        { id: "clients", label: langZaloBot[7], icon: "groups" },
        { id: "dev-log", label: langZaloBot[8], icon: "media-text" },
    ];

    return (
        <SidebarNav>
            {menuItems.map((item) => (
                <NavItem
                    key={item.id}
                    active={activeTab === item.id}
                    onClick={() => onTabChange(item.id)}
                >
                    <span className={`dashicons dashicons-${item.icon}`}></span>
                    {item.label}
                </NavItem>
            ))}
        </SidebarNav>
    );
};
