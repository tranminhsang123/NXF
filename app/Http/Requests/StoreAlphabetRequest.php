<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlphabetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Cho phép tất cả user (có thể thêm auth sau)
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
                // Unique: không được trùng character + type
                Rule::unique('alphabets', 'character')
                    ->where('type', $this->type)
                    ->whereNull('deleted_at')
            ],
            'romaji' => 'required|string|max:50',
            'type' => 'required|in:hiragana,katakana,romaji',
            'category' => 'nullable|string|max:20'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'character.required' => 'Ký tự không được để trống.',
            'character.max' => 'Ký tự không được vượt quá 10 ký tự.',
            'character.unique' => 'Ký tự này đã tồn tại cho loại này.',
            'romaji.required' => 'Romaji không được để trống.',
            'romaji.max' => 'Romaji không được vượt quá 50 ký tự.',
            'type.required' => 'Loại không được để trống.',
            'type.in' => 'Loại phải là hiragana, katakana hoặc romaji.',
            'category.max' => 'Phân loại không được vượt quá 20 ký tự.',
        ];
    }
}
