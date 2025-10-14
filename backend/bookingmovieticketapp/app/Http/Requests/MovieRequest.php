<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer|min:1',
            'releasedate' => 'required|date',
            'posterurl' => 'nullable|string|max:255',
            'bannerurl' => 'nullable|string|max:255',
            'agerating' => 'nullable|string|max:10',
            'voteaverage' => 'required|numeric|min:0|max:10',
            'director' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên phim là bắt buộc.',
            'name.max' => 'Tên phim không được vượt quá 255 ký tự.',
            'duration.integer' => 'Thời lượng phim phải là số nguyên.',
            'duration.min' => 'Thời lượng phim phải lớn hơn 0.',
            'releasedate.required' => 'Ngày phát hành là bắt buộc.',
            'releasedate.date' => 'Ngày phát hành không hợp lệ.',
            'voteaverage.required' => 'Điểm đánh giá là bắt buộc.',
            'voteaverage.numeric' => 'Điểm đánh giá phải là số.',
            'voteaverage.min' => 'Điểm đánh giá tối thiểu là 0.',
            'voteaverage.max' => 'Điểm đánh giá tối đa là 10.',
        ];
    }
}
