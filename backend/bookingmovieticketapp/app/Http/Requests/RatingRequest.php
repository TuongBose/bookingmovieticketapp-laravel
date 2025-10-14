<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RatingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'movieid' => 'required|integer|exists:movies,id',
            'userid' => 'required|integer|exists:users,id',
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'movieid.required' => 'Phim là bắt buộc.',
            'movieid.exists' => 'Phim không tồn tại trong hệ thống.',
            'userid.required' => 'Người dùng là bắt buộc.',
            'userid.exists' => 'Người dùng không tồn tại.',
            'rating.required' => 'Điểm đánh giá là bắt buộc.',
            'rating.integer' => 'Điểm đánh giá phải là số nguyên.',
            'rating.min' => 'Điểm đánh giá phải từ 1 trở lên.',
            'rating.max' => 'Điểm đánh giá không được vượt quá 10.',
            'comment.string' => 'Bình luận phải là chuỗi ký tự hợp lệ.',
            'comment.max' => 'Bình luận không được vượt quá 255 ký tự.',
        ];
    }
}
