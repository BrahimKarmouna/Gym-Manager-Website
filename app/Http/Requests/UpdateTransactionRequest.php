<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateTransactionRequest extends FormRequest
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
      'date' => ['required', 'date'],
      'amount' => ['required', 'numeric', 'gt:0'],
      'source_account_id' => [
        'nullable',
        'integer',
        'exists:accounts,id',
        'different:destination_account_id',
      ],
      'destination_account_id' => ['nullable', 'integer', 'exists:accounts,id', 'different:source_account_id'],
      'note' => ['nullable', 'string', 'max:255'],
    ];
  }

  public function prepareForValidation()
  {
    $this->merge([

      'source_account_id' => $this->input('source_account.id'),
      'destination_account_id' => $this->input('destination_account.id'),
      'date' => $this->input('date'),
    ]);
  }
}
