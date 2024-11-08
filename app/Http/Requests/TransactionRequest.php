<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class TransactionRequest extends FormRequest
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
      'date' => ['required', 'date', 'date_format:Y/m/d'],
      'amount' => ['required', 'numeric', 'gt:0'],
      'source_account_id' => [
        'required',
        'integer',
        'exists:accounts,id',
        'different:destination_account_id',
      ],
      'destination_account_id' => ['nullable', 'integer', 'exists:accounts,id', 'different:source_account_id', 'required_if:transaction_type,' . TransactionType::TRANSFER->value],
      'note' => ['nullable', 'string', 'max:255'],
      'transaction_type' => ['required', 'string', Rule::enum(TransactionType::class)],
      'category_id' => ['nullable', 'integer', 'exists:categories,id'],
    ];
  }

  public function prepareForValidation()
  {
    $this->merge([
      'transaction_type' => $this->input('transaction_type.value'),
      'category_id' => $this->input('category.id'),
      'source_account_id' => $this->input('source_account.id'),
      'destination_account_id' => $this->input('destination_account.id'),
      'date' => $this->input('date'),
    ]);
  }

}
