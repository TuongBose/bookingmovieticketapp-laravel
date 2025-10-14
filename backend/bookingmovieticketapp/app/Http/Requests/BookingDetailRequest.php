<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingDetailRequest extends FormRequest
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
            'seatid'    => 'required|integer|exists:seats,id',
            'price'     => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'bookingid.required' => 'Booking ID là bắt buộc.',
            'bookingid.exists'   => 'Booking ID không tồn tại.',
            'seatid.required'    => 'Seat ID là bắt buộc.',
            'seatid.exists'      => 'Seat ID không tồn tại.',
            'price.required'     => 'Giá vé là bắt buộc.',
            'price.integer'      => 'Giá vé phải là số nguyên.',
            'price.min'          => 'Giá vé không được nhỏ hơn 0.',
        ];
    }
}
