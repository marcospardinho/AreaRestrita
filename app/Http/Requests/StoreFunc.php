<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFunc extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'usuario' => 'required|unique:funcionarios',
            'cpf' => 'required|unique:funcionarios',
            'nome' => 'required',
            'setor' => 'required',
            'senha' => 'required'
        ];
    }
}
