# Kiểm thử và bàn giao OceanQuiz

## Phạm vi đã triển khai

| Vai | Chức năng | Trạng thái |
|---|---|---|
| A | Khởi tạo Laravel/Breeze, database, tài khoản demo | Hoàn thành |
| A | Đăng nhập, đăng ký, phân quyền, tiếng Việt, layout responsive | Hoàn thành |
| A | CRUD bài viết, giới thiệu, thư viện, tìm kiếm, trang đọc | Hoàn thành |
| A | Wikipedia tìm, xem trước, lưu, chống trùng, nguồn/giấy phép | Hoàn thành; đã gọi thật |
| B | Bắt đầu/tiếp tục lượt, quiz 5 câu, chấm server | Hoàn thành |
| B | Chống gửi lặp/đồng thời, quyền sở hữu, làm lại | Hoàn thành |
| B | Kết quả, giải thích, lịch sử cá nhân có phân trang | Hoàn thành |
| C | CRUD bộ/câu/4 lựa chọn, kiểm tra xuất bản, khóa nội dung đã dùng | Hoàn thành |
| C | 3 bộ published + 1 bộ draft, seeder chạy lại không ghi đè | Hoàn thành |
| C | Xếp hạng điểm cao nhất → thời điểm đạt điểm sớm nhất | Hoàn thành |
| C | Gemini sinh nháp, validate, transaction, nhật ký, dự phòng thủ công | Hoàn thành; đã gọi thật thành công và kiểm thử lỗi giả lập |

Mỗi nhóm chức năng có commit tiền tố A/B/C và được push trực tiếp lên main theo yêu cầu. Tác giả Git là tài khoản đang cấu hình, không tạo ba danh tính tác giả giả.

## Môi trường kiểm chứng

- Windows, XAMPP, PHP 8.2.12.
- Laravel 12 và Breeze được cài từ Composer, dependency khóa trong composer.lock.
- MariaDB 10.4.32, database ứng dụng `oceanquiz1`, test `oceanquiz1_test`.
- Test nghiệp vụ chạy trên MariaDB, không thay bằng SQLite.
- Kiểm tra frontend bằng `npm run build`; kiểm tra Blade bằng `php artisan view:cache` rồi clear cache.
- Đã kiểm tra bằng trình duyệt ở chiều rộng CSS 1280 px và 375 px. Bảng dài có cuộn ngang trong vùng bảng, không làm tràn toàn trang.

## Những ca đã kiểm tra

### A — nền tảng, quyền và bài viết

- Khách bị chuyển tới login khi vào trang cần đăng nhập.
- User bị từ chối truy cập admin; admin đăng nhập về `/admin`.
- Gửi role=admin khi đăng ký không nâng quyền tài khoản.
- CRUD bài chưa dùng; từ chối URL không phải HTTP/HTTPS.
- Bài gắn với bộ có lượt làm không sửa/xóa được.
- Bộ nháp không hiện thư viện, không đọc/bắt đầu trực tiếp qua URL.
- Thư viện lọc đúng từ khóa; seeder chạy lại không nhân đôi bộ mẫu.
- Wikipedia: fake API thành công/lỗi và kiểm tra nguồn, User-Agent, page ID.
- Đã gọi thật từ CLI và trình duyệt: tìm “cá heo”, xem trước bài page ID 525201, lưu thành công cùng nguồn/giấy phép. Có một lần provider trả lỗi tạm thời; trang hiển thị lỗi và phương án nhập thủ công, tải lại đã thành công.
- Điện thoại: đã phát hiện và sửa nút đăng xuất bị ẩn; thử đăng xuất thành công.

### B — quiz và lịch sử

- Đúng 5 câu: 50 điểm; bỏ trống hết: 0 điểm.
- Hai đúng, một sai, hai bỏ trống: 20 điểm, 2 câu đúng, 2 option_id null.
- Từ chối lựa chọn của câu khác, câu của bộ khác, hoặc nhiều lựa chọn gửi cho một câu.
- Bất kỳ dữ liệu đáp án lỗi nào cũng không lưu kết quả một phần.
- Điểm do server tính; client gửi điểm giả không có tác dụng.
- Trang quiz không chứa is_correct hoặc giải thích trước khi nộp.
- Gửi lại sau hoàn thành không đổi điểm/finished_at, không nhân đôi câu trả lời.
- Không xem hoặc nộp lượt của user khác.
- Làm lại tạo lượt mới; lịch sử chỉ hiện completed của chính user, phân trang 10.
- Gỡ xuất bản giữ lượt đang làm và kết quả cũ, chặn lượt mới.
- Đã thao tác trên trình duyệt bằng user demo: đọc cá voi → chọn 5 đáp án → kết quả 50/50 → xem lịch sử và xếp hạng.

### Thí nghiệm đồng thời trên database riêng

Ngoài kiểm thử gửi lặp tuần tự, đã chạy hai tiến trình PHP độc lập đồng thời gọi nghiệp vụ controller, dùng MariaDB thật và database riêng `oceanquiz1_concurrency_20261102`.

1. Hai lệnh start cùng user/bộ chạy đồng thời: cả hai thành công, database chỉ có 1 attempt đang làm.
2. Hai lệnh submit cùng lượt chạy đồng thời: cả hai thành công, database chỉ có 1 completed attempt, 5 answers, tổng điểm 50.

Thí nghiệm kiểm tra khóa và transaction ở lớp controller/database; không thay thế kiểm thử tải HTTP nhiều người. Script/log local nằm dưới `storage/app/qa/` và bị Git bỏ qua; database thử này không phải database ứng dụng.

### C — quản trị, xếp hạng và AI

- Bộ mặc định draft kể cả khi client gửi published lúc tạo.
- Không xuất bản khi chưa đủ 5 câu; không thêm câu thứ 6.
- Câu phải đủ 4 lựa chọn, correct_index 0–3; nested route không dùng câu thuộc bộ khác.
- Bộ đã có lượt làm không sửa/xóa được, nhưng vẫn gỡ/tái xuất bản được khi hợp lệ.
- Xóa bộ nháp chưa dùng không xóa bài nguồn.
- Một người nhiều lượt chỉ xuất hiện một lần ở bảng xếp hạng.
- Điểm cao nhất và thời điểm thuộc cùng một lượt; lượt thấp điểm làm sớm không chiếm thời điểm của lượt cao điểm.
- Cùng điểm: người đạt mức điểm đó sớm hơn đứng trước; cùng thời điểm dùng user ID ổn định.
- Cùng user/điểm/thời gian: chọn attempt ID nhỏ nhất; trang 2 bắt đầu hạng 11.
- Không tính in_progress hoặc lượt thuộc bộ khác; bảng nháp bị chặn.
- Gemini giả lập: đủ 5 câu thì tạo draft và log success; một câu sai thì không tạo bộ/câu nào và log failed.
- Đã kiểm tra JSON hỏng, phản hồi bị cắt, thiếu key, HTTP 429, và bài nguồn thay đổi lúc chờ API.
- Không đưa key vào URL hoặc log lỗi. Không tự xuất bản dữ liệu AI.

### Bổ sung kiểm chứng Gemini thật ngày 03/11/2026

- Kiểm tra danh sách model bằng khóa cấu hình trong `.env`: HTTP 200, model `gemini-3.8-flash` có sẵn.
- Các lần đầu đã gặp hết thời gian chờ; ứng dụng ghi log failed và không lưu câu hỏi một phần.
- Điều chỉnh tác vụ quiz dùng thinkingLevel low cho Gemini 3 và timeout 90 giây; Gemini 2.5 không bị gửi tham số thinkingLevel không tương thích. Tham khảo [hướng dẫn thinking của Google](https://ai.google.dev/gemini-api/docs/generate-content/thinking.md).
- Sau khi người dùng cập nhật key, gọi `gemini-3.8-flash` thành công trong 55,11 giây: HTTP 200, finishReason STOP, nhật ký local ID 4 ở trạng thái success.
- Bộ local ID 6: “Gemini kiểm tra key mới — Cá voi xanh”, source ai, status draft, 5 câu, 20 lựa chọn, mỗi câu đúng 1 đáp án và có giải thích. `isReady()` trả true. Chưa xuất bản.
- Trước đó, `gemini-3.5-flash-lite` cũng đã tạo thành công bộ local ID 5 trong 25,82 giây; giữ nguyên bộ này để đối chiếu.
- ID và bộ thử chỉ tồn tại trong database local, không phải dữ liệu mặc định khi clone repo. Không lưu key hoặc dấu nhận dạng key trong báo cáo.
- Kiểm thử sau điều chỉnh: 51 tests passed, 209 assertions trên MariaDB. Có thêm kiểm thử cấu hình theo họ model và lỗi kết nối không làm lộ key.

## Cách chạy lại kiểm thử

Tạo riêng database `oceanquiz1_test`, kiểm tra cấu hình test trước khi chạy:

```powershell
php artisan test --compact
npm run build
php artisan view:cache
php artisan view:clear
composer validate --strict
```

Kết quả kiểm thử cuối được ghi trong phần cập nhật bàn giao của README. Không chạy test bằng database ứng dụng. Không dùng migrate:fresh cho dữ liệu cần giữ.

## Kịch bản demo đề xuất

1. Mở trang giới thiệu, đăng nhập user demo.
2. Tìm “Cá voi”, đọc bài và xem nguồn.
3. Làm quiz có câu đúng/sai/bỏ trống; so điểm và giải thích.
4. Xem lịch sử, làm lại và kiểm tra không ghi đè kết quả cũ.
5. Xem xếp hạng theo bộ; dùng dữ liệu test để giải thích trường hợp bằng điểm.
6. Đăng nhập admin, tạo một bài/bộ mới, thử xuất bản khi thiếu câu, sau đó thêm đủ và xuất bản.
7. Thử sửa bộ đã có lượt làm và giải thích vì sao bị khóa.
8. Tìm/xem/lưu bài Wikipedia; khi mạng lỗi dùng form thủ công.
9. Sau khi có GEMINI_API_KEY, chọn bài → sinh 5 câu nháp → kiểm tra → xuất bản; mở nhật ký khi có lỗi.

## Giới hạn cần biết

- Đã xác nhận gọi thật Gemini thành công trên môi trường local. Các lần kiểm chứng không bảo đảm API luôn có cùng tốc độ hoặc hạn mức ở tài khoản khác; test Http::fake chỉ xác nhận logic ứng dụng và hợp đồng request/response.
- Chưa triển khai hosting công khai. Push GitHub là lưu mã nguồn, không tự deploy website.
- Không đồng bộ database/.env qua GitHub. Máy khác phải cài dependency, tạo database, migrate và seed.
- Mail reset mật khẩu mặc định ghi log; chưa có SMTP thật.
- Chưa đo tải lớn hoặc triển khai CI; ứng dụng hướng đến đồ án local theo phạm vi đã chốt.
- Seed nội dung tham khảo có nguồn, nhưng admin vẫn cần kiểm tra kiến thức và câu hỏi AI trước khi xuất bản.
