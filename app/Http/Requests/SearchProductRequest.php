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
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
