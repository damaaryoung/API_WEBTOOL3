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
            'id_calon_debitur'  => 'numeric',
            'id_penjamin'       => 'numeric',
            'id_provinsi'       => 'numeric',
            'id_kabupaten'      => 'numeric',
            'id_kecamatan'      => 'numeric',
            'id_kelurahan'      => 'numeric',
            'rt'                => 'numeric',
            'rw'                => 'numeric',
            'lama_usaha'        => 'numeric',
            'telp_tempat_usaha' => 'numeric',
            'flg_aktif'         => 'numeric'
        ];
    }

    public function messages(){
        return [
            'id_calon_debitur.numeric'  => ':attribute harus berupa angka',
            'id_penjamin.numeric'       => ':attribute harus berupa angka',
            'id_provinsi.numeric'       => ':attribute harus berupa angka',
            'id_kabupaten.numeric'      => ':attribute harus berupa angka',
            'id_kecamatan.numeric'      => ':attribute harus berupa angka',
            'id_kelurahan.numeric'      => ':attribute harus berupa angka',
            'rt.numeric'                => ':attribute harus berupa angka',
            'rw.numeric'                => ':attribute harus berupa angka',
            'lama_usaha.numeric'        => ':attribute harus berupa angka',
            'telp_tempat_usaha.numeric' => ':attribute harus berupa angka',
            'flg_aktif.numeric'         => ':attribute harus berupa angka'
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
