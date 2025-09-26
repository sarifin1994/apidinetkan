<?php

namespace App\Http\Requests\Owner;

use App\Enums\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'username' => 'required|lowercase|regex:/^[a-z0-9_]+$/|unique:users,username',
            'company_name' => 'required',
            'full_name' => 'required',
            'email' => 'required|email|unique:users',
            'whatsapp' => 'required',
            'status' => 'required|in:' . implode(',', UserStatusEnum::getValues()),
            'license_id' => 'required|exists:license,id',
            'next_due' => 'required|date',
            'password' => ['required', 'min:6', \Illuminate\Validation\Rules\Password::defaults()],
        ];

        if ($this->isMethod('put')) {
            $rules['username'] = 'required|exists:users,username';
            $rules['email'] = 'required|email|exists:users,email';
            $rules['password'] = 'nullable|min:6';
        }

        return $rules;
    }
}
