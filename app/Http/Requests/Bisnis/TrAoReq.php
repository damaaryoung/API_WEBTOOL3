<?php

namespace App\Http\Requests\Bisnis;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class TrAoReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plafon_kredit'         => 'integer',
            'jangka_waktu'          => 'in:12,18,24,30,36,48,60',
            'suku_bunga'            => 'numeric',
            'pembayaran_bunga'      => 'integer',
            'akad_kredit'           => 'in:ADENDUM,NOTARIS,INTERNAL',
            'biaya_provisi'         => 'integer',
            'biaya_administrasi'    => 'integer',
            'biaya_credit_checking' => 'integer',
            'biaya_tabungan'        => 'integer'
        ];
    }

    public function messages(){
        return [
            'plafon_kredit.integer'         => ':attribute harus berupa bilangan bulat',
            'jangka_waktu.in'               => ':attribute harus salah satu dari nilai berikut :values',
            'suku_bunga.numeric'            => ':attribute harus berupa angka',
            'pembayaran_bunga.integer'      => ':attribute harus berupa bilangan bulat',
            'akad_kredit.in'                => ':attribute harus salah satu dari jenis berikut :values',
            'biaya_provisi.integer'         => ':attribute harus berupa bilangan bulat',
            'biaya_administrasi.integer'    => ':attribute harus berupa bilangan bulat',
            'biaya_credit_checking.integer' => ':attribute harus berupa bilangan bulat',
            'biaya_tabungan.integer'        => ':attribute harus berupa bilangan bulat'
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
