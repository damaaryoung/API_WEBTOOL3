<?php

namespace App\Http\Requests\Rekomendasi;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RekomCaReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            'plafon_kredit'             => 'integer',
            'jangka_waktu'              => 'integer',
            'biaya_provisi'             => 'integer',
            'biaya_administrasi'        => 'integer',
            'biaya_credit_checking'     => 'integer',
            'biaya_asuransi_jiwa'       => 'integer',
            'biaya_asuransi_jaminan'    => 'integer',
            'notaris'                   => 'integer',
            'biaya_tabungan'            => 'integer',
            'rekom_angsuran'            => 'integer'
        ];
    }

    public function messages(){
        $integer = ':attribute harus berupa angka / bilangan bulat dan tidak boleh dimulai dari 0';

        return [
            'plafon_kredit.integer'             => $integer,
            'jangka_waktu.integer'              => $integer,
            'biaya_provisi.integer'             => $integer,
            'biaya_administrasi.integer'        => $integer,
            'biaya_credit_checking.integer'     => $integer,
            'biaya_asuransi_jiwa.integer'       => $integer,
            'biaya_asuransi_jaminan.integer'    => $integer,
            'notaris.integer'                   => $integer,
            'biaya_tabungan.integer'            => $integer,
            'rekom_angsuran.integer'            => $integer
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
