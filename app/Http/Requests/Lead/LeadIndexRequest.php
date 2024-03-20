<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class LeadIndexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => 'nullable|string',
            'startDate' => 'nullable|string',
            'endDate' => 'nullable|string',
            'sendStartDate' => 'nullable|string',
            'sendEndDate' => 'nullable|string',
            'funnel' => 'nullable|string',
            'buyer' => 'nullable|string',
            'leadStatus' => 'nullable|string',
            'sentStatus' => 'nullable|string',
            'domain' => 'nullable|string',
            'crm' => 'nullable|string',

        ];
    }
}
