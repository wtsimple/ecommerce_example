<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class ListOutOfStockProductsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:1000',
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can(Role::LIST_OUT_OF_STOCK_PRODUCTS);
    }
}
