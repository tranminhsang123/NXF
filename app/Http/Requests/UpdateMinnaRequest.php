<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMinnaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $minnaId = $this->route('minna')?->id;

        return [
            'number' => ['required', 'integer', Rule::unique('minna_lessons', 'number')->ignore($minnaId)],
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Số bài không được để trống.',
            'number.unique' => 'Số bài này đã tồn tại.',
            'title.required' => 'Tiêu đề không được để trống.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
        ];
    }
}
