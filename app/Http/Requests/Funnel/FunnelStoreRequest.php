<?php

namespace App\Http\Requests\Funnel;

use Illuminate\Foundation\Http\FormRequest;

class FunnelStoreRequest extends FormRequest
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
            'description' => 'string|nullable',
            'name' => 'required|string|max:255',
            'settings' => 'required|array',
            'settings.*.crm_id' => 'required|integer|exists:crms,id',
            'settings.*.score' => 'required',
        ];
    }
}
