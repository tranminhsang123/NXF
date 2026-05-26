# Cấu trúc tính năng Admin

Tài liệu này mô tả phần admin hiện có trong dự án `japanese-study`: admin có những module nào, mỗi module quản lý dữ liệu gì, đi qua route/controller/view nào, và đang được bảo vệ bởi lớp quyền nào.

## 1. Tổng quan admin

Admin panel nằm dưới prefix `/admin`, dùng Blade view và layout riêng:

- Route web chính: `routes/web.php`, nhóm `Route::prefix('admin')->name('admin.')`.
- Layout admin: `resources/views/adminlayout/app.blade.php`.
- Sidebar admin: `resources/views/adminlayout/sidebar.blade.php`.
- Header admin: `resources/views/adminlayout/header.blade.php`.
- Controller admin: `app/Http/Controllers/Admin/*`.
- View admin: `resources/views/admin/*`.
- API admin cho mobile/tooling: `routes/api.php`, nhóm `/api/admin`.

Toàn bộ route web admin đang đi qua middleware:

- `auth`: bắt buộc đăng nhập.
- `admin`: chỉ user có `role = admin`.
- `admin.route.permission`: kiểm tra RBAC theo route name.
- `throttle:admin`: giới hạn tần suất request admin.

## 2. Nhóm tính năng chính

### 2.1 Dashboard tổng quan

Mục đích:

- Hiển thị thống kê nhanh cho quản trị.
- Theo dõi tổng user, alphabet, kanji, bài Minna, dữ liệu khóa học N5.
- Hiển thị user mới gần đây.
- Thống kê số kanji theo level.

File chính:

- Route: `admin.dashboard`
- Controller: `App\Http\Controllers\Admin\DashboardController`
- View: `resources/views/admin/dashboard.blade.php`
- Cache key: `admin:dashboard:stats`
- Permission: `dashboard.view`

Trạng thái: đã có dashboard cơ bản, có cache 5 phút.

### 2.2 Thông báo admin

Mục đích:

- Xem danh sách thông báo dành cho admin.
- Đánh dấu một thông báo đã đọc.
- Đánh dấu tất cả đã đọc.
- Header admin có badge thông báo.

File chính:

- Routes: `admin.notifications.index`, `admin.notifications.mark-read`, `admin.notifications.mark-all-read`
- Controller: `App\Http\Controllers\Admin\NotificationController`
- View: `resources/views/admin/notifications/index.blade.php`
- Models: `Notification`, `NotificationRead`
- Permission: `notifications.view`

Trạng thái: đã có phần đọc/thao tác thông báo.

### 2.3 Inbox 1-1 giữa admin và user

Mục đích:

- Admin xem danh sách hội thoại trực tiếp với user.
- Admin mở hội thoại theo user.
- Tải tin nhắn theo conversation.
- Admin gửi tin nhắn hỗ trợ.
- Sidebar/header có unread badge.

File chính:

- Routes: `admin.inbox.*`
- Controller: `App\Http\Controllers\Admin\DirectInboxController`
- Views:
  - `resources/views/admin/inbox/index.blade.php`
  - `resources/views/admin/inbox/show.blade.php`
- Models: `DirectConversation`, `DirectMessage`, `User`
- Permissions:
  - `inbox.view`
  - `inbox.reply`

Trạng thái: đã có inbox hỗ trợ 1-1, có endpoint unread count.

### 2.4 Quản lý bảng chữ cái

Mục đích:

- CRUD dữ liệu alphabet Hiragana, Katakana, Romaji.
- Quản lý ký tự, romaji, loại, mô tả, stroke order, độ khó, thứ tự hiển thị.
- Dữ liệu này cấp cho trang học bảng chữ cái phía user.

File chính:

- Resource route: `admin.alphabets.*`
- Controller: `App\Http\Controllers\Admin\AlphabetController`
- Views:
  - `resources/views/admin/alphabets/index.blade.php`
  - `resources/views/admin/alphabets/create.blade.php`
  - `resources/views/admin/alphabets/edit.blade.php`
- Model: `Alphabet`
- Observer: `AlphabetObserver`
- Permissions:
  - `alphabets.view`
  - `alphabets.edit`
  - `alphabets.delete`

Trạng thái: đã có CRUD đầy đủ.

### 2.5 Quản lý Kanji

Mục đích:

- CRUD Kanji theo level.
- Quản lý nghĩa, âm On/Kun, số nét, bộ thủ, ví dụ.
- Dữ liệu này dùng cho trang Kanji, flashcard Kanji và phần luyện viết tay phía user.

File chính:

- Resource route: `admin.kanjis.*`
- Controller: `App\Http\Controllers\Admin\KanjiController`
- Views:
  - `resources/views/admin/kanjis/index.blade.php`
  - `resources/views/admin/kanjis/create.blade.php`
  - `resources/views/admin/kanjis/edit.blade.php`
  - `resources/views/admin/kanjis/show.blade.php`
- Model: `Kanji`
- Observer: `KanjiObserver`
- Permissions:
  - `kanjis.view`
  - `kanjis.edit`
  - `kanjis.delete`

Trạng thái: đã có CRUD đầy đủ.

### 2.6 Quản lý Minna no Nihongo

Mục đích:

- CRUD bài Minna.
- Xem chi tiết một bài và danh sách section.
- Tự thêm bộ section chuẩn cho bài.
- Sửa từng section bằng editor riêng theo loại nội dung.

Các section/editor đã có:

- `tu-vung`: từ vựng.
- `ngu-phap`: ngữ pháp.
- `hoi-thoai`: hội thoại.
- `luyen-doc`: luyện đọc.
- `han-tu`: Hán tự.
- `json-fallback`: fallback cho content chưa có editor riêng.

File chính:

- Resource route: `admin.minna.*`
- Extra routes:
  - `admin.minna.add-sections`
  - `admin.minna-section.edit`
  - `admin.minna-section.update`
- Controllers:
  - `App\Http\Controllers\Admin\MinnaController`
  - `App\Http\Controllers\Admin\MinnaSectionController`
- Views:
  - `resources/views/admin/minna/index.blade.php`
  - `resources/views/admin/minna/create.blade.php`
  - `resources/views/admin/minna/edit.blade.php`
  - `resources/views/admin/minna/show.blade.php`
  - `resources/views/admin/minna/section-edit.blade.php`
  - `resources/views/admin/minna/editors/*`
- Models: `MinnaLesson`, `MinnaSection`
- Observers: `MinnaLessonObserver`, `MinnaSectionObserver`
- Permissions:
  - `minna.view`
  - `minna.edit`
  - `minna.delete`

Trạng thái: đã có CRUD bài học và editor section theo loại.

### 2.7 Quản lý dữ liệu khóa học JLPT/N5

Mục đích:

- CRUD dữ liệu khóa học JLPT/N5.
- Hỗ trợ nhiều dạng section học như từ vựng, ngữ pháp, đọc hiểu, luyện đọc, Marugoto.
- Duplicate dữ liệu khóa học để tạo bài mới nhanh.

File chính:

- Resource route: `admin.course-data.*`
- Extra route: `admin.course-data.duplicate`
- Controller: `App\Http\Controllers\Admin\CourseDataController`
- Views:
  - `resources/views/admin/course-data/index.blade.php`
  - `resources/views/admin/course-data/create.blade.php`
  - `resources/views/admin/course-data/edit.blade.php`
  - `resources/views/admin/course-data/editors/*`
- Model: `N5CourseData`
- Observer: `N5CourseDataObserver`
- Permissions:
  - `course_data.view`
  - `course_data.edit`
  - `course_data.delete`

Trạng thái: đã có CRUD và duplicate.

### 2.8 Quản lý chat group và duyệt tham gia

Mục đích:

- Admin tạo/sửa/xóa nhóm chat.
- Xem thành viên, tin nhắn và yêu cầu tham gia.
- Duyệt hoặc từ chối yêu cầu vào nhóm.

File chính:

- Routes: `admin.chat.groups.*`
- Controller: `App\Http\Controllers\Admin\ChatGroupAdminController`
- Views:
  - `resources/views/admin/chat/groups/index.blade.php`
  - `resources/views/admin/chat/groups/create.blade.php`
  - `resources/views/admin/chat/groups/edit.blade.php`
  - `resources/views/admin/chat/groups/show.blade.php`
- Models: `ChatGroup`, `ChatGroupMember`, `ChatJoinRequest`, `ChatMessage`
- Permissions:
  - `chat_groups.view`
  - `chat_groups.edit`
  - `chat_groups.delete`
  - `chat_groups.moderate`

Trạng thái: đã có quản lý group và moderation join request.

### 2.9 Quản lý users và RBAC admin

Mục đích:

- Xem danh sách user.
- Lọc theo role, trạng thái khóa, ngày tạo, search name/email.
- Sửa name, email, role.
- Khóa/mở khóa tài khoản.
- Xóa user.
- Gán vai trò RBAC cho admin.

File chính:

- Resource route: `admin.users.*`, trừ `create`, `store`.
- Extra routes:
  - `admin.users.lock`
  - `admin.users.unlock`
  - `admin.users.admin-roles.update`
- Controller: `App\Http\Controllers\Admin\UserController`
- Views:
  - `resources/views/admin/users/index.blade.php`
  - `resources/views/admin/users/edit.blade.php`
- Models: `User`, `AdminRole`, `AdminPermission`, `SystemLog`
- Permissions:
  - `users.view`
  - `users.edit`
  - `users.delete`
  - `users.lock`
  - `users.assign_roles`

Trạng thái: đã có user management và RBAC theo role/permission.

### 2.10 Bảo mật, DevTools và khóa tính năng

Mục đích:

- Xem log vi phạm DevTools.
- Bật/tắt ghi log DevTools.
- Cấu hình số lần vi phạm trước khi khóa tài khoản.
- Cấu hình thời gian window tính vi phạm.
- Cấu hình auto unlock.
- Cấu hình thông báo khóa.
- Khóa/mở quyền truy cập của guest cho các feature học.

Feature lock hiện hỗ trợ:

- `alphabet`
- `kanji`
- `flashcard`
- `vocabulary`
- `course`
- `minna`

File chính:

- Routes: `admin.security.index`, `admin.security.update`
- Controller: `App\Http\Controllers\Admin\SecurityController`
- View: `resources/views/admin/security/index.blade.php`
- Models: `SecuritySetting`, `DevtoolsViolation`
- Related middleware: `FeatureAccessMiddleware`
- Permissions:
  - `security.view`
  - `security.edit`

Trạng thái: đã có security setting và feature lock cho guest.

### 2.11 Cài đặt logo/site identity

Mục đích:

- Quản lý logo, title, subtitle hiển thị ở layout user/admin.
- Tạo/cập nhật/xóa logo setting.

File chính:

- Routes: `admin.logo-settings.*`
- Controller: `App\Http\Controllers\Admin\LogoSettingController`
- View: `resources/views/admin/logo-settings/index.blade.php`
- Model: `LogoSetting`
- View composer: `AppServiceProvider`
- Permissions:
  - `settings.view`
  - `settings.edit`

Trạng thái: đã có cài đặt nhận diện cơ bản.

### 2.12 Log hệ thống

Mục đích:

- Xem log hệ thống.
- Lọc log theo type.
- Theo dõi sự kiện như khóa/mở user.

File chính:

- Route: `admin.system-logs.index`
- Controller: `App\Http\Controllers\Admin\SystemLogController`
- View: `resources/views/admin/system-logs/index.blade.php`
- Model: `SystemLog`
- Permission: `system_logs.view`

Trạng thái: đã có trang xem log.

## 3. RBAC admin

RBAC admin gồm 4 phần:

- Bảng quyền: `admin_permissions`.
- Bảng vai trò: `admin_roles`.
- Pivot quyền-vai trò: `admin_permission_role`.
- Pivot user-vai trò: `admin_role_user`.

File chính:

- Migration: `database/migrations/2026_05_13_120000_create_admin_rbac_tables.php`
- Seeder: `database/seeders/AdminRbacSeeder.php`
- Models:
  - `AdminPermission`
  - `AdminRole`
  - `User::adminRoles()`
  - `User::hasAdminPermission()`
- Middleware: `App\Http\Middleware\EnsureAdminRoutePermission`
- Config map route quyền: `config/admin_route_permissions.php`
- Blade directive: `@adminCan(...)` trong `AppServiceProvider`

Vai trò seed sẵn:

- `super_admin`: có toàn bộ quyền.
- `content_editor`: quản lý nội dung học.
- `moderator`: chat, inbox, users view.
- `support_staff`: dashboard, notification, inbox.

Lưu ý tương thích: admin chưa được gán role nào sẽ tạm có full quyền để không khóa hệ thống sau migrate. Khi đã gán role, quyền được kiểm tra theo permission.

## 4. Admin API

API admin nằm trong `/api/admin`, yêu cầu `auth:sanctum` và user `role = admin`.

Endpoint đã có:

- `GET /api/admin/dashboard`: thống kê nhanh.
- `GET /api/admin/users`: danh sách user, hỗ trợ search/role.
- `POST /api/admin/users/{user}/lock`: khóa user.
- `POST /api/admin/users/{user}/unlock`: mở khóa user.
- `GET /api/admin/notifications`: danh sách notification.
- `POST /api/admin/notifications/{notification}/read`: đánh dấu đã đọc.
- `POST /api/admin/notifications/read-all`: đánh dấu tất cả đã đọc.
- `GET /api/admin/moderation`: gom dữ liệu moderation gồm Kanji, Minna sections, join requests.
- `POST /api/admin/join-requests/{joinRequest}/approve`: duyệt join request.
- `POST /api/admin/join-requests/{joinRequest}/decline`: từ chối join request.

File chính:

- Route: `routes/api.php`
- Controller: `App\Http\Controllers\Api\AdminApiController`

Lưu ý: trong `AdminApiController` có method `updateKanji` và `updateMinnaSection`, nhưng hiện route API chưa expose hai method này.

## 5. Cấu trúc file admin

```text
app/
  Http/
    Controllers/
      Admin/
        DashboardController.php
        NotificationController.php
        DirectInboxController.php
        AlphabetController.php
        KanjiController.php
        MinnaController.php
        MinnaSectionController.php
        CourseDataController.php
        ChatGroupAdminController.php
        UserController.php
        SecurityController.php
        LogoSettingController.php
        SystemLogController.php
      Api/
        AdminApiController.php
    Middleware/
      EnsureAdminRoutePermission.php
      FeatureAccessMiddleware.php
config/
  admin_route_permissions.php
resources/
  views/
    adminlayout/
      app.blade.php
      header.blade.php
      sidebar.blade.php
    admin/
      dashboard.blade.php
      notifications/
      inbox/
      alphabets/
      kanjis/
      minna/
      course-data/
      chat/groups/
      users/
      security/
      logo-settings/
      system-logs/
database/
  migrations/
    2026_05_13_120000_create_admin_rbac_tables.php
  seeders/
    AdminRbacSeeder.php
```

## 6. Những phần admin đã có

- Dashboard thống kê tổng quan.
- Vận hành nội dung: xem trước, kiểm tra, bản nháp/xuất bản/lưu trữ, lịch sử phiên bản, khôi phục phiên bản.
- CRUD bảng chữ cái.
- CRUD Kanji.
- CRUD Minna lesson và editor section.
- CRUD dữ liệu khóa học JLPT/N5.
- Quản lý Audio/TTS: xem cache, tạo audio một mục, tạo hàng loạt theo bài Minna, xóa cache.
- Phân tích học tập: DAU, WAU, người sắp mất streak, hoàn thành bài, điểm nghẽn quiz, từ/câu yêu thích.
- Trung tâm hỗ trợ / kiểm duyệt: yêu cầu tham gia đang chờ, inbox chưa đọc, chat gần đây, tín hiệu DevTools, mẫu trả lời nhanh.
- Công cụ tăng trưởng: tạo chiến dịch thông báo theo nhóm người nhận và gửi tới user.
- Quản lý chat group và duyệt yêu cầu tham gia.
- Inbox hỗ trợ 1-1.
- Notification admin.
- Quản lý user, khóa/mở khóa, xóa user.
- RBAC admin theo role/permission.
- Cài đặt security/devtools và feature lock.
- Cài đặt logo/site identity.
- Xem system logs.
- Xem admin audit logs.
- API admin cơ bản cho dashboard, user, notification, moderation.

## 7. Những phần admin có thể mở rộng sau

### Giai đoạn A - Vận hành nội dung

- Import/export CSV/JSON cho Kanji, Alphabet, Minna sections và Course Data.
- Xem trước bài học như user thật ở từng template cụ thể, thay vì chỉ xem snapshot tổng quát.
- Quy trình xuất bản nâng cao: hẹn lịch xuất bản, bắt buộc duyệt, ghi chú reviewer.
- So sánh phiên bản trực quan hơn cho nội dung JSON lớn.

### Giai đoạn B - Học tập nâng cao

- Quản lý audio/TTS trong admin: xem audio đã cache, thử lại nhà cung cấp, tạo lại audio.
- Quản lý pronunciation provider và trạng thái key Google/Azure/Forvo.
- Bulk queue/worker cho generate audio số lượng lớn.
- Quản lý quiz nâng cao: xem câu hỏi sinh ra, chỉnh template, xem attempt lỗi nhiều.

### Giai đoạn C - Growth và moderation

- Cohort retention sâu hơn: D1/D7/D30, funnel theo onboarding.
- Hàng đợi kiểm duyệt cho tin nhắn chat bị report.
- Gửi chiến dịch email/push ngoài thông báo trong app.
- Màn hình leaderboard cho admin: lọc tuần/tháng, phát hiện spam XP.
- Nhật ký thao tác đầy đủ cho mọi hành động admin quan trọng.

## 8. Kết luận nhanh

Phần admin hiện đã có nền tảng khá đầy: quản lý nội dung học, user, bảo mật, RBAC, chat, inbox, notification và API admin cơ bản. Phần còn thiếu chủ yếu không phải CRUD, mà là tooling vận hành thật: import/export, publish workflow, audit history, quản lý audio/TTS, thống kê retention và moderation queue.
