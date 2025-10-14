<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SeatRequest extends FormRequest
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
            'roomid' => 'required|integer|exists:rooms,id',
            'seatnumber' => 'required|string|max:10',
        ];
    }

    public function messages(): array
    {
        return [
            'roomid.required' => 'Trường phòng chiếu là bắt buộc.',
            'roomid.exists' => 'Phòng chiếu không tồn tại trong hệ thống.',
            'seatnumber.required' => 'Số ghế là bắt buộc.',
            'seatnumber.max' => 'Số ghế không được vượt quá 10 ký tự.',
        ];
    }
}
