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

class VerifRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            // Verifikasi
            'ver_ktp_debt'            => 'numeric',
            'ver_kk_debt'             => 'numeric',
            'ver_akta_cerai_debt'     => 'numeric',
            'ver_akta_kematian_debt'  => 'numeric',
            'ver_rek_tabungan_debt'   => 'numeric',
            'ver_sertifikat_debt'     => 'numeric',
            'ver_sttp_pbb_debt'       => 'numeric',
            'ver_imb_debt'            => 'numeric',
            'ver_ktp_pasangan'        => 'numeric',
            'ver_akta_nikah_pasangan' => 'numeric',
            'ver_data_penjamin'       => 'numeric',
            'ver_sku_debt'            => 'numeric',
            'ver_pembukuan_usaha_debt'=> 'numeric'
        ];
    }

    public function messages(){
        return [
            // Verifikasi
            'ver_ktp_debt.numeric'            => ':attribute harus berupa angka',
            'ver_kk_debt.numeric'             => ':attribute harus berupa angka',
            'ver_akta_cerai_debt.numeric'     => ':attribute harus berupa angka',
            'ver_akta_kematian_debt.numeric'  => ':attribute harus berupa angka',
            'ver_rek_tabungan_debt.numeric'   => ':attribute harus berupa angka',
            'ver_sertifikat_debt.numeric'     => ':attribute harus berupa angka',
            'ver_sttp_pbb_debt.numeric'       => ':attribute harus berupa angka',
            'ver_imb_debt.numeric'            => ':attribute harus berupa angka',
            'ver_ktp_pasangan.numeric'        => ':attribute harus berupa angka',
            'ver_akta_nikah_pasangan.numeric' => ':attribute harus berupa angka',
            'ver_data_penjamin.numeric'       => ':attribute harus berupa angka',
            'ver_sku_debt.numeric'            => ':attribute harus berupa angka',
            'ver_pembukuan_usaha_debt.numeric'=> ':attribute harus berupa angka'
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
