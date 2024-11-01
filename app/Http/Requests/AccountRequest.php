<?php

namespace App\Http\Requests;

use App\Enums\AccountType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'rib' => 'required|string|max:24|min:24|unique:accounts',
            'balance' => 'required|numeric',
            'total_expense' => 'nullable|numeric',
            'total_income' => 'nullable|numeric',
            'account_type' => [
              'required',
              'string',
              Rule::enum(AccountType::class)
            ],
        ];
    }

    public function prepareForValidation()
    {
      $this->merge([
        'account_type' => $this->input('account_type.value')
      ]);
    }
}
