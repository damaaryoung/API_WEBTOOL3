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
            'id_area_kerja' => 'required',
            'id_area_cabang'=> 'required',
            'nama_area_pic' => 'required',
            'id_prov'       => 'required',
            'id_kab'        => 'required',
            'id_kec'        => 'required',
            'id_kel'        => 'required',
            'flg_aktif'     => 'in:false,true'
        ];
    }

    public function messages(){
        return [
            'id_area_kerja.required' => ':attribute belum diisi',
            'id_area_cabang.required'=> ':attribute belum diisi',
            'nama_area_pic.required' => ':attribute belum diisi',
            'id_prov.required'       => ':attribute belum diisi',
            'id_kab.required'        => ':attribute belum diisi',
            'id_kec.required'        => ':attribute belum diisi',
            'id_kel.required'        => ':attribute belum diisi',
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
