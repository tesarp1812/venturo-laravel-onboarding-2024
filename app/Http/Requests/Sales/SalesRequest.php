<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // Add Validator class import

class SalesRequest extends FormRequest
{
    public $validator;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }


    public function rules()
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    private function createRules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'm_customer_id' => 'required',
            'details.*.t_sales_id' => 'required',
            'details.*.m_product_id' => 'required',
            'details.*.m_product_detail_id' => 'required',
            'details.*.total_item' => 'numeric',
            'details.*.price' => 'numeric',
        ];
    }

    private function updateRules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'm_customer_id' => 'required',
        ];
    }

    public function attributes()
    {
        return [
            'date' => 'Transaction date',
            'm_customer_id' => 'Customer ID',
        ];
    }
}
