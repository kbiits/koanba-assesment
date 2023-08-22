<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
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
            'customerName' => ['required', 'string'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'redirectUrl' => ['required', 'string'],
        ];
    }
}
