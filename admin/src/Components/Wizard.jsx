import { useState } from "@wordpress/element";
import { Button, Dashicon, TextControl } from "@wordpress/components";
import styled from "@emotion/styled";
import { AdminContainer } from "./Styled";

const StepWrapper = styled.div`
    padding: 40px;
    text-align: center;
`;

const StepIcon = styled.div`
    font-size: 40px;
    margin-bottom: 20px;
    color: #2271b1;
`;

export const Wizard = ({ onComplete, apiKey, setApiKey }) => {
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
                        <h2>Chào mừng bạn đến với Zalo Bot</h2>
                        <p>
                            Hãy cùng thiết lập kết nối với Zalo OA của bạn chỉ
                            trong vài phút.
                        </p>
                        <Button variant="primary" isLarge onClick={nextStep}>
                            Bắt đầu ngay
                        </Button>
                    </StepWrapper>
                )}

                {currentStep === 2 && (
                    <StepWrapper>
                        <h2>Bước 1: Lấy Access Token</h2>
                        <p>
                            Truy cập <strong>Zalo Developer Portal</strong>, tạo
                            ứng dụng và lấy Access Token.
                        </p>
                        <ul
                            style={{
                                textAlign: "left",
                                display: "inline-block",
                            }}
                        >
                            <li>1. Tạo App trên Zalo Developers</li>
                            <li>2. Thêm quyền cho Official Account</li>
                            <li>3. Copy Access Token dán vào dưới đây</li>
                        </ul>
                        <TextControl
                            label="Zalo Access Token"
                            value={apiKey}
                            onChange={setApiKey}
                            placeholder="Nhập token tại đây..."
                        />
                        <Button
                            variant="primary"
                            disabled={!apiKey}
                            onClick={nextStep}
                        >
                            Tiếp theo
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
