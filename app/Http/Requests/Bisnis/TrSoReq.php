<?php

namespace App\Http\Requests\Bisnis;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class TrSoReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id'       => 'required',
            'kode_kantor'   => 'required',
            'id_asal_data'  => 'required',
            'nama_marketing'=> 'required',
            'plafon'        => 'required|numeric',
            'tenor'         => 'required|numeric'
        ];
    }

    public function messages(){
        return [
            'user_id.required'        => ':attribute belum diisi',
            'kode_kantor.required'    => ':attribute belum diisi',
            'id_asal_data.required'   => ':attribute belum diisi',
            'nama_marketing.required' => ':attribute belum diisi',
            'plafon.required'         => ':attribute belum diisi',
            'plafon.numeric'          => ':attribute harus berupa angka',
            'tenor.required'          => ':attribute belum diisi',
            'tenor.numeric'           => ':attribute harus berupa angka'
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
