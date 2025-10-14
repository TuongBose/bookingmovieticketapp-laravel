<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
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
            'userid' => 'required|integer|exists:users,id',
            'showtimeid' => 'required|integer|exists:showtimes,id',
            'totalprice' => 'required|integer|min:0',
            'paymentmethod' => 'required|string|max:50',
            'paymentstatus' => 'required|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'userid.required' => 'User ID là bắt buộc.',
            'showtimeid.required' => 'Showtime ID là bắt buộc.',
            'totalprice.required' => 'Tổng giá là bắt buộc.',
            'paymentmethod.required' => 'Phương thức thanh toán là bắt buộc.',
            'paymentstatus.required' => 'Trạng thái thanh toán là bắt buộc.',
        ];
    }
}
