import { useState } from "@wordpress/element";
import {
    Button,
    Dashicon,
    ExternalLink,
    TextControl,
} from "@wordpress/components";
import styled from "@emotion/styled";
import { AdminContainer } from "./Styled";
import langZaloBot from "../lang/translations";

const StepWrapper = styled.div`
    padding: 40px;
    text-align: center;
`;

const StepIcon = styled.div`
    font-size: 40px;
    margin-bottom: 20px;
    color: #2271b1;
`;

/**
 * component Wizard
 * * Chạy ngay khi Token Zalo chưa tồn tại
 *
 */
const Wizard = ({ onComplete, apiKey, setApiKey }) => {
    const [currentStep, setCurrentStep] = useState(1);
    const nextStep = () => setCurrentStep(currentStep + 1);

    return (
        <StepWrapper>
            <AdminContainer>
                {currentStep === 1 && (
                    <StepWrapper>
                        <StepIcon>
                            <Dashicon icon="welcome-learn-more" size={48} />
                        </StepIcon>
                        <h2>{langZaloBot[14]}</h2>
                        <p>{langZaloBot[15]}</p>
                        <Button variant="primary" isLarge onClick={nextStep}>
                            Bắt đầu ngay
                        </Button>
                    </StepWrapper>
                )}

                {currentStep === 2 && (
                    <StepWrapper>
                        <h2>Bước 1: Tạo Zalo Bot</h2>
                        <p>
                            <span>Truy cập</span>
                            <strong style={{ marginLeft: "5px" }}>
                                <ExternalLink href="https://zalo.me/s/botcreator/">
                                    Zalo Botcreator
                                </ExternalLink>
                            </strong>
                            <span>, tạo bot và lấy token.</span>
                        </p>
                        <ul
                            style={{
                                textAlign: "left",
                                display: "inline-block",
                            }}
                        >
                            <li>
                                1. Tạo App trên Zalo Bot Creator( Giao diện tạo
                                và quản lý Bot trên Mobile, cho phép quản lý
                                thông tin, cấu hình Bot trực tiếp trên Zalo.)
                            </li>
                            <li>2. Copy Access Token dán vào dưới đây</li>
                        </ul>
                        <TextControl
                            __next40pxDefaultSize={true}
                            label={langZaloBot[17]}
                            value={apiKey}
                            onChange={setApiKey}
                            placeholder={langZaloBot[18]}
                        />
                        <Button
                            variant="primary"
                            disabled={!apiKey}
                            onClick={nextStep}
                            __nextHasNoMarginBottom
                        >
                            {langZaloBot[16]}
                        </Button>
                    </StepWrapper>
                )}

                {currentStep === 3 && (
                    <StepWrapper>
                        <StepIcon>
                            <Dashicon
                                icon="yes-alt"
                                size={48}
                                style={{ color: "#46b450" }}
                            />
                        </StepIcon>
                        <h2>Tuyệt vời! Cấu hình hoàn tất</h2>
                        <p>
                            Hệ thống đã sẵn sàng. Bạn có thể bật Bot và bắt đầu
                            nhận tin nhắn.
                        </p>
                        <Button variant="primary" isLarge onClick={onComplete}>
                            Đi tới bảng điều khiển
                        </Button>
                    </StepWrapper>
                )}
            </AdminContainer>
        </StepWrapper>
    );
};

export default Wizard;
