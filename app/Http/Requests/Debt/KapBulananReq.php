<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class KapBulananReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pemasukan_cadebt'      => 'numeric',
            'pemasukan_pasangan'    => 'numeric',
            'pemasukan_penjamin'    => 'numeric',
            'biaya_rumah_tangga'    => 'numeric',
            'biaya_transport'       => 'numeric',
            'biaya_pendidikan'      => 'numeric',
            'biaya_telp_listr_air'  => 'numeric',
            'biaya_lain'            => 'numeric'
        ];
    }

    public function messages(){
        return [
            'pemasukan_cadebt.numeric'      => ':attribute harus berupa angka',
            'pemasukan_pasangan.numeric'    => ':attribute harus berupa angka',
            'pemasukan_penjamin.numeric'    => ':attribute harus berupa angka',
            'biaya_rumah_tangga.numeric'    => ':attribute harus berupa angka',
            'biaya_transport.numeric'       => ':attribute harus berupa angka',
            'biaya_pendidikan.numeric'      => ':attribute harus berupa angka',
            'biaya_telp_listr_air.numeric'  => ':attribute harus berupa angka',
            'biaya_lain.numeric'            => ':attribute harus berupa angka'
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
