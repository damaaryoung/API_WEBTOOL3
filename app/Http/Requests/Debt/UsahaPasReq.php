<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class UsahaPasReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_calon_debitur'  => 'numeric',
            'id_pasangan'       => 'numeric',
            'id_provinsi'       => 'numeric',
            'id_kabupaten'      => 'numeric',
            'id_kecamatan'      => 'numeric',
            'id_kelurahan'      => 'numeric',
            'rt'                => 'numeric',
            'rw'                => 'numeric',
            'tgl_mulai_usaha'   => 'date_format:d-m-Y',
            'telp_tempat_usaha' => 'numeric',
            'flg_aktif'         => 'numeric'
        ];
    }

    public function messages(){
        return [
            'id_calon_debitur.numeric'    => ':attribute harus berupa angka',
            'id_pasangan.numeric'         => ':attribute harus berupa angka',
            'id_provinsi.numeric'         => ':attribute harus berupa angka',
            'id_kabupaten.numeric'        => ':attribute harus berupa angka',
            'id_kecamatan.numeric'        => ':attribute harus berupa angka',
            'id_kelurahan.numeric'        => ':attribute harus berupa angka',
            'rt.numeric'                  => ':attribute harus berupa angka',
            'rw.numeric'                  => ':attribute harus berupa angka',
            'tgl_mulai_usaha.date_format' => ':attribute harus berupa angka dengan format :format',
            'telp_tempat_usaha.numeric'   => ':attribute harus berupa angka',
            'flg_aktif.numeric'           => ':attribute harus berupa angka'
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
