<?php

namespace App\Http\Requests\AreaKantor;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class AreaRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nama'         => 'required',
            'id_provinsi'  => 'required',
            'id_kabupaten' => 'required',
            'flg_aktif'    => 'in:0,1'
        ];
    }

    public function messages(){
        return [
            'nama.required'         => ':attribute belum diisi',
            'id_provinsi.required'  => ':attribute belum diisi',
            'id_kabupaten.required' => ':attribute belum diisi',
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
                "message" => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
