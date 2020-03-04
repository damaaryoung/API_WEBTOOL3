<?php

namespace App\Http\Requests\AreaKantor;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class AreaPICReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_mk_area'    => 'required',
            'id_mk_cabang'  => 'required',
            'nama_area_pic' => 'required',
            'id_provinsi'   => 'required',
            'id_kabupaten'  => 'required',
            'id_kecamatan'  => 'required',
            'id_kelurahan'  => 'required',
            'id_pic'        => 'required',
            'flg_aktif'     => 'in:false,true'
        ];
    }

    public function messages(){
        return [
            'id_mk_area.required'    => ':attribute wajib diisi',
            'id_mk_cabang.required'  => ':attribute wajib diisi',
            'nama_area_pic.required' => ':attribute wajib diisi',
            'id_provinsi.required'   => ':attribute wajib diisi',
            'id_kabupaten.required'  => ':attribute wajib diisi',
            'id_kecamatan.required'  => ':attribute wajib diisi',
            'id_kelurahan.required'  => ':attribute wajib diisi',
            'id_pic.required'        => ':attribute wajib diisi',
            'flg_aktif.in'           => ':attribute harus salah satu dari jenis berikut :values'
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
