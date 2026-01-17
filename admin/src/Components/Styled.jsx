import styled from "@emotion/styled";

// --- layout --- //
export const LayoutBody = styled.div`
    display: flex;
    min-height: 600px;
    background: #fff;
    margin-top: 0;
    overflow: hidden; /* Để sidebar không bị lòi góc bo */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    min-height: calc(100vh - 150px); /* Thân dashboard cao gần hết màn hình */
`;

export const ContentArea = styled.div`
    flex: 1;
    padding: 25px;
    background: #ffffff;

    h2 {
        font-size: 21px;
        font-weight: 600;
        margin-bottom: 24px;
        color: #1d2327;
    }
`;

export const AdminContainer = styled.div`
    margin-left: 0px;
    padding-right: 10px;
    margin-top: 10px;
    background: #f0f0f1;
`;

export const HeaderWrapper = styled.div`
    background: #fff;
    padding: 20px 20px 0px;
    border-bottom: 1px solid #dcdcde;
    display: flex;
    justify-content: flex-end;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;

    h1 {
        margin: 0;
        font-size: 24px;
        font-weight: 600;
    }
    p {
        margin-bottom: 0px;
    }
`;

export const CodeBlock = styled.code`
    display: block;
    padding: 12px;
    background: #f0f0f1;
    border-left: 4px solid #2271b1;
    margin-top: 10px;
    color: #1d2327;
    font-family: monospace;
    word-break: break-all;
`;

export const StatusBadge = styled.span`
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    background: ${(props) => (props.active ? "#e7f6ed" : "#fcf0f1")};
    color: ${(props) => (props.active ? "#207b4d" : "#d63638")};
    margin-left: 10px;
`;

export const Title = styled.span`
    margin-left: 10px;
`;

// --- Sidebar Styled Component --- //

export const SidebarNav = styled.div`
    width: 200px;
    background: #f0f0f1;
    border-right: 1px solid #c3c4c7;
`;
export const NavItem = styled.div`
    padding: 18px 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    font-size: 14px;
    transition: all 0.3s ease;
    background: ${(props) => (props.active ? "#ffffff" : "transparent")};
    color: ${(props) => (props.active ? "#2271b1" : "#50575e")};
    border-left: 4px solid
        ${(props) => (props.active ? "#2271b1" : "transparent")};
    font-weight: ${(props) => (props.active ? "600" : "500")};

    &:hover {
        background: #f6f7f7;
        color: #2271b1;
    }

    .dashicons {
        margin-right: 12px;
        filter: ${(props) => (props.active ? "none" : "grayscale(1)")};
        opacity: ${(props) => (props.active ? "1" : "0.7")};
    }
`;
