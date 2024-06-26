<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class LeadStoreRequest extends FormRequest
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
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'required|string',
            'utm' => 'nullable',
            'user_agent' => 'nullable',
            'ip' => 'nullable|ip',
            'extra' => 'nullable',
            'domain' => 'nullable',
            'country' => 'nullable',
            'key' => 'nullable',
            'funnel' => 'nullable'
        ];
    }
}
