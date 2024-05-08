<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateRequest extends FormRequest
{

    protected $redirect = '/dashboard';

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
            'id' => 'required',
            'name' => 'required|max:100',
            // 'photo' => 'nullable|file|image',
            // 'email' => 'required|email|unique:m_user',
            'password' => 'required|min:6',
        ];
    }

}
