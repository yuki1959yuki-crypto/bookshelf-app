<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreReadingPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'book_id' => [
                'required',
                'exists:books,id',
                Rule::unique('reading_plans', 'book_id')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'target_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'book_id.required' => '書籍を選択してください。',
            'book_id.exists' => '選択された書籍が存在しません。',
            'book_id.unique' => 'この書籍の読書計画はすでに作成されています。',
            'target_date.required' => '目標期日を入力してください。',
            'target_date.date' => '有効な日付を入力してください。',
            'target_date.after_or_equal' => '目標期日は本日以降の日付を指定してください。',
        ];
    }
}
