<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShowtimeRequest extends FormRequest
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
            'roomid' => 'required|integer|exists:rooms,id',
            'showdate' => 'required|date',
            'starttime' => 'required|date_format:Y-m-d H:i:s',
            'price' => 'required|integer|min:0',
            'isactive' => 'boolean'
        ];
    }

    public function messages(): array
    {
        return [
            'movieid.required' => 'Phim là bắt buộc.',
            'movieid.exists' => 'Phim không tồn tại.',
            'roomid.required' => 'Phòng chiếu là bắt buộc.',
            'roomid.exists' => 'Phòng chiếu không tồn tại.',
            'showdate.required' => 'Ngày chiếu là bắt buộc.',
            'showdate.date' => 'Ngày chiếu không hợp lệ.',
            'starttime.required' => 'Giờ chiếu là bắt buộc.',
            'starttime.date_format' => 'Giờ chiếu phải có định dạng Y-m-d H:i:s.',
            'price.required' => 'Giá vé là bắt buộc.',
            'price.integer' => 'Giá vé phải là số nguyên.',
            'price.min' => 'Giá vé không được âm.',
        ];
    }
}
