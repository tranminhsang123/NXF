# Phân tích dự án Laravel – Japanese Study

*Phân tích dựa hoàn toàn trên source code hiện có. Không suy đoán.*

---

## 1. Danh sách module/tính năng

| # | Module | Mô tả ngắn (1–2 dòng) |
|---|--------|------------------------|
| 1 | **Auth (Đăng nhập/Đăng ký)** | Đăng nhập, đăng ký, đăng xuất. Session-based. Admin redirect về admin dashboard. |
| 2 | **Home/Landing** | Trang chủ public; có sections: hero, features, learning-path, testimonials, cta. |
| 3 | **User Dashboard** | Tổng quan tiến độ Minna, bài tiếp tục, mục tiêu ngày, streak, % hoàn thành. |
| 4 | **User Progress** | Danh sách tiến độ Minna (đang học/đã hoàn thành) theo user. |
| 5 | **User Statistics** | Thống kê: bài hoàn thành theo ngày/tuần, tổng bài và ước tính từ vựng. |
| 6 | **Alphabet (User)** | Xem bảng chữ: Hiragana, Katakana, Romaji, Kanji N5/N4/N3 (trang `/alphabet`). |
| 7 | **Kanji (User)** | Chọn cấp N5–N1, danh sách Kanji theo cấp, flashcard Kanji theo cấp. |
| 8 | **Từ vựng (Vocabulary)** | Danh sách bài có từ vựng; xem từ vựng theo bài (lấy từ Minna section `tu-vung`). |
| 9 | **Flashcard (từ vựng)** | Danh sách bài có flashcard; học flashcard 1 bài hoặc nhiều bài (shuffle/reverse). |
| 10 | **Course (Khóa học JLPT)** | Trang tổng N5–N1; theo level: metadata + N5 có sections (Speed Master, Luyện đọc, Marugoto) + list/detail từng loại. |
| 11 | **Minna no Nihongo** | Danh sách bài; chi tiết bài (từ vựng, ngữ pháp, luyện đọc, hội thoại, hán tự); đánh dấu hoàn thành (auth). |
| 12 | **Admin Dashboard** | Thống kê: users, alphabets, kanjis, minna lessons, n5_course_data; 5 user gần nhất. |
| 13 | **Admin Notifications** | Danh sách thông báo; đánh dấu đọc 1 / đọc tất cả. Trigger: user mới đăng ký. |
| 14 | **Admin Alphabets** | CRUD bảng chữ (Hiragana/Katakana/Romaji): index, create, store, edit, update, destroy. |
| 15 | **Admin Kanjis** | CRUD Kanji (character, meaning, on/kun reading, level, stroke_count): index, create, store, show, edit, update, destroy. |
| 16 | **Admin Minna** | CRUD bài Minna; thêm 5 section mặc định; sửa từng section (từ vựng, ngữ pháp, luyện đọc, hội thoại, hán tự). |
| 17 | **Admin Course Data (N5)** | CRUD dữ liệu khóa N5 (section_type, section_key, bai, title, content, order); editor theo loại (luyen-doc, marugoto, ngu-phap, words, doc-hieu, no-editor). |
| 18 | **Admin Users** | Danh sách user (filter role, search); edit, update, destroy (không xóa chính mình). Không có create/store. |

---

## 2. Phân loại

### Core feature (tính năng chính cho người học)

- **Auth** – Đăng nhập/đăng ký/logout  
- **User Dashboard** – Trang chủ sau khi đăng nhập  
- **User Progress** – Theo dõi tiến độ  
- **User Statistics** – Thống kê học  
- **Alphabet (User)** – Học bảng chữ  
- **Kanji (User)** – Học Kanji theo cấp + flashcard  
- **Từ vựng (Vocabulary)** – Xem từ vựng theo bài  
- **Flashcard** – Ôn từ vựng bằng flashcard  
- **Course** – Khóa học theo level (N5 có nội dung chi tiết)  
- **Minna no Nihongo** – Học theo giáo trình Minna (bài + sections + hoàn thành)  

### Supporting feature (hỗ trợ)

- **Home/Landing** – Trang chủ public  
- **User Progress Service** – Cập nhật/đánh dấu tiến độ Minna  
- **Notification (model + createForAdmins)** – Thông báo cho admin (ví dụ user mới đăng ký)  

### Admin feature (quản trị)

- **Admin Dashboard**  
- **Admin Notifications**  
- **Admin Alphabets**  
- **Admin Kanjis**  
- **Admin Minna** (+ MinnaSection edit/update)  
- **Admin Course Data**  
- **Admin Users**  

---

## 3. Mức độ hoàn thiện

| Module | Mức độ | Ghi chú (dựa trên code) |
|--------|--------|---------------------------|
| Auth | **Hoàn chỉnh** | Login, register, logout có; có redirect admin; không có forgot/reset password (có bảng `password_reset_tokens` nhưng không có route/controller). |
| Home | **Hoàn chỉnh** | View + sections đủ. |
| User Dashboard | **Hoàn chỉnh** | Logic Minna + streak + daily goal + resume. |
| User Progress | **Hoàn chỉnh** | List progress + view. |
| User Statistics | **Hoàn chỉnh** | By day, by week, summary; StatisticsService + FlashcardService. |
| Alphabet (User) | **Hoàn chỉnh** | AlphabetService, view alphabet. |
| Kanji (User) | **Hoàn chỉnh** | Index, list, flashcard; KanjiService. |
| Vocabulary | **Hoàn chỉnh** | Index + show theo bài từ Minna. |
| Flashcard | **Hoàn chỉnh** | Index + study (1/nhiều bài, shuffle, reverse); FlashcardService. |
| Course | **Hoàn chỉnh** | N5–N1 metadata; N5: Speed Master, Luyện đọc, Marugoto (list + detail). N4–N1 chỉ metadata, không có section data. |
| Minna | **Hoàn chỉnh** | Index, show, section, complete; MinnaService, UserProgressService. |
| Admin Dashboard | **Hoàn chỉnh** | Stats + view. |
| Admin Notifications | **Hoàn chỉnh** | Index, mark read, mark all read; model Notification. |
| Admin Alphabets | **Hoàn chỉnh** | Full CRUD (không có show); Form Requests. |
| Admin Kanjis | **Hoàn chỉnh** | Full CRUD + show; Form Requests. |
| Admin Minna | **Hoàn chỉnh** | CRUD + addSections + section edit/update; editor theo key. |
| Admin Course Data | **Hoàn chỉnh** | CRUD + editor theo section_type. |
| Admin Users | **Cần cải thiện** | Có index, edit, update, destroy; không có create/store (except trong route). |
| Forgot/Reset Password | **Thiếu** | Có migration `password_reset_tokens`, không có route/controller/view. |
| API | **Thiếu / tối thiểu** | Chỉ `GET /api/user` (auth:sanctum). |

---

## 4. File / thư mục liên quan

| Module | File / thư mục chính |
|--------|----------------------|
| **Auth** | `app/Http/Controllers/AuthController.php`, `resources/views/auth/login.blade.php`, `resources/views/auth/register.blade.php`, `routes/web.php` (guest + auth). |
| **Home** | `routes/web.php` (GET `/`), `resources/views/home.blade.php`, `resources/views/sections/*` (hero, features, learning-path, testimonials, cta). |
| **User Dashboard** | `app/Http/Controllers/UserController.php` (dashboard), `resources/views/user/dashboard.blade.php`. |
| **User Progress** | `UserController::progress`, `resources/views/user/progress.blade.php`, `app/Models/UserProgress.php`, `app/Services/UserProgressService.php`. |
| **User Statistics** | `UserController::statistics`, `resources/views/user/statistics.blade.php`, `app/Services/StatisticsService.php`. |
| **Alphabet (User)** | `app/Http/Controllers/UserAlphabetController.php`, `app/Services/AlphabetService.php`, `resources/views/user/alphabet/alphabet.blade.php`. |
| **Kanji (User)** | `app/Http/Controllers/UserKanjiController.php`, `app/Services/KanjiService.php`, `resources/views/user/kanji/index.blade.php`, `list.blade.php`, `flashcard.blade.php`. |
| **Vocabulary** | `app/Http/Controllers/VocabularyController.php`, `app/Services/FlashcardService.php`, `resources/views/vocabulary/index.blade.php`, `show.blade.php`. |
| **Flashcard** | `app/Http/Controllers/FlashcardController.php`, `FlashcardService`, `resources/views/flashcard/index.blade.php`, `study.blade.php`. |
| **Course** | `app/Http/Controllers/CourseController.php`, `app/Services/CourseService.php`, `resources/views/course/index.blade.php`, `show.blade.php`, `section.blade.php`, `resources/views/course/sections/*` (luyen_doc_*, marugoto_n5_*, speed_master_n5_*). |
| **Minna** | `app/Http/Controllers/MinnaController.php`, `app/Services/MinnaService.php`, `app/Models/MinnaLesson.php`, `app/Models/MinnaSection.php`, `resources/views/minna/index.blade.php`, `show.blade.php`, `section.blade.php`, `resources/views/minna/sections/*`. |
| **Admin (chung)** | `routes/web.php` (prefix `admin`, middleware `auth` + `admin`), `app/Http/Middleware/AdminMiddleware.php`, `resources/views/adminlayout/*`, `resources/views/admin/*`. |
| **Admin Dashboard** | `app/Http/Controllers/Admin/DashboardController.php`, `resources/views/admin/dashboard.blade.php`. |
| **Admin Notifications** | `app/Http/Controllers/Admin/NotificationController.php`, `app/Models/Notification.php`, `app/Models/NotificationRead.php`, `resources/views/admin/notifications/index.blade.php`. |
| **Admin Alphabets** | `app/Http/Controllers/AlphabetController.php` (trong admin prefix), `app/Services/AlphabetService.php`, `app/Http/Requests/StoreAlphabetRequest.php`, `UpdateAlphabetRequest.php`, `app/Models/Alphabet.php`, `resources/views/admin/alphabets/*`. |
| **Admin Kanjis** | `app/Http/Controllers/Admin/KanjiController.php`, `KanjiService`, `StoreKanjiRequest`, `UpdateKanjiRequest`, `app/Models/Kanji.php`, `resources/views/admin/kanjis/*`. |
| **Admin Minna** | `app/Http/Controllers/Admin/MinnaController.php`, `app/Http/Controllers/Admin/MinnaSectionController.php`, `resources/views/admin/minna/*`, `resources/views/admin/minna/editors/*` (tu-vung, ngu-phap, luyen-doc, hoi-thoai, han-tu, json-fallback). |
| **Admin Course Data** | `app/Http/Controllers/Admin/CourseDataController.php`, `app/Models/N5CourseData.php`, `resources/views/admin/course-data/*`, `resources/views/admin/course-data/editors/*`. |
| **Admin Users** | `app/Http/Controllers/Admin/UserController.php`, `resources/views/admin/users/index.blade.php`, `edit.blade.php`. |
| **Services** | `app/Services/AlphabetService.php`, `CourseService.php`, `FlashcardService.php`, `KanjiService.php`, `MinnaService.php`, `StatisticsService.php`, `UserProgressService.php`. |
| **Migrations** | `database/migrations/` (users, role, alphabets, kanjis, minna_lessons, minna_sections, n5_course_data, user_progresses, notifications, notification_reads, password_reset_tokens, …). |

---

## 5. Tóm tắt

- **Core:** 10 module (Auth, Dashboard, Progress, Statistics, Alphabet, Kanji, Vocabulary, Flashcard, Course, Minna) – đều có controller, service (khi cần), view và route tương ứng.
- **Supporting:** Home + Progress/Notification hỗ trợ trải nghiệm và thông báo admin.
- **Admin:** 7 nhóm tính năng (Dashboard, Notifications, Alphabets, Kanjis, Minna, Course Data, Users); phần lớn CRUD đầy đủ.
- **Thiếu / cần cải thiện:** (1) Quên mật khẩu / đặt lại mật khẩu (có bảng, không có luồng). (2) Admin Users không có trang tạo user. (3) API chỉ có endpoint user (Sanctum).

*Tài liệu này chỉ phản ánh cấu trúc và hành vi thực tế trong code.*

---

## 6. Tính năng cần TỐI ƯU và NÂNG CẤP

### 6.1. NÂNG CẤP (bổ sung tính năng còn thiếu)

| # | Tính năng | Mô tả | Độ ưu tiên |
|---|-----------|------|------------|
| 1 | **Quên mật khẩu / Đặt lại mật khẩu** | Có bảng `password_reset_tokens`, chưa có route/controller/view. Cần: form quên mật khẩu, gửi email link reset, form đặt lại mật khẩu, xử lý token. | Cao |
| 2 | **Admin tạo User** | Admin Users hiện `except(['create','store'])`. Cần thêm trang tạo user (create/store) và Form Request validation. | Trung bình |
| 3 | **Rate limit cho Auth (web)** | Login/Register chưa throttle; dễ brute force. Nên thêm `throttle` middleware cho nhóm route login/register (vd: 5 lần/phút). | Cao |
| 4 | **Chính sách mật khẩu** | Register chỉ `min:6`. Nên nâng: độ dài, chữ hoa/thường, số, ký tự đặc biệt (config + validation). | Trung bình |
| 5 | **Xác thực email (optional)** | Chưa có verify email. Có thể thêm `MustVerifyEmail`, queue mail, link xác nhận. | Thấp |
| 6 | **API mở rộng (nếu cần mobile/app)** | Hiện chỉ `GET /api/user`. Nếu cần app: thêm API cho courses, minna, progress, flashcard (auth:sanctum). | Tùy nhu cầu |

---

### 6.2. TỐI ƯU (hiệu năng, trải nghiệm, bảo trì)

| # | Khu vực | Việc cần làm | File / ghi chú |
|---|---------|---------------|----------------|
| 1 | **User Dashboard** | Gộp/nhẹ hóa truy vấn: nhiều query riêng (Minna count, progress, streak, Kanji::count). Cân nhắc cache ngắn hạn cho `totalMinnaLessons`, `totalKanjis` hoặc dùng 1–2 query tổng hợp. | `UserController::dashboard` |
| 2 | **User Progress** | `MinnaLesson::whereIn(...)->get()->keyBy('id')` — đã dùng whereIn, ổn. Có thể thêm `withCount` nếu sau này cần. | `UserController::progress` |
| 3 | **Statistics** | `getLessonsCompletedByDay`: `$start->startOfDay()` mutate Carbon — kiểm tra lại biên ngày đầu. `getLessonsCompletedByWeek`: YEARWEEK MySQL — đổi sang ISO week nếu dùng DB khác. | `StatisticsService` |
| 4 | **Alphabet (user)** | Trang `/alphabet`: 2 query full (Alphabet by types + Kanji by levels), không cache. Khi dữ liệu lớn: cache theo type/level hoặc pagination/lazy load. | `AlphabetService`, `UserAlphabetController` |
| 5 | **Flashcard** | Đã cache `flashcard:lessons` (600s), invalidate khi cập nhật section. Có thể cho user tùy chọn TTL hoặc cache per-user nếu có nhiều nội dung riêng. | `FlashcardService` |
| 6 | **Admin Dashboard** | Nhiều `Model::count()` + `User::latest()->take(5)`. Có thể cache stats 1–5 phút; recent_users giữ realtime. | `Admin\DashboardController` |
| 7 | **Index DB** | `user_progresses`: truy vấn dashboard hay dùng `user_id`, `lesson_type`, `last_accessed_at`, `completed_at`. Cân nhắc composite index `(user_id, lesson_type, last_accessed_at)` hoặc `(user_id, completed_at)`. | Migration mới |
| 8 | **Admin list pages** | Alphabets, Kanjis, Minna, Course Data, Users đều paginate(20). Có thể thêm tùy chọn per_page (10/20/50) qua query. | Các controller admin |
| 9 | **Form Request** | Admin Minna (store/update), Admin CourseData (store/update) đang validate trong controller. Nên tách sang Form Request để tái sử dụng và rõ ràng. | `Admin\MinnaController`, `Admin\CourseDataController` |
| 10 | **AlphabetController namespace** | AlphabetController nằm ngoài `Admin\`, dùng trong admin prefix. Đồng nhất: chuyển vào `Admin\AlphabetController` hoặc giữ và ghi chú rõ. | `app/Http/Controllers` |
| 11 | **Course N4–N1** | Course chỉ có dữ liệu section cho N5; N4–N1 chỉ metadata. Nếu sau này thêm dữ liệu: mở rộng CourseService/CourseData theo level. | `CourseService`, `N5CourseData` (có thể đổi tên mở rộng) |
| 12 | **Frontend (JS/CSS)** | View dùng CDN Tailwind. Nên build Tailwind local + purge để giảm dung lượng và kiểm soát version. | `resources/views`, build pipeline |

---

### 6.3. Tóm tắt ưu tiên

- **Làm trước (bảo mật + trải nghiệm):** Quên mật khẩu, Rate limit Auth, (tùy chọn) chính sách mật khẩu.
- **Làm khi cần:** Admin tạo User, Index `user_progresses`, tách Form Request admin.
- **Làm khi scale:** Cache dashboard/stats, cache Alphabet trang user, tối ưu StatisticsService, per_page admin.
