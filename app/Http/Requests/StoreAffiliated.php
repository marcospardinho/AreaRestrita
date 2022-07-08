<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAffiliated extends FormRequest
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
     *
     */

    public function rules()
    {
        return [
            'siape_status' => 'required',
            'siape_matricula' => 'required',
            'cpf' => 'required',
            'ncompleto' => 'required',
            'dtnasc' => 'required',
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
            'telefone' => 'required',
            'cep' => 'required',
            'uf' => 'required',
            'bairro' => 'required',
            'numero' => 'required',
            'cidade' => 'required',
            'terms' => 'required',
            'endereco' => 'required',

        ];
    }
}
