# Cấu trúc dự án Japanese Study

Tài liệu này mô tả cấu trúc hiện tại của dự án, các phần chính trong source code và các tính năng đã có.

## 1. Tổng quan

`japanese-study` là một hệ thống học tiếng Nhật gồm:

- Backend web/API viết bằng Laravel 10.
- Giao diện web dùng Blade views, CSS/JS qua Vite.
- App mobile nằm riêng trong thư mục `mobile`, viết bằng Expo + React Native + TypeScript.
- Database dùng migration, seeder và Eloquent models của Laravel.
- API mobile dùng Laravel Sanctum để xác thực token.
- Admin panel để quản lý nội dung, người dùng, bảo mật, chat và thông báo.

## 2. Công nghệ chính

- PHP `^8.1`
- Laravel `^10.10`
- Laravel Sanctum cho API auth
- MySQL theo `.env.example`
- Vite cho frontend assets của web
- Expo `~54`, React Native `0.81`, React `19`, TypeScript cho mobile
- React Navigation và TanStack Query trong app mobile
- PHPUnit cho test backend

## 3. Cấu trúc thư mục chính

```text
japanese-study/
  app/                 Logic chính của Laravel
    Console/           Lệnh artisan và lịch chạy command
    Events/            Event realtime/chat
    Exceptions/        Xử lý exception
    Http/              Controllers, middleware, requests
    Models/            Eloquent models
    Observers/         Tự động clear cache/cập nhật khi dữ liệu đổi
    Policies/          Quyền truy cập chat/inbox
    Providers/         Service providers, boot observer, blade directive
    Services/          Business logic cho học tập, flashcard, thống kê
    Support/           Helper/cache/data editor
  bootstrap/           Bootstrap Laravel
  config/              Cấu hình app, auth, database, chat, admin permissions
  database/
    migrations/        Schema database
    seeders/           Seed dữ liệu ban đầu
    factories/         Factory dùng cho test
  docs/                Tài liệu kỹ thuật
  mobile/              App mobile Expo + React Native
  public/              Entry public, ảnh, logo, gif nét chữ
  resources/
    views/             Blade views web và admin
    css/               CSS web
    js/                JS web
  routes/              Web/API/console/broadcast routes
  storage/             Log, cache, session, file runtime
  tests/               Unit và feature tests
  vendor/              Composer dependencies
  node_modules/        NPM dependencies cho web
```

## 4. Backend Laravel

### 4.1 Routes

- `routes/web.php`: route web cho trang học, auth, dashboard, flashcard, chat, inbox và admin.
- `routes/api.php`: route API cho mobile, gồm auth, learning, minna, flashcard, social và admin.
- `routes/channels.php`: private broadcast channel cho user và chat group.
- `routes/console.php`: console route mặc định của Laravel.

### 4.2 Controllers

Nhóm controller chính:

- `AuthController`: đăng nhập, đăng ký, đăng xuất, Google login trên web.
- `Api/AuthController`: đăng ký, đăng nhập, Google login, lấy user hiện tại, logout cho mobile/API.
- `UserController`: dashboard cá nhân, tiến độ, thống kê, lịch sử học.
- `UserAlphabetController`, `UserKanjiController`, `VocabularyController`: các màn học alphabet, kanji, từ vựng.
- `MinnaController`: danh sách bài Minna, chi tiết bài, section, roadmap, hoàn thành bài/section, quiz.
- `CourseController`: khóa học JLPT/course N5, các section như luyện đọc, Marugoto, Speed Master.
- `FlashcardController`: màn flashcard web và review SRS.
- `ChatGroupController`, `ChatMessageController`: nhóm chat, tin nhắn, sửa/xóa/chuyển tiếp, xin vào nhóm.
- `DirectInboxController`: inbox trực tiếp giữa user và admin.
- `DevtoolsViolationController`: ghi nhận vi phạm DevTools của user.
- `Admin/*`: dashboard admin, quản lý user, alphabet, kanji, Minna, course data, chat group, inbox, security, logo, notification, system log.
- `Api/*`: API tương ứng cho learning, minna, flashcard, social, admin.

### 4.3 Services

Các service chứa business logic:

- `AlphabetService`: lấy, lọc và nhóm alphabet/kanji.
- `KanjiService`: lấy kanji theo level, thống kê số lượng theo level.
- `MinnaService`: lấy bài Minna, section, bài trước/sau.
- `CourseService`: xử lý dữ liệu khóa học JLPT/N5, section, chi tiết bài.
- `FlashcardService`: tạo danh sách flashcard từ từ vựng, chọn card cho SRS, thống kê SRS.
- `SpacedRepetitionService`: thuật toán review flashcard và lịch ôn tiếp theo.
- `UserProgressService`: lưu tiến độ bài Minna và tiến độ từng section.
- `UserDashboardService`: tổng hợp dữ liệu dashboard cá nhân.
- `StatisticsService`: thống kê theo ngày, tuần và timeline hoạt động.
- `GamificationService`: XP, streak, badge khi học/review.
- `PersonalizedRoadmapService`: tạo lộ trình học cá nhân hóa.

### 4.4 Models

Các nhóm model chính:

- Học tập: `Alphabet`, `Kanji`, `MinnaLesson`, `MinnaSection`, `N5CourseData`.
- Tiến độ: `UserProgress`, `UserMinnaSectionProgress`, `MinnaQuizAttempt`, `FlashcardCardState`.
- User/admin: `User`, `AdminRole`, `AdminPermission`.
- Gamification: `Badge`.
- Chat/social: `ChatGroup`, `ChatGroupMember`, `ChatJoinRequest`, `ChatMessage`.
- Inbox trực tiếp: `DirectConversation`, `DirectMessage`.
- Hệ thống: `Notification`, `NotificationRead`, `SystemLog`, `SecuritySetting`, `DevtoolsViolation`, `LogoSetting`.

## 5. Tính năng web hiện có

### 5.1 Trang công khai

- Trang chủ `home`.
- Học bảng chữ cái qua `/alphabet`.
- Học kanji theo level `/kanji`, `/kanji/{level}`, `/kanji/{level}/flashcard`.
- Học từ vựng theo bài `/tu-vung`, `/tu-vung/bai-{number}`.
- Danh sách khóa học `/courses`.
- Chi tiết khóa học `/course/{level}`.
- Các section khóa học như luyện đọc, Marugoto N5, Speed Master N5.

### 5.2 Minna no Nihongo

- Danh sách bài học Minna.
- Lộ trình học `/minna/lo-trinh`.
- Chi tiết bài `/minna/bai-{number}`.
- Section trong bài gồm:
  - Từ vựng.
  - Ngữ pháp.
  - Hội thoại.
  - Luyện đọc.
  - Hán tự.
- Hoàn thành từng section.
- Hoàn thành bài.
- Quiz theo bài và lưu lịch sử làm quiz.

### 5.3 Flashcard và SRS

- Danh sách bài có flashcard.
- Học flashcard theo bài hoặc nhiều bài.
- Review flashcard bằng `quality`.
- Lưu trạng thái từng card: lịch ôn, số lần ôn, lapses.
- Dashboard SRS và danh sách card yếu.
- Tích hợp XP/streak/badge khi review.

### 5.4 Dashboard người dùng

- Dashboard cá nhân.
- Theo dõi tiến độ học.
- Thống kê học tập.
- Lịch sử hoạt động.
- Tiến độ bài Minna và section.
- Thống kê quiz, flashcard và hoàn thành bài.

### 5.5 Chat và social

- Danh sách nhóm chat.
- Xem nhóm chat và tin nhắn.
- Gửi tin nhắn nhóm.
- Sửa, xóa, chuyển tiếp tin nhắn.
- Reply tin nhắn.
- User xin tham gia nhóm.
- Admin duyệt hoặc từ chối yêu cầu tham gia.
- Direct inbox giữa user và admin.
- Đếm tin nhắn chưa đọc.
- Cursor pagination cho tin nhắn.
- Idempotency bằng `client_message_id` để tránh tạo trùng tin nhắn khi retry.
- Kill switch chat qua `CHAT_WRITE_MODE`:
  - `normal`
  - `degrade_no_broadcast`
  - `disable_write`

### 5.6 Gamification

- XP khi review flashcard.
- XP khi hoàn thành section Minna.
- Streak học tập.
- Badge/thành tích:
  - First review.
  - First Minna lesson.
  - Streak 3 ngày.
  - Streak 7 ngày.
  - Mốc XP.
  - Mốc số từ đã ôn.

### 5.7 Auth

- Đăng nhập.
- Đăng ký.
- Đăng xuất.
- Google login.
- Middleware chặn user bị khóa.
- Laravel Sanctum cho API mobile.

## 6. Admin panel

Admin routes nằm dưới `/admin`, có middleware:

- `auth`
- `admin`
- `admin.route.permission`
- `throttle:admin`

Các tính năng admin:

- Dashboard admin.
- Quản lý logo/site title/subtitle.
- Cài đặt bảo mật.
- Xem system logs.
- Quản lý notification.
- Quản lý user:
  - Danh sách user.
  - Sửa user.
  - Khóa/mở khóa user.
  - Gán admin role.
  - Xóa user.
- Quản lý alphabet.
- Quản lý kanji.
- Quản lý bài Minna.
- Quản lý section Minna bằng editor riêng cho từng loại nội dung.
- Quản lý dữ liệu khóa học N5/course data.
- Duplicate course data.
- Quản lý nhóm chat.
- Duyệt/từ chối yêu cầu tham gia nhóm.
- Admin direct inbox với user.

Tài liệu chi tiết riêng cho admin: `docs/ADMIN_FEATURE_STRUCTURE.md`.

## 7. RBAC admin

Dự án có hệ thống phân quyền admin theo role/permission.

Role mặc định trong seeder:

- `super_admin`: có toàn bộ quyền.
- `content_editor`: quản lý nội dung alphabet, kanji, Minna, course data.
- `moderator`: quản lý chat, inbox, notification và xem user.
- `support_staff`: xem notification và trả lời inbox.

Permission được map với route trong:

```text
config/admin_route_permissions.php
```

Middleware kiểm tra quyền:

```text
app/Http/Middleware/EnsureAdminRoutePermission.php
```

## 8. API cho mobile

API chính trong `routes/api.php`:

```text
GET  /api/health

POST /api/auth/login
POST /api/auth/register
POST /api/auth/google
GET  /api/auth/me
POST /api/auth/logout

GET  /api/learning/dashboard
GET  /api/learning/progress
GET  /api/learning/statistics
GET  /api/learning/kanji/levels
GET  /api/learning/kanji/{level}
GET  /api/learning/vocabulary/lessons
GET  /api/learning/vocabulary/{number}
GET  /api/learning/courses
GET  /api/learning/courses/{level}
GET  /api/learning/courses/{level}/{sectionType}
GET  /api/learning/courses/{level}/{sectionType}/{itemKey}
GET  /api/learning/search

GET  /api/minna/lessons
GET  /api/minna/lessons/{number}

POST /api/flashcards/study
POST /api/flashcards/review

GET  /api/social/groups
POST /api/social/groups/{group}/join
GET  /api/social/groups/{group}/messages
POST /api/social/groups/{group}/messages
GET  /api/social/inbox/conversations
GET  /api/social/inbox/conversations/{conversation}/messages
POST /api/social/inbox/conversations/{conversation}/messages

GET  /api/admin/dashboard
GET  /api/admin/users
POST /api/admin/users/{user}/lock
POST /api/admin/users/{user}/unlock
GET  /api/admin/notifications
POST /api/admin/notifications/{notification}/read
POST /api/admin/notifications/read-all
GET  /api/admin/moderation
POST /api/admin/join-requests/{joinRequest}/approve
POST /api/admin/join-requests/{joinRequest}/decline
```

Các route bên trong nhóm `auth:sanctum` yêu cầu token.

## 9. App mobile

Thư mục mobile:

```text
mobile/
  App.tsx
  app.json
  index.ts
  package.json
  src/
    components/       Component dùng chung
    config/           Biến môi trường/API URL
    context/          AuthContext
    hooks/            Hook dùng chung
    lib/              React Query client
    navigation/       AppNavigator
    providers/        QueryProvider
    screens/          Auth và các tab chính
    services/         API service theo domain
    theme/            Theme/UI tokens
    types/            Kiểu dữ liệu TypeScript
```

Tính năng mobile hiện có:

- Auth: register, login, Google login, me, logout.
- Lưu token bằng `expo-secure-store`.
- Khôi phục session khi mở app.
- Tách stack `Auth` và `Main`.
- Tabs theo role:
  - User: Home, Learn, Social, Profile.
  - Admin: thêm Admin tab.
- Learn stack:
  - Minna.
  - Kanji.
  - Vocabulary.
  - Courses.
  - Progress.
  - Search.
  - Flashcards.
- Social stack:
  - Chat groups.
  - Direct inbox.
- Admin stack:
  - Dashboard stats.
  - Users lock/unlock.
  - Notifications.
  - Moderation.
- React Query cho data fetching và cache.

## 10. Database

Nhóm migration chính:

- User/auth:
  - `users`
  - `password_reset_tokens`
  - `personal_access_tokens`
  - role/user lock/gamification columns
- Nội dung học:
  - `alphabets`
  - `kanjis`
  - `minna_lessons`
  - `minna_sections`
  - `n5_course_data`
- Tiến độ:
  - `user_progresses`
  - `user_minna_section_progresses`
  - `minna_quiz_attempts`
  - `flashcard_card_states`
- Chat/social:
  - `chat_groups`
  - `chat_group_members`
  - `chat_messages`
  - `chat_join_requests`
  - direct inbox tables
- Admin/security:
  - `admin_roles`
  - `admin_permissions`
  - `logo_settings`
  - `security_settings`
  - `system_logs`
  - `notifications`
  - `notification_reads`
  - `devtools_violations`
- Gamification:
  - `badges`
  - badge/user pivot tables

Seeder chính:

- `AlphabetSeeder`
- `KanjiSeeder`
- `MinnaSeeder`
- `N5CourseSeeder`
- `BadgeSeeder`
- `AdminRbacSeeder`

## 11. Cache, observers và hiệu năng

Dự án có nhiều lớp cache riêng:

- `AlphabetService`, `KanjiService`, `MinnaService`, `CourseService`, `FlashcardService`.
- `app/Support/Cache/*` cho cache theo domain.
- Observer tự động invalidate cache khi dữ liệu học thay đổi:
  - `AlphabetObserver`
  - `KanjiObserver`
  - `MinnaLessonObserver`
  - `MinnaSectionObserver`
  - `N5CourseDataObserver`

Ngoài ra có tài liệu index/performance:

```text
INDEXES_EXPLANATION.md
```

## 12. Console commands và lịch chạy

Lịch trong `app/Console/Kernel.php`:

- `chat:retry-pending-events --limit=200`: chạy mỗi phút.
- `chat:reconcile-state --days=14 --limit=500`: chạy hằng ngày lúc 03:15.
- `chat:cleanup-idempotency --limit=5000`: chạy hằng ngày lúc 03:30.

Các command này phục vụ độ bền của hệ thống chat/event:

- Retry event pending.
- Reconcile trạng thái chat.
- Dọn idempotency key cũ.

## 13. Bảo mật và chống lạm dụng

Các phần đã có:

- Middleware auth/admin.
- Middleware phân quyền route admin.
- Middleware feature access để bật/tắt từng module học.
- Middleware chặn user bị khóa.
- Rate limit cho login, register, study get/post, chat write, admin.
- Ghi nhận DevTools violation.
- Security settings trong admin.
- Chat write kill switch.
- Idempotency chống gửi trùng tin nhắn.

## 14. Views web

Nhóm view chính:

- `resources/views/home.blade.php`: trang chủ.
- `resources/views/layouts/*`: layout/header/footer public.
- `resources/views/adminlayout/*`: layout admin.
- `resources/views/auth/*`: login/register.
- `resources/views/user/*`: dashboard, progress, statistics, activity.
- `resources/views/user/alphabet/*`: học alphabet.
- `resources/views/user/kanji/*`: học kanji.
- `resources/views/vocabulary/*`: từ vựng.
- `resources/views/minna/*`: Minna list/detail/roadmap/section.
- `resources/views/course/*`: course list/detail/section.
- `resources/views/flashcard/*`: flashcard index/study.
- `resources/views/chat/*`: chat group.
- `resources/views/inbox/*`: user inbox.
- `resources/views/admin/*`: toàn bộ màn admin.

## 15. Public assets

Các asset chính:

- Logo/site images trong `public/images/logo`.
- Icon/svg trong `public/images`.
- GIF nét chữ Hiragana/Katakana trong:
  - `public/images/gif/Hiragana`
  - `public/images/gif/Katakana`
- Avatar mặc định `public/images/avatar.svg`.

## 16. Test hiện có

Feature tests chính:

- `LearningPathWebTest`: roadmap, hoàn thành section, quiz, activity timeline.
- `GamificationTest`: XP, streak, badge.
- `ApiParitySmokeTest`: smoke test API learning/social/admin.
- `ChatResilienceTest`: idempotency, kill switch, cursor pagination, cleanup command.
- `AdminRbacTest`: phân quyền admin role.

## 17. Các phần có thể xem là module nghiệp vụ

Có thể chia dự án thành các module sau:

1. Auth và user account.
2. Learning content: alphabet, kanji, vocabulary, Minna, JLPT/course N5.
3. Learning progress: dashboard, progress, statistics, activity.
4. Practice: quiz, flashcard, spaced repetition.
5. Gamification: XP, streak, badge.
6. Social: chat group, join request, direct inbox.
7. Admin CMS: quản lý nội dung học.
8. Admin operations: user, security, notification, log, logo.
9. Mobile API parity: API cho app mobile dùng lại logic backend.
10. Infrastructure: cache, observer, scheduler, rate limit, tests.
