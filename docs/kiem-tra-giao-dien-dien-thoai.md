# Kiểm tra giao diện điện thoại

Ngày: 03/11/2026. Kiểm tra bằng mô phỏng khung nhìn trong trình duyệt trên Windows, không phải thiết bị điện thoại thật.

## Lỗi phát hiện và sửa

- Ở chiều rộng 320 px, ô chọn chủ đề trên bảng xếp hạng vượt khỏi khung, khiến cả trang cuộn ngang. Biểu mẫu tìm kiếm/lọc nay xếp thành một cột trên màn hình nhỏ; ô chọn và nút vừa chiều rộng khung.
- Nút “Tạo bộ nháp & thêm câu hỏi” và nút Gemini vượt phần nội dung của biểu mẫu trên màn hình nhỏ. Các nút dài nay có thể xuống dòng và không rộng hơn khung chứa.
- Trang đăng nhập có nút và liên kết bị ép thành các dòng ngắn. Nhóm thao tác tài khoản nay xếp dọc trên màn hình nhỏ; tiêu đề và nút đăng nhập trên đầu trang vẫn vừa khung.
- Khi cuộn ngang bảng xếp hạng, tên bộ câu hỏi bị kéo ra ngoài cùng bảng. Đã tách tiêu đề khỏi vùng cuộn; chỉ bảng dữ liệu cuộn ngang. Vùng bảng có nhãn truy cập và có thể nhận tiêu điểm bàn phím.
- Cho phép các thành phần trong grid/flex co lại và ngắt chuỗi dài; giữ kích thước nút radio và nhãn A/B/C/D. Ô nhập trên màn hình nhỏ dùng cỡ chữ 16 px; nút và các mục điều hướng có chiều cao tối thiểu 44 px.

Không dùng cách ẩn phần tràn ngang của toàn trang. Bảng nhiều cột và thanh điều hướng vẫn có vùng cuộn ngang riêng.

## Phạm vi và kết quả

- Các chiều rộng điện thoại: 320, 375, 390, 430 px; kiểm tra thêm 768 px, điện thoại xoay ngang 844 × 390 và máy tính 1280 px.
- Bao phủ trang chủ, đăng nhập/đăng ký, quên/đặt lại/xác nhận mật khẩu, xác minh email, hồ sơ, thư viện, bài đọc, làm bài, kết quả, lịch sử, bảng xếp hạng và các trang quản trị bài viết/bộ câu hỏi/câu hỏi, Wikipedia, Gemini.
- 122 lượt kiểm tra bố cục sau sửa, không còn phát hiện tràn chiều ngang toàn trang hoặc ô nhập/nút vượt khung chứa theo phép đo. Có kiểm tra trực quan trang đăng nhập, Gemini, bộ lọc và thao tác cuộn bảng xếp hạng.
- `npm run build`: thành công.
- `php artisan test`: 52 kiểm thử, 227 assertions đều đạt trên cơ sở dữ liệu kiểm thử riêng.
- `git diff --check`: không có lỗi khoảng trắng.

Chỉ đọc lượt làm đang có; không nộp bài, không tạo thêm lượt làm, không gọi Gemini và không lưu biểu mẫu nội dung trong đợt kiểm tra. Đã trả phiên trình duyệt về admin và bỏ cấu hình mô phỏng kích thước sau khi kiểm tra.

Chi tiết đo và ảnh trước/sau được lưu local trong `storage/app/qa/mobile-*`, không đưa lên Git.
