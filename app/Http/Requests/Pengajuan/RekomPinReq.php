<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class RekomPinReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            // Rekomendasi Pinjaman pada CA
            // 'penyimpangan_struktur'=> 'in',
            'recom_nilai_pinjaman' => 'integer',
            'recom_tenor'          => 'integer',
            'recom_angsuran'       => 'integer',
            // 'recom_produk_kredit.integer'  => $integer,
        ];
    }

    public function messages(){
        $integer     = ':attribute harus berupa angka / bilangan bulat dan tidak boleh dimulai dari 0';

        return [
            // Rekomendasi Pinjaman pada CA
            // 'penyimpangan_struktur.in'     => $in,
            'recom_nilai_pinjaman.integer' => $integer,
            'recom_tenor.integer'          => $integer,
            'recom_angsuran.integer'       => $integer,
            // 'recom_produk_kredit.integer'  => $integer,
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
