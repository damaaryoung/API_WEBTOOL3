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

class RecomAORequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            // Transaksi AO
            'jangka_waktu'          => 'required|integer',
            'suku_bunga'            => 'required|integer',
            'pembayaran_bunga'      => 'required|integer',
            'akad_kredit'           => 'required|in:ADENDUM,NOTARIS,INTERNAL',
            'ikatan_agunan'         => 'required|in:APHT,SKMHT,FIDUSIA',
            'biaya_provisi'         => 'required|integer',
            'biaya_administrasi'    => 'required|integer',
            'biaya_credit_checking' => 'required|integer',
            'biaya_tabungan'        => 'required|integer',
        ];
    }

    public function messages(){
        return [
            // Transaksi AO
            'jangka_waktu.integer'          => ':attribute harus berupa angka',
            'suku_bunga.integer'            => ':attribute harus berupa angka',
            'pembayaran_bunga.integer'      => ':attribute harus berupa angka',
            'akad_kredit.in'                => ':attribute harus salah satu dari jenis berikut :values',
            'ikatan_agunan.in'              => ':attribute harus salah satu dari jenis berikut :values',
            'biaya_provisi.integer'         => ':attribute harus berupa angka',
            'biaya_administrasi.integer'    => ':attribute harus berupa angka',
            'biaya_credit_checking.integer' => ':attribute harus berupa angka',
            'biaya_tabungan.integer'        => ':attribute harus berupa angka',
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
