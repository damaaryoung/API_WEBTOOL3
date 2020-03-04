<?php

namespace App\Http\Requests\Pengajuan;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class AsJaminanReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {

        return [
            // Asuransi Jaminan pada CA
            // 'jangka_waktu_as_jaminan'      => 'integer|in:12;18;24;30;36;48;60',
            'nilai_pertanggungan_as_jaminan' => 'integer',
            'jatuh_tempo_as_jaminan'         => 'date_format:d-m-Y',
        ];
    }

    public function messages(){
        $integer     = ':attribute harus berupa angka / bilangan bulat dan tidak boleh dimulai dari 0';
        $date_format = ':attribute harus berupa angka dengan format :format';

        return [
            // Asuransi Jaminan pada CA
            // 'jangka_waktu_as_jaminan.integer'        => $integer,
            // 'jangka_waktu_as_jaminan.in'             => $in,
            'nilai_pertanggungan_as_jaminan.integer' => $integer,
            'jatuh_tempo_as_jaminan.date_format'     => $date_format,
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
