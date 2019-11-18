<?php

namespace App\Http\Requests\AreaKantor;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class CabangRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'id_mk_area'  => 'required',
            'nama'         => 'required',
            'id_provinsi'  => 'required',
            'id_kabupaten' => 'required',
            'id_kecamatan' => 'required',
            'id_kelurahan' => 'required',
            'flg_aktif'    => 'required|in:0,1'
        ];
    }

    public function messages(){
        return [
            'id_mk_area.required'  => ':attribute belum diisi',
            'nama.required'         => ':attribute belum diisi',
            'id_provinsi.required'  => ':attribute belum diisi',
            'id_kabupaten.required' => ':attribute belum diisi',
            'id_kecamatan.required' => ':attribute belum diisi',
            'id_kelurahan.required' => ':attribute belum diisi',
            'flg_aktif.required'    => ':attribute belum diisi',
            'flg_aktif.in'          => ':attribute harus salah satu dari jenis berikut :values'
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
                "errors"  => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
