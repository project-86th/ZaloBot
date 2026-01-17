import styled from "@emotion/styled";

export const AdminContainer = styled.div`
    max-width: 900px;
    margin: 20px 20px 0 0;
    padding: 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.04);
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
