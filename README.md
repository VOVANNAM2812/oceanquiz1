# OceanQuiz

Website học kiến thức sinh vật biển bằng Laravel 12, PHP 8.2, Breeze, Blade và MySQL/MariaDB. Phát triển theo vai A/B/C; mỗi chức năng có commit riêng trên `main`.

## Cài đặt local

Yêu cầu PHP 8.2+, Composer, Node.js/npm và MySQL/MariaDB (XAMPP). PHP cần PDO MySQL, mbstring, openssl, curl, fileinfo; SQLite không bắt buộc.

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
npm run build
```

Tạo database `oceanquiz1` dùng `utf8mb4`, sửa `DB_*` trong `.env` cho máy của bạn. Khởi động MySQL trong XAMPP trước khi migrate.

```powershell
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8001
```

Mở http://127.0.0.1:8001. Dùng `npm run dev` nếu muốn phát triển giao diện với Vite. Khi dùng Apache, document root phải là `public/`.

## Kiểm thử

Tạo database riêng `oceanquiz1_test` với cùng tài khoản DB trong `.env`, sau đó chạy `php artisan test`. PHPUnit dùng database này, không dùng database ứng dụng. Chỉ chạy trên database thử có thể xóa; `RefreshDatabase` sẽ khởi tạo lại bảng của database test.

## Cấu hình tích hợp

Đặt `GEMINI_API_KEY`, `GEMINI_MODEL` và `WIKI_USER_AGENT` trong `.env`. Không commit `.env` hoặc API key. Khi chưa có key Gemini, chức năng nhập thủ công vẫn hoạt động.

## Quy tắc nghiệp vụ

- Mỗi bộ có 5 câu, mỗi câu 4 lựa chọn và 1 đáp án đúng. Đúng 10 điểm, sai/bỏ trống 0; tối đa 50.
- Xếp hạng theo bộ: điểm cao nhất mỗi người; cùng mức điểm chọn lượt hoàn thành sớm nhất; sắp theo điểm giảm dần, thời điểm tăng dần, user ID tăng dần. Hạng liên tiếp, không đồng hạng.
- Thời gian lưu UTC, hiển thị theo giờ Việt Nam.

Xem [phân chia công việc.md](phân%20chia%20công%20việc.md) để biết kế hoạch chi tiết.
