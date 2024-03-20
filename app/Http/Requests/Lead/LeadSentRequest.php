<?php

namespace App\Http\Requests\Lead;

use Illuminate\Foundation\Http\FormRequest;

class LeadSentRequest extends FormRequest
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
            'crm' => 'required|integer',
            'fromInterval' => 'required|integer',
            'toInterval' => 'required|integer',
            'sendNow' => 'required|boolean',
            'startDate' => 'nullable|date',
            'leads' => 'required|array'
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('startDate', 'required|date|after_or_equal:today', function ($input) {
            return !$input->sendNow;
        });
    }


}
