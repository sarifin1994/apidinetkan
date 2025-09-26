<?php

namespace App\Http\Requests\Admin;

use App\Enums\OltDeviceEnum;
use Illuminate\Foundation\Http\FormRequest;

class OltDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'Admin';
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'model' => 'required|string|in:' . implode(',', OltDeviceEnum::getValues()),
            'host' => ['required', 'string', 'max:255'],
        ];

        

        $model = $this->input('model');

        if (in_array($model, ['zte', 'fiberhome'])) {
            $rules = array_merge($rules, [
                'version' => 'required|string|max:255',
                'snmp_read_write' => 'required|string',
                'udp_port' => 'string|max:255'
            ]);
        } else {
            $rules = array_merge($rules, [
                'username' => 'required|string|max:255',
                'type' => 'required|string|in:epon,gpon',
            ]);

            if ($this->isMethod('POST') || !empty($this->password)) {
                $rules['password'] = 'required|string|min:6|max:255';
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'host.ip' => 'The IP address must be valid.',
            'password.min' => 'The password must be at least 6 characters.',
        ];
    }
}
