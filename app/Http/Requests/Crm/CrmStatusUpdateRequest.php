<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class CrmStatusUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fields' => 'present|array',
            'method' => 'required|string',
            'content_type' => 'required|string',
            'crmId' => 'required|integer',
            'base_url' => 'required|url',
            'path_leads' => 'nullable|string',
            'path_uuid' => 'nullable|string',
            'path_status' => 'nullable|string',
            'local_field' => 'nullable|string',
        ];
    }
}
