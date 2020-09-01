<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductsRequest extends FormRequest
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
            'name' => 'required',
            'code' => 'required',
            'price' => 'required',
            'modules' => 'required',
            'planId' => 'required',
            'numberOfUser' => 'required',
<<<<<<< HEAD
            'renewal_charge_type' => 'required',
            'renewal_charge' => 'required',
            'features' => 'nullable',
            'description' => 'nullable',
            'image' => 'nullable'
=======
            'description' => 'nullable'
>>>>>>> f69b94ae2f0e9bb805351bcc9b751d302a3d26e7
        ];
    }
}
