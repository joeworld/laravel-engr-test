<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'provider_name' => 'required|string|max:255',
            'code' => 'required|string|exists:hmos,code',
            'encounter_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Get custom messages for validation errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'provider_name.required' => 'The provider name is required.',
            'code.required' => 'The HMO code is required.',
            'code.exists' => 'The specified HMO does not exist.',
            'encounter_date.required' => 'The encounter date is required.',
            'items.required' => 'At least one order item is required.',
            'items.*.name.required' => 'Each order item must have a name.',
            'items.*.unit_price.required' => 'Each order item must have a unit price.',
            'items.*.quantity.required' => 'Each order item must have a quantity.',
        ];
    }
}