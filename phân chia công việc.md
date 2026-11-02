# PHÂN CHIA CÔNG VIỆC DỰ ÁN OCEANQUIZ CHO A, B, C

## 1. Điểm xuất phát và mục tiêu

> Cập nhật phạm vi đã chốt ngày 02/11/2026: triển khai Wikipedia và Gemini ngay trong phiên bản đầu. Làm lần lượt vai A/B/C, commit theo `A- ...`, `B- ...`, `C- ...` và push từng chức năng trực tiếp lên `main`. Hai API chỉ phục vụ admin: Wikipedia tìm/xem/lưu bài; Gemini sinh 5 câu nháp, validate toàn bộ và ghi nhật ký. Khi API lỗi vẫn nhập thủ công được. Xem README và `docs/kiem-thu-va-ban-giao.md` để biết trạng thái triển khai thực tế.

- Dự án bắt đầu từ con số 0: chỉ có ý tưởng và đặc tả, chưa có mã nguồn Laravel, database, giao diện hoặc dữ liệu mẫu.
- Thư mục triển khai: `C:\xampp\htdocs\oceanquiz1`.
- Tài liệu này là kế hoạch để ba thành viên thực hiện; chưa có chức năng nào được coi là đã hoàn thành.
- Sản phẩm: website tiếng Việt giúp người dùng đọc kiến thức sinh vật biển, làm quiz, xem kết quả, lịch sử và xếp hạng.
- Công nghệ thống nhất: Laravel 12, PHP 8.2, Breeze, Blade, CSS/Tailwind, JavaScript cơ bản và MySQL/MariaDB trên XAMPP.
- Ưu tiên MVC, CRUD, Eloquent, validation, middleware và transaction. Không thêm framework frontend hoặc kiến trúc nhiều lớp không cần thiết.

### 1.1. Phạm vi bàn giao

1. Giới thiệu, đăng ký, đăng nhập, đăng xuất.
2. Danh sách và tìm kiếm bộ câu hỏi đã xuất bản.
3. Đọc bài kiến thức, bắt đầu làm bài.
4. Quiz 5 câu, mỗi câu 4 lựa chọn, đúng 1 đáp án.
5. Server chấm điểm: đúng 10 điểm, sai/bỏ trống 0 điểm; tối đa 50 điểm.
6. Kết quả và lịch sử cá nhân.
7. Xếp hạng theo từng bộ: điểm cao nhất trước; bằng điểm thì ưu tiên người đạt mức điểm đó sớm hơn.
8. Admin quản lý bài viết, bộ câu hỏi, xuất bản/gỡ xuất bản và xem tổng quan.
9. Dữ liệu mẫu, kiểm thử trọng tâm, README và kịch bản demo.

### 1.2. Ngoài phạm vi

Chưa làm highlight, đếm ngược, điểm theo tốc độ, hiệu ứng parallax, thi đấu nhiều người, bảng xếp hạng tổng, nhập JSON, upload ảnh hoặc giao diện đổi vai trò tài khoản. Ảnh dùng đường dẫn URL hợp lệ. Wikipedia và Gemini thuộc phạm vi phiên bản đầu theo quyết định đã cập nhật.

## 2. Phân công tổng thể

| Thành viên | Vai trò chính | Các phần phải hoàn thành | Người rà soát |
|---|---|---|---|
| A | Nền tảng, phân quyền và nội dung đọc | Khởi tạo, database nền, Breeze, layout, trang giới thiệu/thư viện/đọc, CRUD bài viết, tổng quan admin, tích hợp | B |
| B | Nghiệp vụ làm bài | Lượt làm, form quiz, chấm điểm, chống nộp lặp, kết quả, lịch sử, kiểm thử nghiệp vụ | C |
| C | Bộ câu hỏi và xếp hạng | CRUD bộ/câu/lựa chọn, kiểm tra xuất bản, bảo toàn nội dung đã dùng, seed nội dung, bảng xếp hạng | A |

Mỗi người tự làm cả backend, Blade và kiểm thử cho phần mình. A quản lý các file dùng chung nhưng không làm thay toàn bộ frontend hoặc kiểm thử của B và C.

## 3. Công việc chi tiết của A

### A01. Chuẩn bị dự án và quy trình Git

- Kiểm tra PHP, Composer, Node/npm, MySQL/MariaDB và extension cần thiết.
- Tạo mới Laravel 12. Vì thư mục đích đã có `.git` và tài liệu này, không chạy lệnh khởi tạo yêu cầu thư mục rỗng ngay trong thư mục đó; tạo Laravel ở thư mục tạm cùng cấp rồi đưa các file ứng dụng vào `oceanquiz1`, giữ nguyên `.git` và tài liệu.
- Cài Breeze với Blade; cài dependency và xác nhận trang mặc định chạy được.
- Tạo database `oceanquiz1`, cấu hình `.env` local và `.env.example` không chứa bí mật.
- Cấu hình thời gian lưu nhất quán; giao diện hiển thị theo `Asia/Ho_Chi_Minh`.
- Bổ sung `.gitignore`, README cài đặt ban đầu; commit nền để B và C cùng bắt đầu.
- Không commit `.env`, `vendor/`, `node_modules/`, database dump chứa dữ liệu thật hoặc log.

**Bàn giao:** bộ khung chạy được, hướng dẫn cài đặt và commit nền. **Đạt khi:** B và C clone/pull, cài dependency, cấu hình database và mở được website trên máy riêng.

### A02. Chốt và tạo database nền

- Cùng B và C duyệt thiết kế ở mục 6 trước khi viết migration.
- Tạo migration cho 7 bảng nghiệp vụ, khóa ngoại, ràng buộc và index đã thống nhất.
- Quản lý mọi migration dùng chung để tránh hai người tạo trùng bảng/cột.
- Tạo `User`, `Article` và quan hệ thuộc hai model này.
- Sau khi B/C hoàn thành model liên quan, kiểm tra toàn bộ quan hệ hai chiều hoạt động.
- Các bảng hỗ trợ mặc định của Laravel/Breeze được giữ nếu ứng dụng sử dụng.

**Bàn giao:** migration chạy được trên database trống, sơ đồ quan hệ trong README. **Đạt khi:** migrate thành công, rollback trên database thử nghiệm được, khóa ngoại từ chối dữ liệu mồ côi.

### A03. Đăng ký, đăng nhập và phân quyền

- Dùng Breeze cho đăng ký, đăng nhập và đăng xuất.
- Bổ sung `role`, mặc định `user`; không cho client đăng ký tài khoản `admin`.
- Viết và đăng ký middleware `admin`.
- Đăng nhập xong: admin về `/admin`, user về `/home`.
- Mọi route admin đều kiểm tra quyền ở server; ẩn menu không thay thế middleware.
- Tạo tài khoản demo qua `UserSeeder`; thông tin chỉ dành cho local.

**Đạt khi:** khách không vào được trang cần đăng nhập, user bị từ chối ở mọi route admin và tài khoản mới luôn là user.

### A04. Layout và giao diện dùng chung

- Tạo layout khách, người dùng và quản trị; tái sử dụng thành phần nếu phù hợp.
- Làm menu, logo chữ OceanQuiz, thông báo thành công/lỗi và phân trang.
- Quy định màu xanh đại dương, font, nút, form, bảng và khoảng cách.
- Giao diện hoạt động ở chiều rộng khoảng 375 px và 1280 px.
- Bàn giao một trang mẫu có form, bảng và thẻ để B/C sử dụng.
- Tận dụng layout Breeze; tránh để layout mặc định gọi một route dashboard không tồn tại.

**Đạt khi:** các trang dùng chung menu, trạng thái đăng nhập và cách hiển thị lỗi; điện thoại không tràn ngang toàn trang.

### A05. Trang giới thiệu, thư viện và đọc bài

- `/`: giới thiệu ngắn, nút đăng ký/đăng nhập và nút vào thư viện khi đã đăng nhập.
- `/home`: danh sách bộ published, tìm theo tên, phân trang 12 bộ/trang.
- Thẻ bộ hiển thị tên, ảnh nếu có, mô tả ngắn và số câu.
- Trang đọc hiển thị bài gắn với bộ, nguồn tham khảo và nút bắt đầu trỏ đúng route của B.
- Chặn truy cập bộ nháp qua URL trực tiếp; xử lý trường hợp không có kết quả tìm kiếm.
- Hiển thị văn bản bài viết bằng cách escape và giữ xuống dòng; không render HTML tùy ý từ dữ liệu nhập.

**Phụ thuộc:** model nội dung và quy tắc xuất bản của C; hợp đồng route bắt đầu của B. **Đạt khi:** chọn bộ → đọc đúng bài → bấm bắt đầu gọi đúng nghiệp vụ B.

### A06. CRUD bài viết và tổng quan admin

- Tạo danh sách, form thêm/sửa và chức năng xóa bài viết chưa được sử dụng.
- Validate tiêu đề, nội dung, URL ảnh và thông tin nguồn; báo lỗi tại form.
- Chỉ cho xóa bài chưa gắn với bộ nào; không xóa dây chuyền.
- Không cho sửa bài gắn với bộ đã có bất kỳ lượt làm nào; trường hợp khác được sửa.
- Trang tổng quan hiển thị số bài, số bộ published, số lượt completed và liên kết quản lý.
- Không làm trang quản lý người dùng ngoài phạm vi.

**Đạt khi:** CRUD đúng quyền, đúng validation, không làm thay đổi/mất nội dung đang được lịch sử tham chiếu.

### A07. Tích hợp và tài liệu chung

- Quản lý `routes/web.php`, import các route riêng của B/C theo mục 7.
- Quản lý `DatabaseSeeder`; gọi các seeder theo đúng thứ tự.
- Chạy kiểm thử toàn bộ và build frontend sau mỗi mốc ghép lớn.
- Viết phần README về môi trường, cài đặt, migration, seeder, chạy app và tài khoản demo.
- Thu thập hướng dẫn module của B/C, kiểm tra đường dẫn/menu liên kết đúng.
- Tổ chức kiểm thử chéo; A không tự chịu trách nhiệm sửa lỗi nghiệp vụ của B/C.

**Kiểm thử A phụ trách:** khách/user/admin, đăng ký không nâng quyền, CRUD bài viết, bộ nháp, tìm kiếm và truy cập trang đọc.

## 4. Công việc chi tiết của B

**Bổ sung phần A đã chốt:** A08 triển khai `WikipediaService`, trang admin tìm/xem/lưu bài, unique page ID, thông tin nguồn/giấy phép và kiểm thử API lỗi. API được gọi ở server, không nhận URL bất kỳ từ client.

### B01. Thiết kế và tạo model lượt làm

- Góp ý schema `attempts`, `attempt_answers` trước khi A viết migration.
- Tạo `Attempt`, `AttemptAnswer`, quan hệ và ép kiểu dữ liệu cần thiết.
- Thống nhất trạng thái `in_progress`, `completed`; chưa cần trạng thái đọc hoặc hết giờ.
- Viết mô tả đầu vào/đầu ra cho thao tác bắt đầu và nộp bài để A/C kết nối.

**Đạt khi:** tải được lượt, chủ tài khoản, bộ câu hỏi, câu trả lời và lựa chọn qua Eloquent.

### B02. Bắt đầu và tiếp tục lượt làm

- Nhận POST từ trang đọc; kiểm tra bộ published, đủ 5 câu hợp lệ trước khi tạo lượt mới.
- Lấy `user_id` từ tài khoản đăng nhập; ghi `started_at` bằng thời gian server.
- Nếu đã có lượt `in_progress` của người đó với bộ đó thì trả về lượt hiện có.
- Xử lý bấm bắt đầu đồng thời để không tạo nhiều lượt đang làm; có thể khóa dòng user trong transaction rồi kiểm tra lại.
- Khi tạo lượt mới, phối hợp với C để việc kiểm tra/xuất bản/sửa nội dung không gây dữ liệu lệch trong thao tác đồng thời.
- Lượt đã tồn tại vẫn được chủ tài khoản tiếp tục nếu bộ bị gỡ xuất bản sau đó.

**Đạt khi:** bấm nhiều lần hoặc mở hai tab không phát sinh hai lượt đang làm của cùng user/bộ.

### B03. Trang quiz

- Tạo trang hiển thị đủ 5 câu, mỗi câu 4 radio button và nút nộp bài.
- Không đánh dấu sẵn lựa chọn; cho phép bỏ trống.
- Cho phép đổi lựa chọn trước khi nộp; không cần đồng hồ hoặc lưu nháp lựa chọn.
- Chỉ truyền nội dung và ID cần chọn; không truyền `is_correct` hoặc giải thích vào HTML, JavaScript hay JSON.
- Kiểm tra quyền sở hữu lượt; lượt completed chuyển sang kết quả.
- Khóa nút khi gửi để cải thiện trải nghiệm, không dùng cách này thay cho kiểm tra server.

**Đạt khi:** tải lại không tạo lượt mới; xem source trang quiz không thấy thông tin đáp án đúng.

### B04. Validate, chấm điểm và chống nộp lặp

- Dữ liệu form thống nhất là `answers[question_id] = option_id`; câu không chọn có thể không xuất hiện.
- Từ chối ID câu ngoài bộ, lựa chọn không thuộc câu, kiểu dữ liệu sai hoặc gửi nhiều lựa chọn cho một câu.
- Không nhận `total_score`, `is_correct`, `user_id`, `finished_at` từ trình duyệt để tính kết quả.
- Trong transaction, khóa lượt làm và kiểm tra lại trạng thái trước khi ghi.
- Lưu một `attempt_answers` cho mỗi câu trong 5 câu, kể cả bỏ trống; bỏ trống có `option_id = null`.
- Mỗi câu đúng 10 điểm, sai/bỏ trống 0; tổng hợp `correct_count`, `total_score` ở server.
- Ghi `status = completed`, `finished_at` bằng thời gian server trong cùng transaction.
- Nếu lượt đã completed, chuyển đến kết quả hiện có, không sửa câu trả lời hoặc thời gian hoàn thành.
- Không để lỗi giữa chừng tạo kết quả một phần.

**Đạt khi:** đúng hết 50 điểm; sai/bỏ trống hết 0 điểm; gửi lặp không đổi kết quả và không ghi trùng câu trả lời.

### B05. Kết quả, làm lại và lịch sử

- Kết quả hiển thị điểm, số câu đúng/5, thời gian hoàn thành và đáp án/giải thích từng câu.
- Câu bỏ trống ghi rõ “Chưa trả lời”.
- Nút làm lại gọi đúng route bắt đầu; tạo lượt mới sau khi lượt cũ completed nếu bộ vẫn published.
- Với bộ đã gỡ xuất bản, vẫn xem kết quả nhưng không cho bắt đầu lượt mới.
- Lịch sử chỉ gồm lượt completed của chính user, mới nhất trước, phân trang 10 dòng.
- Có tên bộ, điểm, số câu đúng, thời gian và liên kết kết quả.
- Không truy cập được lượt người khác qua GET hoặc POST khi thay ID.

**Đạt khi:** lịch sử và trang kết quả khớp database; thao tác làm lại không ghi đè lượt cũ.

### B06. Kiểm thử và tài liệu module

- Kiểm thử quyền sở hữu ở trang quiz, nộp bài và kết quả.
- Kiểm thử 50 điểm, 0 điểm, điểm hỗn hợp và bỏ trống.
- Kiểm thử đáp án ngoài câu/bộ bị từ chối, kết quả không lưu một phần.
- Kiểm thử bắt đầu/nộp lặp và không cập nhật `finished_at` của kết quả đã có.
- Rà soát kịch bản hai tab; nếu test tự động khó mô phỏng khóa database thì ghi rõ bước thử thủ công trên MySQL/MariaDB.
- Tạo fixture/factory phục vụ test; không đưa dữ liệu kết quả giả vào seeder demo mặc định.
- Viết phần README về luồng quiz, cách tính điểm và các trường hợp kiểm thử.

**Bàn giao B:** model, controller, route, Blade, test, hướng dẫn và bằng chứng chạy kiểm thử module.

## 5. Công việc chi tiết của C

### C01. Model nội dung và hợp đồng dữ liệu

- Góp ý schema `question_sets`, `questions`, `question_options` trước khi A tạo migration.
- Tạo `QuestionSet`, `Question`, `QuestionOption`, quan hệ và ép kiểu dữ liệu.
- Câu được sắp bằng `position`; lựa chọn cũng có `position` ổn định.
- Có phương thức kiểm tra điều kiện xuất bản để không lặp quy tắc giữa giao diện và backend.

**Đạt khi:** tải được bộ → bài → câu → lựa chọn đúng thứ tự; A/B dùng được các quan hệ đã thống nhất.

### C02. CRUD bộ câu hỏi

- Danh sách admin có phân trang và lọc trạng thái draft/published.
- Tạo bộ bằng form chọn bài viết, tên và mô tả; mặc định draft.
- Trang sửa có thông tin bộ, danh sách câu và trạng thái đủ/chưa đủ 5 câu.
- Dùng form HTML thông thường; không cần trình sửa động phức tạp.
- Không nhận trạng thái published trực tiếp từ form tạo/sửa để bỏ qua bước kiểm tra xuất bản.

### C03. CRUD câu hỏi và lựa chọn

- Mỗi form câu có nội dung, 4 ô lựa chọn, 1 radio đáp án đúng và giải thích tùy chọn.
- Validate toàn bộ, không cho lựa chọn trống hoặc nhiều đáp án đúng.
- Đáp án chọn qua `correct_index` là số nguyên 0–3; server tự gán `is_correct` cho 4 lựa chọn.
- Tạo/cập nhật câu cùng lựa chọn trong transaction.
- Bộ nháp chưa sử dụng có thể có 0–5 câu trong quá trình biên soạn; không cho thêm quá 5 câu.
- Khi xóa câu, xử lý các lựa chọn trong transaction và giữ thứ tự còn lại rõ ràng.

**Đạt khi:** thao tác lỗi không để câu có thiếu lựa chọn hoặc sai số đáp án đúng.

### C04. Xuất bản và bảo toàn nội dung

- Chỉ xuất bản bộ có đúng 5 câu; mỗi câu đủ 4 lựa chọn và 1 đáp án đúng.
- Gỡ xuất bản chỉ đổi trạng thái, không xóa dữ liệu hoặc kết quả.
- Muốn sửa bộ đang published nhưng chưa có lượt làm: phải gỡ xuất bản trước.
- Bộ có bất kỳ lượt làm nào thì không cho sửa nội dung bộ/câu/lựa chọn hoặc xóa; cho phép đổi trạng thái xuất bản.
- Bộ draft không có lượt làm được xóa cùng câu/lựa chọn trong transaction.
- Phối hợp B: thao tác tạo lượt và sửa/xóa nội dung phải cùng khóa dòng bộ và kiểm tra lại điều kiện trong transaction để tránh xung đột.
- Phối hợp A: bài đã gắn với bộ có lượt làm không được sửa; thao tác ghi và kiểm tra liên quan phải được bảo vệ nhất quán.

**Đạt khi:** không thể đổi đáp án của lượt đang làm hoặc kết quả cũ, kể cả gọi trực tiếp endpoint.

### C05. Seed nội dung mẫu

- Viết `OceanContentSeeder`: 3 bài và 3 bộ published, mỗi bộ 5 câu, mỗi câu 4 lựa chọn; thêm 1 bộ draft.
- Có giải thích cho câu hỏi demo; kiểm tra lại tính đúng đắn của kiến thức.
- Bài tham khảo có nguồn phù hợp; ảnh có quyền sử dụng và nguồn khi cần.
- Bàn giao tên seeder cho A gọi sau `UserSeeder`.
- Tránh tạo bản trùng khi chạy lại seeder trên database demo theo cách README hướng dẫn.

**Đạt khi:** sau seed có thể demo ngay luồng đọc và làm bài; bộ draft không hiện ở thư viện.

### C06. Truy vấn xếp hạng

- Viết `LeaderboardController` và trang chọn bộ published để xem xếp hạng.
- Khi bộ bị gỡ xuất bản: không hiện trong danh sách chọn công khai và không mở bảng của bộ đó qua URL; kết quả cá nhân cũ vẫn xem được ở phần B.
- Chỉ lấy lượt `completed` của bộ được chọn.
- Chọn đúng 1 lượt đại diện/user: `total_score` cao nhất → `finished_at` sớm nhất → `attempts.id` nhỏ nhất.
- Sắp toàn bộ các lượt đại diện: `total_score DESC`, `finished_at ASC`, `user_id ASC`.
- Không dùng độc lập `MAX(total_score)` và `MIN(finished_at)` trên toàn bộ lượt của một người.
- Ghép tên từ `users`; không công khai email hoặc câu trả lời riêng của người khác.
- Không tạo bảng lưu xếp hạng riêng. Không tải toàn bộ lượt lên PHP chỉ để sắp xếp.
- Dùng truy vấn database phù hợp phiên bản MySQL/MariaDB nhóm đã chốt; ghi chú ngắn phần chọn lượt tốt nhất.
- Kiểm tra truy vấn trên MySQL/MariaDB thực tế, không chỉ trên SQLite.

### C07. Giao diện xếp hạng

- Hiển thị hạng, tên, điểm, số câu đúng của lượt đại diện và thời điểm đạt điểm.
- Dùng hạng liên tiếp 1, 2, 3..., không đồng hạng.
- Phân trang 10 người/trang, thứ hạng tiếp nối giữa các trang.
- Tô nổi bật dòng của người đang đăng nhập.
- Có trạng thái chưa có lượt completed và bộ không có người chơi.
- Thời gian là `finished_at` của lượt đại diện, không phải lúc tạo tài khoản hoặc lần làm mới nhất.

### C08. Kiểm thử và tài liệu module

- Kiểm thử validation câu, số lựa chọn, đáp án và điều kiện xuất bản.
- Kiểm thử user không dùng được endpoint admin và không sửa được bộ đã có lượt làm.
- Kiểm thử xếp hạng bằng bộ dữ liệu ở mục 8.
- Kiểm thử phân trang không reset hạng và một người không xuất hiện nhiều lần.
- Viết phần README về quản trị nội dung, trạng thái và thuật toán xếp hạng.

**Bàn giao C:** model nội dung, CRUD, route, Blade, seeder, xếp hạng, test và hướng dẫn.

## 6. Hợp đồng database chung

**Bổ sung phần C đã chốt:** C09 triển khai `GeminiService`, sinh đúng 5 câu dạng JSON, validate toàn bộ, lưu transaction ở trạng thái draft, nhật ký pending/success/failed, timeout và form thủ công dự phòng. Tạo bảng `ai_generation_logs`; bổ sung metadata Wikipedia cho articles và nguồn manual/ai cho question_sets. Không commit khóa API.

Đây là thiết kế khởi tạo mới, không lấy cấu trúc từ dự án OceanQuiz cũ. A viết migration sau khi B/C xác nhận; mỗi người sở hữu model theo phân công.

| Bảng | Cột nghiệp vụ chính | Model phụ trách |
|---|---|---|
| `users` | `id`, `name`, `email` unique, `password`, `role` mặc định user, cột Breeze cần, timestamps | A |
| `articles` | `id`, `title`, `content` longText, `image_url` nullable, `source_name` nullable, `source_url` nullable, timestamps | A |
| `question_sets` | `id`, `article_id`, `title`, `description` nullable, `status` draft/published, timestamps | C |
| `questions` | `id`, `question_set_id`, `content`, `explanation` nullable, `position`, timestamps | C |
| `question_options` | `id`, `question_id`, `content`, `is_correct` boolean, `position`, timestamps | C |
| `attempts` | `id`, `user_id`, `question_set_id`, `status` in_progress/completed, `started_at`, `finished_at` nullable, `total_score` mặc định 0, `correct_count` mặc định 0, timestamps | B |
| `attempt_answers` | `id`, `attempt_id`, `question_id`, `option_id` nullable, `is_correct` boolean, `score` mặc định 0, timestamps | B |

### 6.1. Quan hệ

- User có nhiều Attempt.
- Article có nhiều QuestionSet.
- QuestionSet thuộc Article, có nhiều Question và Attempt.
- Question có nhiều QuestionOption và AttemptAnswer.
- Attempt thuộc User/QuestionSet, có nhiều AttemptAnswer.
- AttemptAnswer thuộc Attempt/Question, thuộc QuestionOption nếu đã chọn.
- Câu trả lời bỏ trống vẫn có dòng dữ liệu với `option_id = null`, `is_correct = false`, `score = 0`.

### 6.2. Ràng buộc và index

- Unique `attempt_answers(attempt_id, question_id)`.
- Unique vị trí trong một bộ/câu: `questions(question_set_id, position)` và `question_options(question_id, position)`.
- Index lịch sử: `attempts(user_id, status, finished_at)`.
- Index xếp hạng ban đầu: `attempts(question_set_id, status, total_score, finished_at)`; C kiểm tra lại theo truy vấn thực tế.
- Khóa ngoại dùng chính sách ngăn xóa dữ liệu đang được tham chiếu; không tự động xóa lịch sử qua cascade.
- Xóa bộ nháp chưa dùng: ứng dụng xóa lựa chọn → câu → bộ trong transaction.
- Xóa tài khoản/lượt đã làm không thuộc phạm vi; không để tính năng xóa tài khoản mặc định của Breeze làm mất hoặc lỗi lịch sử. A bỏ tính năng này khỏi giao diện/route phiên bản cơ bản.
- Quy tắc “đúng 4 lựa chọn, đúng 1 đáp án” kiểm tra ở ứng dụng; không coi index là đủ để bảo đảm quy tắc này.

### 6.3. Quy tắc dữ liệu dùng chung

- Tên bảng/cột tiếng Anh, `snake_case`; model số ít, `PascalCase`.
- Dùng `position` tăng từ 1 để hiển thị; riêng `correct_index` của form dùng 0–3.
- Không dùng thời gian trình duyệt cho kết quả/xếp hạng.
- Lưu timestamp nhất quán theo UTC, chuyển sang `Asia/Ho_Chi_Minh` khi hiển thị.
- Migration đã merge và được người khác chạy thì không sửa; tạo migration bổ sung qua A.
- Không chạy `migrate:fresh` trên database đang chứa dữ liệu cần giữ. Chỉ dùng trên database local/test có thể xóa và được ghi rõ trong hướng dẫn.

## 7. Hợp đồng route và quyền sửa file

### 7.1. Route thống nhất

| Method | URL | Tên route | Quyền | Chủ trì |
|---|---|---|---|---|
| GET | `/` | `landing` | Khách/user | A |
| GET | `/home` | `home` | auth | A |
| GET | `/question-sets/{questionSet}/read` | `question-sets.read` | auth, bộ published | A |
| POST | `/question-sets/{questionSet}/start` | `attempts.start` | auth | B |
| GET | `/attempts/{attempt}/play` | `attempts.play` | auth, chủ lượt | B |
| POST | `/attempts/{attempt}/submit` | `attempts.submit` | auth, chủ lượt | B |
| GET | `/attempts/{attempt}/result` | `attempts.result` | auth, chủ lượt | B |
| GET | `/history` | `history` | auth | B |
| GET | `/leaderboard` | `leaderboard` | auth | C |
| GET | `/admin` | `admin.dashboard` | auth + admin | A |
| Resource | `/admin/articles` | `admin.articles.*` | auth + admin | A |
| Resource | `/admin/question-sets` | `admin.question-sets.*` | auth + admin | C |
| Resource | `/admin/question-sets/{questionSet}/questions` | `admin.question-sets.questions.*` | auth + admin | C |
| POST | `/admin/question-sets/{questionSet}/publish` | `admin.question-sets.publish` | auth + admin | C |
| POST | `/admin/question-sets/{questionSet}/unpublish` | `admin.question-sets.unpublish` | auth + admin | C |

- Auth dùng route của Breeze; không tự viết lại hệ thống mật khẩu.
- Resource chỉ khai báo các action cần dùng, không cần tạo trang `show` nếu đã có trang sửa/đọc thay thế.
- Route lồng câu hỏi phải kiểm tra câu thực sự thuộc bộ trên URL, không chỉ kiểm tra quyền admin.
- Bộ lọc dùng GET: `/home?search=...`, `/leaderboard?question_set_id=...`.
- Mọi thao tác tạo/sửa/xóa/xuất bản/nộp bài dùng POST/PATCH/DELETE phù hợp và CSRF; không dùng GET để thay đổi dữ liệu.

### 7.2. File/thư mục sở hữu

| Người | File/thư mục chính |
|---|---|
| A | `routes/web.php`, `routes/library.php`, `routes/admin-articles.php`, auth Breeze, middleware admin, `bootstrap/app.php`, migration, `User`, `Article`, `LibraryController`, `Admin/ArticleController`, `Admin/DashboardController`, layout/component dùng chung, CSS nền, `UserSeeder`, `DatabaseSeeder`, README tổng |
| B | `routes/quiz.php`, `Attempt`, `AttemptAnswer`, `QuizController`, `HistoryController`, `views/quiz/*`, `views/history/*`, kiểm thử Quiz/History và factory/fixture liên quan |
| C | `routes/admin-question-sets.php`, `routes/leaderboard.php`, model nội dung, `Admin/QuestionSetController`, `Admin/QuestionController`, `LeaderboardController`, view quản lý bộ/câu, `views/leaderboard/*`, `OceanContentSeeder`, kiểm thử nội dung/xếp hạng |

- A thiết lập cơ chế `require` các route riêng đúng một lần; mỗi file route tự khai báo nhóm middleware/prefix đã thống nhất.
- B/C không tự đổi layout chung, `composer.json`, `package.json`, lockfile hoặc cấu hình nền trên nhánh riêng khi chưa phối hợp A.
- Người phụ trách trang tự viết Blade và dùng lớp giao diện đã thống nhất, không chờ A làm hộ trang.
- Nếu phát hiện cần thêm cột/quan hệ ngoài phần sở hữu, báo người phụ trách trước, không tạo hai phiên bản cùng chức năng.

## 8. Bộ kiểm tra bắt buộc cho xếp hạng

Các thời gian sau là dữ liệu thử theo giờ Việt Nam, không phải lịch triển khai. Tất cả lượt thuộc cùng một bộ, trừ khi có ghi chú.

| Người | Các lượt thử | Lượt đại diện mong đợi |
|---|---|---|
| An | 30 điểm 08:00; 50 điểm 09:00; 50 điểm 10:00 | 50 điểm 09:00 |
| Bình | 50 điểm 09:10 | 50 điểm 09:10 |
| Chi | 40 điểm 07:00; 40 điểm 08:00 | 40 điểm 07:00 |
| Dũng | in_progress, chưa nộp | Không xuất hiện |
| Hà | 50 điểm 06:00 ở bộ khác | Không xuất hiện trong bộ đang xét |

Kết quả: An hạng 1, Bình hạng 2, Chi hạng 3. An chỉ có một dòng; tuyệt đối không hiển thị An đạt 50 điểm lúc 08:00.

Kiểm tra thêm:

1. Hai người cùng điểm và cùng `finished_at`: `user_id` nhỏ hơn đứng trước.
2. Một người có hai lượt cùng điểm/thời gian: lấy lượt `id` nhỏ hơn.
3. Có hơn 10 người: trang 2 bắt đầu từ hạng 11.
4. Làm lại được điểm thấp hơn: điểm đại diện không giảm.
5. Làm lại bằng điểm cao nhất: giữ thời điểm đạt điểm trước đó.
6. Làm lại được điểm cao hơn: chọn điểm mới và thời điểm của lượt mới, không giữ thời điểm của mức điểm cũ.
7. Không có lượt completed: hiển thị trạng thái rỗng, không phát sinh lỗi.

## 9. Thứ tự triển khai và mốc bàn giao

Đề xuất 12 buổi, mỗi buổi khoảng 3–4 giờ/người. Đây là khung tham khảo khoảng 36–48 giờ/người, chưa phải cam kết thời hạn; nhóm điều chỉnh theo kinh nghiệm và lỗi thực tế. Các buổi cùng hàng được làm song song sau khi đạt điều kiện đầu vào.

| Buổi | A | B | C | Mốc/điều kiện |
|---|---|---|---|---|
| 1 | Khởi tạo, cài môi trường | Phân tích luồng lượt làm, trường hợp lỗi | Phân tích nội dung, quy tắc xuất bản/xếp hạng | Cả nhóm chốt phạm vi, schema, route |
| 2 | Migration, auth, middleware, commit nền | Cài project trên máy riêng, model Attempt/Answer sau khi có schema | Cài project, model nội dung sau khi có schema | M1: cả ba chạy được nền và database |
| 3–4 | Layout, CRUD bài viết, tài khoản mẫu | Start/resume, trang quiz bằng dữ liệu thử | CRUD bộ/câu/lựa chọn, seed nội dung | M2: admin tạo được nội dung mẫu |
| 5–6 | Trang giới thiệu/thư viện/đọc, nối nút bắt đầu | Chấm điểm, transaction, nộp lặp | Xuất bản, gỡ xuất bản, khóa sửa nội dung đã dùng | M3: chạy trọn một lượt và lưu kết quả |
| 7–8 | Tổng quan admin, ghép route/menu, test A | Kết quả, lịch sử, làm lại, test B | Truy vấn/giao diện xếp hạng, test C | M4: đủ chức năng trong phạm vi |
| 9 | Rà soát module C | Rà soát module A | Rà soát module B | Phát hiện lỗi quyền/dữ liệu, không thêm tính năng |
| 10 | Sửa phần A, tích hợp | Sửa phần B | Sửa phần C | M5: kiểm thử trọng tâm và build đều qua |
| 11 | Cài sạch thử, hợp nhất README | Kịch bản demo người dùng, rà soát lịch sử | Kịch bản demo admin/xếp hạng, rà dữ liệu mẫu | M6: tái tạo được từ clone mới |
| 12 | Kiểm tra cài đặt và bàn giao | Demo quiz/chấm điểm | Demo nội dung/xếp hạng | M7: nghiệm thu nhóm |

### 9.1. Phụ thuộc cần nhớ

- B/C có thể viết thiết kế và giao diện phác thảo trước; không tự tạo schema khác khi A chưa chốt.
- A05 cần model nội dung C01 và hợp đồng bắt đầu B02.
- B02–B04 cần quy tắc xuất bản C04; B không tin chỉ riêng nút trên giao diện.
- C06 cần B chốt chính xác thời điểm ghi `completed`, `total_score`, `finished_at`.
- Seed nội dung C05 cần CRUD/quy tắc nội dung ổn định; không chờ xong giao diện mới chuẩn bị dữ liệu.
- Khi có thể, thống nhất route/model trước để tích hợp từng phần thay vì chờ đến buổi cuối.

## 10. Quy trình Git và phối hợp

1. A khởi tạo repository ứng dụng trên nền Git hiện có; nhánh tích hợp là `main`.
2. Tạo nhánh theo việc, ví dụ `codex/a-foundation`, `codex/b-quiz`, `codex/c-question-sets`, rồi các nhánh nhỏ tiếp theo.
3. Sau commit nền, B/C tạo nhánh từ cùng mốc; không gửi zip hoặc copy đè cả dự án để ghép.
4. Mỗi commit giải quyết một phần rõ ràng; không commit file phát sinh không liên quan.
5. Trước khi ghép: cập nhật từ `main`, kiểm tra lại module, ghi nội dung đổi/cách thử và nhờ người rà soát theo mục 2.
6. Tác giả xử lý conflict và lỗi module mình; A hỗ trợ file dùng chung và thực hiện tích hợp.
7. Kiểm tra sau mỗi lần merge lớn: migration trên database thử, kiểm thử liên quan và build giao diện nếu asset thay đổi.
8. Không force-push nhánh chung, không sửa lịch sử migration đã dùng, không đưa mật khẩu/API key lên Git.

Mỗi cuối buổi, mỗi người cập nhật ngắn: đã xong mã việc nào, còn việc gì, vướng ai và cần thống nhất điều gì. Không báo “xong” chỉ vì đã có giao diện.

## 11. Tiêu chí hoàn thành và nghiệm thu chung

Một hạng mục chỉ hoàn thành khi:

- Backend, Blade và route được nối đầy đủ; không còn dữ liệu giả thay cho nghiệp vụ cần có.
- Có validation, kiểm tra quyền, CSRF và thông báo lỗi phù hợp.
- Không làm lộ đáp án trước nộp hoặc dữ liệu riêng của người khác.
- Thử được luồng thành công, dữ liệu rỗng và ít nhất một trường hợp lỗi quan trọng.
- Kiểm thử tự động trọng tâm của module qua; kịch bản cần database thực được ghi và thử riêng.
- Không phá module khác; phần thay đổi có người rà soát.
- Tài liệu cài đặt hoặc sử dụng đã cập nhật nếu cần.

### 11.1. Checklist bàn giao cả dự án

- [ ] Clone/cài mới từ repository chạy được theo README.
- [ ] Có admin, user, 3 bộ published và 1 bộ draft từ seeder.
- [ ] Khách, user, admin bị giới hạn đúng quyền ở server.
- [ ] Admin tạo bài → tạo đủ câu → xuất bản thành công.
- [ ] Bộ chưa đủ điều kiện không được xuất bản.
- [ ] User đọc → bắt đầu → nộp → xem kết quả → xem lịch sử hoạt động.
- [ ] Điểm đúng/sai/bỏ trống chính xác; tối đa 50 điểm.
- [ ] Không tạo lượt đang làm trùng hoặc chấm lại khi gửi lặp.
- [ ] Không xem/nộp lượt của tài khoản khác.
- [ ] Bộ nháp không truy cập/làm mới được qua URL trực tiếp.
- [ ] Nội dung đã có lượt làm không bị sửa/xóa; gỡ xuất bản không làm mất kết quả.
- [ ] Xếp hạng chọn đúng lượt cao nhất/sớm nhất và mỗi người chỉ một dòng.
- [ ] Điểm bằng nhau ưu tiên thời điểm đạt điểm sớm hơn; phân trang tiếp nối hạng.
- [ ] Giao diện dùng được trên máy tính và điện thoại, không có liên kết hỏng.
- [ ] Kiểm thử trọng tâm và `npm run build` thành công.
- [ ] Không có `.env`, dependency hoặc dữ liệu nhạy cảm trong commit bàn giao.
- [ ] README, dữ liệu mẫu và kịch bản demo hoàn chỉnh.

## 12. Nội dung mỗi người phải giải thích khi demo

| Người | Nội dung giải thích | Thao tác demo |
|---|---|---|
| A | Cấu trúc MVC, migration, quan hệ database, Breeze, phân quyền và layout | Đăng ký, thử quyền user/admin, tạo bài, tìm bộ và đọc bài |
| B | Tạo lượt, validation đáp án, transaction, chấm điểm server, nộp lặp và quyền sở hữu | Làm đúng/sai/bỏ trống, nộp lại, xem lịch sử và làm lại |
| C | Quan hệ bộ–câu–lựa chọn, điều kiện xuất bản, bảo toàn dữ liệu và cách chọn lượt xếp hạng | Tạo câu, thử xuất bản thiếu câu, xuất bản hợp lệ, chứng minh bằng điểm thì người đạt sớm đứng trước |

Nguyên tắc cuối: ai phụ trách phần nào phải hiểu và giải thích được phần đó. Ưu tiên hoàn thành trọn luồng cơ bản trước khi xem xét mở rộng.
