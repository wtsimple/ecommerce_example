<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can(Role::UPDATE_PRODUCT);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'sku' => 'string|required|exists:products,sku',
            'name' => 'string|required',
            'price' => 'sometimes|integer|min:0',
            'amount' => 'sometimes|integer|min:0',
            'description' => 'sometimes|string',
            'additional_info' => 'sometimes|string',
            'avg_rating' => 'sometimes|numeric|min:0'
        ];
    }
}
