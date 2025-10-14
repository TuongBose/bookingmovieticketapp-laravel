<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'password'      => 'required|string|min:6',
            'retypepassword'=> 'required|same:password',
            'phonenumber'   => 'required|string|max:20',
            'dateofbirth'   => 'required|date_format:Y-m-d',
            'address'       => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'           => 'Họ và tên không được bỏ trống',
            'email.required'          => 'Email không được bỏ trống',
            'email.email'             => 'Email không hợp lệ',
            'password.required'       => 'Password không được bỏ trống',
            'retypepassword.required' => 'Vui lòng nhập lại mật khẩu',
            'retypepassword.same'     => 'Xác nhận mật khẩu không khớp',
            'phonenumber.required'    => 'Số điện thoại không được bỏ trống',
            'dateofbirth.required'    => 'Ngày sinh không được bỏ trống',
            'dateofbirth.date_format' => 'Ngày sinh phải theo định dạng yyyy-MM-dd',
        ];
    }
}
