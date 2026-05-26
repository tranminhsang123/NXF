<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKanjiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'character' => [
                'required',
                'string',
                'max:10',
                Rule::unique('kanjis', 'character')->whereNull('deleted_at'),
            ],
            'meaning' => 'required|string|max:255',
            'on_reading' => 'nullable|string|max:100',
            'kun_reading' => 'nullable|string|max:100',
            'level' => 'required|in:N5,N4,N3,N2,N1',
            'stroke_count' => 'required|integer|min:1|max:30',
            'radical' => 'nullable|string|max:50',
            'examples' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'character.required' => 'Ký tự Kanji không được để trống.',
            'character.max' => 'Ký tự Kanji không được vượt quá 10 ký tự.',
            'character.unique' => 'Ký tự Kanji này đã tồn tại.',
            'meaning.required' => 'Nghĩa của Kanji không được để trống.',
            'meaning.max' => 'Nghĩa của Kanji không được vượt quá 255 ký tự.',
            'on_reading.max' => 'Âm On không được vượt quá 100 ký tự.',
            'kun_reading.max' => 'Âm Kun không được vượt quá 100 ký tự.',
            'level.required' => 'Cấp độ JLPT không được để trống.',
            'level.in' => 'Cấp độ phải thuộc N5, N4, N3, N2 hoặc N1.',
            'stroke_count.required' => 'Số nét không được để trống.',
            'stroke_count.integer' => 'Số nét phải là số nguyên.',
            'stroke_count.min' => 'Số nét phải lớn hơn hoặc bằng 1.',
            'stroke_count.max' => 'Số nét không được vượt quá 30.',
            'radical.max' => 'Bộ thủ không được vượt quá 50 ký tự.',
        ];
    }
}
