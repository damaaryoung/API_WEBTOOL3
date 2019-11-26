<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class PemAgKeReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status_pengguna_ken.*' => 'in:PEMILIK,PENYEWA',
            'jml_roda_ken.*'        => 'numeric',
            'kondisi_ken.*'         => 'in:LAYAK,KURANG,TIDAK',
            'km_ken.*'              => 'numeric'
        ];
    }

    public function messages(){
        return [
            'status_pengguna_ken.*.in'  => ':attribute harus salah satu dari jenis berikut :values',
            'jml_roda_ken.*.numeric'    => ':attribute harus berupa angka',
            'kondisi_ken.*.in'          => ':attribute harus salah satu dari jenis berikut :values',
            'km_ken.*.numeric'          => ':attribute harus berupa angka'
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
