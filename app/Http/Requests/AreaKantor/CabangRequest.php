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
            'id_mk_area'   => 'required|numeric',
            'nama'         => 'required',
            'id_provinsi'  => 'required|numeric',
            'id_kabupaten' => 'required|numeric',
            'id_kecamatan' => 'required|numeric',
            'id_kelurahan' => 'required|numeric',
            'jenis_kantor' => 'in:CABANG,KAS',
            'flg_aktif'    => 'in:false,true'
        ];
    }

    public function messages(){
        return [
            'id_mk_area.required'   => ':attribute belum diisi',
            'nama.required'         => ':attribute belum diisi',
            'id_provinsi.required'  => ':attribute belum diisi',
            'id_kabupaten.required' => ':attribute belum diisi',
            'id_kecamatan.required' => ':attribute belum diisi',
            'id_kelurahan.required' => ':attribute belum diisi',
            'jenis_kantor.in'       => ':attribute harus salah satu dari jenis berikut :values',
            'flg_aktif.in'          => ':attribute harus salah satu dari jenis berikut :values',
            'id_mk_area'            => ':attribute harus berupa angka',
            'id_provinsi'           => ':attribute harus berupa angka',
            'id_kabupaten'          => ':attribute harus berupa angka',
            'id_kecamatan'          => ':attribute harus berupa angka',
            'id_kelurahan'          => ':attribute harus berupa angka'
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
