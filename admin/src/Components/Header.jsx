import langZaloBot from "../lang/translations";
import { HeaderWrapper, StatusBadge, Title } from "./Styled";
import { Icon } from "@wordpress/components";

export const Header = ({ isEnabled }) => (
    <HeaderWrapper>
        <h1 style={{ display: "inline-flex", alignItems: "center" }}>
            <Icon
                icon="format-chat"
                style={{ fontSize: "2rem", marginRight: "1rem" }}
            />
            <Title>{langZaloBot[0]} (Beta)</Title>
            <StatusBadge active={isEnabled}>
                {isEnabled ? langZaloBot[2] : langZaloBot[3]}
            </StatusBadge>
        </h1>
        <p className="description">{langZaloBot[1]}</p>
        <hr />
    </HeaderWrapper>
);
