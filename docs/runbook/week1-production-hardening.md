# Runbook Siết Production - Tuần 1

## Phạm vi

Tuần 1 tập trung giảm rủi ro production cho chat/inbox, đồng nhất authorization, và ổn định API contract mà không làm refactor lớn.

## Mục tiêu

- Chặn trùng tin nhắn khi mobile retry request.
- Đồng nhất quyền truy cập và phạm vi dữ liệu chat/inbox.
- Chuẩn hóa response cho endpoint critical mà không phá client cũ.
- Trace được xuyên suốt request -> DB write -> broadcast event.
- Có kill switch để tắt chat write nhanh khi sự cố.

## Nguyên tắc thực thi

- Không làm big-bang refactor trong tuần 1.
- Chỉ thay đổi API theo kiểu additive (chưa xóa field cũ).
- Migration phải backward-compatible.
- Mỗi thay đổi write-path phải có ít nhất 1 regression test.

## Kế hoạch theo ngày

### Ngày 1 - Thiết lập quan sát (observability)

#### Kết quả cần có

- Structured log cho write-path chat/inbox với:
  - `request_id`
  - `user_id`
  - `conversation_id` hoặc `group_id`
  - `message_uuid`
  - `event_id`
  - `parent_event_id` (cho reply/forward chain)
- Mục runbook ngắn mô tả cách trace incident.

#### Tiêu chí đạt

- Với mỗi tin nhắn gửi đi, có thể nối được log theo chuỗi:
  - request vào
  - DB insert/update
  - broadcast dispatch

### Ngày 2 - Policy-first + Scoped Query

#### Kết quả cần có

- Áp policy cho các action chat/inbox critical.
- Bắt buộc scoped query ở read/write flow critical.

#### Quy tắc

- Endpoint bảo vệ phải gọi `authorize(...)`.
- Query phải giới hạn theo quyền nhìn thấy dữ liệu (membership/ownership), không chỉ dựa vào policy.
- Fail-closed mặc định: nếu không xác định được scope hợp lệ thì từ chối truy cập, không fallback query rộng.

#### Tiêu chí đạt

- Không endpoint nào trả về hoặc mutate chat/inbox ngoài phạm vi dữ liệu user được phép.

### Ngày 3 - Transaction + Idempotency cho chat write

#### Kết quả cần có

- Write-path chat chạy trong DB transaction.
- Chỉ emit event sau khi commit thành công.
- Hỗ trợ `client_message_id` để retry an toàn.
- Unique index cho `(sender_id, client_message_id)` (hoặc cột user tương đương).
- Khi dính unique constraint do race/concurrent retry:
  - bắt exception constraint violation
  - đọc lại message đã tồn tại theo `(sender_id, client_message_id)` và trả về response thành công theo contract
- Chuẩn hóa response idempotency:
  - retry phải trả về payload giống hệt lần đầu (cùng shape, cùng giá trị field chính)
  - bắt buộc giữ nguyên `message_uuid`, `timestamp`, `status`

#### Tiêu chí đạt

- Request giống hệt nhau khi replay không tạo row trùng.
- Không còn half-state kiểu broadcast thành công nhưng DB thiếu/inconsistent.
- Concurrent retry vẫn trả về cùng một message, không phát sinh lỗi 500 không cần thiết.
- Retry response không làm client hiểu nhầm thành message mới.

### Quy tắc nhất quán timestamp và ordering (áp dụng toàn hệ)

- Server time là nguồn chuẩn cho message timestamp.
- Timestamp phải có độ phân giải nhất quán (millisecond).
- UI chỉ dùng một rule sort duy nhất:
  - ưu tiên sort theo `id` tăng dần, hoặc
  - sort theo server timestamp nhất quán
- Không trộn nhiều rule sort giữa các màn hình chat/inbox.

### Ngày 4 - Ổn định API contract cho endpoint critical

#### Kết quả cần có

- Đồng nhất response envelope cho endpoint critical (auth/social/learning write).
- Giữ field legacy trong giai đoạn chuyển tiếp.

#### Quy tắc rollout response

- Chỉ additive:
  - được phép: thêm `data`, `meta`, `error.code`
  - chưa làm: xóa/đổi tên field cũ trong tuần 1

#### Tiêu chí đạt

- Mobile/Web vẫn chạy ổn, không cần bắt buộc release app cùng ngày.

### Ngày 5 - Siết test + security

#### Kết quả cần có

- Test cho:
  - rò rỉ phạm vi dữ liệu do thiếu scoped query
  - transaction integrity cho chat
  - tính nhất quán API contract endpoint critical
  - idempotency (burst request trùng)
- Security quick wins:
  - throttle riêng cho API login/register
  - siết CORS whitelist cho production
  - nâng baseline rule password

#### Tiêu chí đạt

- Bộ test local/CI cover được failure path critical phát sinh trong tuần này.

### Ngày 6 - Soak test staging + chaos nhỏ

#### Kết quả cần có

- Soak test flow chat/inbox/auth ở mức concurrency vừa phải.
- Chaos checks:
  - giả lập mất kết nối DB tạm thời giữa write-path
  - delay/fail broadcast path
- Burst test:
  - 1 user gửi liên tục 20 message trong thời gian ngắn
  - kiểm tra ordering, duplicate, unread count
- Multi-device race:
  - 2 thiết bị cùng gửi message gần như đồng thời
  - kiểm tra không overwrite và timestamp không lệch bất thường

#### Tiêu chí đạt

- Hệ thống recover mà không hỏng dữ liệu.
- Không có half-state kéo dài ở flow chat/inbox chính.

### Ngày 7 - Rollout kiểm soát + giám sát

#### Kết quả cần có

- Deploy trong khung giờ rủi ro thấp.
- Checklist giám sát realtime cho:
  - spike 4xx/5xx
  - tín hiệu duplicate message
  - bất thường unread count
  - suy giảm latency
  - event lag (thời gian từ event tạo đến broadcast)
- Kill switch chat write có sẵn và đã verify.

#### Tiêu chí đạt

- Có thể tắt chat write (read-only mode) trong vòng 1 phút.
- Rollback path được viết rõ và diễn tập được.

## Kịch bản test bắt buộc phải pass

1. **Idempotency test**
   - Gửi 2 request giống nhau với cùng `client_message_id` trong 100ms.
   - Kỳ vọng chỉ có đúng 1 message được lưu.
2. **Scoped read test**
   - User đọc conversation không thuộc membership.
   - Kỳ vọng forbidden/not found, không leak dữ liệu.
3. **Transaction integrity test**
   - Ép lỗi sau bước create message nhưng trước bước update tiếp theo.
   - Kỳ vọng rollback toàn bộ, không partial state.
4. **Contract compatibility test**
   - Client parser cũ vẫn parse được khi response chỉ thêm field mới.

## Cơ chế an toàn khi deploy

### Kill switch

- Thêm config toggle để tắt endpoint chat write.
- Khi bật:
  - endpoint send/update/delete/forward trả về trạng thái service unavailable (hoặc lỗi có kiểm soát)
  - endpoint read vẫn hoạt động
- Hỗ trợ 2 mode:
  - `disable_write`: chặn write hoàn toàn
  - `degrade_no_broadcast`: vẫn ghi DB nhưng không broadcast realtime (dùng khi Redis/queue lỗi)

### Event trạng thái tối thiểu

- Theo dõi trạng thái event ở mức tối thiểu:
  - `pending`
  - `sent`
  - `failed`
- Mục tiêu:
  - biết event nào fail để xử lý/ retry thủ công
  - điều tra mismatch DB vs realtime nhanh hơn
- Retry policy tối thiểu cho event:
  - retry 3 lần
  - exponential backoff
  - sau ngưỡng retry giữ trạng thái `failed` để theo dõi và xử lý thủ công

### Chuẩn response graceful khi kill switch bật

- Khi chat write bị tắt, trả response lỗi có cấu trúc ổn định thay vì lỗi mơ hồ.
- Mẫu khuyến nghị:
  - `error.code = CHAT_DISABLED`
  - `error.message = Chat is temporarily unavailable`
- Mục tiêu:
  - mobile/web hiển thị thông báo đúng
  - tránh retry spam gây nhiễu hệ thống

### Quy tắc rollback

- Ưu tiên rollback bằng feature toggle trước rollback code.
- Giữ migration của tuần 1 backward-safe.
- Ghi lại lý do rollback và timeline incident.

## Definition of Done - Tuần 1

- Không còn duplicate message khi client retry.
- Không endpoint critical trong scope trả response lệch shape.
- Chat write kill switch hoạt động và đã test.
- Authorization gồm cả policy check và scoped query enforcement.
- Trace request -> DB -> event quan sát được bằng `request_id` + `message_uuid` + `event_id`.
- Nếu có bug, hệ thống vẫn giữ được kiểm soát vận hành (degrade/kill switch/rollback dùng được).

## Chỉ số giám sát bắt buộc trong rollout

- Duplicate detection ratio:
  - so sánh số lần insert message thành công với số `client_message_id` unique
- Event lag:
  - thời gian từ lúc tạo event đến lúc broadcast thực sự
- Unread mismatch signal:
  - phát hiện chênh lệch unread giữa DB và trạng thái client
- Write latency (DB):
  - thời gian từ request vào đến DB commit thành công
- Broadcast success rate:
  - `sent_events / total_events`
- Retry hit rate (idempotency):
  - `idempotent_hits / total_send`
- Alert thresholds (bắt buộc khai báo):
  - duplicate ratio > 1.01 -> alert
  - event lag p95 > 2s -> alert
  - 429 login spike bất thường -> alert
  - send success rate < 99% -> alert (`successful_send / total_send_request`)

## Phân loại sự cố nhanh (Incident Classification)

- P0:
  - mất khả năng gửi/nhận message (chat down)
- P1:
  - duplicate message
  - unread mismatch
  - event lag cao kéo dài
- P2:
  - latency tăng nhưng hệ thống vẫn usable

## Playbook 5 phút đầu khi có incident chat

1. Kiểm tra nhanh:
   - có spike 5xx không
   - event lag có vượt ngưỡng không
   - duplicate ratio có tăng bất thường không
2. Nếu event lag cao:
   - bật mode `degrade_no_broadcast`
3. Nếu duplicate/unread sai rõ ràng:
   - bật `disable_write`
4. Nếu nghi ngờ DB write-path lỗi:
   - bật kill switch cho toàn bộ chat write
5. Ghi lại evidence tối thiểu để điều tra:
   - `request_id`
   - `message_uuid`
   - thời điểm bật/tắt kill switch

## Test replay từ request production (staging)

- Định kỳ lấy mẫu request thật từ production (đã ẩn dữ liệu nhạy cảm).
- Replay vào staging với dữ liệu gần production.
- Mục tiêu:
  - phát hiện edge case mà synthetic test không bao phủ
  - kiểm tra độ ổn định parser/contract khi request shape đa dạng
- Khuyến nghị batch:
  - replay ít nhất 100 request đại diện cho flow critical.

## Khóa an toàn bổ sung cho chat scale

### 1) Idempotency window (TTL cho `client_message_id`)

- Quy định rõ thời gian hiệu lực của `client_message_id` (khuyến nghị 24-72h).
- Sau TTL:
  - hoặc yêu cầu client sinh ID mới,
  - hoặc cho phép reuse theo policy đã định.
- Mục tiêu:
  - tránh bảng idempotency phình không kiểm soát
  - tránh case client bug reuse ID sau nhiều ngày làm "nuốt" message mới
- Gợi ý triển khai:
  - thêm index theo thời gian tạo (`created_at`) để dọn dữ liệu hiệu quả
  - chạy job dọn dữ liệu idempotency theo lịch
  - cân nhắc UUID v7 để truy vết thời gian tốt hơn

### 2) Pagination consistency cho read-path

- Chuẩn hóa phân trang chat theo cursor-based (không dùng offset cho message stream).
- Cursor khuyến nghị:
  - `last_seen_id`, hoặc
  - `(timestamp, id)` nếu cần ràng buộc chặt hơn
- Ordering phải cố định và đồng bộ với rule sort toàn hệ.
- Mục tiêu:
  - tránh duplicate/missing message khi scroll nhanh
  - tránh lệch thứ tự giữa mobile và web

### 3) Chaos test: kill switch flip khi đang có traffic

- Thêm test khi hệ thống đang nhận message liên tục:
  - bật/tắt kill switch trong lúc traffic chạy
- Kỳ vọng:
  - hệ thống không crash
  - response chuyển mượt sang lỗi `CHAT_DISABLED` khi write bị chặn
  - không phát sinh state nửa vời

### 4) Rate limit riêng cho chat write

- Thiết lập rate limit chat write theo user (ví dụ N message / 5 giây) kèm burst allowance nhỏ.
- Mục tiêu:
  - chống spam/flood (vô tình hoặc cố ý)
  - giảm nghẽn dây chuyền ở DB/queue/broadcast

## Bẫy thực tế cần kiểm soát

### Bẫy 1: Scoped query đúng logic nhưng sai hiệu năng/an toàn

- Rủi ro:
  - join sai hoặc thiếu index gây timeout
  - timeout/fallback có thể vô tình mở rộng phạm vi query
- Bắt buộc:
  - có index cho cặp khóa chính của scope (ví dụ `conversation_id`, `user_id`)
  - kiểm tra explain plan cho query read/write chính của chat

### Bẫy 2: Event after commit nhưng queue delay cao

- Rủi ro:
  - DB đã commit nhưng realtime đến chậm 5-10 giây
  - user thấy message đã lưu nhưng UI realtime chưa nhảy
- Cách xử lý:
  - giữ mode `degrade_no_broadcast` khi hạ tầng realtime bất ổn
  - client nên có optimistic UI để giảm cảm giác "mất tin nhắn" trong cửa sổ delay

## Self-healing nhẹ cho unread mismatch

- Khi phát hiện unread mismatch, trigger job `recalculate_unread(conversation_id, user_id)`.
- Mục tiêu:
  - tự sửa lệch theo conversation cụ thể
  - không cần chạy full consistency job toàn hệ thống

## Canary rollout thực dụng

- Khi chưa có hạ tầng chia traffic, rollout theo thứ tự:
  - internal/admin users
  - nhóm pilot nhỏ
  - khoảng 1% user theo hash `user_id`
- Chỉ mở rộng khi metric duplicate/event lag/error vẫn trong ngưỡng.

## Ownership khi vận hành

- Chat system (backend logic, DB consistency, broadcast): Backend team
- Mobile parsing + retry behavior + UX degrade: Mobile team
- Incident commander (điều phối trong sự cố): chỉ định rõ theo ca trực/on-call
- Nguyên tắc:
  - incident mở ra phải có 1 người chịu trách nhiệm điều phối ngay từ phút đầu

## Anti-pattern cần tránh trong tuần 1

- Refactor toàn bộ chat layer cùng lúc.
- Xóa field API legacy trước khi client cập nhật.
- Nghĩ rằng policy check là đủ, bỏ qua scoped query.
- Làm idempotency nhưng không có unique constraint ở DB.
