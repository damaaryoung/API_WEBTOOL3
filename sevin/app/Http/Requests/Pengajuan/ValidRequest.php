<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class ValidRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return  [
            /// Validasi
            'val_data_debt'       => 'numeric',
            'val_lingkungan_debt' => 'numeric',
            'val_domisili_debt'   => 'numeric',
            'val_pekerjaan_debt'  => 'numeric',
            'val_data_pasangan'   => 'numeric',
            'val_data_penjamin'   => 'numeric',
            'val_agunan'          => 'numeric'
        ];
    }

    public function messages(){
        return [
            // Validasi
            'val_data_debt.numeric'       => ':attribute harus berupa angka',
            'val_lingkungan_debt.numeric' => ':attribute harus berupa angka',
            'val_domisili_debt.numeric'   => ':attribute harus berupa angka',
            'val_pekerjaan_debt.numeric'  => ':attribute harus berupa angka',
            'val_data_pasangan.numeric'   => ':attribute harus berupa angka',
            'val_data_penjamin.numeric'   => ':attribute harus berupa angka',
            'val_agunan.numeric'          => ':attribute harus berupa angka'
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
