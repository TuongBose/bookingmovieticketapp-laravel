<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CastRequest extends FormRequest
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
            "movieid"=> "required|integer|exists:movies,id",
            "actorname"=> "required|string|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            'movieid.required' => 'Phim là bắt buộc.',
            'movieid.exists' => 'Phim không tồn tại trong hệ thống.',
            'actorname.required' => 'Tên diễn viên là bắt buộc.',
            'actorname.max' => 'Tên diễn viên không được vượt quá 255 ký tự.',
        ];
    }
}
