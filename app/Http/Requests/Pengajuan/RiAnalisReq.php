<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RiAnalisReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            // Ringkasan Analisa CA
            'kuantitatif_ttl_pendapatan'  => 'integer',
            'kuantitatif_ttl_pengeluaran' => 'integer',
            'kuantitatif_pendapatan'      => 'integer',
            'kuantitatif_angsuran'        => 'integer',
            // 'kuantitatif_ltv'             => 'integer',
            // 'kuantitatif_dsr'             => 'integer',
            // 'kuantitatif_idir'            => 'integer',
            // 'kuantitatif_hasil'           => 'integer',
        ];
    }

    public function messages(){
        $integer     = ':attribute harus berupa angka / bilangan bulat dan tidak boleh dimulai dari 0';

        return [
            // Ringkasan Analisa CA
            'kuantitatif_ttl_pendapatan.integer'  => $integer,
            'kuantitatif_ttl_pengeluaran.integer' => $integer,
            'kuantitatif_pendapatan.integer'      => $integer,
            'kuantitatif_angsuran.integer'        => $integer,
            // 'kuantitatif_ltv.integer'             => $integer,
            // 'kuantitatif_dsr.integer'             => $integer,
            // 'kuantitatif_idir.integer'            => $integer,
            // 'kuantitatif_hasil.integer'           => $integer,
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
