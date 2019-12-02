<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class KapBulananReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'pemasukan_cadebt'      => 'integer',
            'pemasukan_pasangan'    => 'integer',
            'pemasukan_penjamin'    => 'integer',
            'biaya_rumah_tangga'    => 'integer',
            'biaya_transport'       => 'integer',
            'biaya_pendidikan'      => 'integer',
            'biaya_telp_listr_air'  => 'integer',
            'biaya_lain'            => 'integer'
        ];
    }

    public function messages(){
        return [
            'pemasukan_cadebt.integer'      => ':attribute harus berupa bilangan bulat',
            'pemasukan_pasangan.integer'    => ':attribute harus berupa bilangan bulat',
            'pemasukan_penjamin.integer'    => ':attribute harus berupa bilangan bulat',
            'biaya_rumah_tangga.integer'    => ':attribute harus berupa bilangan bulat',
            'biaya_transport.integer'       => ':attribute harus berupa bilangan bulat',
            'biaya_pendidikan.integer'      => ':attribute harus berupa bilangan bulat',
            'biaya_telp_listr_air.integer'  => ':attribute harus berupa bilangan bulat',
            'biaya_lain.integer'            => ':attribute harus berupa bilangan bulat'
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
