<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_type' => 'required|string|max:255',
            'section_key' => 'nullable|string|max:255',
            'bai' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable',
            'content_json' => 'nullable|string|max:524288',
            'order' => 'required|integer',
        ];
    }

    public function messages(): array
    {
        return [
            'section_type.required' => 'Loại section không được để trống.',
            'order.required' => 'Thứ tự không được để trống.',
        ];
    }
}
