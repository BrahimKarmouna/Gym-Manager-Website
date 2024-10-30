<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
      'title' => ['required', 'string', 'max:255'],
      'content' => ['required', 'string', 'max:65536'],
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
