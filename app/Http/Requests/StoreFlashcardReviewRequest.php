<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlashcardReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'minna_section_id' => ['required', 'integer', 'exists:minna_sections,id'],
            'card_index' => ['required', 'integer', 'min:0'],
            'quality' => ['required', 'integer', 'min:0', 'max:5'],
        ];
    }

    public function messages(): array
    {
        return [
            'minna_section_id.required' => 'Vui lòng chọn phần bài học.',
            'minna_section_id.integer' => 'Mã phần bài học không hợp lệ.',
            'minna_section_id.exists' => 'Phần bài học không tồn tại.',
            'card_index.required' => 'Thiếu vị trí thẻ cần lưu.',
            'card_index.integer' => 'Vị trí thẻ không hợp lệ.',
            'card_index.min' => 'Vị trí thẻ phải lớn hơn hoặc bằng 0.',
            'quality.required' => 'Vui lòng chọn mức độ ghi nhớ.',
            'quality.integer' => 'Mức độ ghi nhớ không hợp lệ.',
            'quality.min' => 'Mức độ ghi nhớ tối thiểu là 0.',
            'quality.max' => 'Mức độ ghi nhớ tối đa là 5.',
        ];
    }
}
