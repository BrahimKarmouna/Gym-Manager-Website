<?php

namespace App\Http\Requests;

use App\Enums\TransactionType;
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
      'date' => ['required', Rule::unique('date')->ignore($this->date)],
      'amount' => ['required', 'numeric', 'gt:0'],
      'from' => ['required', 'string', 'exists:accounts,id'],
      'to' => ['required', 'integer', 'exists:accounts,id'],
      'note' => ['required', 'string', 'max:255'],
      'type' => ['required', Rule::enum(TransactionType::class)],
      'category' => ['required', 'integer', 'exists:transaction_categories,id'],
    ];
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
