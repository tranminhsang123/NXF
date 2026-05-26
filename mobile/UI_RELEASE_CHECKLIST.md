# UI Release Checklist (Mobile)

Dùng checklist này để test nhanh trước khi đóng gói Android/iOS.

## 1) Visual consistency

- [ ] Tiêu đề màn hình dùng cùng scale (H1/H2/body/caption) và không bị lệch font.
- [ ] Khoảng cách giữa card/button/input đồng đều, không có màn nào "chật/chưa".
- [ ] Màu trạng thái (`loading/success/error`) hiển thị đúng màu, dễ đọc.
- [ ] Shadow/radius card nhất quán, không có card vuông bất thường.

## 2) Touch targets and controls

- [ ] Tất cả nút bấm dễ ấn, không bị quá nhỏ.
- [ ] Input/chip/toggle có chiều cao ổn định, không nhảy layout khi focus.
- [ ] Nút trong list/chat không bấm nhầm (nhất là `Social`, `Flashcards`, `LessonDetail`).
- [ ] Disabled/loading state rõ ràng (opacity + indicator đúng).

## 3) Navigation and safe-area

- [ ] Tab bar không đè text/icon trên tai thỏ (notch/dynamic island).
- [ ] Bottom tab không bị che bởi home indicator (iPhone) hoặc gesture bar (Android).
- [ ] Chuyển màn hình giữa tabs + stack mượt, không giật.
- [ ] Tiêu đề trên header không bị cắt trên màn nhỏ.

## 4) Core flow smoke test

- [ ] Auth: đăng nhập/đăng ký + thông báo lỗi hiển thị dễ hiểu.
- [ ] Learn: vào `LearnHub` -> `LessonList` -> `LessonDetail` bình thường.
- [ ] Search: nhập từ khóa >= 2 ký tự, kết quả hiển thị đúng section.
- [ ] Flashcards: tải bộ thẻ, lật thẻ, review, next/prev không lỗi.
- [ ] Social: chọn group/inbox, gửi tin nhắn, trạng thái cập nhật đúng.
- [ ] Profile/Admin: các card/nút không vỡ layout.

## 5) Text and locale quality

- [ ] Không còn text kỹ thuật lộ ra UI (ID, baseUrl, wording debug).
- [ ] Wording tiếng Việt nhất quán giữa các màn.
- [ ] Không có chữ bị tràn dòng trên màn hình nhỏ.

## 6) Device matrix (tối thiểu)

- [ ] Android màn nhỏ (<= 6.1")
- [ ] Android màn lớn (>= 6.6")
- [ ] iPhone có notch
- [ ] iPhone màn nhỏ (SE/mini nếu có)

## 7) Performance quick check

- [ ] Mở app lần đầu không giật màn splash.
- [ ] Scroll list dài (`LessonList`, `Social`) không rung lag rõ.
- [ ] Chuyển tab liên tục 10-20 lần không crash.

## 8) Release gate (go/no-go)

- [ ] Không còn bug P0/P1 về UI/UX.
- [ ] Không có warning quan trọng khi chạy bản release.
- [ ] Pass full checklist ở ít nhất 1 máy Android + 1 máy iOS.

Nếu tất cả mục trên đều pass -> có thể tiến hành build/phát hành.
