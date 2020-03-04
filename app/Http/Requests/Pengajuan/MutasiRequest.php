<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class MutasiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            // Mutasi Bank pada CA
            'urutan_mutasi'           => 'integer',
            'no_rekening_mutasi'      => 'numeric',
            'frek_debet_mutasi.*'     => 'integer',
            'nominal_debet_mutasi.*'  => 'integer',
            'frek_kredit_mutasi.*'    => 'integer',
            'nominal_kredit_mutasi.*' => 'integer',
            'saldo_mutasi.*'          => 'integer',
        ];
    }

    public function messages(){
        $integer     = ':attribute harus berupa angka / bilangan bulat dan tidak boleh dimulai dari 0';
        $numeric     = ':attribute harus berupa angka';

        return [
            // Mutasi Bank pada CA
            'urutan_mutasi.integer'           => $integer,
            'no_rekening_mutasi.numeric'      => $numeric,
            'frek_debet_mutasi.*.integer'     => $integer,
            'nominal_debet_mutasi.*.integer'  => $integer,
            'frek_kredit_mutasi.*.integer'    => $integer,
            'nominal_kredit_mutasi.*.integer' => $integer,
            'saldo_mutasi.*.integer'          => $integer,
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            // response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
