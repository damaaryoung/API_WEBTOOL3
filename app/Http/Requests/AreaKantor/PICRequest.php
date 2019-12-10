<?php

namespace App\Http\Requests\AreaKantor;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class PICRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id'      => 'numeric',
            'id_area'      => 'required',
            'id_cabang'    => 'required',
            'id_jenis_pic' => 'required',
            'nama'         => 'required',
            'flg_aktif'    => 'in:0,1'
       ];
    }

    public function messages(){
        return [
            'id_area.required'      => ':attribute belum diisi',
            'id_cabang.required'    => ':attribute belum diisi',
            'user_id.numeric'       => ':attribute harus berupa angka',
            'id_jenis_pic.required' => ':attribute belum diisi',
            'nama.required'         => ':attribute belum diisi',
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
