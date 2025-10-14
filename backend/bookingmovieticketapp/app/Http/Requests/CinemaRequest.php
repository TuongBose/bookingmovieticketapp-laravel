<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CinemaRequest extends FormRequest
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
            "name"=> "required|string|max:255",
            "city"=> "required|string|max:100",
            "coordinates"=> "required|string|max:50",
            "address"=> "required|string|max:255",
            "phonenumber"=> "required|string|max:20",
            "maxroom"=> "required|integer|min:1",
            "imagename"=> "required|string|max:100",
            "isactive"=> "boolean",
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên rạp chiếu là bắt buộc.',
            'city.required' => 'Thành phố là bắt buộc.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'maxroom.required' => 'Số lượng phòng chiếu tối đa là bắt buộc.',
            'maxroom.integer' => 'Số lượng phòng chiếu phải là số nguyên.',
            'maxroom.min' => 'Số lượng phòng chiếu tối thiểu là 1.',
            'phonenumber.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'imagename.max' => 'Tên hình ảnh không được vượt quá 100 ký tự.',
        ];
    }
}
