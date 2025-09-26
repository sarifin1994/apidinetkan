<?php

namespace App\Http\Requests\Owner;

use App\Enums\OltDeviceEnum;
use Illuminate\Foundation\Http\FormRequest;

class LicenseRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'limit_nas' => 'required|integer|min:0',
            'limit_pppoe' => 'required|integer|min:0',
            'limit_hs' => 'required|integer|min:0',
            'limit_vpn' => 'required|integer|min:0',
            'limit_vpn_remote' => 'required|integer|min:0',
            'limit_user' => 'required|integer|min:0',
            'olt_epon_limit' => 'required|integer|min:0',
            'olt_gpon_limit' => 'required|integer|min:0',
            'olt_epon' => 'nullable|boolean',
            'olt_gpon' => 'nullable|boolean',
            'olt_models' => 'nullable|array',
            'olt_models.*' => 'nullable|string|in:' . implode(',', OltDeviceEnum::getValues()),
            'payment_gateway' => 'nullable|boolean',
            'whatsapp' => 'nullable|boolean',
            'invoice_addon' => 'nullable|boolean',
            'max_buy' => 'required|integer|min:0',
            'color' => 'required|string|max:7'
        ];
    }
}
