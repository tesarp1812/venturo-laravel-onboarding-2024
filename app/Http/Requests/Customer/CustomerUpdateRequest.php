<?php

namespace App\Http\Requests\Customer;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use ProtoneMedia\LaravelMixins\Request\ConvertsBase64ToFiles;

class CustomerUpdateRequest extends FormRequest
{
    use ConvertsBase64ToFiles; // Library untuk convert base64 menjadi File
    public $validator;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }


    /**
     * inisialisasi key "photo" dengan value base64 sebagai "FILE"
     *
     * @return array
     */
    protected function base64FileKeys(): array
    {
        return [
            'photo' => 'foto-customer.jpg',
        ];
    }
}
