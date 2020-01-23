<?php

namespace App\Http\Requests\AreaKantor;

use Illuminate\Http\Request;
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

    public function rules(Request $request)
    {
        $method = $request->method();

        switch ($method) {
            case 'POST':
                $value = 'integer';
                break;

            case 'PUT':
                $value = 'integer';
                break;
        }

        return [
            'user_id'      => $value,
            'id_mk_area'   => $value,
            'id_mk_cabang' => $value,
            'id_mj_pic'    => $value,
            'flg_aktif'    => 'in:false,true'
       ];
    }

    public function messages(){
        return [
            // 'user_id.required'      => ':attribute wajib diisi',
            'user_id.integer'       => ':attribute harus berupa angka',
            'id_mk_area.required'   => ':attribute wajib diisi',
            'id_mk_cabang.required' => ':attribute wajib diisi',
            'id_mj_pic.required'    => ':attribute wajib diisi',
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
