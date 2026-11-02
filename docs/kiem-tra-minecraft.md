# Rà soát, xuất bản và kiểm tra bộ Minecraft

Ngày kiểm tra: 03/11/2026, giờ Việt Nam. Bộ local ID 8: **Minecraft — Tạo trực tiếp từ giao diện**.

## Rà soát nội dung

Đã đọc cả 5 câu, 20 lựa chọn, đáp án, giải thích và các đoạn tương ứng trong bài Minecraft đã lưu. Đối chiếu thêm các nguồn chính thức dưới đây.

| Câu | Đáp án được kiểm tra | Nguồn đối chiếu |
|---|---|---|
| 1 | Markus Persson (Notch) tạo Minecraft ban đầu | [Xbox Wire: lịch sử 15 năm](https://news.xbox.com/en-us/2024/05/17/minecraft-15-years-most-important-moments/) và bài đọc đã lưu |
| 2 | Công khai lần đầu vào tháng 5/2009, khác bản phát hành đầy đủ năm 2011 | [Xbox Wire: các mốc phát triển](https://news.xbox.com/en-us/2024/05/17/minecraft-15-years-most-important-moments/) |
| 3 | Microsoft mua Mojang và Minecraft năm 2014 với giá 2,5 tỷ USD | [Thông cáo Microsoft](https://news.microsoft.com/source/2014/09/15/minecraft-to-join-microsoft) |
| 4 | Chu kỳ ngày đêm mặc định dài 20 phút thực | [Tài liệu Minecraft Education, trang 18](https://education.minecraft.net/lessonsupportfiles/8325548264145603-Coding%20with%20Minecraft_Unit%206_Functions.pdf) |
| 5 | Redstone dùng cho mạch điều khiển và thiết bị/cửa tự động | [Minecraft: Researching Redstone](https://www.minecraft.net/en-us/creator/article/redstone) |

Trước khi xuất bản, chỉnh cách diễn đạt ở 3 câu thông qua form admin qua HTTP:

- Câu 3: đổi “IP” thành “quyền sở hữu trí tuệ” để dễ hiểu.
- Câu 4: ghi rõ tốc độ trò chơi mặc định và thế giới Overworld, tránh hiểu là mọi thiết lập tùy biến đều có cùng chu kỳ.
- Câu 5: đổi “cánh cổng tự động” thành “cửa tự động”, tránh nhầm với cổng Nether bằng obsidian.

Không đổi đáp án đúng hoặc nội dung kiến thức chính. Sau chỉnh sửa, bộ vẫn đủ 5 câu, 4 lựa chọn/câu, 1 đáp án đúng/câu, có giải thích; `isReady()` true. Bộ chưa có lượt làm tại thời điểm biên tập.

Đã đăng nhập admin, gửi yêu cầu xuất bản kèm CSRF/session qua route của ứng dụng. Trạng thái sau thao tác: **published**.

## Làm bài bằng hai tài khoản user

Hai tài khoản đã có, đều role user: `user@oceanquiz.local` và `user2@oceanquiz.local`. Không tạo thêm tài khoản và không đổi mật khẩu.

Mỗi tài khoản sử dụng cookie/session HTTP riêng. Các lượt đều đi qua trang đọc, route bắt đầu, trang quiz, route nộp và trang kết quả. Server tự tính điểm và thời gian; không ghi trực tiếp điểm hoặc finished_at vào database.

| Lượt local | Tài khoản | Đáp án thử | Điểm nhận được | Hoàn thành 03/11/2026, giờ VN |
|---|---|---|---|---|
| 9 | user2 | 4 đúng, 1 sai | 40 | 01:02:00.859470 |
| 10 | user | 3 đúng, 1 sai, 1 bỏ trống | 30 | 01:02:01.740150 |
| 11 | user | 5 đúng | 50 | 01:02:02.840520 |
| 12 | user2 | 5 đúng | 50 | 01:02:05.076482 |
| 13 | user | Làm lại: 3 đúng, 1 sai, 1 bỏ trống | 30 | 01:02:06.095546 |
| 14 | user2 | Làm lại: 5 đúng | 50 | 01:02:07.131799 |

Mỗi lượt có đúng 5 dòng trả lời; các câu bỏ trống có option_id null. Điểm và số câu đúng trên trang kết quả khớp database. Trang quiz hiển thị 20 lựa chọn và không đưa metadata is_correct ra HTML.

## Kết quả xếp hạng

Đối chiếu HTML bảng xếp hạng của riêng bộ ID 8 sau từng giai đoạn:

1. Khi user2 có 40 và user có 30: user2 đứng trên.
2. Khi user đạt 50: user đứng trên user2 đang có 40.
3. Khi cả hai cùng 50: user đứng hạng 1 vì đạt mức 50 lúc 01:02:02; user2 hạng 2 vì đạt mức 50 lúc 01:02:05.
4. Lượt 40 sớm hơn của user2 không bị dùng nhầm làm thời điểm đạt 50.
5. User làm lại được 30: giữ mức 50 và thời điểm của lượt 11.
6. User2 làm lại bằng 50: giữ thời điểm đạt 50 đầu tiên của lượt 12, không lấy lượt 14 mới hơn.
7. Gửi lại lượt 12 với đáp án trống: vẫn 50 điểm, finished_at không đổi, không sinh thêm câu trả lời.
8. Bảng luôn chỉ có một dòng cho mỗi người.

| Hạng cuối | Tài khoản | Điểm cao nhất | Thời điểm đạt điểm, giờ VN |
|---|---|---|---|
| 1 | user@oceanquiz.local | 50 | 03/11/2026 01:02:02 |
| 2 | user2@oceanquiz.local | 50 | 03/11/2026 01:02:05 |

Đã mở thêm bảng xếp hạng trên trình duyệt và xác nhận hai dòng hiển thị đúng.

## Bàn giao

- Đọc và làm bài local: `http://127.0.0.1:8001/question-sets/8/read`.
- Bảng xếp hạng local: `http://127.0.0.1:8001/leaderboard?question_set_id=8`.
- Kết quả thử chi tiết local: `storage/app/qa/minecraft-ranking-result.json`.
- Dữ liệu xuất bản, câu hỏi đã chỉnh và các lượt làm nằm trong database local; Git chỉ lưu biên bản, không tự đồng bộ database hoặc lịch sử sang máy khác.
- Không thay đổi mã chấm điểm hoặc truy vấn xếp hạng vì các trường hợp kiểm tra đều đạt.
