<?php

namespace App\Http\Requests\Dinetkan;

use App\Enums\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UserDinetkanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->ext_role === 'dinetkan';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
//            'username' => 'required|lowercase|regex:/^[a-z0-9_]+$/|unique:users,username',
            'company_name' => 'required',
//            'full_name' => 'required',
            'email' => 'required|email|unique:users',
//            'whatsapp' => 'required|unique:users,whatsapp',
            'whatsapp' => 'required',
            'status' => 'required|in:' . implode(',', UserStatusEnum::getValues()),
//            'license_dinetkan_id' => 'required|exists:license_dinetkan,id',
//            'next_due' => 'required|date',
//            'password' => ['required', 'min:6', \Illuminate\Validation\Rules\Password::defaults()],

//            'vlan' => 'required',
//            'metro' => 'required',
//            'vendor' => 'required',
//            'trafic_mrtg' => 'required',
//            'ip_prefix' => 'required',
//            'otc_license_dinetkan_id' => 'required|integer',
//            'mrc_license_dinetkan_id' => 'required|integer'

            'first_name' => 'required',
            'last_name' => 'required',
            'id_card' => 'required',
            'npwp' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'province_id' => 'required',
            'regency_id' => 'required',
            'district_id' => 'required',
            'village_id' => 'required'
        ];

        if ($this->isMethod('put')) {
//            $rules['username'] = 'required|exists:users,username';
            $rules['email'] = 'required|email|exists:users,email';
            $rules['password'] = 'nullable|min:6';
        }

        return $rules;
    }
}
