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
- Bộ local ID 6: “Gemini kiểm tra key mới — Cá voi xanh”, source ai, khi sinh có status draft, 5 câu, 20 lựa chọn, mỗi câu đúng 1 đáp án và có giải thích. `isReady()` trả true. Bộ được rà soát và xuất bản ở đợt kiểm tra tiếp theo bên dưới.
- Trước đó, `gemini-3.5-flash-lite` cũng đã tạo thành công bộ local ID 5 trong 25,82 giây; giữ nguyên bộ này để đối chiếu.
- ID và bộ thử chỉ tồn tại trong database local, không phải dữ liệu mặc định khi clone repo. Không lưu key hoặc dấu nhận dạng key trong báo cáo.
- Kiểm thử sau điều chỉnh: 51 tests passed, 209 assertions trên MariaDB. Có thêm kiểm thử cấu hình theo họ model và lỗi kết nối không làm lộ key.

### Xuất bản và kiểm tra hai người dùng ngày 03/11/2026

- Đã đọc lại cả 5 câu, bốn lựa chọn/câu và giải thích, đối chiếu trực tiếp bài cá voi xanh đã lưu. Các đáp án đều được bài nguồn hỗ trợ; không cần sửa nội dung.
- Đăng nhập admin qua HTTP, dùng CSRF/session thật để xuất bản bộ ID 6. Trạng thái sau thao tác là published.
- Đăng ký mới qua HTTP tài khoản `user2@oceanquiz.local`, tên “Nhà thám hiểm 2”, role user. Mật khẩu demo `OceanQuiz@123`; thêm tài khoản này vào UserSeeder để máy clone mới cũng có hai user thử xếp hạng. Seeder không ghi đè tài khoản đang có.
- Dùng các phiên HTTP độc lập của user và user2: đọc bài, bắt đầu, lấy trang quiz, nộp đáp án, mở trang kết quả và bảng xếp hạng. Không ghi trực tiếp điểm hoặc thời gian vào database.

| Lượt local | Tài khoản | Đáp án thử | Điểm server | Hoàn thành, giờ Việt Nam |
|---|---|---|---|---|
| 4 | user@oceanquiz.local | 4 đúng, 1 sai | 40 | 03/11/2026 00:19:29.075266 |
| 5 | user2@oceanquiz.local | 3 đúng, 1 sai, 1 bỏ trống | 30 | 03/11/2026 00:19:29.958224 |
| 6 | user2@oceanquiz.local | 5 đúng | 50 | 03/11/2026 00:19:30.960523 |
| 7 | user@oceanquiz.local | 5 đúng | 50 | 03/11/2026 00:19:33.246991 |
| 8 | user2@oceanquiz.local | 3 đúng, 1 sai, 1 bỏ trống | 30 | 03/11/2026 00:19:34.275217 |

Kết quả bảng xếp hạng từ HTML trả về qua HTTP:

1. Sau hai lượt đầu: user 40 đứng trước user2 30.
2. Sau lượt 6: user2 50 đứng trước user 40.
3. Sau lượt 7: hai người cùng 50, user2 đứng hạng 1 vì đạt 50 lúc 00:19:30, user hạng 2 vì đạt 50 lúc 00:19:33. Lượt 40 trước đó của user không được dùng làm thời điểm đạt 50.
4. Sau lượt 8: user2 vẫn giữ 50 và thời điểm 00:19:30, không nhân đôi dòng và không lấy điểm 30 mới hơn.
5. Gửi lại lượt 7 với đáp án trống: vẫn 50 điểm, finished_at giữ nguyên, chỉ có 5 câu trả lời.

Mọi đối chiếu đều đạt. Dữ liệu lượt làm chỉ ở database local, không đưa lên GitHub hoặc áp dụng vào máy khác qua seeder. Báo cáo chi tiết local nằm trong `storage/app/qa/published-ranking-result.json` và bị Git bỏ qua.

### Sửa trường hợp CLI có key nhưng web báo chưa cấu hình

- Server local trước đó được khởi động với `--no-reload`, nên giữ giá trị môi trường cũ khi người dùng thay `.env`. CLI mới nhận key nhưng web cũ vẫn báo trống dù không có config cache.
- Đã dừng đúng tiến trình server của dự án, xóa config cache và chạy lại `php artisan serve --host=127.0.0.1 --port=8001` không có `--no-reload`.
- Kiểm tra qua phiên HTTP đăng nhập admin xác nhận trang `/admin/ai` nhận cấu hình key, không còn cảnh báo trống. Không hiển thị hoặc ghi lại giá trị key.
- Bổ sung hướng dẫn khởi động lại ngay trong cảnh báo giao diện và README để tránh nhầm lỗi cấu hình với lỗi API.
- Phát hiện thêm yêu cầu Gemini từ web bị PHP dừng do max_execution_time=60 trong khi HTTP timeout là 90 giây. Controller đã nâng giới hạn PHP lên tối thiểu 120 giây cho riêng thao tác sinh câu hỏi. Một log pending cũ của yêu cầu đã chết được đánh dấu failed; không tác động các yêu cầu còn chạy.

## Cách chạy lại kiểm thử

### Kiểm tra tạo trực tiếp trên giao diện ngày 03/11/2026

- Mở trang `/admin/ai` trên trình duyệt, chọn đúng bài Minecraft người dùng đang gặp lỗi, nhập tên bộ và bấm nút “Sinh 5 câu nháp bằng Gemini”. Không dùng CLI/controller giả lập để thực hiện các lần tạo trong đợt này.
- Lần đầu (log local 8) vẫn bị PHP giới hạn 60 giây. Đã đặt lại giới hạn cho mọi yêu cầu web của thao tác sinh, khởi động lại tiến trình và đo được PHP SAPI `cli-server`, execution limit 120 giây. Không áp giới hạn này vào CLI.
- Lần tiếp theo (log local 9) model `gemini-3.8-flash` chạm HTTP timeout 90 giây. Web trả thông báo lỗi có kiểm soát và cập nhật log failed, không còn PHP fatal hoặc log pending treo.
- Giữ nguyên API key, đổi model local sang `gemini-3.5-flash-lite`, tải lại form và xác nhận trang ghi “Đã cấu hình key” cùng đúng tên model.
- Bấm tạo từ form với tên “Minecraft — Tạo trực tiếp từ giao diện”: thành công trong 30 giây, log local 10 success, tự chuyển tới `/admin/question-sets/8/edit` với thông báo “Gemini đã tạo 5 câu nháp”.
- Đối chiếu database: source ai, status draft, 5 câu, 20 lựa chọn, mỗi câu có một đáp án đúng và giải thích; `isReady()` true. Không xuất bản bộ thử này.
- Đã lưu ảnh xác nhận giao diện trong `storage/app/qa/gemini-web-success.jpg` (artifact local, không chứa key).
- Đặt Flash-Lite làm model mặc định trong cấu hình mẫu để phù hợp bản quiz cơ bản. Hiển thị model đang dùng trên form admin. Không đưa `.env` hoặc key lên Git.
- Bổ sung mã HTTP, trạng thái Google và chi tiết lỗi được che key thay cho thông báo từ chối chung. Kiểm thử 400/403/404/503 xác nhận đúng loại lỗi và không lộ key.
- Kết quả kiểm thử mới: 52 tests passed, 227 assertions. Kết quả thành công của Flash-Lite không chứng minh model 3.8 hết lỗi chờ; các lỗi nhà cung cấp vẫn được xử lý và ghi nhật ký.


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
