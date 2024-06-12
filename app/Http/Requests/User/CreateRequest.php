<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // Add Validator class import
use ConvertsBase64ToFiles;
use App\Http\Requests\User\UserResource;
use App\Http\Resources\User\UserResource as UserUserResource;

class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Change to true if authorization is not required
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // 'user_roles_id' => 'required',
            'name' => 'required|max:100',
            'photo' => 'nullable|file|image',
            'email' => 'required|email|unique:m_user',
            'password' => 'required|min:6',
        ];
    }

    /**
     * inisialisasi key "photo" dengan value base64 sebagai "FILE"
     *
     * @return array
     */
    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-user.jpg',
        ];
    }


    /**
     * Get the custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'password' => 'Kolom Password',
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

        $payload = $request->only(['email', 'name', 'password', 'photo','user_roles_id']);
        $user = $this->user->create($payload);

        if (!$user['status']) {
            return response()->failed($user['error']);
        }

        return response()->success(
            //new UserResource(
                new UserUserResource(
                    $user['data']
                ),
            'Data user berhasil disimpan'
        );
    }
}
