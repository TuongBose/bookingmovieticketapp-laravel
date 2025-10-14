<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'bookingid' => 'required|integer|exists:bookings,id',
            'totalprice' => 'required|integer|min:0',
            'paymentmethod' => 'required|string|max:50',
            'paymentstatus' => 'required|string|max:50',
            'paymenttime' => 'required|date', // kiểm tra định dạng ngày giờ hợp lệ
        ];
    }

    public function messages(): array
    {
        return [
            'bookingid.required' => 'Mã đặt vé (bookingid) là bắt buộc.',
            'bookingid.exists' => 'Mã đặt vé không tồn tại.',
            'totalprice.required' => 'Tổng giá là bắt buộc.',
            'paymentmethod.required' => 'Phương thức thanh toán là bắt buộc.',
            'paymentstatus.required' => 'Trạng thái thanh toán là bắt buộc.',
            'paymenttime.required' => 'Thời gian thanh toán là bắt buộc.',
            'paymenttime.date' => 'Thời gian thanh toán phải đúng định dạng ngày giờ.',
        ];
    }
}
