<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class FasPinRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jenis_pinjaman'  => 'required|in:KONSUMTIF,MODAL,INVESTASI',
            'tujuan_pinjaman' => 'required',
            'plafon'          => 'required|numeric',
            'tenor'           => 'required|numeric'
        ];
    }

    public function messages(){
        return [
            'jenis_pinjaman.required'  => ':attribute belum diisi',
            'jenis_pinjaman.in'        => ':attribute harus bertipe :values',
            'tujuan_pinjaman.required' => ':attribute belum diisi',
            'plafon.required'          => ':attribute belum diisi',
            'plafon.numeric'           => ':attribute harus berupa angka',
            'tenor.required'           => ':attribute belum diisi',
            'tenor.numeric'            => ':attribute harus berupa angka'
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
