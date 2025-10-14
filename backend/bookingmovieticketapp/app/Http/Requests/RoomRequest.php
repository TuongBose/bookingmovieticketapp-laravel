<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoomRequest extends FormRequest
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
            'cinemaid' => 'required|integer|exists:cinemas,id',
            'name' => 'required|string|max:50',
            'seatcolumnmax' => 'required|integer|min:1',
            'seatrowmax' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'cinemaid.required' => 'Trường rạp chiếu phim là bắt buộc.',
            'cinemaid.exists' => 'Rạp chiếu phim không tồn tại trong hệ thống.',
            'name.required' => 'Tên phòng chiếu là bắt buộc.',
            'name.max' => 'Tên phòng chiếu không được vượt quá 50 ký tự.',
            'seatcolumnmax.required' => 'Số cột ghế là bắt buộc.',
            'seatcolumnmax.min' => 'Số cột ghế phải lớn hơn hoặc bằng 1.',
            'seatrowmax.required' => 'Số hàng ghế là bắt buộc.',
            'seatrowmax.min' => 'Số hàng ghế phải lớn hơn hoặc bằng 1.',
        ];
    }
}
