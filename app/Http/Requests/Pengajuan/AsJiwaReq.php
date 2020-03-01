<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class AsJiwaReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            // Asuransi Jiwa pada CA
            // 'jangka_waktu_as_jiwa'        => 'integer|in:12;18;24;30;36;48;60',
            'nilai_pertanggungan_as_jiwa' => 'integer',
            'jatuh_tempo_as_jiwa'         => 'date_format:d-m-Y',
            'berat_badan_as_jiwa'         => 'integer',
            'tinggi_badan_as_jiwa'        => 'integer',
            'umur_nasabah_as_jiwa'        => 'integer',
        ];
    }

    public function messages(){
        $integer     = ':attribute harus berupa angka / bilangan bulat dan tidak boleh dimulai dari 0';
        $date_format = ':attribute harus berupa angka dengan format :format';

        return [
            // Asuransi Jiwa pada CA
            // 'jangka_waktu_as_jiwa.integer'        => $integer,
            // 'jangka_waktu_as_jiwa.in'             => $in,
            'nilai_pertanggungan_as_jiwa.integer' => $integer,
            'jatuh_tempo_as_jiwa.date_format'     => $date_format,
            'berat_badan_as_jiwa.integer'         => $integer,
            'tinggi_badan_as_jiwa.integer'        => $integer,
            'umur_nasabah_as_jiwa.integer'        => $integer,
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
