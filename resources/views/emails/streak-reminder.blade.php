<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nhắc học</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <h1 style="font-size: 20px; margin-bottom: 8px;">Streak của bạn sắp đứt</h1>
    <p>Xin chào {{ $user->name }},</p>
    <p>Hôm qua bạn đã học, nhưng hôm nay chưa có hoạt động mới. Hãy ôn vài thẻ SRS hoặc hoàn thành một phần Minna để giữ streak.</p>

    @if(!empty($roadmap['next_section']))
        @php($section = $roadmap['next_section'])
        <p><strong>Gợi ý tiếp theo:</strong> Bài {{ $section['lesson_number'] }} - {{ $section['section_title'] }}</p>
        <p>{{ $roadmap['reason'] ?? '' }}</p>
    @endif

    <p>
        <a href="{{ route('user.dashboard') }}" style="display: inline-block; padding: 10px 16px; background: #dc2626; color: #ffffff; text-decoration: none; border-radius: 8px; font-weight: bold;">
            Mở dashboard
        </a>
    </p>

    <p style="font-size: 12px; color: #6b7280;">Bạn nhận email này vì đã bật nhắc học qua email trong lộ trình cá nhân.</p>
</body>
</html>
