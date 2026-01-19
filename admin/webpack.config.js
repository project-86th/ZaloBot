/**
 * Import cấu hình mặc định từ gói @wordpress/scripts
 */
const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require("path");

module.exports = {
    ...defaultConfig, // Kế thừa toàn bộ cấu hình mặc định
    // mode: "production", // <-- Chế độ Dev | Prod

    // Cấu hình đầu vào và đầu ra
    entry: {
        index: path.resolve(process.cwd(), "src", "index.jsx"), // File nguồn React
    },
    output: {
        // Nhảy ra khỏi thư mục admin/ và đi vào dist/build
        path: path.resolve(process.cwd(), "..", "dist", "build"),
        filename: "[name].js",
        // Đảm bảo xóa sạch folder build cũ trước khi tạo bản mới
        clean: true,
    },

    // Cấu hình Alias để viết code ngắn gọn hơn (Tùy chọn)
    resolve: {
        ...defaultConfig.resolve,
        alias: {
            ...defaultConfig.resolve.alias,
            "@components": path.resolve(process.cwd(), "src/components"),
        },
        extensions: [".js", ".jsx", ".json"],
    },

    module: {
        rules: [
            {
                test: /\.(js|jsx)$/, // Đảm bảo có đuôi .jsx ở đây
                exclude: /node_modules/,
                use: {
                    loader: "babel-loader",
                    options: {
                        presets: ["@wordpress/babel-preset-default"],
                    },
                },
            },
        ],
    },

    // Plugins bổ sung (nếu cần)
    plugins: [...defaultConfig.plugins],
};
