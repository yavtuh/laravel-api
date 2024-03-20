<?php

namespace App\Http\Requests\Crm;

use Illuminate\Foundation\Http\FormRequest;

class CrmSettingsUpdateRequest extends FormRequest
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
            'working_days' => 'required|array',
            'working_days.*' => 'integer|min:0|max:6',
            'working_hours_start' => 'required|date_format:H:i',
            'working_hours_end' => 'required|date_format:H:i',
            'daily_cap' => 'required|integer',
            'crmId' => 'required|integer',
            'is_active' => 'required|boolean',
            'generate_email_if_missing' => 'required|boolean',
            'skip_after_workings' => 'required|boolean',
        ];
    }
}
