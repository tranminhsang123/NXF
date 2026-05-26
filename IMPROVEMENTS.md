# 📋 IMPROVEMENTS CÒN LẠI (ĐÃ LỌC)

Tài liệu này chỉ giữ **những việc chưa hoàn thành**.  
Các phần đã xong trước đó đã được xóa khỏi danh sách.

---

## 🔍 1. SEO & META TAGS

### Cần làm:
- ❌ Bổ sung hệ thống meta tags đầy đủ (title/description/canonical)
- ❌ Thêm Open Graph, Twitter Cards, JSON-LD
- ⚠️ Thêm logic override meta theo route:
  - `vocabulary.*`
  - `course.*`
  - `kanji.*`
  - `flashcard.*`

---

## 🗄️ 2. DATABASE OPTIMIZATION (PHẦN CÒN LẠI)

### Trạng thái:
- ✅ Indexes đã có
- ✅ Soft deletes đã có
- ❌ Chưa có database seeding đầy đủ

### Cần làm:
- Tạo/hoàn thiện seeders cho dữ liệu học tập cốt lõi
- Chuẩn hóa dữ liệu mẫu cho môi trường dev/test

---

## 📱 3. RESPONSIVE & MOBILE (PHẦN CÒN LẠI)

### Trạng thái:
- ✅ Homepage và một số màn đã responsive tốt hơn
- ⚠️ Còn cần đồng bộ UX giữa desktop/mobile

### Cần làm:
- Dropdown nhóm menu mobile đồng nhất với desktop (Học tập / Công cụ)
- Test trên nhiều thiết bị và viewport
- Kiểm tra touch interactions
- Tối ưu ảnh cho mobile

---

## 🧪 4. TESTING

### Cần làm:
- ❌ Chưa có unit tests đủ dùng
- ❌ Chưa có feature tests cho các luồng quan trọng
- ❌ Chưa có test coverage tracking

### Gợi ý:
- Feature:
  - user xem lesson list/detail
  - admin CRUD alphabet/kanji/minna
  - inbox unread-count endpoints
- Unit:
  - relation models
  - service/repository behavior chính

---

## 📝 5. DOCUMENTATION

### Cần làm:
- Hoàn thiện `README.md` (setup/run/migrate/seed/test/deploy)
- Bổ sung docs cho các module mới (Service/Repository/Cache)
- Viết ghi chú vận hành cho cache + log + security

---

## 🌐 6. API ENDPOINTS

### Cần làm:
- Thiết kế API cho mobile app/public client nếu cần
- Tạo API Resources/Transformers chuẩn hóa response
- Chuẩn hóa auth/rate limit/versioning cho API

---

## 🎨 7. FRONTEND IMPROVEMENTS

### Cần làm:
- Chuyển từ Tailwind CDN sang build process (Vite)
- Tách JS inline trong Blade ra file/module riêng
- Thiết lập cấu trúc component frontend rõ ràng hơn

---

## 🌍 8. LOCALIZATION (ĐA NGÔN NGỮ)

### Cần làm:
- Tách text hardcoded sang `resources/lang`
- Hỗ trợ ít nhất `vi` và `en`
- Dùng `__()` nhất quán trong view/controller message

---

## 📊 9. ANALYTICS & MONITORING

### Cần làm:
- Tích hợp analytics (GA hoặc giải pháp khác)
- Error tracking (vd: Sentry)
- Monitoring/observability cho production

---

## 🎯 10. USER EXPERIENCE

### Cần làm:
- Loading states cho async operations
- Toast notifications thay flash message ở luồng realtime
- Search/filter tốt hơn cho lesson/content
- Progress tracking + bookmark/favorite
- Cân nhắc dark mode

---

## 🔐 11. DATA VALIDATION & SANITIZATION

### Cần làm:
- Chuẩn sanitize input cho các form text dài
- Rà upload validation (mime/size/security)
- Tăng cường XSS hardening ở nơi render HTML

---

## 📦 12. DEPENDENCIES & PACKAGES

### Cân nhắc:
- `spatie/laravel-permission` (nếu cần RBAC chi tiết)
- `spatie/laravel-query-builder` (filter/sort chuẩn)
- `barryvdh/laravel-debugbar` (dev only)
- `laravel/horizon` (nếu scale queue)

---

## 🚀 13. DEPLOYMENT & CI/CD

### Cần làm:
- Setup CI (lint/test/build)
- Chuẩn hóa deployment steps (migrate/cache/build)
- Xây dựng rollback playbook

---

## 🎨 14. CODE QUALITY

### Cần làm:
- Bổ sung static analysis (PHPStan/Psalm)
- Checklist review cho PR
- Chuẩn hóa coding standards toàn project

---

## 📚 15. CONTENT MANAGEMENT

### Cần làm:
- Rich text editor cho admin (nếu có nhu cầu nội dung dài)
- Quản lý upload/media tập trung
- Cân nhắc versioning nội dung

---

## 🔄 16. VERSION CONTROL

### Cần làm:
- Kiểm tra và chuẩn hóa `.env.example`
- Bổ sung pre-commit checks cần thiết

---

## ⚙️ 17. CONFIGURATION

### Cần làm:
- Environment-specific configs rõ ràng
- Feature flags cho rollout an toàn
- Chiến lược config/cache cho production

---

## 📊 18. DATABASE BACKUP & RECOVERY

### Cần làm:
- Automated backup strategy
- Seeding strategy theo môi trường
- Migration rollback plan chi tiết

---

## 📈 19. FEATURE ENHANCEMENTS

### Backlog:
- User progress/statistics nâng cao
- Quiz/Test system
- Audio pronunciation
- Social/study group features
- Gamification (points, streaks, leaderboard)

---

## 🎯 ƯU TIÊN GỢI Ý (TỪ HIỆN TẠI)

### High Priority:
1. SEO & meta system hoàn chỉnh
2. Hoàn thiện seeding dữ liệu
3. Hoàn thiện mobile header dropdown groups
4. Test luồng inbox/chat/admin quan trọng

### Medium Priority:
5. Tách JS inline + setup Vite
6. Test suite (feature + unit)
7. README/docs vận hành
8. Localization cơ bản

### Low Priority:
9. Analytics/monitoring nâng cao
10. Advanced features backlog

# 📋 IMPROVEMENTS CÒN LẠI (ĐÃ LỌC)

Tài liệu này chỉ giữ **những việc chưa hoàn thành**.  
Các phần đã xong trước đó đã được xóa khỏi danh sách.

---

## 🔍 1. SEO & META TAGS

### Cần làm:
- ❌ Bổ sung hệ thống meta tags đầy đủ (title/description/canonical)
- ❌ Thêm Open Graph, Twitter Cards, JSON-LD
- ⚠️ Thêm logic override meta theo route:
  - `vocabulary.*`
  - `course.*`
  - `kanji.*`
  - `flashcard.*`

---

## 🗄️ 3. DATABASE OPTIMIZATION (PHẦN CÒN LẠI)

### Trạng thái:
- ✅ Indexes đã có
- ✅ Soft deletes đã có
- ❌ Chưa có database seeding đầy đủ

### Cần làm:
- Tạo/hoàn thiện seeders cho dữ liệu học tập cốt lõi
- Chuẩn hóa dữ liệu mẫu cho môi trường dev/test

---

## 📱 4. RESPONSIVE & MOBILE (PHẦN CÒN LẠI)

### Trạng thái:
- ✅ Homepage và một số màn đã responsive tốt hơn
- ⚠️ Còn cần đồng bộ UX giữa desktop/mobile

### Cần làm:
- Dropdown nhóm menu mobile đồng nhất với desktop (Học tập / Công cụ)
- Test trên nhiều thiết bị và viewport
- Kiểm tra touch interactions
- Tối ưu ảnh cho mobile

---

## 🧪 5. TESTING

### Cần làm:
- ❌ Chưa có unit tests đủ dùng
- ❌ Chưa có feature tests cho các luồng quan trọng
- ❌ Chưa có test coverage tracking

### Gợi ý:
- Feature:
  - user xem lesson list/detail
  - admin CRUD alphabet/kanji/minna
  - inbox unread-count endpoints
- Unit:
  - relation models
  - service/repository behavior chính

---

## 📝 6. DOCUMENTATION

### Cần làm:
- Hoàn thiện `README.md` (setup/run/migrate/seed/test/deploy)
- Bổ sung docs cho các module mới (Service/Repository/Cache)
- Viết ghi chú vận hành cho cache + log + security

---

## 🌐 7. API ENDPOINTS

### Cần làm:
- Thiết kế API cho mobile app/public client nếu cần
- Tạo API Resources/Transformers chuẩn hóa response
- Chuẩn hóa auth/rate limit/versioning cho API

---

## 🎨 8. FRONTEND IMPROVEMENTS

### Cần làm:
- Chuyển từ Tailwind CDN sang build process (Vite)
- Tách JS inline trong Blade ra file/module riêng
- Thiết lập cấu trúc component frontend rõ ràng hơn

---

## 🌍 9. LOCALIZATION (ĐA NGÔN NGỮ)

### Cần làm:
- Tách text hardcoded sang `resources/lang`
- Hỗ trợ ít nhất `vi` và `en`
- Dùng `__()` nhất quán trong view/controller message

---

## 📊 10. ANALYTICS & MONITORING

### Cần làm:
- Tích hợp analytics (GA hoặc giải pháp khác)
- Error tracking (vd: Sentry)
- Monitoring/observability cho production

---

## 🎯 11. USER EXPERIENCE

### Cần làm:
- Loading states cho async operations
- Toast notifications thay flash message ở luồng realtime
- Search/filter tốt hơn cho lesson/content
- Progress tracking + bookmark/favorite
- Cân nhắc dark mode

---

## 🔐 12. DATA VALIDATION & SANITIZATION

### Cần làm:
- Chuẩn sanitize input cho các form text dài
- Rà upload validation (mime/size/security)
- Tăng cường XSS hardening ở nơi render HTML

---

## 📦 13. DEPENDENCIES & PACKAGES

### Cân nhắc:
- `spatie/laravel-permission` (nếu cần RBAC chi tiết)
- `spatie/laravel-query-builder` (filter/sort chuẩn)
- `barryvdh/laravel-debugbar` (dev only)
- `laravel/horizon` (nếu scale queue)

---

## 🚀 14. DEPLOYMENT & CI/CD

### Cần làm:
- Setup CI (lint/test/build)
- Chuẩn hóa deployment steps (migrate/cache/build)
- Xây dựng rollback playbook

---

## 🎨 15. CODE QUALITY

### Cần làm:
- Bổ sung static analysis (PHPStan/Psalm)
- Checklist review cho PR
- Chuẩn hóa coding standards toàn project

---

## 📚 16. CONTENT MANAGEMENT

### Cần làm:
- Rich text editor cho admin (nếu có nhu cầu nội dung dài)
- Quản lý upload/media tập trung
- Cân nhắc versioning nội dung

---

## 🔄 17. VERSION CONTROL

### Cần làm:
- Kiểm tra và chuẩn hóa `.env.example`
- Bổ sung pre-commit checks cần thiết

---

## ⚙️ 18. CONFIGURATION

### Cần làm:
- Environment-specific configs rõ ràng
- Feature flags cho rollout an toàn
- Chiến lược config/cache cho production

---

## 📊 19. DATABASE BACKUP & RECOVERY

### Cần làm:
- Automated backup strategy
- Seeding strategy theo môi trường
- Migration rollback plan chi tiết

---

## 📈 20. FEATURE ENHANCEMENTS

### Backlog:
- User progress/statistics nâng cao
- Quiz/Test system
- Audio pronunciation
- Social/study group features
- Gamification (points, streaks, leaderboard)

---

## 🎯 ƯU TIÊN GỢI Ý (TỪ HIỆN TẠI)

### High Priority:
1. SEO & meta system hoàn chỉnh
2. Hoàn thiện seeding dữ liệu
3. FormRequest Vietnamese messages
4. Hoàn thiện mobile header dropdown groups
5. Test luồng inbox/chat/admin quan trọng

### Medium Priority:
6. Tách JS inline + setup Vite
7. Test suite (feature + unit)
8. README/docs vận hành
9. Localization cơ bản

### Low Priority:
10. Analytics/monitoring nâng cao
11. Advanced features backlog

# 📋 Đề Xuất Cải Tiến Dự Án Japanese Study

## 🔒 1. BẢO MẬT & XÁC THỰC

### Vấn đề hiện tại:
- ❌ Admin routes không có authentication middleware
- ❌ Không có authorization (phân quyền)
- ❌ CSRF protection chưa được kiểm tra đầy đủ

### Đề xuất:
```php
// routes/web.php
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('alphabets', AlphabetController::class);
});

// Tạo middleware AdminMiddleware
// Thêm role/permission system
```

---

## 🏗️ 2. KIẾN TRÚC CODE

### Vấn đề hiện tại:
- ❌ Logic nghiệp vụ nằm trong Controller
- ❌ Không có Service Layer
- ❌ Không có Repository Pattern
- ❌ Validation logic trực tiếp trong Controller

### Đề xuất:
```
app/
├── Services/
│   ├── MinnaService.php
│   ├── AlphabetService.php
│   └── KanjiService.php
├── Repositories/
│   ├── MinnaLessonRepository.php
│   └── AlphabetRepository.php
└── Http/
    └── Requests/
        ├── StoreAlphabetRequest.php
        └── UpdateAlphabetRequest.php
```

---

## ⚡ 3. HIỆU NĂNG (PERFORMANCE)

### Vấn đề hiện tại:
- ❌ N+1 query problem có thể xảy ra
- ❌ Không có caching
- ❌ Load tất cả dữ liệu cùng lúc (UserAlphabetController)

### Đề xuất:
```php
// Sử dụng eager loading
$lessons = MinnaLesson::with('sections')->get();

// Thêm caching
Cache::remember('alphabets_hiragana', 3600, function() {
    return Alphabet::where('type', 'hiragana')->get();
});

// Pagination cho alphabet
$alphabets = Alphabet::paginate(50);
```

---

## ✅ 4. VALIDATION & FORM REQUESTS

### Vấn đề hiện tại:
- ❌ Validation logic trong Controller
- ❌ Không tái sử dụng được
- ❌ Khó maintain

### Đề xuất:
```php
// app/Http/Requests/StoreAlphabetRequest.php
class StoreAlphabetRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'character' => 'required|string|max:10|unique:alphabets',
            'romaji' => 'required|string|max:50',
            'type' => 'required|in:hiragana,katakana,romaji',
        ];
    }
}
```

---

## 🧪 5. TESTING

### Vấn đề hiện tại:
- ❌ Chưa có unit tests
- ❌ Chưa có feature tests
- ❌ Không có test coverage

### Đề xuất:
```php
// tests/Feature/MinnaTest.php
public function test_user_can_view_lesson_list()
public function test_user_can_view_lesson_detail()
public function test_admin_can_create_alphabet()

// tests/Unit/Models/MinnaLessonTest.php
public function test_lesson_has_many_sections()
```

---

## 📝 6. DOCUMENTATION

### Vấn đề hiện tại:
- ❌ README.md chỉ có template Laravel
- ❌ Không có API documentation
- ❌ Không có code comments đầy đủ

### Đề xuất:
- Viết README.md với hướng dẫn setup
- Thêm PHPDoc cho các methods
- Tạo API documentation (nếu có API)

---

## 🌐 7. API ENDPOINTS

### Vấn đề hiện tại:
- ❌ Chưa có API cho mobile app
- ❌ Chưa có API resources

### Đề xuất:
```php
// routes/api.php
Route::apiResource('lessons', MinnaLessonController::class);
Route::apiResource('alphabets', AlphabetController::class);

// app/Http/Resources/MinnaLessonResource.php
```

---

## 🎨 8. FRONTEND IMPROVEMENTS

### Vấn đề hiện tại:
- ❌ Tailwind CDN (nên dùng build process)
- ❌ Không có asset compilation
- ❌ Không có JavaScript modules
- ❌ Inline styles trong views

### Đề xuất:
- Setup Vite cho asset compilation
- Tách JavaScript ra file riêng
- Sử dụng Laravel Mix hoặc Vite
- Tạo component system

---

## 🔍 9. SEO & META TAGS

### Vấn đề hiện tại:
- ❌ Meta tags chưa đầy đủ
- ❌ Không có Open Graph tags
- ❌ Không có structured data

### Đề xuất:
```php
// Tạo MetaService
// Thêm meta tags động cho từng trang
// Thêm Open Graph, Twitter Cards
```

---

## 🌍 10. LOCALIZATION (ĐA NGÔN NGỮ)

### Vấn đề hiện tại:
- ❌ Hardcoded Vietnamese text
- ❌ Không hỗ trợ đa ngôn ngữ

### Đề xuất:
```php
// resources/lang/vi/messages.php
// resources/lang/en/messages.php
// Sử dụng __() helper
{{ __('messages.welcome') }}
```

---

## 🗄️ 11. DATABASE OPTIMIZATION

### Vấn đề hiện tại:
- ❌ Chưa có indexes cho các cột thường query
- ❌ Không có soft deletes
- ❌ Chưa có database seeding đầy đủ

### Đề xuất:
```php
// Thêm indexes
$table->index('type');
$table->index('level');
$table->index(['lesson_id', 'key']);

// Thêm soft deletes
use SoftDeletes;
$table->softDeletes();
```

---

## 🚨 12. ERROR HANDLING

### Vấn đề hiện tại:
- ❌ Chưa có custom error pages (404, 500)
- ❌ Error handling chưa đầy đủ
- ❌ Không có logging strategy

### Đề xuất:
```php
// resources/views/errors/404.blade.php
// resources/views/errors/500.blade.php
// Thêm try-catch trong controllers
// Setup logging
```

---

## 📊 13. ANALYTICS & MONITORING

### Vấn đề hiện tại:
- ❌ Không có analytics
- ❌ Không có error tracking
- ❌ Không có performance monitoring

### Đề xuất:
- Tích hợp Google Analytics
- Setup Sentry cho error tracking
- Thêm Laravel Telescope cho development

---

## 🔄 14. CACHING STRATEGY

### Vấn đề hiện tại:
- ❌ Không có caching
- ❌ Query lại database mỗi request

### Đề xuất:
```php
// Cache::remember('lessons', 3600, fn() => MinnaLesson::all());
// Cache::remember('alphabets_hiragana', 3600, ...);
// Cache tags cho invalidation
```

---

## 📱 15. RESPONSIVE & MOBILE

### Đã cải thiện:
- ✅ Đã sửa responsive cho homepage
- ✅ Đã sửa responsive cho alphabet

### Cần kiểm tra:
- ⚠️ Test trên nhiều thiết bị
- ⚠️ Kiểm tra touch interactions
- ⚠️ Optimize images cho mobile

---

## 🎯 16. USER EXPERIENCE

### Đề xuất thêm:
- ⭐ Loading states cho async operations
- ⭐ Toast notifications thay vì flash messages
- ⭐ Search functionality cho lessons
- ⭐ Progress tracking cho users
- ⭐ Bookmark/favorite lessons
- ⭐ Dark mode toggle

---

## 🔐 17. DATA VALIDATION & SANITIZATION

### Đề xuất:
```php
// Sanitize user input
// Validate file uploads
// XSS protection
// SQL injection prevention (đã có với Eloquent)
```

---

## 📦 18. DEPENDENCIES & PACKAGES

### Đề xuất thêm:
- `spatie/laravel-permission` - Role & Permission
- `spatie/laravel-query-builder` - Advanced filtering
- `barryvdh/laravel-debugbar` - Debug toolbar
- `laravel/horizon` - Queue monitoring (nếu dùng queues)

---

## 🚀 19. DEPLOYMENT & CI/CD

### Đề xuất:
- Setup GitHub Actions
- Environment configuration
- Database migration strategy
- Asset compilation trong deployment

---

## 📈 20. FEATURE ENHANCEMENTS

### Tính năng có thể thêm:
1. **User Progress Tracking**
   - Lưu tiến độ học của user
   - Đánh dấu bài đã học
   - Statistics dashboard

2. **Quiz/Test System**
   - Tạo quiz cho từng bài
   - Flashcard system
   - Spaced repetition

3. **Audio Pronunciation**
   - Audio cho từ vựng
   - Text-to-speech
   - Recording practice

4. **Social Features**
   - Comments trên lessons
   - Share progress
   - Study groups

5. **Gamification**
   - Points & badges
   - Leaderboard
   - Streaks

---

## 🎨 21. CODE QUALITY

### Đề xuất:
- Setup Laravel Pint (đã có)
- Setup PHPStan hoặc Psalm
- Code review checklist
- Coding standards

---

## 📚 22. CONTENT MANAGEMENT

### Đề xuất:
- Rich text editor cho admin
- Image upload & management
- Media library
- Content versioning

---

## 🔄 23. VERSION CONTROL

### Đề xuất:
- `.gitignore` đã có
- Thêm `.env.example` với đầy đủ config
- Git hooks cho pre-commit checks

---

## ⚙️ 24. CONFIGURATION

### Đề xuất:
- Environment-specific configs
- Feature flags
- Config caching cho production

---

## 📊 25. DATABASE BACKUP & RECOVERY

### Đề xuất:
- Automated backups
- Database seeding strategy
- Migration rollback plan

---

## 🎯 ƯU TIÊN THỰC HIỆN

### High Priority (Làm ngay):
1. ✅ Authentication & Authorization cho admin
2. ✅ Form Requests cho validation
3. ✅ Error handling & custom error pages
4. ✅ Caching cho dữ liệu tĩnh
5. ✅ Eager loading để tránh N+1

### Medium Priority:
6. ⚠️ Service Layer pattern
7. ⚠️ API endpoints
8. ⚠️ Testing
9. ⚠️ SEO improvements
10. ⚠️ Asset compilation với Vite

### Low Priority:
11. 📝 Documentation
12. 📝 Localization
13. 📝 Analytics
14. 📝 Advanced features

---

## 📝 GHI CHÚ

Dự án hiện tại có cấu trúc tốt và code khá sạch. Các cải tiến trên sẽ giúp:
- Tăng tính bảo mật
- Cải thiện hiệu năng
- Dễ maintain hơn
- Sẵn sàng scale
- Professional hơn

