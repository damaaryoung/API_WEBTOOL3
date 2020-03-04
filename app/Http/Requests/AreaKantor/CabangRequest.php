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
            'id_mk_area'   => 'required|integer',
            'nama'         => 'required',
            'id_provinsi'  => 'required|integer',
            'id_kabupaten' => 'required|integer',
            'id_kecamatan' => 'required|integer',
            'id_kelurahan' => 'required|integer',
            'jenis_kantor' => 'in:CABANG,KAS',
            'flg_aktif'    => 'in:false,true'
        ];
    }

    public function messages(){
        return [
            'id_mk_area.required'   => ':attribute wajib diisi',
            'nama.required'         => ':attribute wajib diisi',
            'id_provinsi.required'  => ':attribute wajib diisi',
            'id_kabupaten.required' => ':attribute wajib diisi',
            'id_kecamatan.required' => ':attribute wajib diisi',
            'id_kelurahan.required' => ':attribute wajib diisi',
            'jenis_kantor.in'       => ':attribute harus salah satu dari jenis berikut :values',
            'flg_aktif.in'          => ':attribute harus salah satu dari jenis berikut :values',
            'id_mk_area.integer'    => ':attribute harus berupa angka',
            'id_provinsi.integer'   => ':attribute harus berupa angka',
            'id_kabupaten.integer'  => ':attribute harus berupa angka',
            'id_kecamatan.integer'  => ':attribute harus berupa angka',
            'id_kelurahan.integer'  => ':attribute harus berupa angka'
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
