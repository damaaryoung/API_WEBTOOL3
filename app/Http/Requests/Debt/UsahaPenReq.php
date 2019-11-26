<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class UsahaPenReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_prov_usaha_pen.*'   => 'numeric',
            'id_kab_usaha_pen.*'    => 'numeric',
            'id_kec_usaha_pen.*'    => 'numeric',
            'id_kel_usaha_pen.*'    => 'numeric',
            'rt_usaha_pen.*'        => 'numeric',
            'rw_usaha_pen.*'        => 'numeric',
            'tgl_mulai_usaha_pen.*' => 'date_format:d-m-Y',
            'no_telp_usaha_pen.*'   => 'numeric'
        ];
    }

    public function messages(){
        return [
            'id_prov_usaha_pen.*.numeric'       => ':attribute harus berupa angka',
            'id_kab_usaha_pen.*.numeric'        => ':attribute harus berupa angka',
            'id_kec_usaha_pen.*.numeric'        => ':attribute harus berupa angka',
            'id_kel_usaha_pen.*.numeric'        => ':attribute harus berupa angka',
            'rt_usaha_pen.*.numeric'            => ':attribute harus berupa angka',
            'rw_usaha_pen.*.numeric'            => ':attribute harus berupa angka',
            'tgl_mulai_usaha_pen.*.date_format' => ':attribute harus berupa angka dengan format :format',
            'no_telp_usaha_pen.*.numeric'       => ':attribute harus berupa angka'
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
