# Rà soát phông chữ và tiếng Việt

Ngày kiểm tra: 03/11/2026. Môi trường: ứng dụng local tại `http://127.0.0.1:8001`, trình duyệt trong ứng dụng Codex trên Windows.

## Kết quả và thay đổi

- Không phát hiện thêm chữ sai mã UTF-8 hoặc ký tự thay thế `�` trong 100 tệp thuộc `app`, `resources`, `routes`, `database/seeders`, `lang` và các trường văn bản của bài viết, bộ câu hỏi, câu hỏi, lựa chọn, tên người dùng hiện có.
- Khai báo chung `--font-ui` với Segoe UI, Arial, sans-serif; cả CSS giao diện và tiện ích `font-sans` của Tailwind dùng cùng cấu hình. Loại bỏ cấu hình Figtree còn sót trong Tailwind. Phần bài đọc, tiêu đề và thanh bên kế thừa phông này.
- Bổ sung bản dịch có dấu cho xác minh email, trạng thái đặt lại mật khẩu, lỗi mật khẩu hiện tại và phân trang. Các chuỗi dịch trong giao diện hiện không còn thiếu mục trong `lang/vi.json`, kể cả mẫu Breeze chưa được dùng bởi tuyến hiện tại.
- Đổi nhãn trạng thái Gemini trên trang tổng quan từ `success`/`failed` thành “Thành công”/“Thất bại”.

## Phạm vi kiểm tra

Đã mở và kiểm tra phông chữ thực tế bằng trình duyệt trên 30 trang/trạng thái dưới đây, đồng thời kiểm tra phản hồi HTTP với các phiên khách, admin và user riêng biệt. Tất cả vùng chữ, tiêu đề, liên kết, nhãn, biểu mẫu và bảng dữ liệu được kiểm tra đều dùng `"Segoe UI", Arial, sans-serif`, không phát hiện ký tự sai mã.

| Nhóm | Trang/trạng thái |
| --- | --- |
| Công khai, tài khoản | Trang chủ; đăng nhập; đăng ký; quên mật khẩu; biểu mẫu đặt lại mật khẩu; xác nhận mật khẩu; xác minh email |
| Người học | Thư viện có dữ liệu và không có kết quả; bài đọc Minecraft; lượt làm bài đang có; kết quả Minecraft; lịch sử; bảng xếp hạng có dữ liệu và chưa có lượt làm; hồ sơ user |
| Quản trị | Tổng quan; danh sách, tạo và sửa bài viết; danh sách, tạo và sửa bộ câu hỏi ở trạng thái nháp/đã xuất bản; thêm và sửa câu hỏi; Wikipedia ban đầu và xem trước Minecraft; Gemini cùng nhật ký; hồ sơ admin |

Kiểm tra bổ sung khi render: đoạn giới thiệu, nút gửi lại và thông báo xác minh email; nút trước/sau và tóm tắt phân trang; trạng thái đặt lại mật khẩu và lỗi mật khẩu hiện tại. Tất cả đều hiển thị tiếng Việt đúng dấu.

Không gửi email xác minh/đặt lại mật khẩu, không gọi sinh Gemini, không lưu biểu mẫu nội dung, không nộp lượt làm đang có. Phiên đăng nhập trình duyệt được trả về admin sau kiểm tra.

## Xác nhận kỹ thuật

- `npm run build`: thành công.
- `php artisan test`: 52 kiểm thử, 227 assertions, đều thành công trên cơ sở dữ liệu kiểm thử riêng.
- `git diff --check`: không có lỗi khoảng trắng.
- Biên bản chi tiết và ảnh kiểm tra local ở `storage/app/qa/font-audit-*` (thư mục không đưa lên Git).

Kết quả xác nhận trên môi trường Windows và trình duyệt nêu trên; các hệ điều hành khác dùng phông dự phòng trong cùng cấu hình.
