<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'hold_id' => ['required', 'exists:holds,id', new \App\Rules\IsHoldExpiredOrUsed],
        ];
    }


    public function messages(): array
    {
        return [
            'hold_id.required' => 'Hold ID is required',
            'hold_id.exists' => 'Hold ID does not exist',
        ];
    }
}
