<?php

namespace App\Http\Requests\Debt;

use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class PemAgTaReq extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status_penghuni.*'       => 'in:PEMILIK,PENYEWA',
            'bentuk_bangunan.*'       => 'in:RUMAH,KONTRAKAN,VILLA,RUKO,APARTMENT',
            'kondisi_bangunan.*'      => 'in:LAYAK,KURANG,TIDAK',
            'nilai_taksasi_agunan.*'  => 'integer',
            'nilai_taksasi_bangunan.*'=> 'integer',
            'tgl_taksasi.*'           => 'date_format:d-m-Y',
            'nilai_likuidasi.*'       => 'integer'
        ];
    }

    public function messages(){
        return [
            'status_penghuni.*.in'              => ':attribute harus salah satu dari jenis berikut :values',
            'bentuk_bangunan.*.in'              => ':attribute harus salah satu dari jenis berikut :values',
            'kondisi_bangunan.*.in'             => ':attribute harus salah satu dari jenis berikut :values',
            'nilai_taksasi_agunan.*.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'nilai_taksasi_bangunan.*.integer'  => ':attribute harus berupa angka / bilangan bulat',
            'tgl_taksasi.*.date_format'         => ':attribute harus berupa angka dengan format :format',
            'nilai_likuidasi.*.integer'         => ':attribute harus berupa angka / bilangan bulat'
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
