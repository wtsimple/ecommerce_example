<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'sku' => 'sometimes|string|exists:products,sku',
            'category' => 'sometimes|string|exists:categories,name',
            'tags' => 'sometimes|array',
            'rating_higher_than' => 'sometimes|numeric|min:0|max:5',
            'min_price' => 'sometimes|int|min:0',
            'max_price' => 'sometimes|int|min:0',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
