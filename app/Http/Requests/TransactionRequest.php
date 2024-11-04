<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
      'source_account_id' => ['nullable', 'integer', 'exists:accounts,id', 'different:destination_account_id', 'required_if:transaction_type,' . TransactionType::TRANSFER->value],
      'destination_account_id' => ['nullable', 'integer', 'exists:accounts,id', 'different:source_account_id', 'required_if:transaction_type,' . TransactionType::TRANSFER->value],
      'note' => ['required', 'string', 'max:255'],
      'transaction_type' => ['required', 'string', Rule::enum(TransactionType::class)],
      'category_id' => ['required', 'integer', 'exists:categories,id'],
    ];
  }

  public function prepareForValidation()
  {
    $this->merge([
      'transaction_type' => $this->input('transaction_type.value'),
      'category_id' => $this->input('category.id'),
      'source_account_id' => $this->input('source_account.id'),
      'destination_account_id' => $this->input('destination_account.id'),
      'date'=> $this->input('date'),
    ]);
  }

  // public function messages()
  // {
  //   return [
  //     // 'user_id.exists' => 'The user specified does not exist.',
  //     // 'user_id.required' => 'Please select a user.',

  //     'user_id.required' => 'The user field is required.',
  //     'user_id.exists' => 'The selected user is invalid.',
  //   ];
  // }

  // public function attributes()
  // {
  //   return [
  //     'user_id' => 'writer'
  //   ];
  // }
}
