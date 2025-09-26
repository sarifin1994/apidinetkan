<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HotspotUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'Admin';
    }

    public function rules(): array
    {
        $rules = [
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
            // 'profile' => 'required|string|exists:hotspot_profiles,name',
            // 'server' => 'nullable|string|max:255',
            // 'nas' => 'nullable|string|max:255',
            // 'statusPayment' => 'required|string|in:Paid,Unpaid',
            // 'total' => 'nullable|numeric',
            // 'reseller_id' => 'nullable|exists:hotspot_resellers,id'
        ];

        // Only require password for new users or if password field is not empty
        if ($this->isMethod('POST') || !empty($this->password)) {
            $rules['password'] = 'required|string|min:6|max:255';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'username.required' => 'The username is required.',
            'username.max' => 'The username cannot be longer than 255 characters.',
            'profile.required' => 'The profile is required.',
            'profile.exists' => 'The selected profile is invalid.',
            'server.max' => 'The server name cannot be longer than 255 characters.',
            'nas.max' => 'The NAS cannot be longer than 255 characters.',
            'statusPayment.required' => 'The payment status is required.',
            'statusPayment.in' => 'The payment status must be either Paid or Unpaid.',
            'total.numeric' => 'The total must be a number.',
            'reseller_id.exists' => 'The selected reseller is invalid.',
            'password.required' => 'The password is required.',
            'password.min' => 'The password must be at least 6 characters.',
            'password.max' => 'The password cannot be longer than 255 characters.',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'username',
            'profile' => 'profile',
            'server' => 'hotspot server',
            'nas' => 'NAS',
            'statusPayment' => 'payment status',
            'total' => 'total',
            'reseller_id' => 'reseller',
            'password' => 'password',
        ];
    }
}
