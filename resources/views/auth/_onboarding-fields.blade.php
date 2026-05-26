@php
    $currentUser = $user ?? null;
    $savedReasons = old('learning_reasons', $currentUser?->learning_reasons ?? []);
    $savedAnswers = old('placement_answers', $currentUser?->placement_answers ?? []);
    $placementLevels = \App\Support\OnboardingOptions::placementQuestionLevels();
@endphp

<div class="pt-4 border-t border-gray-100 space-y-6">
    <div>
        <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">Placement test đầu vào</h2>
                <p class="text-sm text-gray-500">Làm nhanh 10-15 câu để hệ thống chọn điểm bắt đầu chính xác hơn.</p>
            </div>
            <span class="inline-flex w-fit rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">Ưu tiên cao</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($placementQuestions ?? [] as $question)
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <div class="mb-2 flex items-start justify-between gap-2">
                        <p class="text-sm font-semibold text-gray-900">{{ $loop->iteration }}. {{ $question['prompt'] }}</p>
                        <span class="shrink-0 rounded-full bg-blue-50 px-2 py-1 text-[11px] font-semibold text-blue-700">{{ $placementLevels[$question['key']] ?? 'Tổng hợp' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($question['options'] as $option)
                            <label class="flex items-center gap-2 rounded border border-gray-200 bg-white px-2 py-2 text-sm text-gray-700 hover:border-red-200">
                                <input type="radio"
                                       name="placement_answers[{{ $question['key'] }}]"
                                       value="{{ $option }}"
                                       class="border-gray-300 text-red-600 focus:ring-red-500"
                                       @checked(($savedAnswers[$question['key']] ?? null) === $option)>
                                <span>{{ $option }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <p class="mt-2 text-xs text-gray-500">Nếu bỏ qua test, hệ thống sẽ dùng trình độ bạn tự chọn bên dưới.</p>
    </div>

    <div>
        <h2 class="text-base font-bold text-gray-900 mb-1">Lý do học</h2>
        <p class="text-sm text-gray-500 mb-3">Dùng để cá nhân hóa từ vựng ưu tiên, ví dụ trong bài và thông điệp nhắc học.</p>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($learningReasonOptions ?? [] as $value => $label)
                <label class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 hover:border-amber-200">
                    <input type="checkbox"
                           name="learning_reasons[]"
                           value="{{ $value }}"
                           class="rounded border-gray-300 text-amber-500 focus:ring-amber-500"
                           @checked(in_array($value, $savedReasons, true))>
                    <span>{{ $label }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="text-base font-bold text-gray-900 mb-1">Lộ trình cá nhân</h2>
        <p class="text-sm text-gray-500 mb-4">Phần này là fallback và mục tiêu dài hạn; placement test nếu có sẽ tự chọn lại điểm bắt đầu.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="onboarding_level" class="block text-sm font-medium text-gray-700 mb-1.5">Trình độ hiện tại</label>
                <select id="onboarding_level" name="onboarding_level" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                    @foreach($levelOptions ?? [] as $value => $label)
                        <option value="{{ $value }}" @selected(old('onboarding_level', $currentUser?->onboarding_level ?: 'new') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="jlpt_goal" class="block text-sm font-medium text-gray-700 mb-1.5">Mục tiêu JLPT</label>
                <select id="jlpt_goal" name="jlpt_goal" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                    @foreach($goalOptions ?? [] as $value => $label)
                        <option value="{{ $value }}" @selected(old('jlpt_goal', $currentUser?->jlpt_goal ?: 'N5') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            <div>
                <label for="daily_study_minutes" class="block text-sm font-medium text-gray-700 mb-1.5">Thời gian rảnh mỗi ngày</label>
                <select id="daily_study_minutes" name="daily_study_minutes" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-sm">
                    @foreach($dailyMinuteOptions ?? [10, 20, 30, 45, 60] as $minutes)
                        <option value="{{ $minutes }}" @selected((int) old('daily_study_minutes', $currentUser?->daily_study_minutes ?: 20) === $minutes)>{{ $minutes }} phút/ngày</option>
                    @endforeach
                </select>
            </div>

            <label class="flex items-center gap-3 rounded border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-700 md:mt-7">
                <input type="checkbox" name="email_reminders_enabled" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500" @checked(old('email_reminders_enabled', $currentUser?->email_reminders_enabled ?? true))>
                <span>Gửi email nhắc học khi streak sắp đứt</span>
            </label>
        </div>
    </div>
</div>
