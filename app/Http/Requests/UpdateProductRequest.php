<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'productName' => ['string', 'required'],
            'productPrice' => ['numeric', 'required'],
            'stock' => ['numeric', 'required'],
            'productDescription' => ['string', 'required'],
            'redirectUrl' => ['string', 'required'],
        ];
    }
}
