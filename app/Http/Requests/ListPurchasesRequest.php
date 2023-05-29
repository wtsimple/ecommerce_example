<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class ListPurchasesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'per_page' => 'sometimes|integer|min:1',
            'from' => 'sometimes|date',
            'to' => 'sometimes|date'
        ];
    }

    public function authorize(): bool
    {
        return $this->user()->can(Role::READ_ALL_PURCHASES);
    }
}
