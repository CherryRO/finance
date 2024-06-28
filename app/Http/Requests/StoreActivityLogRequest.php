<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityLogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'event_type_id' => 'required|exists:event_types,id',
            'user_id' => 'nullable|exists:users,id',
            'ip_address' => 'nullable|ip',
        ];
    }
}
