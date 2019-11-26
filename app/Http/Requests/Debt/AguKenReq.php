<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class AguKenReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tahun_ken.*'                => 'date_format:Y',
            'tgl_exp_pajak_ken.*'        => 'date_format:d-m-Y',
            'tgl_exp_stnk_ken.*'         => 'date_format:d-m-Y',
            'lamp_agunan_depan_ken.*'    => 'mimes:jpg,jpeg,png,pdf',
            'lamp_agunan_kanan_ken.*'    => 'mimes:jpg,jpeg,png,pdf',
            'lamp_agunan_kiri_ken.*'     => 'mimes:jpg,jpeg,png,pdf',
            'lamp_agunan_belakang_ken.*' => 'mimes:jpg,jpeg,png,pdf',
            'lamp_agunan_dalam_ken.*'    => 'mimes:jpg,jpeg,png,pdf',
            'validasi_ken.*'             => 'numeric'
        ];
    }

    public function messages(){
        return [
            'tahun.*.date_format'             => ':attribute harus berupa angka dengan format :format',
            'tgl_exp_pajak_ken.*.date_format' => ':attribute harus berupa angka dengan format :format',
            'tgl_exp_stnk_ken.*.date_format'  => ':attribute harus berupa angka dengan format :format',
            'lamp_agunan_depan_ken.*.mime'    => ':attribute harus bertipe :values',
            'lamp_agunan_kanan_ken.*.mime'    => ':attribute harus bertipe :values',
            'lamp_agunan_kiri_ken.*.mime'     => ':attribute harus bertipe :values',
            'lamp_agunan_belakang_ken.*.mime' => ':attribute harus bertipe :values',
            'lamp_agunan_dalam_ken.*.mime'    => ':attribute harus bertipe :values',
            'validasi_ken.*.numeric'          => ':attribute harus berupa angka'
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
