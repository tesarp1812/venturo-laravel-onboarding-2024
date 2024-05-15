<?php

namespace App\Http\Requests\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // Add Validator class import
use App\Http\Resources\UserRole\UserRoleResource;

class CreateRequest extends FormRequest
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
            'name' => 'required|max:100',
            'access' => 'required|max:100',
        ];
    }

    // failed validator
    public $validator = null;

    /**
     * Simpan pesan error ketika validasi gagal
     *
     * @return void
     */

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function store(CreateRequest $request)
   {
       if (isset($request->validator) && $request->validator->fails()) {
           return response()->failed($request->validator->errors());
       }

       $payload = $request->only(['name', 'access']);
       $role = $this->role->create($payload);
      
       if (!$role['status']) {
           return response()->failed($role['error']);
       }

        return response()->success(
            new UserRoleResource(
                     $role['data']), 
                     'Data Role berhasil disimpan'
                );

    }
}
