<?php

namespace App\Http\Requests\Owner;

use App\Enums\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'Owner';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'coupon_name' => 'required',
            'used' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ];

        return $rules;
    }
}
