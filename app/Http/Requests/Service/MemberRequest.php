<?php

namespace App\Http\Requests\Service;

use App\Enums\MemberStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_member_new' => 'nullable|string',
            'member_id' => 'nullable|string',
            'id_member' => 'nullable|digits_between:3,11',
            'full_name' => 'required|string',
            'email' => 'nullable|email',
            'wa' => 'nullable|numeric',
            'address' => 'nullable|string',
            'status' => 'required|string|in:' . implode(',', MemberStatusEnum::getValues()),
            'no_ktp' => 'nullable|string',
            'npwp' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'province_id' =>'nullable|numeric',
            'regency_id' => 'nullable|numeric',
            'district_id' => 'nullable|numeric',
            'village_id' => 'nullable|numeric'
        ];
    }
}
